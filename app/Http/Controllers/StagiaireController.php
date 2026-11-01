<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Stagiaire;
use App\Models\Absence;
use App\Models\Justificatif;
use App\Models\Notification;

class StagiaireController extends Controller
{
    /**
     * Tableau de bord Stagiaire
     * Tâche 19 & 20: Calcul et consultation de la note de comportement
     */
    public function dashboard()
    {
        $user = auth()->user();
        $stagiaire = $user->stagiaireProfile()->with(['groupe', 'absences.seance', 'absences.enseignant', 'absences.justificatif'])->first();

        if (!$stagiaire) {
            return view('stagiaire.no-profile');
        }

        // Recalcul automatique et récupération des détails de discipline (Tâche 19)
        $statsDiscipline = $stagiaire->recalculerNoteDiscipline();

        // Récentes absences avec calcul de leur délai 48h (Tâche 14)
        $absences = $stagiaire->absences()->with(['seance', 'enseignant', 'justificatif'])->latest('date_absence')->take(6)->get();

        $absencesWithDelai = $absences->map(function ($abs) {
            $abs->delaiInfo = $abs->delai48h();
            return $abs;
        });

        // Notifications non lues
        $notifications = $user->notifications()->take(5)->get();

        // Conseils de discipline du stagiaire
        $conseils = $stagiaire->conseilsDiscipline()->get();

        return view('stagiaire.dashboard', compact(
            'stagiaire',
            'statsDiscipline',
            'absencesWithDelai',
            'notifications',
            'conseils'
        ));
    }

    /**
     * Tâche 12: Consultation de ses absences
     * Tâche 14: Gestion du délai de 48 heures par absence
     * Tâche 16: Suivi du justificatif
     */
    public function absences()
    {
        $user = auth()->user();
        $stagiaire = $user->stagiaireProfile()->firstOrFail();

        $absences = $stagiaire->absences()
            ->with(['seance', 'enseignant', 'justificatif.adminValidateur'])
            ->latest('date_absence')
            ->paginate(10);

        // Injecter le calcul du délai 48h
        $absences->getCollection()->transform(function ($abs) {
            $abs->delaiInfo = $abs->delai48h();
            return $abs;
        });

        return view('stagiaire.absences', compact('stagiaire', 'absences'));
    }

    /**
     * Tâche 13: Dépôt d'un certificat médical ou officiel
     * Tâche 14: Vérification du délai légal de 48 heures
     */
    public function deposerJustificatif(Request $request, $absenceId)
    {
        $user = auth()->user();
        $stagiaire = $user->stagiaireProfile()->firstOrFail();

        $absence = Absence::where('id', $absenceId)
            ->where('stagiaire_id', $stagiaire->id)
            ->firstOrFail();

        // Vérification stricte du délai de 48 heures (Tâche 14)
        $delai = $absence->delai48h();
        if ($delai['est_expire']) {
            return back()->with('error', "Le délai légal de 48 heures est expiré. Vous ne pouvez plus déposer de certificat pour cette absence.");
        }

        // Validation du format et de la taille du fichier (Tâche 13)
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,png,jpg,jpeg|max:5120', // max 5 Mo
            'commentaire' => 'nullable|string|max:500',
        ], [
            'fichier.mimes' => 'Format invalide. Seuls les fichiers PDF, PNG et JPG sont acceptés.',
            'fichier.max' => 'La taille maximale autorisée est de 5 Mo.',
        ]);

        $file = $request->file('fichier');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
        
        // Sauvegarde sécurisée
        $destinationPath = public_path('uploads/justificatifs');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        $file->move($destinationPath, $fileName);
        $filePath = 'uploads/justificatifs/' . $fileName;

        // Supprimer l'ancien justificatif s'il existait
        if ($absence->justificatif) {
            $absence->justificatif->delete();
        }

        // Enregistrer le justificatif dans la base de données
        Justificatif::create([
            'absence_id' => $absence->id,
            'nom_fichier' => $file->getClientOriginalName(),
            'chemin_fichier' => $filePath,
            'type_fichier' => $file->getClientMimeType() ?: 'application/octet-stream',
            'taille_octets' => file_exists(public_path($filePath)) ? filesize(public_path($filePath)) : 150000,
            'date_depot' => Carbon::now(),
            'statut_decision' => 'EN_ATTENTE',
        ]);

        // Mettre à jour le statut de l'absence
        $absence->update(['statut_absence' => 'EN_ATTENTE_VALIDATION']);

        // Recalculer la note
        $stagiaire->recalculerNoteDiscipline();

        // Notifier l'administration (Tâche 17)
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'destinataire_id' => $admin->id,
                'type_notification' => 'JUSTIFICATIF_DEPOSE',
                'titre' => "Nouveau certificat médical déposé",
                'message' => "Le stagiaire {$user->name} ({$stagiaire->matricule_cne}) a téléversé un justificatif pour son absence du {$absence->date_absence->format('d/m/Y')}. Décision requise.",
                'lien_action' => '/admin/justificatifs',
            ]);
        }

        return back()->with('success', "Votre certificat a été téléversé avec succès. Il est en cours d'examen par l'administration.");
    }

    /**
     * Tâche 20: Page détaillée de la note de comportement
     */
    public function noteComportement()
    {
        $user = auth()->user();
        $stagiaire = $user->stagiaireProfile()->with(['groupe', 'absences.seance', 'absences.justificatif'])->firstOrFail();
        $statsDiscipline = $stagiaire->recalculerNoteDiscipline();

        $absences = $stagiaire->absences()->with(['seance', 'justificatif'])->latest('date_absence')->get();
        $conseils = $stagiaire->conseilsDiscipline()->get();

        return view('stagiaire.note-discipline', compact('stagiaire', 'statsDiscipline', 'absences', 'conseils'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function marquerNotificationLue($id)
    {
        $notif = Notification::where('id', $id)
            ->where('destinataire_id', auth()->id())
            ->firstOrFail();

        $notif->update(['est_lue' => true]);
        return back();
    }
}
