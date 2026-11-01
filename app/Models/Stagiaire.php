<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Stagiaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matricule_cne',
        'groupe_id',
        'date_naissance',
        'adresse',
        'note_discipline',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'note_discipline' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe_id');
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'stagiaire_id')->orderBy('date_absence', 'desc');
    }

    public function conseilsDiscipline()
    {
        return $this->hasMany(ConseilDiscipline::class, 'stagiaire_id')->orderBy('date_conseil', 'desc');
    }

    public function signalementsComportement()
    {
        return $this->hasMany(SignalementComportement::class, 'stagiaire_id')->orderBy('date_incident', 'desc');
    }

    /**
     * Calcul du total d'heures d'absences non justifiées
     * Chaque séance compte pour 2.5h (ou selon ses horaires réels)
     */
    public function totalHeuresNonJustifiees(): float
    {
        $absences = $this->absences()->with('seance')->get();
        $totalHeures = 0.0;

        foreach ($absences as $abs) {
            if ($abs->statut_absence === 'NON_JUSTIFIEE' || $abs->statut_absence === 'JUSTIFIEE_REFUSEE') {
                $duree = 2.5;
                if ($abs->seance && $abs->seance->heure_debut && $abs->seance->heure_fin) {
                    $debut = Carbon::parse($abs->seance->heure_debut);
                    $fin = Carbon::parse($abs->seance->heure_fin);
                    $diff = $fin->diffInMinutes($debut);
                    if ($diff > 0) {
                        $duree = round($diff / 60, 1);
                    }
                }
                $totalHeures += $duree;
            }
        }

        return $totalHeures;
    }

    /**
     * Vérification et déclenchement automatique du conseil de discipline à 15h
     */
    public function verifierEtDeclencherConseil15h(): ?ConseilDiscipline
    {
        $heuresSansJustif = $this->totalHeuresNonJustifiees();

        if ($heuresSansJustif >= 15.0) {
            // Vérifier si un conseil automatique pour 15h n'a pas déjà été créé
            $existe = $this->conseilsDiscipline()
                ->where('type', 'ABSENCE_15H')
                ->first();

            if (!$existe) {
                $conseil = ConseilDiscipline::create([
                    'stagiaire_id' => $this->id,
                    'type' => 'ABSENCE_15H',
                    'motif' => 'Cumul critique de ' . number_format($heuresSansJustif, 1) . 'h d\'absences sans justification',
                    'description' => 'Convocation automatique émise par le système : le seuil réglementaire de 15 heures d\'absences non justifiées a été atteint ou dépassé.',
                    'date_conseil' => Carbon::now()->addDays(7)->toDateString(),
                    'statut' => 'CONVOQUE',
                    'reduction_points' => 5.00,
                    'cree_par_id' => null,
                ]);

                // Notification au Stagiaire
                Notification::create([
                    'destinataire_id' => $this->user_id,
                    'type_notification' => 'CONSEIL_DISCIPLINE_AUTO',
                    'titre' => 'Convocation au Conseil de Discipline (15h d\'absences)',
                    'message' => "Vous avez accumulé {$heuresSansJustif}h d'absences sans justificatif. Un conseil de discipline a été convoqué et une pénalité automatique de -5 points a été appliquée à votre note de comportement.",
                    'lien_action' => '/stagiaire/note-discipline',
                ]);

                // Notification à tous les Administrateurs
                $admins = User::where('role', 'admin')->get();
                $nomStagiaire = $this->user ? $this->user->name : 'Stagiaire #' . $this->id;
                $codeGroupe = $this->groupe ? $this->groupe->code_groupe : 'N/A';
                foreach ($admins as $admin) {
                    Notification::create([
                        'destinataire_id' => $admin->id,
                        'type_notification' => 'CONSEIL_DISCIPLINE_AUTO',
                        'titre' => "Alerte 15h : Conseil de discipline automatique ({$nomStagiaire})",
                        'message' => "Le stagiaire {$nomStagiaire} ({$this->matricule_cne}, Groupe {$codeGroupe}) a atteint {$heuresSansJustif}h d'absences sans justification. Conseil de discipline émis et -5 points appliqués.",
                        'lien_action' => '/admin/conseils-discipline',
                    ]);
                }

                return $conseil;
            }
        }

        return null;
    }

    /**
     * Calcul automatique de la note globale de comportement
     * Note initiale : 20 / 20 points
     * Déduction : 0.5 point par absence non justifiée
     * Déduction conseil de discipline : -5 points par conseil de discipline (automatique ou manuel)
     * Note plancher : 0.00 / 20
     */
    public function recalculerNoteDiscipline(): array
    {
        $base = 20.00;
        $now = Carbon::now();

        // Vérifier si seuil 15h atteint pour déclencher le conseil automatique
        $this->verifierEtDeclencherConseil15h();

        $absences = $this->absences()->get();

        $nbJustifieesAcceptees = 0;
        $nbJustifieesRefusees = 0;
        $nbEnAttente = 0;
        $nbNonJustifieesExpirees = 0;
        $nbNonJustifieesEnCours48h = 0;

        foreach ($absences as $abs) {
            $diffHours = Carbon::parse($abs->heure_enregistrement)->diffInHours($now, false);

            if ($abs->statut_absence === 'JUSTIFIEE_ACCEPTEE') {
                $nbJustifieesAcceptees++;
            } elseif ($abs->statut_absence === 'JUSTIFIEE_REFUSEE') {
                $nbJustifieesRefusees++;
            } elseif ($abs->statut_absence === 'EN_ATTENTE_VALIDATION') {
                $nbEnAttente++;
            } elseif ($abs->statut_absence === 'NON_JUSTIFIEE') {
                if ($diffHours > 48) {
                    $nbNonJustifieesExpirees++;
                } else {
                    $nbNonJustifieesEnCours48h++;
                }
            }
        }

        // Pénalités absences (0.50 pt par absence refusée ou expirée >48h)
        $totalPenalisantes = $nbJustifieesRefusees + $nbNonJustifieesExpirees;
        $penaliteAbsences = $totalPenalisantes * 0.50;

        // Pénalités Conseils de Discipline : 5 points par conseil
        $conseils = $this->conseilsDiscipline()->get();
        $nbConseils = $conseils->count();
        $penaliteConseils = $conseils->sum('reduction_points');
        if ($penaliteConseils <= 0 && $nbConseils > 0) {
            $penaliteConseils = $nbConseils * 5.00;
        }

        $penaliteTotale = $penaliteAbsences + $penaliteConseils;
        $nouvelleNote = max(0.00, min(20.00, round($base - $penaliteTotale, 2)));

        $this->note_discipline = $nouvelleNote;
        $this->save();

        $appreciation = "Excellente assiduité";
        $badgeClass = "badge-success";
        if ($nouvelleNote >= 18.0) {
            $appreciation = "Excellente assiduité";
            $badgeClass = "badge-success";
        } elseif ($nouvelleNote >= 14.0) {
            $appreciation = "Discipline satisfaisante";
            $badgeClass = "badge-info";
        } elseif ($nouvelleNote >= 10.0) {
            $appreciation = "Avertissement : Vigilance requise";
            $badgeClass = "badge-warning";
        } else {
            $appreciation = "Situation disciplinaire critique";
            $badgeClass = "badge-danger";
        }

        $heuresSansJustif = $this->totalHeuresNonJustifiees();

        return [
            'base' => $base,
            'note' => $nouvelleNote,
            'noteSur20' => $nouvelleNote,
            'totalAbsences' => $absences->count(),
            'totalHeuresNonJustifiees' => $heuresSansJustif,
            'nbJustifieesAcceptees' => $nbJustifieesAcceptees,
            'nbJustifieesRefusees' => $nbJustifieesRefusees,
            'nbEnAttente' => $nbEnAttente,
            'nbNonJustifieesExpirees' => $nbNonJustifieesExpirees,
            'nbNonJustifieesEnCours48h' => $nbNonJustifieesEnCours48h,
            'totalPenalisantes' => $totalPenalisantes,
            'penaliteAbsences' => number_format($penaliteAbsences, 2),
            'nbConseilsDiscipline' => $nbConseils,
            'penaliteConseils' => number_format($penaliteConseils, 2),
            'penaliteAppliquee' => number_format($penaliteTotale, 2),
            'appreciation' => $appreciation,
            'badgeClass' => $badgeClass,
            'pourcentage' => round(($nouvelleNote / 20.00) * 100),
        ];
    }
}
