<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Justificatif extends Model
{
    use HasFactory;

    protected $fillable = [
        'absence_id',
        'nom_fichier',
        'chemin_fichier',
        'type_fichier',
        'taille_octets',
        'date_depot',
        'statut_decision',
        'admin_validateur_id',
        'date_decision',
        'motif_decision',
    ];

    protected $casts = [
        'date_depot' => 'datetime',
        'date_decision' => 'datetime',
    ];

    public function absence()
    {
        return $this->belongsTo(Absence::class, 'absence_id');
    }

    public function adminValidateur()
    {
        return $this->belongsTo(User::class, 'admin_validateur_id');
    }

    public function tailleLisible(): string
    {
        $bytes = $this->taille_octets;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' Mo';
        }
        return round($bytes / 1024) . ' Ko';
    }
}
