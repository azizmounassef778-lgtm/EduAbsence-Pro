@extends('layouts.app')

@section('title', 'Documentation & Conception (Sprints 1 à 6)')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800;">Dossier de Conception & Product Backlog</h1>
        <p style="color: var(--text-muted);">
            Tâches 1, 2 et 3 : Cahier des Charges, MCD/MLD, Script SQL DDL et Suivi des 22 Tâches Scrum
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('docs.download-sql') }}" class="btn btn-primary btn-sm">
            💾 Télécharger le Script SQL (DDL)
        </a>
    </div>
</div>

<!-- Onglets de Navigation -->
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; overflow-x: auto;">
    <button class="btn btn-secondary btn-sm doc-tab-btn active" onclick="switchDocTab('backlog')">📋 1. Backlog des 22 Tâches</button>
    <button class="btn btn-secondary btn-sm doc-tab-btn" onclick="switchDocTab('cahier')">📖 2. Cahier des Charges (Tâche 1)</button>
    <button class="btn btn-secondary btn-sm doc-tab-btn" onclick="switchDocTab('mcd')">📊 3. MCD & Diagramme (Tâche 2)</button>
    <button class="btn btn-secondary btn-sm doc-tab-btn" onclick="switchDocTab('mld')">📐 4. MLD Relationnel (Tâche 2)</button>
    <button class="btn btn-secondary btn-sm doc-tab-btn" onclick="switchDocTab('sql')">💻 5. Script SQL DDL (Tâche 3)</button>
</div>

<!-- ==========================================
     SECTION 1: BACKLOG DES 22 TÂCHES (SPRINTS 1 À 6)
     ========================================== -->
