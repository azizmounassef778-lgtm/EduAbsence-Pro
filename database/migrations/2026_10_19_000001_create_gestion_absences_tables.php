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
        // 1. Groupes / Classes
        Schema::create('groupes', function (Blueprint $table) {
            $table->id();
            $table->string('code_groupe')->unique();
            $table->string('nom_groupe');
            $table->string('filiere');
            $table->integer('annee_formation')->default(1);
            $table->foreignId('id_responsable')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Stagiaires
        Schema::create('stagiaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('matricule_cne')->unique();
            $table->foreignId('groupe_id')->constrained('groupes')->cascadeOnDelete();
            $table->date('date_naissance')->nullable();
            $table->string('adresse')->nullable();
            $table->decimal('note_discipline', 4, 2)->default(20.00);
            $table->timestamps();
        });

        // 3. Enseignant - Groupes
        Schema::create('enseignant_groupes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('groupe_id')->constrained('groupes')->cascadeOnDelete();
            $table->string('matiere');
            $table->timestamps();
        });

        // 4. Séances
        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->string('code_seance'); // S1, S2, S3, S4
            $table->string('libelle');     // 08h30 - 11h00
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->timestamps();
        });

        // 5. Absences
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->cascadeOnDelete();
            $table->foreignId('enseignant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seance_id')->constrained('seances')->cascadeOnDelete();
            $table->date('date_absence');
            $table->timestamp('heure_enregistrement')->useCurrent();
            $table->string('statut_absence')->default('NON_JUSTIFIEE'); // 'NON_JUSTIFIEE', 'EN_ATTENTE_VALIDATION', 'JUSTIFIEE_ACCEPTEE', 'JUSTIFIEE_REFUSEE'
            $table->text('motif_enseignant')->nullable();
            $table->boolean('est_corrigee')->default(false);
            $table->timestamps();
        });

        // 6. Justificatifs
        Schema::create('justificatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absence_id')->constrained('absences')->cascadeOnDelete();
            $table->string('nom_fichier');
            $table->string('chemin_fichier');
            $table->string('type_fichier');
            $table->integer('taille_octets');
            $table->timestamp('date_depot')->useCurrent();
            $table->string('statut_decision')->default('EN_ATTENTE'); // 'EN_ATTENTE', 'ACCEPTE', 'REFUSE'
            $table->foreignId('admin_validateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_decision')->nullable();
            $table->text('motif_decision')->nullable();
            $table->timestamps();
        });

        // 7. Déclarations des enseignants
        Schema::create('declarations_enseignants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seance_id')->constrained('seances')->cascadeOnDelete();
            $table->foreignId('groupe_id')->constrained('groupes')->cascadeOnDelete();
            $table->date('date_absence');
            $table->text('motif');
            $table->text('remarques_rattrapage')->nullable();
            $table->timestamp('date_declaration')->useCurrent();
            $table->boolean('est_consultee')->default(false);
            $table->timestamps();
        });

        // 8. Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destinataire_id')->constrained('users')->cascadeOnDelete();
            $table->string('type_notification'); // 'ABSENCE_STAGIAIRE', 'ABSENCE_ENSEIGNANT', 'JUSTIFICATIF_DEPOSE', 'JUSTIFICATIF_VALIDE'
            $table->string('titre');
            $table->text('message');
            $table->string('lien_action')->nullable();
            $table->boolean('est_lue')->default(false);
            $table->timestamps();
        });

        // 9. Historique des corrections
        Schema::create('historique_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absence_id')->nullable();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('champ_modifie');
            $table->text('ancienne_valeur')->nullable();
            $table->text('nouvelle_valeur')->nullable();
            $table->text('motif_correction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_corrections');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('declarations_enseignants');
        Schema::dropIfExists('justificatifs');
        Schema::dropIfExists('absences');
        Schema::dropIfExists('seances');
        Schema::dropIfExists('enseignant_groupes');
        Schema::dropIfExists('stagiaires');
        Schema::dropIfExists('groupes');
    }
};
