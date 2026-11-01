
CREATE DATABASE IF NOT EXISTS db_gestion_absences
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE db_gestion_absences;

-- 1. Utilisateurs : administrateurs, enseignants et stagiaires
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    prenom VARCHAR(100) NULL,
    nom VARCHAR(100) NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'enseignant', 'stagiaire')
        NOT NULL DEFAULT 'stagiaire',
    statut_compte ENUM('actif', 'desactive')
        NOT NULL DEFAULT 'actif',
    telephone VARCHAR(30) NULL,
    avatar VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- 2. Groupes de formation
CREATE TABLE IF NOT EXISTS groupes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code_groupe VARCHAR(30) NOT NULL UNIQUE,
    nom_groupe VARCHAR(255) NOT NULL,
    filiere VARCHAR(150) NOT NULL,
    annee_formation INT NOT NULL DEFAULT 1
        CHECK (annee_formation IN (1, 2)),
    id_responsable BIGINT UNSIGNED NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_groupes_responsable
        FOREIGN KEY (id_responsable)
        REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 3. Stagiaires
CREATE TABLE IF NOT EXISTS stagiaires (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    matricule_cne VARCHAR(50) NOT NULL UNIQUE,
    groupe_id BIGINT UNSIGNED NOT NULL,
    date_naissance DATE NULL,
    adresse VARCHAR(255) NULL,
    note_discipline DECIMAL(4, 2) NOT NULL DEFAULT 2.50
        CHECK (note_discipline >= 0.00 AND note_discipline <= 2.50),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_stagiaires_user
        FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_stagiaires_groupe
        FOREIGN KEY (groupe_id)
        REFERENCES groupes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Affectation des enseignants aux groupes et matières
CREATE TABLE IF NOT EXISTS enseignant_groupes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    enseignant_id BIGINT UNSIGNED NOT NULL,
    groupe_id BIGINT UNSIGNED NOT NULL,
    matiere VARCHAR(150) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_eg_enseignant
        FOREIGN KEY (enseignant_id)
        REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_eg_groupe
        FOREIGN KEY (groupe_id)
        REFERENCES groupes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Séances
CREATE TABLE IF NOT EXISTS seances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code_seance VARCHAR(20) NOT NULL,
    libelle VARCHAR(100) NOT NULL,
    heure_debut TIME NULL,
    heure_fin TIME NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- 6. Absences
CREATE TABLE IF NOT EXISTS absences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id BIGINT UNSIGNED NOT NULL,
    enseignant_id BIGINT UNSIGNED NOT NULL,
    seance_id BIGINT UNSIGNED NOT NULL,
    date_absence DATE NOT NULL,
    heure_enregistrement TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut_absence ENUM(
        'NON_JUSTIFIEE',
        'EN_ATTENTE_VALIDATION',
        'JUSTIFIEE_ACCEPTEE',
        'JUSTIFIEE_REFUSEE'
    ) NOT NULL DEFAULT 'NON_JUSTIFIEE',
    motif_enseignant TEXT NULL,
    est_corrigee BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_abs_stagiaire
        FOREIGN KEY (stagiaire_id)
        REFERENCES stagiaires(id) ON DELETE CASCADE,
    CONSTRAINT fk_abs_enseignant
        FOREIGN KEY (enseignant_id)
        REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_abs_seance
        FOREIGN KEY (seance_id)
        REFERENCES seances(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Justificatifs d'absence
CREATE TABLE IF NOT EXISTS justificatifs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    absence_id BIGINT UNSIGNED NOT NULL UNIQUE,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(255) NOT NULL,
    type_fichier VARCHAR(100) NOT NULL,
    taille_octets INT NOT NULL,
    date_depot TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut_decision ENUM('EN_ATTENTE', 'ACCEPTE', 'REFUSE')
        NOT NULL DEFAULT 'EN_ATTENTE',
    admin_validateur_id BIGINT UNSIGNED NULL,
    date_decision TIMESTAMP NULL,
    motif_decision TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_justif_absence
        FOREIGN KEY (absence_id)
        REFERENCES absences(id) ON DELETE CASCADE,
    CONSTRAINT fk_justif_admin
        FOREIGN KEY (admin_validateur_id)
        REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 8. Déclarations d'absence des enseignants
CREATE TABLE IF NOT EXISTS declarations_enseignants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    enseignant_id BIGINT UNSIGNED NOT NULL,
    seance_id BIGINT UNSIGNED NOT NULL,
    groupe_id BIGINT UNSIGNED NOT NULL,
    date_absence DATE NOT NULL,
    motif TEXT NOT NULL,
    remarques_rattrapage TEXT NULL,
    date_declaration TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    est_consultee BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_decl_enseignant
        FOREIGN KEY (enseignant_id)
        REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_decl_seance
        FOREIGN KEY (seance_id)
        REFERENCES seances(id) ON DELETE CASCADE,
    CONSTRAINT fk_decl_groupe
        FOREIGN KEY (groupe_id)
        REFERENCES groupes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Notifications
CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    destinataire_id BIGINT UNSIGNED NOT NULL,
    type_notification VARCHAR(100) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    lien_action VARCHAR(255) NULL,
    est_lue BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_notif_destinataire
        FOREIGN KEY (destinataire_id)
        REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Historique des corrections
CREATE TABLE IF NOT EXISTS historique_corrections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    absence_id BIGINT UNSIGNED NULL,
    admin_id BIGINT UNSIGNED NOT NULL,
    champ_modifie VARCHAR(100) NOT NULL,
    ancienne_valeur TEXT NULL,
    nouvelle_valeur TEXT NULL,
    motif_correction TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_hist_admin
        FOREIGN KEY (admin_id)
        REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. Signalements de comportement
CREATE TABLE IF NOT EXISTS signalements_comportement (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    enseignant_id BIGINT UNSIGNED NOT NULL,
    stagiaire_id BIGINT UNSIGNED NOT NULL,
    seance_id BIGINT UNSIGNED NULL,
    date_incident DATE NOT NULL,
    motif VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    gravite VARCHAR(50) NOT NULL DEFAULT 'MOYENNE',
    statut VARCHAR(50) NOT NULL DEFAULT 'EN_ATTENTE',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_sig_enseignant FOREIGN KEY (enseignant_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_sig_stagiaire FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id) ON DELETE CASCADE,
    CONSTRAINT fk_sig_seance FOREIGN KEY (seance_id) REFERENCES seances(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 12. Conseils de discipline
CREATE TABLE IF NOT EXISTS conseils_discipline (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'COMPORTEMENT',
    motif VARCHAR(255) NOT NULL,
    description TEXT NULL,
    date_conseil DATE NOT NULL,
    statut VARCHAR(50) NOT NULL DEFAULT 'CONVOQUE',
    reduction_points DECIMAL(4, 2) NOT NULL DEFAULT 5.00,
    cree_par_id BIGINT UNSIGNED NULL,
    signalement_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_cd_stagiaire FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id) ON DELETE CASCADE,
    CONSTRAINT fk_cd_createur FOREIGN KEY (cree_par_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_cd_signalement FOREIGN KEY (signalement_id) REFERENCES signalements_comportement(id) ON DELETE SET NULL
) ENGINE=InnoDB;