<div id="tab-backlog" class="doc-tab-content">
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-title">
            <span>Product Backlog - Application Web de Gestion des Absences</span>
            <span class="badge badge-success">22 Tâches Réalisées • 100% Livré</span>
        </div>

        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem;">
            Tableau conforme au document de spécification de l'équipe Scrum. Toutes les fonctionnalités ci-dessous sont opérationnelles dans cette application Laravel 12 connectée à une vraie base de données.
        </p>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sprint</th>
                        <th>Tâche</th>
                        <th>Description Fonctionnelle</th>
                        <th>Priorité</th>
                        <th>Effort / Temps</th>
                        <th>État</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Sprint 1 -->
                    <tr>
                        <td><strong>tache 1</strong></td>
                        <td><span class="badge badge-primary">Sprint 1</span></td>
                        <td><strong>Rédaction du cahier des charges</strong></td>
                        <td style="font-size: 0.8rem;">Contexte du projet, objectifs, besoins fonctionnels et non fonctionnels, utilisateurs et contraintes.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 8H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 2</strong></td>
                        <td><span class="badge badge-primary">Sprint 1</span></td>
                        <td><strong>Conception de la base de données</strong></td>
                        <td style="font-size: 0.8rem;">Identification des entités, attributs, MCD et MLD (comptes, groupes, stagiaires, absences, justificatifs).</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>5 SP / 16H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 3</strong></td>
                        <td><span class="badge badge-primary">Sprint 1</span></td>
                        <td><strong>Création de la base de données</strong></td>
                        <td style="font-size: 0.8rem;">Création de la base et tables nécessaires, clés primaires et étrangères, vérification de cohérence.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 8H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>

                    <!-- Sprint 2 -->
                    <tr>
                        <td><strong>tache 4</strong></td>
                        <td><span class="badge badge-primary">Sprint 2</span></td>
                        <td><strong>Authentification</strong></td>
                        <td style="font-size: 0.8rem;">Connexion sécurisée en base de données avec identifiants et interface adaptée au rôle (Admin, Enseignant, Stagiaire).</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>5 SP / 16H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 5</strong></td>
                        <td><span class="badge badge-primary">Sprint 2</span></td>
                        <td><strong>Gestion des comptes</strong></td>
                        <td style="font-size: 0.8rem;">Création, modification et désactivation des comptes, attribution des rôles et droits d'accès.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>5 SP / 16H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 6</strong></td>
                        <td><span class="badge badge-primary">Sprint 2</span></td>
                        <td><strong>Déconnexion sécurisée</strong></td>
                        <td style="font-size: 0.8rem;">Fermeture sécurisée de la session et protection de toutes les routes après déconnexion.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>2 SP / 4H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 7</strong></td>
                        <td><span class="badge badge-primary">Sprint 2</span></td>
                        <td><strong>Gestion des groupes (listes)</strong></td>
                        <td style="font-size: 0.8rem;">Création, modification et consultation des groupes pour organiser les utilisateurs par classe ou filière.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 8H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 8</strong></td>
                        <td><span class="badge badge-primary">Sprint 2</span></td>
                        <td><strong>Gestion des stagiaires</strong></td>
                        <td style="font-size: 0.8rem;">Ajout, modification et suppression des dossiers stagiaires et affectation aux groupes correspondants.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>5 SP / 16H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>

                    <!-- Sprint 3 -->
                    <tr>
                        <td><strong>tache 9</strong></td>
                        <td><span class="badge badge-primary">Sprint 3</span></td>
                        <td><strong>Consultation listes stagiaires</strong></td>
                        <td style="font-size: 0.8rem;">Permettre aux enseignants de consulter les listes des stagiaires des groupes qui leur sont attribués.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 8H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 10</strong></td>
                        <td><span class="badge badge-primary">Sprint 3</span></td>
                        <td><strong>Enregistrement d'une absence</strong></td>
                        <td style="font-size: 0.8rem;">Saisie par l'enseignant de l'absence (stagiaire, date, séance) et sauvegarde en base de données.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>5 SP / 16H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 11</strong></td>
                        <td><span class="badge badge-primary">Sprint 3</span></td>
                        <td><strong>Déclaration d'absence enseignant</strong></td>
                        <td style="font-size: 0.8rem;">Permettre à l'enseignant de déclarer sa propre absence et enregistrer la déclaration pour l'administration.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>5 SP / 16H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 12</strong></td>
                        <td><span class="badge badge-primary">Sprint 3</span></td>
                        <td><strong>Consultation des absences</strong></td>
                        <td style="font-size: 0.8rem;">Consultation par le stagiaire de ses propres absences, dates, séances et statut du justificatif.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 8H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>

                    <!-- Sprint 4 -->
                    <tr>
                        <td><strong>tache 13</strong></td>
                        <td><span class="badge badge-primary">Sprint 4</span></td>
                        <td><strong>Dépôt d'un certificat</strong></td>
                        <td style="font-size: 0.8rem;">Téléversement d'un certificat numérique lié à une absence, vérification du format et stockage sécurisé.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>5 SP / 16H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 14</strong></td>
                        <td><span class="badge badge-primary">Sprint 4</span></td>
                        <td><strong>Gestion du délai de 48 heures</strong></td>
                        <td style="font-size: 0.8rem;">Autoriser le dépôt pendant 48h suivant l'absence, désactiver le dépôt à l'expiration et afficher message explicatif.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>5 SP / 12H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 15</strong></td>
                        <td><span class="badge badge-primary">Sprint 4</span></td>
                        <td><strong>Validation des justificatifs</strong></td>
                        <td style="font-size: 0.8rem;">Examen exclusif par l'administration des certificats déposés, acceptation ou refus avec statut.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>5 SP / 16H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 16</strong></td>
                        <td><span class="badge badge-primary">Sprint 4</span></td>
                        <td><strong>Suivi du justificatif</strong></td>
                        <td style="font-size: 0.8rem;">Consultation par le stagiaire de l'état de son justificatif (en attente, accepté, refusé) et décision admin.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 8H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>

                    <!-- Sprint 5 -->
                    <tr>
                        <td><strong>tache 17</strong></td>
                        <td><span class="badge badge-primary">Sprint 5</span></td>
                        <td><strong>Notification à l'administration</strong></td>
                        <td style="font-size: 0.8rem;">Informer l'administration lorsqu'un enseignant déclare sa propre absence avec date et séance.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 8H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 18</strong></td>
                        <td><span class="badge badge-primary">Sprint 5</span></td>
                        <td><strong>Notification au stagiaire</strong></td>
                        <td style="font-size: 0.8rem;">Informer le stagiaire dès qu'une absence est enregistrée pour qu'il dépose un justificatif dans les 48h.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 8H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 19</strong></td>
                        <td><span class="badge badge-primary">Sprint 5</span></td>
                        <td><strong>Calcul de la note de discipline</strong></td>
                        <td style="font-size: 0.8rem;">Calcul automatique à partir de 2,5 points initiaux, retirant 0,5 pt par absence non justifiée. Une absence justifiée ne provoque aucune perte.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>5 SP / 16H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 20</strong></td>
                        <td><span class="badge badge-primary">Sprint 5</span></td>
                        <td><strong>Consultation note de comportement</strong></td>
                        <td style="font-size: 0.8rem;">Consultation par le stagiaire de sa note de comportement et des informations pour comprendre son évolution.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 8H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>

                    <!-- Sprint 6 -->
                    <tr>
                        <td><strong>tache 21</strong></td>
                        <td><span class="badge badge-primary">Sprint 6</span></td>
                        <td><strong>Recherche et filtres</strong></td>
                        <td style="font-size: 0.8rem;">Recherche et filtrage des absences par groupe, stagiaire, enseignant, date ou période.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 12H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                    <tr>
                        <td><strong>tache 22</strong></td>
                        <td><span class="badge badge-primary">Sprint 6</span></td>
                        <td><strong>Correction d'une absence</strong></td>
                        <td style="font-size: 0.8rem;">Correction par l'administration d'une absence enregistrée par erreur avec recalcul automatique des statuts et de la note.</td>
                        <td><span class="badge badge-danger">Haute</span></td>
                        <td>3 SP / 8H</td>
                        <td><span class="badge badge-success">Terminé ✅</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem; background: var(--bg-tertiary); padding: 1.25rem; border-radius: var(--radius-md);">
            <div style="font-weight: 700; color: var(--accent-cyan); margin-bottom: 0.5rem;">
                👥 Crédits et Équipe Scrum de Développement :
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.9rem;">
                <div>👑 <strong>Réalisé par & Scrum Master :</strong> Louzi Boutayna</div>
                <div>💻 <strong>Équipe Dev :</strong> Ali Ouchen, Aziz Mounassef, Meryem Elazri, Hassan Ouiarrara, Mohammed Chihab</div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     SECTION 2: CAHIER DES CHARGES (TÂCHE 1)
     ========================================== -->
