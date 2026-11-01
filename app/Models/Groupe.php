<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_groupe',
        'nom_groupe',
        'filiere',
        'annee_formation',
        'id_responsable',
        'description',
    ];

    public function stagiaires()
    {
        return $this->hasMany(Stagiaire::class, 'groupe_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'id_responsable');
    }

    public function enseignantGroupes()
    {
        return $this->hasMany(EnseignantGroupe::class, 'groupe_id');
    }
}
