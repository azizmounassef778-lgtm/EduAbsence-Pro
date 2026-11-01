<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seance extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_seance',
        'libelle',
        'heure_debut',
        'heure_fin',
    ];

    public function absences()
    {
        return $this->hasMany(Absence::class, 'seance_id');
    }
}