<div id="tab-cahier" class="doc-tab-content" style="display: none;">
    <div class="card" style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 1rem;">Cahier des Charges Fonctionnel & Technique</h2>
        
        <h3 style="color: var(--primary); font-size: 1.1rem; margin-top: 1.25rem;">1. Contexte du Projet</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Dans les établissements de formation professionnelle et d'enseignement supérieur, la gestion des absences papier ou informelle génère des retards, des contestations lors de la saisie des notes de fin de semestre, et des difficultés pour faire respecter les délais réglementaires de justification médicale.
            L'application <strong>EduAbsence Pro</strong> a été conçue pour automatiser intégralement le cycle de vie d'une absence : du pointage par le formateur en séance jusqu'au calcul automatique de la note de discipline sur 2,5 points.
        </p>

        <h3 style="color: var(--primary); font-size: 1.1rem; margin-top: 1.25rem;">2. Rôles et Acteurs du Système</h3>
        <ul style="margin-left: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
            <li><strong>Administrateur :</strong> Gestion des comptes, groupes et stagiaires ; examen, acceptation ou rejet des justificatifs ; correction des erreurs de saisie ; consultation des alertes formateurs et reporting multi-critères.</li>
            <li><strong>Enseignant :</strong> Consultation des listes des groupes assignés ; saisie des absences par créneau (S1 à S4) ; déclaration anticipée de sa propre absence avec proposition de rattrapage.</li>
            <li><strong>Stagiaire :</strong> Consultation de ses absences ; suivi du compte à rebours de 48h ; téléversement numérique du certificat médical ; suivi en temps réel de la décision admin ; visualisation de la note de comportement (2,50 pts) et de son historique.</li>
        </ul>

        <h3 style="color: var(--primary); font-size: 1.1rem; margin-top: 1.25rem;">3. Règles de Gestion Spécifiques (Backlog)</h3>
        <div style="background: var(--bg-tertiary); padding: 1rem; border-radius: var(--radius-md); font-size: 0.85rem; line-height: 1.8;">
            <div>• <strong>RG-01 (Barème initial) :</strong> Tout stagiaire dispose initialement de 2,50 points de discipline.</div>
            <div>• <strong>RG-02 (Pénalité absence non justifiée) :</strong> Chaque absence non justifiée ou dont le certificat a été rejeté entraîne une déduction stricte de 0,50 point.</div>
            <div>• <strong>RG-03 (Absence validée) :</strong> Un justificatif médical accepté par l'administration neutralise la pénalité (0 point retiré).</div>
            <div>• <strong>RG-04 (Note plancher) :</strong> La note globale ne peut descendre en dessous de 0,00 point.</div>
            <div>• <strong>RG-05 (Délai légal de 48h) :</strong> Le téléversement est ouvert pendant exactement 48 heures suivant l'horodatage de l'absence. Passé ce délai, le bouton est désactivé et un message explicatif s'affiche.</div>
        </div>
    </div>
