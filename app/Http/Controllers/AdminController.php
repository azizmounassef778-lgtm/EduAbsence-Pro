<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Groupe;
use App\Models\Stagiaire;
use App\Models\Seance;
use App\Models\Absence;
use App\Models\Justificatif;
use App\Models\DeclarationEnseignant;
use App\Models\Notification;
use App\Models\HistoriqueCorrection;
use App\Models\ConseilDiscipline;
use App\Models\SignalementComportement;

class AdminController extends Controller
{
    /**
     * Tableau de bord Administrateur
     */
    public function dashboard(Request $request)
    {
        $totalStagiaires = Stagiaire::count();
        $totalGroupes = Groupe::count();
        $totalAbsences = Absence::count();
        $certificatsEnAttente = Justificatif::where('statut_decision', 'EN_ATTENTE')->count();
        $declarationsEnseignants = DeclarationEnseignant::where('est_consultee', false)->count();

        // Conseils de discipline & Signalements
        $totalConseils = ConseilDiscipline::count();
        $conseilsConvoques = ConseilDiscipline::where('statut', 'CONVOQUE')->count();
        $signalementsEnAttente = SignalementComportement::where('statut', 'EN_ATTENTE')->count();

        // Récentes déclarations des enseignants
        $dernieresDeclarations = DeclarationEnseignant::with(['enseignant', 'seance', 'groupe'])
            ->latest('date_declaration')
            ->take(5)
            ->get();

        // Récents justificatifs à valider
        $justificatifsAValider = Justificatif::with(['absence.stagiaire.user', 'absence.seance', 'absence.enseignant'])
            ->where('statut_decision', 'EN_ATTENTE')
            ->latest('date_depot')
            ->get();

        // Absences récentes
        $dernieresAbsences = Absence::with(['stagiaire.user', 'stagiaire.groupe', 'enseignant', 'seance', 'justificatif'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // Derniers conseils de discipline
        $derniersConseils = ConseilDiscipline::with(['stagiaire.user', 'stagiaire.groupe', 'createur'])
            ->latest('date_conseil')
            ->take(5)
            ->get();

        // Derniers signalements de comportement
        $derniersSignalements = SignalementComportement::with(['enseignant', 'stagiaire.user', 'stagiaire.groupe', 'seance'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStagiaires',
            'totalGroupes',
            'totalAbsences',
            'certificatsEnAttente',
            'declarationsEnseignants',
            'totalConseils',
            'conseilsConvoques',
            'signalementsEnAttente',
            'dernieresDeclarations',
            'justificatifsAValider',
            'dernieresAbsences',
            'derniersConseils',
            'derniersSignalements'
        ));
    }

    /**
     * Tâche 21: Recherche et filtres multi-critères des absences
     */
    public function absences(Request $request)
    {
        $query = Absence::with(['stagiaire.user', 'stagiaire.groupe', 'enseignant', 'seance', 'justificatif']);

        // Filtre par groupe
        if ($request->filled('groupe_id')) {
            $query->whereHas('stagiaire', function ($q) use ($request) {
                $q->where('groupe_id', $request->groupe_id);
            });
        }

        // Filtre par stagiaire (recherche nom ou CNE)
        if ($request->filled('search_stagiaire')) {
            $search = $request->search_stagiaire;
            $query->whereHas('stagiaire', function ($q) use ($search) {
                $q->where('matricule_cne', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par enseignant
        if ($request->filled('enseignant_id')) {
            $query->where('enseignant_id', $request->enseignant_id);
        }

        // Filtre par statut d'absence
        if ($request->filled('statut_absence')) {
            $query->where('statut_absence', $request->statut_absence);
        }

        // Filtre par période
        if ($request->filled('date_debut')) {
            $query->whereDate('date_absence', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_absence', '<=', $request->date_fin);
        }

        // Export CSV si demandé
        if ($request->get('export') === 'csv') {
            return $this->exportAbsencesCsv($query->get());
        }

        $absences = $query->latest('date_absence')->paginate(15)->withQueryString();
        $groupes = Groupe::all();
        $enseignants = User::where('role', 'enseignant')->get();
        $seances = Seance::all();

        return view('admin.absences', compact('absences', 'groupes', 'enseignants', 'seances'));
    }

    /**
     * Tâche 15: Validation des justificatifs par l'administrateur
     */
    public function justificatifs()
    {
        $justificatifs = Justificatif::with(['absence.stagiaire.user', 'absence.stagiaire.groupe', 'absence.seance', 'absence.enseignant', 'adminValidateur'])
            ->latest('date_depot')
            ->paginate(15);

        return view('admin.justificatifs', compact('justificatifs'));
    }

    public function validerJustificatif(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:ACCEPTE,REFUSE',
            'motif_decision' => 'nullable|string|max:500',
        ]);

        $justif = Justificatif::with('absence.stagiaire')->findOrFail($id);
        $decision = $request->decision;

        $justif->update([
            'statut_decision' => $decision,
            'admin_validateur_id' => auth()->id(),
            'date_decision' => Carbon::now(),
            'motif_decision' => $request->motif_decision ?? ($decision === 'ACCEPTE' ? 'Certificat conforme et accepté.' : 'Pièce non recevable.'),
        ]);

        $absence = $justif->absence;
        $absence->update([
            'statut_absence' => $decision === 'ACCEPTE' ? 'JUSTIFIEE_ACCEPTEE' : 'JUSTIFIEE_REFUSEE',
        ]);

        // Recalculer automatiquement la note de discipline du stagiaire
        $stagiaire = $absence->stagiaire;
        $statsDiscipline = $stagiaire->recalculerNoteDiscipline();

        // Notification envoyée au stagiaire (Tâche 16 & 18)
        Notification::create([
            'destinataire_id' => $stagiaire->user_id,
            'type_notification' => 'JUSTIFICATIF_VALIDE',
            'titre' => $decision === 'ACCEPTE' ? 'Justificatif validé avec succès ✅' : 'Justificatif refusé ❌',
            'message' => $decision === 'ACCEPTE'
                ? "Votre justificatif pour l'absence du {$absence->date_absence->format('d/m/Y')} a été accepté. Aucune déduction sur votre note de discipline."
                : "Votre justificatif a été refusé. Motif : \"{$justif->motif_decision}\". Une pénalité de -0.5 pt a été appliquée.",
            'lien_action' => '/stagiaire/absences',
        ]);

        return back()->with('success', "Le justificatif #{$justif->id} a été statué comme [{$decision}]. La note du stagiaire est maintenant de {$statsDiscipline['note']}/20.00 pts.");
    }

    /**
     * Tâche 22: Correction d'une absence
     */
    public function corrigerAbsence(Request $request, $id)
    {
        $absence = Absence::with(['stagiaire.user', 'seance'])->findOrFail($id);

        if ($request->has('action_supprimer')) {
            $stagiaire = $absence->stagiaire;
            $oldInfo = "Absence du {$absence->date_absence} (Séance {$absence->seance->code_seance}) pour {$stagiaire->user->name}";

            HistoriqueCorrection::create([
                'absence_id' => $absence->id,
                'admin_id' => auth()->id(),
                'champ_modifie' => 'ANNULATION_ABSENCE',
                'ancienne_valeur' => $oldInfo,
                'nouvelle_valeur' => 'SUPPRIMÉE',
                'motif_correction' => $request->motif_correction ?? 'Absence enregistrée par erreur, supprimée par l\'administration.',
            ]);

            $absence->delete();
            $stagiaire->recalculerNoteDiscipline();

            return redirect()->route('admin.absences')->with('success', 'L\'absence a été annulée et supprimée. La note de comportement a été recalculée.');
        }

        $request->validate([
            'date_absence' => 'required|date',
            'seance_id' => 'required|exists:seances,id',
            'motif_enseignant' => 'nullable|string',
            'motif_correction' => 'required|string|min:5',
        ]);

        $modifs = [];
        if ($absence->date_absence->format('Y-m-d') !== $request->date_absence) {
            $modifs[] = "Date: {$absence->date_absence->format('Y-m-d')} -> {$request->date_absence}";
        }
        if ($absence->seance_id != $request->seance_id) {
            $modifs[] = "Séance: {$absence->seance_id} -> {$request->seance_id}";
        }

        $absence->update([
            'date_absence' => $request->date_absence,
            'seance_id' => $request->seance_id,
            'motif_enseignant' => $request->motif_enseignant,
            'est_corrigee' => true,
        ]);

        HistoriqueCorrection::create([
            'absence_id' => $absence->id,
            'admin_id' => auth()->id(),
            'champ_modifie' => 'MODIFICATION_CHAMPS',
            'ancienne_valeur' => implode(' | ', $modifs) ?: 'Détails modifiés',
            'nouvelle_valeur' => "Mis à jour par " . auth()->user()->name,
            'motif_correction' => $request->motif_correction,
        ]);

        $absence->stagiaire->recalculerNoteDiscipline();

        return back()->with('success', 'Absence corrigée avec succès. L\'historique d\'audit a été enregistré.');
    }

    /**
     * Tâche 5: Gestion des comptes utilisateurs
     */
    public function users()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

  public function storeUser(Request $request)
{
$request->validate([
'name' => 'required|string|max:255',
'email' => 'required|string|email|max:255|unique:users,email',
'password' => 'required|string|min:6',
'role' => 'required|in:admin,enseignant,stagiaire',
'telephone' => 'nullable|string',
'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
], [
'avatar_file.image' => 'Le fichier doit être une image valide.',
'avatar_file.mimes' => 'Formats acceptés : JPG, PNG, WEBP, GIF.',
'avatar_file.max' => 'La taille maximale autorisée est de 3 Mo.',
]);


$avatarPath = null;

if ($request->hasFile('avatar_file')) {
    $file = $request->file('avatar_file');
    $fileName = 'avatar_' . uniqid() . '.' . $file->getClientOriginalExtension();

    $destination = public_path('uploads/avatars');

    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }

    $file->move($destination, $fileName);

    $avatarPath = asset('uploads/avatars/' . $fileName);
}

$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role' => $request->role,
    'statut_compte' => 'actif',
    'telephone' => $request->telephone,
    'avatar' => $avatarPath,
]);

return back()->with(
    'success',
    "Le compte de {$user->name} ({$user->role}) a été créé avec succès dans la base de données !"
);


}

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->statut_compte = $user->statut_compte === 'actif' ? 'desactive' : 'actif';
        $user->save();

        return back()->with('success', "Le statut du compte de {$user->name} est maintenant : {$user->statut_compte}.");
    }

    /**
     * Tâche 7: Gestion des groupes
     */
    public function groupes()
    {
        $groupes = Groupe::with(['responsable', 'stagiaires'])->get();
        $enseignants = User::where('role', 'enseignant')->get();
        return view('admin.groupes', compact('groupes', 'enseignants'));
    }

    public function storeGroupe(Request $request)
    {
        $request->validate([
            'code_groupe' => 'required|string|unique:groupes,code_groupe',
            'nom_groupe' => 'required|string',
            'filiere' => 'required|string',
            'annee_formation' => 'required|integer|in:1,2',
            'id_responsable' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        Groupe::create($request->all());

        return back()->with('success', 'Nouveau groupe créé avec succès dans la base de données.');
    }

    /**
     * Tâche 8: Gestion des stagiaires
     */
    public function stagiaires()
    {
        $stagiaires = Stagiaire::with(['user', 'groupe', 'absences'])->paginate(15);
        $groupes = Groupe::all();
        return view('admin.stagiaires', compact('stagiaires', 'groupes'));
    }

    public function storeStagiaire(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'matricule_cne' => 'required|string|unique:stagiaires,matricule_cne',
            'groupe_id' => 'required|exists:groupes,id',
            'telephone' => 'nullable|string',
            'date_naissance' => 'nullable|date',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password envoyé dans la base de données!
            'role' => 'stagiaire',
            'statut_compte' => 'actif',
            'telephone' => $request->telephone,
        ]);

        Stagiaire::create([
            'user_id' => $user->id,
            'matricule_cne' => strtoupper($request->matricule_cne),
            'groupe_id' => $request->groupe_id,
            'date_naissance' => $request->date_naissance,
            'note_discipline' => 20.00,
        ]);

        return back()->with('success', "Le dossier du stagiaire {$user->name} a été enregistré dans la base de données.");
    }

    /**
     * Tâche 17: Consultation des déclarations des enseignants
     */
    public function declarations()
    {
        $declarations = DeclarationEnseignant::with(['enseignant', 'seance', 'groupe'])
            ->latest('date_declaration')
            ->paginate(15);

        // Marquer comme consultées
        DeclarationEnseignant::where('est_consultee', false)->update(['est_consultee' => true]);

        return view('admin.declarations', compact('declarations'));
    }

    /**
     * Journal d'audit des corrections (Tâche 22)
     */
    public function auditCorrections()
    {
        $corrections = HistoriqueCorrection::with(['admin', 'absence.stagiaire.user'])
            ->latest()
            ->paginate(20);

        return view('admin.audit', compact('corrections'));
    }

    /**
     * Tâche Spéciale : Conseils de Discipline (15h automatiques et sanctions de comportement)
     */
    public function conseilsDiscipline(Request $request)
    {
        // Déclencher la vérification automatique du seuil de 15h sur l'ensemble des stagiaires
        $stagiairesNonJustifies = Stagiaire::with(['absences.seance', 'user', 'groupe'])->get();
        foreach ($stagiairesNonJustifies as $st) {
            $st->verifierEtDeclencherConseil15h();
        }

        $query = ConseilDiscipline::with(['stagiaire.user', 'stagiaire.groupe', 'createur', 'signalement.enseignant']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search_stagiaire')) {
            $search = $request->search_stagiaire;
            $query->whereHas('stagiaire', function ($q) use ($search) {
                $q->where('matricule_cne', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $conseils = $query->latest('date_conseil')->paginate(15)->withQueryString();

        // Récupérer les signalements des professeurs
        $signalements = SignalementComportement::with(['enseignant', 'stagiaire.user', 'stagiaire.groupe', 'seance'])
            ->latest('date_incident')
            ->take(10)
            ->get();

        $stagiaires = Stagiaire::with(['user', 'groupe'])->get();

        return view('admin.conseils-discipline', compact('conseils', 'signalements', 'stagiaires'));
    }

    public function storeConseilDiscipline(Request $request)
    {
        $request->validate([
            'stagiaire_id' => 'required|exists:stagiaires,id',
            'type' => 'required|in:COMPORTEMENT,ABSENCE_15H,INFRACTION_REGLEMENT',
            'date_conseil' => 'required|date',
            'motif' => 'required|string|min:4|max:255',
            'description' => 'nullable|string|max:1000',
            'signalement_id' => 'nullable|exists:signalements_comportement,id',
        ]);

        $stagiaire = Stagiaire::with(['user', 'groupe'])->findOrFail($request->stagiaire_id);

        $conseil = ConseilDiscipline::create([
            'stagiaire_id' => $stagiaire->id,
            'type' => $request->type,
            'motif' => $request->motif,
            'description' => $request->description,
            'date_conseil' => $request->date_conseil,
            'statut' => 'CONVOQUE',
            'reduction_points' => 5.00,
            'cree_par_id' => auth()->id(),
            'signalement_id' => $request->signalement_id,
        ]);

        if ($request->filled('signalement_id')) {
            SignalementComportement::where('id', $request->signalement_id)->update(['statut' => 'CONSEIL_PROGRAMME']);
        }

        // Recalculer automatiquement la note de comportement (-5 points déduits)
        $statsDiscipline = $stagiaire->recalculerNoteDiscipline();

        // Notification envoyée au stagiaire
        Notification::create([
            'destinataire_id' => $stagiaire->user_id,
            'type_notification' => 'CONSEIL_DISCIPLINE_ADMIN',
            'titre' => 'Convocation au Conseil de Discipline (Comportement)',
            'message' => "L'administration vous convoque à un conseil de discipline pour le " . Carbon::parse($request->date_conseil)->format('d/m/Y') . ". Motif : \"{$request->motif}\". Une déduction automatique de -5 points a été appliquée à votre note de comportement.",
            'lien_action' => '/stagiaire/note-discipline',
        ]);

        return back()->with('success', "Conseil de discipline pour comportement programmé pour {$stagiaire->user->name}. Une réduction automatique de -5 points a été appliquée à sa note de comportement (Nouvelle note : {$statsDiscipline['note']}/20.00).");
    }

    public function updateConseilStatut(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|in:CONVOQUE,TENU,SANCTIONNE,CLOTURE',
        ]);

        $conseil = ConseilDiscipline::with('stagiaire')->findOrFail($id);
        $conseil->update(['statut' => $request->statut]);

        return back()->with('success', "Le statut du conseil de discipline #{$conseil->id} a été mis à jour : [{$request->statut}].");
    }

    public function destroyConseil($id)
    {
        $conseil = ConseilDiscipline::with('stagiaire')->findOrFail($id);
        $stagiaire = $conseil->stagiaire;
        $conseil->delete();

        // Recalculer la note (restitue automatiquement les 5 points au stagiaire)
        $stats = $stagiaire->recalculerNoteDiscipline();

        return back()->with('success', "Le conseil de discipline a été supprimé. Les 5 points ont été restitués au stagiaire (Note de comportement : {$stats['note']}/20.00).");
    }

    /**
     * Export CSV des absences filtrées
     */
    private function exportAbsencesCsv($absences)
    {
        $filename = "absences_export_" . date('Y-m-d_His') . ".csv";
        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($absences) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['ID Absence', 'Date', 'Séance', 'Stagiaire', 'CNE', 'Groupe', 'Enseignant', 'Statut', 'Motif']);

            foreach ($absences as $abs) {
                fputcsv($handle, [
                    $abs->id,
                    $abs->date_absence->format('d/m/Y'),
                    $abs->seance->code_seance,
                    $abs->stagiaire->user->name ?? 'N/A',
                    $abs->stagiaire->matricule_cne ?? 'N/A',
                    $abs->stagiaire->groupe->code_groupe ?? 'N/A',
                    $abs->enseignant->name ?? 'N/A',
                    $abs->statut_absence,
                    $abs->motif_enseignant,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // delete
    public function destroyUser(User $user)
{
    
    if ($user->id === auth()->id()) {
        return back()->with(
            'error',
            'Vous ne pouvez pas supprimer votre propre compte.'
        );
    }

    try {
        \Illuminate\Support\Facades\DB::transaction(function () use ($user) {

           
            if ($user->isStagiaire()) {
                $user->stagiaireProfile()->delete();
            }
            $user->delete();
        });

        return back()->with(
            'success',
            'Le compte utilisateur a été supprimé avec succès.'
        );

    } catch (\Illuminate\Database\QueryException $e) {
        return back()->with(
            'error',
            'Impossible de supprimer ce compte car il possède encore des données liées.'
        );
    }
}}