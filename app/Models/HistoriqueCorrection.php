<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueCorrection extends Model
{
    use HasFactory;

    protected $table = 'historique_corrections';

    protected $fillable = [
        'absence_id',
        'admin_id',
        'champ_modifie',
        'ancienne_valeur',
        'nouvelle_valeur',
        'motif_correction',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function absence()
    {
        return $this->belongsTo(Absence::class, 'absence_id');
    }
}