</div>

<!-- ==========================================
     SECTION 3: MODÈLE CONCEPTUEL DE DONNÉES - MCD (TÂCHE 2)
     ========================================== -->
<div id="tab-mcd" class="doc-tab-content" style="display: none;">
    <div class="card" style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 0.5rem;">Modèle Conceptuel de Données (MCD)</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
            Tâche 2 : Représentation des entités, associations et cardinalités (Merise / UML).
        </p>

        <!-- Diagramme SVG Vectoriel du MCD -->
        <div style="background: #0b1120; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; overflow-x: auto;">
            <svg viewBox="0 0 1000 600" width="100%" height="auto" style="min-width: 800px; font-family: 'Plus Jakarta Sans', sans-serif;">
                <!-- Defs pour ombres et flèches -->
                <defs>
                    <marker id="arrow" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                        <path d="M 0 0 L 10 5 L 0 10 z" fill="#6366f1"/>
                    </marker>
                    <filter id="card-shadow" x="-5%" y="-5%" width="110%" height="110%">
                        <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#000" flood-opacity="0.4"/>
                    </filter>
                </defs>

                <!-- ENTITÉ : UTILISATEUR -->
                <g filter="url(#card-shadow)">
                    <rect x="50" y="40" width="220" height="150" rx="8" fill="#1e293b" stroke="#6366f1" stroke-width="2"/>
                    <rect x="50" y="40" width="220" height="34" rx="8" fill="#6366f1"/>
                    <text x="160" y="62" fill="#fff" font-weight="bold" font-size="13" text-anchor="middle">UTILISATEUR</text>
                    <text x="65" y="95" fill="#f8fafc" font-size="11"><u>id_utilisateur</u> (INT)</text>
                    <text x="65" y="115" fill="#94a3b8" font-size="11">nom, prenom, email</text>
                    <text x="65" y="135" fill="#94a3b8" font-size="11">mot_de_passe (hash Bcrypt)</text>
                    <text x="65" y="155" fill="#94a3b8" font-size="11">role, statut_compte, telephone</text>
                </g>

                <!-- ENTITÉ : GROUPE -->
                <g filter="url(#card-shadow)">
                    <rect x="400" y="40" width="200" height="130" rx="8" fill="#1e293b" stroke="#06b6d4" stroke-width="2"/>
                    <rect x="400" y="40" width="200" height="34" rx="8" fill="#06b6d4"/>
                    <text x="500" y="62" fill="#fff" font-weight="bold" font-size="13" text-anchor="middle">GROUPE</text>
                    <text x="415" y="95" fill="#f8fafc" font-size="11"><u>id_groupe</u> (INT)</text>
                    <text x="415" y="115" fill="#94a3b8" font-size="11">code_groupe, nom_groupe</text>
                    <text x="415" y="135" fill="#94a3b8" font-size="11">filiere, annee_formation</text>
                </g>

                <!-- ENTITÉ : STAGIAIRE -->
                <g filter="url(#card-shadow)">
                    <rect x="400" y="240" width="200" height="130" rx="8" fill="#1e293b" stroke="#10b981" stroke-width="2"/>
                    <rect x="400" y="240" width="200" height="34" rx="8" fill="#10b981"/>
                    <text x="500" y="262" fill="#fff" font-weight="bold" font-size="13" text-anchor="middle">STAGIAIRE</text>
                    <text x="415" y="295" fill="#f8fafc" font-size="11"><u>id_stagiaire</u> (INT)</text>
                    <text x="415" y="315" fill="#94a3b8" font-size="11">#id_utilisateur, #id_groupe</text>
                    <text x="415" y="335" fill="#94a3b8" font-size="11">matricule_cne, note_discipline</text>
                </g>

                <!-- ENTITÉ : SEANCE -->
                <g filter="url(#card-shadow)">
                    <rect x="750" y="40" width="190" height="120" rx="8" fill="#1e293b" stroke="#8b5cf6" stroke-width="2"/>
                    <rect x="750" y="40" width="190" height="34" rx="8" fill="#8b5cf6"/>
                    <text x="845" y="62" fill="#fff" font-weight="bold" font-size="13" text-anchor="middle">SEANCE</text>
                    <text x="765" y="95" fill="#f8fafc" font-size="11"><u>id_seance</u> (INT)</text>
                    <text x="765" y="115" fill="#94a3b8" font-size="11">code_seance (S1..S4)</text>
                    <text x="765" y="135" fill="#94a3b8" font-size="11">libelle, heure_debut/fin</text>
                </g>

                <!-- ENTITÉ : ABSENCE -->
                <g filter="url(#card-shadow)">
                    <rect x="740" y="240" width="220" height="160" rx="8" fill="#1e293b" stroke="#ef4444" stroke-width="2"/>
                    <rect x="740" y="240" width="220" height="34" rx="8" fill="#ef4444"/>
                    <text x="850" y="262" fill="#fff" font-weight="bold" font-size="13" text-anchor="middle">ABSENCE</text>
                    <text x="755" y="295" fill="#f8fafc" font-size="11"><u>id_absence</u> (INT)</text>
                    <text x="755" y="315" fill="#94a3b8" font-size="11">#id_stagiaire, #id_enseignant</text>
                    <text x="755" y="335" fill="#94a3b8" font-size="11">#id_seance, date_absence</text>
                    <text x="755" y="355" fill="#94a3b8" font-size="11">heure_enreg (strict 48h)</text>
                    <text x="755" y="375" fill="#94a3b8" font-size="11">statut_absence, motif</text>
                </g>

                <!-- ENTITÉ : JUSTIFICATIF -->
                <g filter="url(#card-shadow)">
                    <rect x="740" y="450" width="220" height="130" rx="8" fill="#1e293b" stroke="#f59e0b" stroke-width="2"/>
                    <rect x="740" y="450" width="220" height="34" rx="8" fill="#f59e0b"/>
                    <text x="850" y="472" fill="#fff" font-weight="bold" font-size="13" text-anchor="middle">JUSTIFICATIF</text>
                    <text x="755" y="505" fill="#f8fafc" font-size="11"><u>id_justificatif</u> (INT)</text>
                    <text x="755" y="525" fill="#94a3b8" font-size="11">#id_absence, nom_fichier</text>
                    <text x="755" y="545" fill="#94a3b8" font-size="11">date_depot, statut_decision</text>
                    <text x="755" y="565" fill="#94a3b8" font-size="11">motif_decision, date_decision</text>
                </g>

                <!-- ENTITÉ : DECLARATION_ENSEIGNANT -->
                <g filter="url(#card-shadow)">
                    <rect x="50" y="450" width="230" height="120" rx="8" fill="#1e293b" stroke="#3b82f6" stroke-width="2"/>
                    <rect x="50" y="450" width="230" height="34" rx="8" fill="#3b82f6"/>
                    <text x="165" y="472" fill="#fff" font-weight="bold" font-size="12" text-anchor="middle">DECLARATION_ENSEIGNANT</text>
                    <text x="65" y="505" fill="#f8fafc" font-size="11"><u>id_declaration</u> (INT)</text>
                    <text x="65" y="525" fill="#94a3b8" font-size="11">#id_enseignant, #id_groupe, #seance</text>
                    <text x="65" y="545" fill="#94a3b8" font-size="11">motif, remarques_rattrapage</text>
                </g>

                <!-- RELATIONS ET LIGNES -->
                <!-- Utilisateur -> Stagiaire -->
                <path d="M 160 190 L 160 305 L 400 305" stroke="#6366f1" stroke-width="2" fill="none"/>
                <text x="280" y="295" fill="#6366f1" font-size="11" font-weight="bold">(1,1)</text>

                <!-- Groupe -> Stagiaire -->
                <line x1="500" y1="170" x2="500" y2="240" stroke="#06b6d4" stroke-width="2"/>
                <text x="508" y="205" fill="#06b6d4" font-size="11" font-weight="bold">(1,n)</text>

                <!-- Stagiaire -> Absence -->
                <line x1="600" y1="310" x2="740" y2="310" stroke="#ef4444" stroke-width="2"/>
                <text x="660" y="300" fill="#ef4444" font-size="11" font-weight="bold">(0,n)</text>

                <!-- Seance -> Absence -->
                <line x1="845" y1="160" x2="845" y2="240" stroke="#8b5cf6" stroke-width="2"/>
                <text x="855" y="200" fill="#8b5cf6" font-size="11" font-weight="bold">(1,n)</text>

                <!-- Absence -> Justificatif -->
                <line x1="850" y1="400" x2="850" y2="450" stroke="#f59e0b" stroke-width="2"/>
                <text x="860" y="425" fill="#f59e0b" font-size="11" font-weight="bold">(0,1)</text>

                <!-- Utilisateur -> Declaration Enseignant -->
                <line x1="160" y1="190" x2="160" y2="450" stroke="#3b82f6" stroke-width="2"/>
                <text x="170" y="350" fill="#3b82f6" font-size="11" font-weight="bold">(0,n)</text>
            </svg>
        </div>
    </div>
