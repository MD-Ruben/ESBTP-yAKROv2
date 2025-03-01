<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\ESBTPFiliereController;
use App\Http\Controllers\ESBTPNiveauEtudeController;
use App\Http\Controllers\ESBTPFormationController;
use App\Http\Controllers\ESBTPClasseController;
use App\Http\Controllers\ESBTPEtudiantController;
use App\Http\Controllers\ESBTPInscriptionController;
use App\Http\Controllers\ESBTPMatiereController;
use App\Http\Controllers\ESBTPAnneeUniversitaireController;
use App\Http\Controllers\ESBTPEmploiTempsController;
use App\Http\Controllers\ESBTPEvaluationController;
use App\Http\Controllers\ESBTPNoteController;
use App\Http\Controllers\ESBTPBulletinController;
use App\Http\Controllers\ESBTPAnnonceController;
use App\Http\Controllers\ESBTPSeanceCoursController;

/*
|--------------------------------------------------------------------------
| Routes Web ESBTP-yAKRO
|--------------------------------------------------------------------------
|
| Ce fichier contient les routes essentielles pour le fonctionnement
| de l'application ESBTP-yAKRO, centré sur les fonctionnalités spécifiées.
|
*/

// Routes pour l'installation
Route::prefix('install')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('install.index');
    Route::get('/database', [InstallController::class, 'database'])->name('install.database');
    Route::post('/database', [InstallController::class, 'setupDatabase'])->name('install.setup-database');
    Route::get('/migration', [InstallController::class, 'migration'])->name('install.migration');
    Route::post('/migration', [InstallController::class, 'runMigration'])->name('install.run-migration');
    Route::get('/check-migrations', [InstallController::class, 'checkMigrations'])->name('install.check-migrations');
    Route::get('/admin', [InstallController::class, 'admin'])->name('install.admin');
    Route::post('/admin', [InstallController::class, 'setupAdmin'])->name('install.setup-admin');
    Route::get('/complete', [InstallController::class, 'complete'])->name('install.complete');
    Route::post('/complete', [InstallController::class, 'finalize'])->name('install.finalize');
});

// Routes d'authentification
Auth::routes(['verify' => true]);

// Routes accessibles uniquement après authentification
Route::middleware(['auth', 'installed'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes pour les fonctionnalités ESBTP
    Route::prefix('esbtp')->name('esbtp.')->group(function () {
        // Routes pour les filières
        Route::resource('filieres', ESBTPFiliereController::class);
        
        // Routes pour les niveaux d'études
        Route::resource('niveaux-etudes', ESBTPNiveauEtudeController::class);
        
        // Routes pour les années universitaires
        Route::resource('annees-universitaires', ESBTPAnneeUniversitaireController::class);
        
        // Routes pour les formations
        Route::resource('formations', ESBTPFormationController::class);
        
        // Routes pour les classes ESBTP
        Route::resource('classes', ESBTPClasseController::class);
        Route::get('classes/{classe}/matieres', [ESBTPClasseController::class, 'matieres'])->name('classes.matieres');
        Route::post('classes/{classe}/matieres', [ESBTPClasseController::class, 'updateMatieres'])->name('classes.update-matieres');
        
        // Routes pour les étudiants ESBTP
        Route::resource('etudiants', ESBTPEtudiantController::class);
        
        // Routes pour les inscriptions ESBTP
        Route::resource('inscriptions', ESBTPInscriptionController::class);
        
        // Routes pour les matières
        Route::resource('matieres', ESBTPMatiereController::class);
        
        // Routes pour les évaluations
        Route::resource('evaluations', ESBTPEvaluationController::class);
        
        // Routes pour les notes
        Route::resource('notes', ESBTPNoteController::class);
        Route::get('evaluations/{evaluation}/saisie-rapide', [ESBTPNoteController::class, 'saisieRapide'])->name('notes.saisie-rapide');
        Route::post('evaluations/{evaluation}/saisie-rapide', [ESBTPNoteController::class, 'enregistrerSaisieRapide'])->name('notes.enregistrer-saisie-rapide');
        
        // Routes pour les bulletins
        Route::resource('bulletins', ESBTPBulletinController::class);
        Route::get('bulletins/select', [ESBTPBulletinController::class, 'select'])->name('bulletins.select');
        Route::get('bulletins/{bulletin}/pdf', [ESBTPBulletinController::class, 'genererPDF'])->name('bulletins.pdf');
        Route::post('bulletins/generer-classe', [ESBTPBulletinController::class, 'genererClasseBulletins'])->name('bulletins.generer-classe');
        
        // Routes pour les annonces
        Route::resource('annonces', ESBTPAnnonceController::class);
        
        // Routes pour les emplois du temps ESBTP
        Route::resource('emplois-temps', ESBTPEmploiTempsController::class);
        
        // Routes pour les séances de cours
        Route::get('/seances-cours/create', [ESBTPSeanceCoursController::class, 'create'])->name('seances-cours.create');
        Route::post('/seances-cours', [ESBTPSeanceCoursController::class, 'store'])->name('seances-cours.store');
        Route::get('/seances-cours/{seancesCour}/edit', [ESBTPSeanceCoursController::class, 'edit'])->name('seances-cours.edit');
        Route::put('/seances-cours/{seancesCour}', [ESBTPSeanceCoursController::class, 'update'])->name('seances-cours.update');
        Route::delete('/seances-cours/{seancesCour}', [ESBTPSeanceCoursController::class, 'destroy'])->name('seances-cours.destroy');
    });
    
    // Routes pour l'espace étudiant
    Route::prefix('esbtp')->name('esbtp.')->group(function () {
        Route::get('/mon-profil', [ESBTPEtudiantController::class, 'profile'])->name('mon-profil.index');
        Route::get('/mes-notes', [ESBTPNoteController::class, 'mesNotes'])->name('mes-notes.index');
        Route::get('/mon-emploi-temps', [ESBTPEmploiTempsController::class, 'monEmploiTemps'])->name('mon-emploi-temps.index');
    });
});
