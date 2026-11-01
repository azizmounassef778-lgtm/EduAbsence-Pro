<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'stagiaire_id',
        'enseignant_id',
        'seance_id',
        'date_absence',
        'heure_enregistrement',
        'statut_absence',
        'motif_enseignant',
        'est_corrigee',
    ];

    protected $casts = [
        'date_absence' => 'date',
        'heure_enregistrement' => 'datetime',
        'est_corrigee' => 'boolean',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class, 'stagiaire_id');
    }

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function seance()
    {
        return $this->belongsTo(Seance::class, 'seance_id');
    }

    public function justificatif()
    {
        return $this->hasOne(Justificatif::class, 'absence_id');
    }

    public function historique()
    {
        return $this->hasMany(HistoriqueCorrection::class, 'absence_id')->orderBy('created_at', 'desc');
    }

    /**
     * Tâche 14: Gestion du délai de dépôt de 48 heures
     */
    public function delai48h(): array
    {
        $limite = Carbon::parse($this->heure_enregistrement)->addHours(48);
        $now = Carbon::now();

        if ($now->greaterThanOrEqualTo($limite)) {
            $heuresDepassees = $limite->diffInHours($now);
            return [
                'est_expire' => true,
                'peut_deposer' => false,
                'texte' => 'Délai expiré (> 48h)',
                'badge_class' => 'badge-expired',
                'heures_restantes' => 0,
                'minutes_restantes' => 0,
                'message' => "Le délai légal de dépôt de 48 heures est dépassé depuis {$heuresDepassees}h. Vous ne pouvez plus déposer de certificat pour cette absence.",
            ];
        }

        $heures = $now->diffInHours($limite);
        $minutes = $now->copy()->addHours($heures)->diffInMinutes($limite);

        $badgeClass = 'badge-timer-ok';
        if ($heures < 12) {
            $badgeClass = 'badge-timer-urgent';
        } elseif ($heures < 24) {
            $badgeClass = 'badge-timer-warning';
        }

        return [
            'est_expire' => false,
            'peut_deposer' => true,
            'texte' => "{$heures}h {$minutes}m restantes",
            'badge_class' => $badgeClass,
            'heures_restantes' => $heures,
            'minutes_restantes' => $minutes,
            'message' => "Il vous reste {$heures}h {$minutes}m pour téléverser votre pièce justificative.",
        ];
    }
}