</div>

<!-- ==========================================
     SECTION 4: MODÈLE LOGIQUE DE DONNÉES - MLD (TÂCHE 2)
     ========================================== -->
<div id="tab-mld" class="doc-tab-content" style="display: none;">
    <div class="card" style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 0.5rem;">Modèle Logique de Données (MLD Relationnel 3NF)</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
            Tâche 2 : Structure relationnelle normalisée en 3ème forme normale avec clés primaires (PK) et étrangères (FK).
        </p>

        <pre style="background: var(--bg-tertiary); padding: 1.25rem; border-radius: var(--radius-md); font-family: var(--font-mono); font-size: 0.85rem; color: #a5f3fc; overflow-x: auto; line-height: 1.7;">
1. USERS (
    <u>id</u> [PK],
    name, prenom, nom,
    email [UNIQUE],
    password,  -- Hashé par Bcrypt en BDD
    role [ENUM: 'admin', 'enseignant', 'stagiaire'],
    statut_compte [ENUM: 'actif', 'desactive'],
    telephone, avatar, remember_token,
    created_at, updated_at
)

2. GROUPES (
    <u>id</u> [PK],
    code_groupe [UNIQUE],  -- Ex: DEV101, INFRA102
    nom_groupe, filiere, annee_formation,
    #id_responsable [FK -> USERS.id, NULLABLE],
    description, created_at, updated_at
)

