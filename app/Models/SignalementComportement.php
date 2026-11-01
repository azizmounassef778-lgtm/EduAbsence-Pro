<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignalementComportement extends Model
{
    use HasFactory;

    protected $table = 'signalements_comportement';

    protected $fillable = [
        'enseignant_id',
        'stagiaire_id',
        'seance_id',
        'date_incident',
        'motif',
        'description',
        'gravite',
        'statut',
    ];

    protected $casts = [
        'date_incident' => 'date',
    ];

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class, 'stagiaire_id');
    }

    public function seance()
    {
        return $this->belongsTo(Seance::class, 'seance_id');
    }

    public function conseilDiscipline()
    {
        return $this->hasOne(ConseilDiscipline::class, 'signalement_id');
    }
}
