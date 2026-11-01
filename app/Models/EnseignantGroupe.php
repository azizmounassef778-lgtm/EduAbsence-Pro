<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnseignantGroupe extends Model
{
    use HasFactory;

    protected $table = 'enseignant_groupes';

    protected $fillable = [
        'enseignant_id',
        'groupe_id',
        'matiere',
    ];

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe_id');
    }
}