3. STAGIAIRES (
    <u>id</u> [PK],
    #user_id [FK -> USERS.id, UNIQUE, ON DELETE CASCADE],
    matricule_cne [UNIQUE],
    #groupe_id [FK -> GROUPES.id, ON DELETE CASCADE],
    date_naissance, adresse,
    note_discipline  -- Décimal (Base 2.50 pts, recalculée automatiquement)
)

4. ENSEIGNANT_GROUPES (
    <u>id</u> [PK],
    #enseignant_id [FK -> USERS.id, ON DELETE CASCADE],
    #groupe_id [FK -> GROUPES.id, ON DELETE CASCADE],
    matiere, created_at, updated_at
)

5. SEANCES (
    <u>id</u> [PK],
    code_seance,  -- S1, S2, S3, S4
    libelle, heure_debut, heure_fin,
    created_at, updated_at
)

6. ABSENCES (
    <u>id</u> [PK],
    #stagiaire_id [FK -> STAGIAIRES.id, ON DELETE CASCADE],
    #enseignant_id [FK -> USERS.id, ON DELETE CASCADE],
    #seance_id [FK -> SEANCES.id, ON DELETE CASCADE],
    date_absence,
    heure_enregistrement,  -- Horodatage de référence pour la règle des 48h
    statut_absence [ENUM: 'NON_JUSTIFIEE', 'EN_ATTENTE_VALIDATION', 'JUSTIFIEE_ACCEPTEE', 'JUSTIFIEE_REFUSEE'],
    motif_enseignant,
    est_corrigee [BOOLEAN DEFAULT FALSE],
    created_at, updated_at
)

