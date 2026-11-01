<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeclarationEnseignant extends Model
{
    use HasFactory;

    protected $table = 'declarations_enseignants';

    protected $fillable = [
        'enseignant_id',
        'seance_id',
        'groupe_id',
        'date_absence',
        'motif',
        'remarques_rattrapage',
        'date_declaration',
        'est_consultee',
    ];

    protected $casts = [
        'date_absence' => 'date',
        'date_declaration' => 'datetime',
        'est_consultee' => 'boolean',
    ];

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function seance()
    {
        return $this->belongsTo(Seance::class, 'seance_id');
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe_id');
    }
}
