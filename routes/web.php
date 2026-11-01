
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\StagiaireController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes - EduAbsence Pro (Laravel 12)
| Application Web de Gestion des Absences
|--------------------------------------------------------------------------
*/

// Redirection racine
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isEnseignant()) {
            return redirect()->route('enseignant.dashboard');
        }

        return redirect()->route('stagiaire.dashboard');
    }

    return redirect()->route('login');
});

// Authentification & Déconnexion
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Documentation
Route::get('/documentation', [DocumentationController::class, 'index'])->name('docs.index');
Route::get('/documentation/download-sql', [DocumentationController::class, 'downloadSql'])->name('docs.download-sql');

// ==========================================
// ESPACE ADMINISTRATEUR
// ==========================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        // Recherche & filtres des absences
        Route::get('/absences', [AdminController::class, 'absences'])
            ->name('absences');

        // Validation des justificatifs
        Route::get('/justificatifs', [AdminController::class, 'justificatifs'])
            ->name('justificatifs');

        Route::post('/justificatifs/{id}/valider', [AdminController::class, 'validerJustificatif'])
            ->name('justificatifs.valider');

        // Correction d'une absence
        Route::post('/absences/{id}/corriger', [AdminController::class, 'corrigerAbsence'])
            ->name('absences.corriger');

        // Gestion des comptes
        Route::get('/users', [AdminController::class, 'users'])
            ->name('users');

        Route::post('/users', [AdminController::class, 'storeUser'])
            ->name('users.store');

        Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])
            ->name('users.toggle');

        // Suppression définitive d'un utilisateur
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])
            ->name('users.destroy');

        // Gestion des groupes
        Route::get('/groupes', [AdminController::class, 'groupes'])
            ->name('groupes');

        Route::post('/groupes', [AdminController::class, 'storeGroupe'])
            ->name('groupes.store');

        // Gestion des stagiaires
        Route::get('/stagiaires', [AdminController::class, 'stagiaires'])
            ->name('stagiaires');

        Route::post('/stagiaires', [AdminController::class, 'storeStagiaire'])
            ->name('stagiaires.store');

        // Déclarations d'absence des enseignants
        Route::get('/declarations', [AdminController::class, 'declarations'])
            ->name('declarations');

        // Conseils de discipline
        Route::get('/conseils-discipline', [AdminController::class, 'conseilsDiscipline'])
            ->name('conseils.index');

        Route::post('/conseils-discipline', [AdminController::class, 'storeConseilDiscipline'])
            ->name('conseils.store');

        Route::post('/conseils-discipline/{id}/statut', [AdminController::class, 'updateConseilStatut'])
            ->name('conseils.statut');

        Route::delete('/conseils-discipline/{id}', [AdminController::class, 'destroyConseil'])
            ->name('conseils.destroy');

        // Journal d'audit des corrections
        Route::get('/audit', [AdminController::class, 'auditCorrections'])
            ->name('audit');
    });

// ==========================================
// ESPACE ENSEIGNANT
// ==========================================
Route::middleware(['auth', 'role:enseignant'])
    ->prefix('enseignant')
    ->name('enseignant.')
    ->group(function () {

        Route::get('/dashboard', [EnseignantController::class, 'dashboard'])
            ->name('dashboard');

        // Consultation des listes de stagiaires
        Route::get('/stagiaires', [EnseignantController::class, 'stagiaires'])
            ->name('stagiaires');

        // Enregistrement d'une absence
        Route::get('/saisir-absence', [EnseignantController::class, 'createAbsence'])
            ->name('saisir-absence');

        Route::post('/saisir-absence', [EnseignantController::class, 'storeAbsence'])
            ->name('saisir-absence.store');

        // Déclaration de comportement
        Route::get('/declarer-comportement', [EnseignantController::class, 'createDeclarationComportement'])
            ->name('declarer-comportement');

        Route::post('/declarer-comportement', [EnseignantController::class, 'storeDeclarationComportement'])
            ->name('declarer-comportement.store');

        // Déclaration d'absence de l'enseignant
        Route::get('/declarer-absence', [EnseignantController::class, 'createDeclaration'])
            ->name('declarer-absence');

        Route::post('/declarer-absence', [EnseignantController::class, 'storeDeclaration'])
            ->name('declarer-absence.store');
    });
// ==========================================
// ESPACE STAGIAIRE
// ==========================================
Route::middleware(['auth', 'role:stagiaire'])
    ->prefix('stagiaire')
    ->name('stagiaire.')
    ->group(function () {

        Route::get('/dashboard', [StagiaireController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/absences', [StagiaireController::class, 'absences'])
            ->name('absences');

        Route::post('/absences/{id}/justificatif', [StagiaireController::class, 'deposerJustificatif'])
            ->name('absences.justificatif');

        Route::get('/note-discipline', [StagiaireController::class, 'noteComportement'])
            ->name('note-discipline');
    });

// Marquer une notification comme lue
Route::post('/notifications/{id}/read', [StagiaireController::class, 'marquerNotificationLue'])
    ->middleware('auth')
    ->name('notifications.read');

// ==========================================
// PROFIL UTILISATEUR
// ==========================================
Route::middleware('auth')->group(function () {

    Route::get('/profil', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::post('/profil/avatar', [ProfileController::class, 'updateAvatar'])
        ->name('profile.avatar.update');
});