7. JUSTIFICATIFS (
    <u>id</u> [PK],
    #absence_id [FK -> ABSENCES.id, UNIQUE, ON DELETE CASCADE],
    nom_fichier, chemin_fichier, type_fichier, taille_octets,
    date_depot,
    statut_decision [ENUM: 'EN_ATTENTE', 'ACCEPTE', 'REFUSE'],
    #admin_validateur_id [FK -> USERS.id, NULLABLE],
    date_decision, motif_decision,
    created_at, updated_at
)

8. DECLARATIONS_ENSEIGNANTS (
    <u>id</u> [PK],
    #enseignant_id [FK -> USERS.id, ON DELETE CASCADE],
    #seance_id [FK -> SEANCES.id, ON DELETE CASCADE],
    #groupe_id [FK -> GROUPES.id, ON DELETE CASCADE],
    date_absence, motif, remarques_rattrapage,
    date_declaration, est_consultee,
    created_at, updated_at
)

9. NOTIFICATIONS (
    <u>id</u> [PK],
    #destinataire_id [FK -> USERS.id, ON DELETE CASCADE],
    type_notification, titre, message, lien_action,
    est_lue [BOOLEAN DEFAULT FALSE],
    created_at, updated_at
)

10. HISTORIQUE_CORRECTIONS (
    <u>id</u> [PK],
    #absence_id,
    #admin_id [FK -> USERS.id, ON DELETE CASCADE],
    champ_modifie, ancienne_valeur, nouvelle_valeur, motif_correction,
    created_at, updated_at
)
        </pre>
    </div>
</div>

