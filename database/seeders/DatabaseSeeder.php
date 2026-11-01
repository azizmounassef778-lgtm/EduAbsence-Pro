<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Groupe;
use App\Models\Stagiaire;
use App\Models\EnseignantGroupe;
use App\Models\Seance;
use App\Models\Absence;
use App\Models\Justificatif;
use App\Models\DeclarationEnseignant;
use App\Models\Notification;
use App\Models\HistoriqueCorrection;
use App\Models\ConseilDiscipline;
use App\Models\SignalementComportement;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. UTILISATEURS (avec mots de passe hashés par Bcrypt dans la vraie base de données)
        $admin = User::create([
            'name' => 'Louzi Boutayna',
            'prenom' => 'Boutayna',
            'nom' => 'Louzi',
            'email' => 'louzi.boutayna@edu.ma',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'statut_compte' => 'actif',
            'telephone' => '+212 6 12 34 56 78',
            'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=150&q=80',
        ]);

        $profAli = User::create([
            'name' => 'Ali Ouchen',
            'prenom' => 'Ali',
            'nom' => 'Ouchen',
            'email' => 'ali.ouchen@edu.ma',
            'password' => Hash::make('prof123'),
            'role' => 'enseignant',
            'statut_compte' => 'actif',
            'telephone' => '+212 6 23 45 67 89',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
        ]);

        $profAziz = User::create([
            'name' => 'Aziz Mounassef',
            'prenom' => 'Aziz',
            'nom' => 'Mounassef',
            'email' => 'aziz.mounassef@edu.ma',
            'password' => Hash::make('prof123'),
            'role' => 'enseignant',
            'statut_compte' => 'actif',
            'telephone' => '+212 6 34 56 78 90',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
        ]);

        $userChihab = User::create([
            'name' => 'Mohammed Chihab',
            'prenom' => 'Mohammed',
            'nom' => 'Chihab',
            'email' => 'mohammed.chihab@edu.ma',
            'password' => Hash::make('stagiaire123'),
            'role' => 'stagiaire',
            'statut_compte' => 'actif',
            'telephone' => '+212 6 45 67 89 01',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80',
        ]);

        $userElazri = User::create([
            'name' => 'Meryem Elazri',
            'prenom' => 'Meryem',
            'nom' => 'Elazri',
            'email' => 'meryem.elazri@edu.ma',
            'password' => Hash::make('stagiaire123'),
            'role' => 'stagiaire',
            'statut_compte' => 'actif',
            'telephone' => '+212 6 56 78 90 12',
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80',
        ]);

        $userOuiarrara = User::create([
            'name' => 'Hassan Ouiarrara',
            'prenom' => 'Hassan',
            'nom' => 'Ouiarrara',
            'email' => 'hassan.ouiarrara@edu.ma',
            'password' => Hash::make('stagiaire123'),
            'role' => 'stagiaire',
            'statut_compte' => 'actif',
            'telephone' => '+212 6 67 89 01 23',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=150&q=80',
        ]);

        $userAlami = User::create([
            'name' => 'Fatima-Zahra Alami',
            'prenom' => 'Fatima-Zahra',
            'nom' => 'Alami',
            'email' => 'fatima.alami@edu.ma',
            'password' => Hash::make('stagiaire123'),
            'role' => 'stagiaire',
            'statut_compte' => 'actif',
            'telephone' => '+212 6 78 90 12 34',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80',
        ]);

        // 2. GROUPES
        $g1 = Groupe::create([
            'code_groupe' => 'DEV101',
            'nom_groupe' => 'Développement Digital (Web & Full-Stack)',
            'filiere' => 'Développement Digital',
            'annee_formation' => 1,
            'id_responsable' => $profAli->id,
            'description' => 'Première année - Algorithmique, Frontend, Laravel, Bases de Données',
        ]);

        $g2 = Groupe::create([
            'code_groupe' => 'DEV201',
            'nom_groupe' => 'Développement Digital (Applications Web & Mobile)',
            'filiere' => 'Développement Digital',
            'annee_formation' => 2,
            'id_responsable' => $profAli->id,
            'description' => 'Deuxième année - Architectures Microservices, API REST, Cloud',
        ]);

        $g3 = Groupe::create([
            'code_groupe' => 'INFRA102',
            'nom_groupe' => 'Infrastructure Digitale & Cloud Computing',
            'filiere' => 'Infrastructure Digitale',
            'annee_formation' => 1,
            'id_responsable' => $profAziz->id,
            'description' => 'Systèmes Réseaux, Administration Linux & Sécurité',
        ]);

        // 3. STAGIAIRES
        $stChihab = Stagiaire::create([
            'user_id' => $userChihab->id,
            'matricule_cne' => 'M130098452',
            'groupe_id' => $g1->id,
            'date_naissance' => '2003-05-14',
            'adresse' => 'Casablanca, Maroc',
            'note_discipline' => 20.00,
        ]);

        $stElazri = Stagiaire::create([
            'user_id' => $userElazri->id,
            'matricule_cne' => 'M145023981',
            'groupe_id' => $g1->id,
            'date_naissance' => '2003-08-22',
            'adresse' => 'Rabat, Maroc',
            'note_discipline' => 20.00,
        ]);

        $stOuiarrara = Stagiaire::create([
            'user_id' => $userOuiarrara->id,
            'matricule_cne' => 'M138472910',
            'groupe_id' => $g3->id,
            'date_naissance' => '2002-11-09',
            'adresse' => 'Marrakech, Maroc',
            'note_discipline' => 20.00,
        ]);

        $stAlami = Stagiaire::create([
            'user_id' => $userAlami->id,
            'matricule_cne' => 'M139120485',
            'groupe_id' => $g2->id,
            'date_naissance' => '2002-03-30',
            'adresse' => 'Tanger, Maroc',
            'note_discipline' => 20.00,
        ]);

        // 4. ATTRIBUTIONS ENSEIGNANT - GROUPES
        EnseignantGroupe::create(['enseignant_id' => $profAli->id, 'groupe_id' => $g1->id, 'matiere' => 'Développement Web Laravel & JS']);
        EnseignantGroupe::create(['enseignant_id' => $profAli->id, 'groupe_id' => $g2->id, 'matiere' => 'Architectures Web Avancées']);
        EnseignantGroupe::create(['enseignant_id' => $profAziz->id, 'groupe_id' => $g1->id, 'matiere' => 'Conception SQL & Modélisation']);
        EnseignantGroupe::create(['enseignant_id' => $profAziz->id, 'groupe_id' => $g3->id, 'matiere' => 'Administration Réseaux Cisco']);

        // 5. SÉANCES
        $s1 = Seance::create(['code_seance' => 'S1', 'libelle' => '08h30 - 11h00', 'heure_debut' => '08:30:00', 'heure_fin' => '11:00:00']);
        $s2 = Seance::create(['code_seance' => 'S2', 'libelle' => '11h00 - 13h30', 'heure_debut' => '11:00:00', 'heure_fin' => '13:30:00']);
        $s3 = Seance::create(['code_seance' => 'S3', 'libelle' => '14h30 - 16h30', 'heure_debut' => '14:30:00', 'heure_fin' => '16:30:00']);
        $s4 = Seance::create(['code_seance' => 'S4', 'libelle' => '16h30 - 18h30', 'heure_debut' => '16:30:00', 'heure_fin' => '18:30:00']);

        // 6. ABSENCES RÉALISTES
        // Absence 1: Mohammed Chihab - Il y a 8h (Dépôt certificat en cours de validation)
        $abs1 = Absence::create([
            'stagiaire_id' => $stChihab->id,
            'enseignant_id' => $profAli->id,
            'seance_id' => $s1->id,
            'date_absence' => $now->copy()->subHours(8)->format('Y-m-d'),
            'heure_enregistrement' => $now->copy()->subHours(8),
            'statut_absence' => 'EN_ATTENTE_VALIDATION',
            'motif_enseignant' => 'Non présent lors de l\'appel en cours de Laravel',
            'est_corrigee' => false,
        ]);

        Justificatif::create([
            'absence_id' => $abs1->id,
            'nom_fichier' => 'certificat_medical_dr_tazi.pdf',
            'chemin_fichier' => 'uploads/justificatifs/certificat_medical_dr_tazi.pdf',
            'type_fichier' => 'application/pdf',
            'taille_octets' => 184500,
            'date_depot' => $now->copy()->subHours(6),
            'statut_decision' => 'EN_ATTENTE',
            'admin_validateur_id' => null,
            'date_decision' => null,
            'motif_decision' => null,
        ]);

        // Absence 2: Mohammed Chihab - Il y a 22h (Reste 26h! Pas de justificatif encore -> le stagiaire peut tester le dépôt direct!)
        $abs2 = Absence::create([
            'stagiaire_id' => $stChihab->id,
            'enseignant_id' => $profAziz->id,
            'seance_id' => $s3->id,
            'date_absence' => $now->copy()->subHours(22)->format('Y-m-d'),
            'heure_enregistrement' => $now->copy()->subHours(22),
            'statut_absence' => 'NON_JUSTIFIEE',
            'motif_enseignant' => 'Absence non signalée en travaux pratiques de Bases de Données',
            'est_corrigee' => false,
        ]);

        // Absence 3: Mohammed Chihab - Il y a 80h (> 48h, délai expiré -> pénalité -0.5 pt!)
        $abs3 = Absence::create([
            'stagiaire_id' => $stChihab->id,
            'enseignant_id' => $profAli->id,
            'seance_id' => $s2->id,
            'date_absence' => $now->copy()->subHours(80)->format('Y-m-d'),
            'heure_enregistrement' => $now->copy()->subHours(80),
            'statut_absence' => 'NON_JUSTIFIEE',
            'motif_enseignant' => 'Absence en cours magistral',
            'est_corrigee' => false,
        ]);

        // Absence 4: Mohammed Chihab - Il y a 10 jours (Justificatif accepté par l'admin -> 0 pt retiré!)
        $abs4 = Absence::create([
            'stagiaire_id' => $stChihab->id,
            'enseignant_id' => $profAziz->id,
            'seance_id' => $s4->id,
            'date_absence' => $now->copy()->subDays(10)->format('Y-m-d'),
            'heure_enregistrement' => $now->copy()->subDays(10),
            'statut_absence' => 'JUSTIFIEE_ACCEPTEE',
            'motif_enseignant' => 'Retard puis absence',
            'est_corrigee' => false,
        ]);

        Justificatif::create([
            'absence_id' => $abs4->id,
            'nom_fichier' => 'attestation_convocation_officielle.pdf',
            'chemin_fichier' => 'uploads/justificatifs/attestation_convocation_officielle.pdf',
            'type_fichier' => 'application/pdf',
            'taille_octets' => 245000,
            'date_depot' => $now->copy()->subDays(9),
            'statut_decision' => 'ACCEPTE',
            'admin_validateur_id' => $admin->id,
            'date_decision' => $now->copy()->subDays(9),
            'motif_decision' => 'Convocation officielle recevable et validée par la direction.',
        ]);

        // Absence 5: Meryem Elazri - Justificatif refusé (-0.5 pt)
        $abs5 = Absence::create([
            'stagiaire_id' => $stElazri->id,
            'enseignant_id' => $profAli->id,
            'seance_id' => $s1->id,
            'date_absence' => $now->copy()->subDays(5)->format('Y-m-d'),
            'heure_enregistrement' => $now->copy()->subDays(5),
            'statut_absence' => 'JUSTIFIEE_REFUSEE',
            'motif_enseignant' => 'Absence constatée au contrôle continu',
            'est_corrigee' => false,
        ]);

        Justificatif::create([
            'absence_id' => $abs5->id,
            'nom_fichier' => 'ordonnance_scan.jpg',
            'chemin_fichier' => 'uploads/justificatifs/ordonnance_scan.jpg',
            'type_fichier' => 'image/jpeg',
            'taille_octets' => 125000,
            'date_depot' => $now->copy()->subDays(4),
            'statut_decision' => 'REFUSE',
            'admin_validateur_id' => $admin->id,
            'date_decision' => $now->copy()->subDays(4),
            'motif_decision' => 'Ordonnance simple sans certificat d\'arrêt de cours ni mention d\'inaptitude.',
        ]);

        // Absence 6: Meryem Elazri - > 48h sans justificatif (-0.5 pt)
        Absence::create([
            'stagiaire_id' => $stElazri->id,
            'enseignant_id' => $profAli->id,
            'seance_id' => $s2->id,
            'date_absence' => $now->copy()->subDays(6)->format('Y-m-d'),
            'heure_enregistrement' => $now->copy()->subDays(6),
            'statut_absence' => 'NON_JUSTIFIEE',
            'motif_enseignant' => 'Absence en séance de projet',
            'est_corrigee' => false,
        ]);

        // Absences 7 à 12 pour Hassan Ouiarrara : Cumul de 15h non justifiées (6 séances de 2.5h) pour déclencher le Conseil de Discipline automatique
        for ($i = 1; $i <= 6; $i++) {
            Absence::create([
                'stagiaire_id' => $stOuiarrara->id,
                'enseignant_id' => ($i % 2 == 0) ? $profAziz->id : $profAli->id,
                'seance_id' => ($i % 2 == 0) ? $s1->id : $s2->id, // S1 et S2 font chacune 2h30 (2.5h * 6 = 15.0h)
                'date_absence' => $now->copy()->subDays($i + 2)->format('Y-m-d'),
                'heure_enregistrement' => $now->copy()->subDays($i + 2),
                'statut_absence' => 'NON_JUSTIFIEE',
                'motif_enseignant' => 'Absence non justifiée constatée en séance ' . (($i % 2 == 0) ? 'S1' : 'S2'),
                'est_corrigee' => false,
            ]);
        }

        // Conseil de discipline manuel ajouté par l'admin pour comportement (Meryem Elazri)
        $conseilManuel = ConseilDiscipline::create([
            'stagiaire_id' => $stElazri->id,
            'type' => 'COMPORTEMENT',
            'motif' => 'Perturbation répétée des cours et attitude non conforme au règlement intérieur',
            'description' => 'Décision administrative prise suite aux réclamations de l\'équipe pédagogique.',
            'date_conseil' => $now->copy()->addDays(5)->toDateString(),
            'statut' => 'CONVOQUE',
            'reduction_points' => 5.00,
            'cree_par_id' => $admin->id,
        ]);

        Notification::create([
            'destinataire_id' => $userElazri->id,
            'type_notification' => 'CONSEIL_DISCIPLINE_ADMIN',
            'titre' => 'Convocation : Conseil de Discipline pour Comportement',
            'message' => 'L\'administration a programmé un conseil de discipline pour comportement le ' . Carbon::parse($conseilManuel->date_conseil)->format('d/m/Y') . '. Une pénalité automatique de -5 points a été appliquée à votre note de comportement.',
            'lien_action' => '/stagiaire/note-discipline',
            'est_lue' => false,
            'created_at' => $now->copy()->subHours(2),
        ]);

        // Signalement de comportement effectué par un enseignant (M. Ali Ouchen signale Mohammed Chihab)
        $signalement = SignalementComportement::create([
            'enseignant_id' => $profAli->id,
            'stagiaire_id' => $stChihab->id,
            'seance_id' => $s1->id,
            'date_incident' => $now->copy()->subHours(5)->toDateString(),
            'motif' => 'Refus répété des consignes d\'atelier et attitude perturbatrice',
            'description' => 'Durant les travaux pratiques de programmation, le stagiaire a refusé d\'appliquer les consignes et a perturbé ses collègues de manière récurrente.',
            'gravite' => 'MOYENNE',
            'statut' => 'EN_ATTENTE',
        ]);

        Notification::create([
            'destinataire_id' => $admin->id,
            'type_notification' => 'SIGNALEMENT_COMPORTEMENT',
            'titre' => 'Signalement comportement enseignant : ' . $stChihab->user->name,
            'message' => 'M. Ali Ouchen a signalé un incident de comportement concernant le stagiaire ' . $stChihab->user->name . ' (' . $stChihab->matricule_cne . ', DEV101). Action requise.',
            'lien_action' => '/admin/conseils-discipline',
            'est_lue' => false,
            'created_at' => $now->copy()->subHours(5),
        ]);

        // Recalculer les notes de discipline des stagiaires (déclenche aussi le conseil automatique 15h pour Hassan Ouiarrara)
        $stChihab->recalculerNoteDiscipline();
        $stElazri->recalculerNoteDiscipline();
        $stOuiarrara->recalculerNoteDiscipline();
        $stAlami->recalculerNoteDiscipline();

        // 7. DÉCLARATION D'ABSENCE ENSEIGNANT (Tâche 11)
        DeclarationEnseignant::create([
            'enseignant_id' => $profAli->id,
            'seance_id' => $s3->id,
            'groupe_id' => $g1->id,
            'date_absence' => $now->copy()->addDays(2)->format('Y-m-d'),
            'motif' => 'Participation en tant que membre du jury régional aux Olympiades des Métiers du Numérique.',
            'remarques_rattrapage' => 'Séance de rattrapage programmée le samedi matin de 09h00 à 11h00 en salle 4.',
            'date_declaration' => $now->copy()->subHours(12),
            'est_consultee' => false,
        ]);

        // 8. NOTIFICATIONS (Tâche 17 & 18)
        Notification::create([
            'destinataire_id' => $admin->id,
            'type_notification' => 'ABSENCE_ENSEIGNANT',
            'titre' => 'Déclaration d\'absence Enseignant',
            'message' => 'M. Ali Ouchen a déclaré une absence pour le cours de Développement Digital (DEV101) - Séance S3.',
            'lien_action' => '/admin/declarations',
            'est_lue' => false,
            'created_at' => $now->copy()->subHours(12),
        ]);

        Notification::create([
            'destinataire_id' => $admin->id,
            'type_notification' => 'JUSTIFICATIF_DEPOSE',
            'titre' => 'Nouveau justificatif à valider',
            'message' => 'Le stagiaire Mohammed Chihab (DEV101) a téléversé un certificat médical. Décision requise.',
            'lien_action' => '/admin/justificatifs',
            'est_lue' => false,
            'created_at' => $now->copy()->subHours(6),
        ]);

        Notification::create([
            'destinataire_id' => $userChihab->id,
            'type_notification' => 'ABSENCE_STAGIAIRE',
            'titre' => 'Nouvelle absence enregistrée',
            'message' => 'Une absence a été signalée aujourd\'hui en séance S1 par M. Ali Ouchen. Vous avez 48 heures pour déposer votre certificat médical.',
            'lien_action' => '/stagiaire/absences',
            'est_lue' => false,
            'created_at' => $now->copy()->subHours(8),
        ]);

        Notification::create([
            'destinataire_id' => $userChihab->id,
            'type_notification' => 'ABSENCE_STAGIAIRE',
            'titre' => 'Rappel : Délai de 48 heures',
            'message' => 'Une absence a été saisie en séance S3. Il vous reste moins de 26 heures pour téléverser votre justificatif.',
            'lien_action' => '/stagiaire/absences',
            'est_lue' => false,
            'created_at' => $now->copy()->subHours(22),
        ]);

        // 9. HISTORIQUE DES CORRECTIONS (Tâche 22)
        HistoriqueCorrection::create([
            'absence_id' => $abs3->id,
            'admin_id' => $admin->id,
            'champ_modifie' => 'seance',
            'ancienne_valeur' => 'Séance S1 (08h30-11h00)',
            'nouvelle_valeur' => 'Séance S2 (11h00-13h30)',
            'motif_correction' => 'Erreur de saisie de créneau horaire rectifiée suite à vérification de la feuille d\'émargement.',
            'created_at' => $now->copy()->subDays(1),
        ]);
    }
}
