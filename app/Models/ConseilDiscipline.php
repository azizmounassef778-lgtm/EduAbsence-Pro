<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConseilDiscipline extends Model
{
    use HasFactory;

    protected $table = 'conseils_discipline';

    protected $fillable = [
        'stagiaire_id',
        'type',
        'motif',
        'description',
        'date_conseil',
        'statut',
        'reduction_points',
        'cree_par_id',
        'signalement_id',
    ];

    protected $casts = [
        'date_conseil' => 'date',
        'reduction_points' => 'decimal:2',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class, 'stagiaire_id');
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par_id');
    }

    public function signalement()
    {
        return $this->belongsTo(SignalementComportement::class, 'signalement_id');
    }
}