<!-- ==========================================
     SECTION 5: SCRIPT SQL DDL (TÂCHE 3)
     ========================================== -->
<div id="tab-sql" class="doc-tab-content" style="display: none;">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div>
                <h2 style="font-size: 1.4rem; font-weight: 800;">Script SQL DDL (Création des Tables & Contraintes)</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Tâche 3 : Script DDL complet prêt pour MySQL / MariaDB / PostgreSQL</p>
            </div>
            <a href="{{ route('docs.download-sql') }}" class="btn btn-primary btn-sm">💾 Télécharger db_gestion_absences.sql</a>
        </div>

        <pre style="background: var(--bg-primary); padding: 1.25rem; border-radius: var(--radius-md); font-family: var(--font-mono); font-size: 0.8rem; color: #7dd3fc; max-height: 480px; overflow-y: auto; border: 1px solid var(--border-color);">
-- =============================================================================
-- BASE DE DONNÉES : db_gestion_absences (Sprint 1 - Tâche 3)
-- =============================================================================

CREATE DATABASE IF NOT EXISTS db_gestion_absences CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_gestion_absences;

-- 1. Table users
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'enseignant', 'stagiaire') NOT NULL DEFAULT 'stagiaire',
    statut_compte ENUM('actif', 'desactive') NOT NULL DEFAULT 'actif',
    telephone VARCHAR(30) NULL,
    avatar VARCHAR(255) NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- 2. Table groupes
CREATE TABLE groupes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code_groupe VARCHAR(30) NOT NULL UNIQUE,
    nom_groupe VARCHAR(255) NOT NULL,
    filiere VARCHAR(150) NOT NULL,
    annee_formation INT NOT NULL DEFAULT 1 CHECK (annee_formation IN (1, 2)),
    id_responsable BIGINT UNSIGNED NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_responsable) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 3. Table stagiaires
CREATE TABLE stagiaires (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    matricule_cne VARCHAR(50) NOT NULL UNIQUE,
    groupe_id BIGINT UNSIGNED NOT NULL,
    date_naissance DATE NULL,
    note_discipline DECIMAL(4, 2) NOT NULL DEFAULT 2.50 CHECK (note_discipline >= 0.00 AND note_discipline <= 2.50),
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (groupe_id) REFERENCES groupes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Table absences
CREATE TABLE absences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id BIGINT UNSIGNED NOT NULL,
    enseignant_id BIGINT UNSIGNED NOT NULL,
    seance_id BIGINT UNSIGNED NOT NULL,
    date_absence DATE NOT NULL,
    heure_enregistrement TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut_absence ENUM('NON_JUSTIFIEE', 'EN_ATTENTE_VALIDATION', 'JUSTIFIEE_ACCEPTEE', 'JUSTIFIEE_REFUSEE') DEFAULT 'NON_JUSTIFIEE',
    motif_enseignant TEXT NULL,
    est_corrigee BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id) ON DELETE CASCADE,
    FOREIGN KEY (enseignant_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Table justificatifs
CREATE TABLE justificatifs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    absence_id BIGINT UNSIGNED NOT NULL UNIQUE,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(255) NOT NULL,
    type_fichier VARCHAR(100) NOT NULL,
    taille_octets INT NOT NULL,
    date_depot TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut_decision ENUM('EN_ATTENTE', 'ACCEPTE', 'REFUSE') DEFAULT 'EN_ATTENTE',
    admin_validateur_id BIGINT UNSIGNED NULL,
    date_decision TIMESTAMP NULL,
    motif_decision TEXT NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    FOREIGN KEY (absence_id) REFERENCES absences(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_validateur_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
        </pre>
    </div>
</div>

<script>
    function switchDocTab(tabName) {
        document.querySelectorAll('.doc-tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.doc-tab-btn').forEach(btn => btn.classList.remove('active'));
        
        document.getElementById('tab-' + tabName).style.display = 'block';
        event.target.classList.add('active');
    }
</script>
@endsection
