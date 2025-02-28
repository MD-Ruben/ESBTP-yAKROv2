<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ESBTPFiliereController;
use App\Http\Controllers\ESBTPNiveauEtudeController;
use App\Http\Controllers\ESBTPAnneeUniversitaireController;
use App\Http\Controllers\ESBTPInscriptionController;
use App\Http\Controllers\ESBTPSalleController;

/*
|--------------------------------------------------------------------------
| Routes ESBTP
|--------------------------------------------------------------------------
|
| Ce fichier contient toutes les routes pour le module ESBTP-YAKRO.
| Ces routes sont chargées par le RouteServiceProvider.
|
*/

// Préfixe 'esbtp' pour toutes les routes
Route::prefix('esbtp')->name('esbtp.')->middleware(['auth'])->group(function () {
    
    // Routes pour les filières
    Route::get('/filieres', [ESBTPFiliereController::class, 'index'])->name('filieres.index');
    Route::get('/filieres/create', [ESBTPFiliereController::class, 'create'])->name('filieres.create');
    Route::post('/filieres', [ESBTPFiliereController::class, 'store'])->name('filieres.store');
    Route::get('/filieres/{filiere}', [ESBTPFiliereController::class, 'show'])->name('filieres.show');
    Route::get('/filieres/{filiere}/edit', [ESBTPFiliereController::class, 'edit'])->name('filieres.edit');
    Route::put('/filieres/{filiere}', [ESBTPFiliereController::class, 'update'])->name('filieres.update');
    Route::delete('/filieres/{filiere}', [ESBTPFiliereController::class, 'destroy'])->name('filieres.destroy');
    
    // Routes pour les niveaux d'études
    Route::get('/niveaux-etudes', [ESBTPNiveauEtudeController::class, 'index'])->name('niveaux-etudes.index');
    Route::get('/niveaux-etudes/create', [ESBTPNiveauEtudeController::class, 'create'])->name('niveaux-etudes.create');
    Route::post('/niveaux-etudes', [ESBTPNiveauEtudeController::class, 'store'])->name('niveaux-etudes.store');
    Route::get('/niveaux-etudes/{niveau}', [ESBTPNiveauEtudeController::class, 'show'])->name('niveaux-etudes.show');
    Route::get('/niveaux-etudes/{niveau}/edit', [ESBTPNiveauEtudeController::class, 'edit'])->name('niveaux-etudes.edit');
    Route::put('/niveaux-etudes/{niveau}', [ESBTPNiveauEtudeController::class, 'update'])->name('niveaux-etudes.update');
    Route::delete('/niveaux-etudes/{niveau}', [ESBTPNiveauEtudeController::class, 'destroy'])->name('niveaux-etudes.destroy');
    
    // Routes pour les années universitaires
    Route::get('/annees-universitaires', [ESBTPAnneeUniversitaireController::class, 'index'])->name('annees-universitaires.index');
    Route::get('/annees-universitaires/create', [ESBTPAnneeUniversitaireController::class, 'create'])->name('annees-universitaires.create');
    Route::post('/annees-universitaires', [ESBTPAnneeUniversitaireController::class, 'store'])->name('annees-universitaires.store');
    Route::get('/annees-universitaires/{annee}', [ESBTPAnneeUniversitaireController::class, 'show'])->name('annees-universitaires.show');
    Route::get('/annees-universitaires/{annee}/edit', [ESBTPAnneeUniversitaireController::class, 'edit'])->name('annees-universitaires.edit');
    Route::put('/annees-universitaires/{annee}', [ESBTPAnneeUniversitaireController::class, 'update'])->name('annees-universitaires.update');
    Route::delete('/annees-universitaires/{annee}', [ESBTPAnneeUniversitaireController::class, 'destroy'])->name('annees-universitaires.destroy');
    Route::post('/annees-universitaires/{annee}/set-current', [ESBTPAnneeUniversitaireController::class, 'setCurrent'])->name('annees-universitaires.set-current');
    
    // Routes pour les inscriptions
    Route::get('/inscriptions', [ESBTPInscriptionController::class, 'index'])->name('inscriptions.index');
    Route::get('/inscriptions/create', [ESBTPInscriptionController::class, 'create'])->name('inscriptions.create');
    Route::post('/inscriptions', [ESBTPInscriptionController::class, 'store'])->name('inscriptions.store');
    Route::get('/inscriptions/{inscription}', [ESBTPInscriptionController::class, 'show'])->name('inscriptions.show');
    Route::get('/inscriptions/{inscription}/edit', [ESBTPInscriptionController::class, 'edit'])->name('inscriptions.edit');
    Route::put('/inscriptions/{inscription}', [ESBTPInscriptionController::class, 'update'])->name('inscriptions.update');
    Route::delete('/inscriptions/{inscription}', [ESBTPInscriptionController::class, 'destroy'])->name('inscriptions.destroy');
    
    // Routes pour les salles de classe
    Route::get('/salles', [ESBTPSalleController::class, 'index'])->name('salles.index');
    Route::get('/salles/create', [ESBTPSalleController::class, 'create'])->name('salles.create');
    Route::post('/salles', [ESBTPSalleController::class, 'store'])->name('salles.store');
    Route::get('/salles/{salle}', [ESBTPSalleController::class, 'show'])->name('salles.show');
    Route::get('/salles/{salle}/edit', [ESBTPSalleController::class, 'edit'])->name('salles.edit');
    Route::put('/salles/{salle}', [ESBTPSalleController::class, 'update'])->name('salles.update');
    Route::delete('/salles/{salle}', [ESBTPSalleController::class, 'destroy'])->name('salles.destroy');
}); 