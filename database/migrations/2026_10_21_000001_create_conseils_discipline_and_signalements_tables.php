<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Signalements de comportement par les enseignants
        Schema::create('signalements_comportement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->cascadeOnDelete();
            $table->foreignId('seance_id')->nullable()->constrained('seances')->nullOnDelete();
            $table->date('date_incident');
            $table->string('motif');
            $table->text('description');
            $table->string('gravite')->default('MOYENNE'); // FAIBLE, MOYENNE, GRAVE
            $table->string('statut')->default('EN_ATTENTE'); // EN_ATTENTE, CONSEIL_PROGRAMME, CLASSE
            $table->timestamps();
        });

        // 2. Conseils de discipline (automatiques pour 15h d'absences ou manuels pour comportement)
        Schema::create('conseils_discipline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->cascadeOnDelete();
            $table->string('type')->default('COMPORTEMENT'); // 'ABSENCE_15H', 'COMPORTEMENT', 'AUTRE'
            $table->string('motif');
            $table->text('description')->nullable();
            $table->date('date_conseil');
            $table->string('statut')->default('CONVOQUE'); // 'CONVOQUE', 'TENU', 'SANCTIONNE', 'CLOTURE'
            $table->decimal('reduction_points', 4, 2)->default(5.00); // Réduction de 5 points
            $table->foreignId('cree_par_id')->nullable()->constrained('users')->nullOnDelete(); // Null si automatique système
            $table->foreignId('signalement_id')->nullable()->constrained('signalements_comportement')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conseils_discipline');
        Schema::dropIfExists('signalements_comportement');
    }
};
