<?php

/*
|--------------------------------------------------------------------------
| ROUTES ACTUELLEMENT NON UTILISÉES
|--------------------------------------------------------------------------
|
| ATTENTION: Ce fichier semble contenir des routes qui ne sont pas utilisées
| car elles sont définies en parallèle dans le fichier web.php. Ce fichier
| n'est pas inclus dans l'application, ce qui peut causer des confusions.
| Toutes les routes devraient être consolidées dans web.php ou correctement
| incluses via RouteServiceProvider.
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ESBTP\SuperAdminController;
use App\Http\Controllers\ESBTP\SecretaireController;
use App\Http\Controllers\ESBTP\EtudiantController;
use App\Http\Controllers\ESBTPFiliereController as FiliereController;
use App\Http\Controllers\ESBTPNiveauEtudeController as NiveauEtudeController;
use App\Http\Controllers\ESBTPAnneeUniversitaireController as AnneeUniversitaireController;
use App\Http\Controllers\ESBTPFormationController as FormationController;
use App\Http\Controllers\ESBTPClasseController as ClasseController;
use App\Http\Controllers\ESBTP\EtudiantController as AdminEtudiantController;
use App\Http\Controllers\ESBTPInscriptionController as InscriptionController;
use App\Http\Controllers\ESBTPMatiereController as MatiereController;
use App\Http\Controllers\ESBTPEvaluationController as EvaluationController;
use App\Http\Controllers\ESBTPNoteController as NoteController;
use App\Http\Controllers\ESBTPBulletinController as BulletinController;
use App\Http\Controllers\ESBTPAnnonceController as AnnonceController;
use App\Http\Controllers\ESBTPEmploiTempsController as EmploiTempsController;
use App\Http\Controllers\ESBTPAttendanceController as AbsenceController;
use App\Http\Controllers\ESBTP\ParentController;
use App\Http\Controllers\ParentNotificationController;
use App\Http\Controllers\ParentMessageController;
use App\Http\Controllers\ParentPaymentController;
use App\Http\Controllers\ParentProfileController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ParentStudentController;
use App\Http\Controllers\ESBTP\ParentAbsenceController;
use App\Http\Controllers\ESBTP\SecretaireAdminController;
use App\Http\Controllers\ESBTP\ResultatController;

/*
|--------------------------------------------------------------------------
| Routes ESBTP
|--------------------------------------------------------------------------
|
| Routes spécifiques à l'application ESBTP-yAKRO
|
*/

Route::prefix('esbtp')->name('esbtp.')->group(function () {
    
    // Routes publiques
    Route::get('/', function () {
        return view('esbtp.welcome');
    })->name('welcome');
    
    Route::get('/login', function () {
        return redirect()->route('login');
    })->name('login');
    
    // Routes protégées par le middleware auth et superAdmin
    Route::middleware(['auth', 'role:superAdmin'])->group(function () {
        Route::resource('filieres', FiliereController::class);
        Route::resource('niveaux-etudes', NiveauEtudeController::class);
        Route::resource('annees-universitaires', AnneeUniversitaireController::class);
        Route::resource('formations', FormationController::class);
        Route::resource('classes', ClasseController::class);
        
        // Gestion des utilisateurs
        // Commentaire des routes qui font référence à des contrôleurs qui n'existent pas
        // Route::resource('utilisateurs', UserController::class);
        // Route::resource('roles', RoleController::class);
        // Route::resource('permissions', PermissionController::class);
        
        // Dashboard superAdmin
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    });
    
    // Routes pour le SuperAdmin uniquement
    Route::middleware(['auth', 'role:superAdmin|secretaire'])->group(function () {
        // Gestion des ressources de base
        Route::resource('filieres', FiliereController::class);
        Route::resource('niveaux-etudes', NiveauEtudeController::class);
        Route::resource('annees-universitaires', AnneeUniversitaireController::class);
        Route::resource('formations', FormationController::class);
        Route::resource('classes', ClasseController::class);
        
        // Gestion des utilisateurs et des rôles (admin uniquement)
        // Route::resource('utilisateurs', UserController::class);
        // Route::resource('roles', RoleController::class);
        // Route::resource('permissions', PermissionController::class);
    });
    
    // Routes pour le SuperAdmin et le secrétaire
    Route::middleware(['auth', 'role:superAdmin,secretaire'])->group(function () {
        // Gestion des étudiants
        Route::resource('etudiants', AdminEtudiantController::class)->except(['destroy']);
        
        // Gestion des inscriptions
        Route::resource('inscriptions', InscriptionController::class)->except(['destroy']);
        
        // Routes pour les notes, bulletins, messages et emplois du temps
        Route::get('resultats', [ResultatController::class, 'index'])->name('resultats.index');
        Route::get('resultats/{classe_id}', [ResultatController::class, 'showByClasse'])->name('resultats.classe');
        Route::get('resultats/{classe_id}/{etudiant_id}', [ResultatController::class, 'showByEtudiant'])->name('resultats.etudiant');
        
        // Gestion des notes
        Route::resource('notes', NoteController::class);
        
        // Bulletins scolaires
        Route::resource('bulletins', BulletinController::class)->except(['destroy']);
        
        // Annonces et messages
        Route::resource('annonces', AnnonceController::class);
        
        // Emplois du temps
        Route::resource('emplois-temps', EmploiTempsController::class);
        
        // Gestion des absences
        Route::resource('absences', AbsenceController::class);
        
        // Dashboard secrétaire
        Route::get('/secretaire/dashboard', [SecretaireController::class, 'dashboard'])->name('secretaire.dashboard');
    });
    
    // Routes exclusivement réservées au SuperAdmin
    Route::middleware(['auth', 'role:superAdmin'])->group(function () {
        // Configuration des matières et évaluations
        Route::resource('matieres', MatiereController::class);
        Route::resource('evaluations', EvaluationController::class);
        
        // Gestion des comptes secrétaires
        Route::resource('secretaires', SecretaireAdminController::class);
    });
    
    // Routes pour les étudiants (commentées car redondantes avec web.php)
    // Route::middleware(['auth', 'role:etudiant'])->prefix('etudiant')->name('etudiant.')->group(function () {
    //     Route::get('/dashboard', [EtudiantController::class, 'dashboard'])->name('dashboard');
    //     Route::get('/profile', [EtudiantController::class, 'profile'])->name('profile');
    //     Route::get('/notes', [EtudiantController::class, 'notes'])->name('notes');
    //     Route::get('/emploi-temps', [EtudiantController::class, 'emploiTemps'])->name('emploi-temps');
    //     Route::get('/bulletins', [EtudiantController::class, 'bulletins'])->name('bulletins');
    //     Route::get('/bulletins/{bulletin}', [EtudiantController::class, 'showBulletin'])->name('bulletins.show');
    //     Route::get('/absences', [EtudiantController::class, 'absences'])->name('absences');
    // });
    
    // Routes pour les parents (commentées car redondantes avec web.php)
    // Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    //     Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
    //     Route::get('/etudiant/{id}', [ParentStudentController::class, 'show'])->name('student.show');
    //     Route::get('/notifications', [ParentController::class, 'notifications'])->name('notifications.index');
    //     Route::get('/notifications/{id}/marquer-comme-lu', [ParentNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    //     Route::get('/notifications/marquer-tout-comme-lu', [ParentNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    //     
    //     // Messages
    //     Route::get('/messages', [ParentMessageController::class, 'index'])->name('messages.index');
    //     Route::get('/messages/creer', [ParentMessageController::class, 'create'])->name('messages.create');
    //     Route::post('/messages', [ParentMessageController::class, 'store'])->name('messages.store');
    //     Route::get('/messages/{id}', [ParentMessageController::class, 'show'])->name('messages.show');
    //     Route::get('/messages/{id}/repondre', [ParentMessageController::class, 'reply'])->name('messages.reply');
    //     Route::post('/messages/{id}/repondre', [ParentMessageController::class, 'storeReply'])->name('messages.store-reply');
    //     Route::get('/messages/{id}/marquer-comme-lu', [ParentMessageController::class, 'markAsRead'])->name('messages.mark-read');
    //     Route::get('/messages/marquer-tout-comme-lu', [ParentMessageController::class, 'markAllAsRead'])->name('messages.mark-all-read');
    //     
    //     // Paiements
    //     Route::get('/paiements', [ParentController::class, 'payments'])->name('payments.index');
    //     Route::get('/paiements/{etudiant_id}/historique', [ParentPaymentController::class, 'studentHistory'])->name('payments.student-history');
    //     Route::get('/paiements/{payment_id}', [ParentPaymentController::class, 'show'])->name('payments.show');
    //     Route::get('/paiements/{payment_id}/telecharger-recu', [ParentPaymentController::class, 'downloadReceipt'])->name('payments.download-receipt');
    //     
    //     // Absences
    //     Route::get('/absences', [ParentAbsenceController::class, 'summary'])->name('absences.summary');
    //     Route::get('/absences/{etudiant_id}', [ParentAbsenceController::class, 'index'])->name('absences.index');
    //     Route::get('/absences/{etudiant_id}/{absence_id}', [ParentAbsenceController::class, 'show'])->name('absences.show');
    //     Route::get('/absences/{etudiant_id}/{absence_id}/justifier', [ParentAbsenceController::class, 'edit'])->name('absences.edit');
    //     Route::put('/absences/{etudiant_id}/{absence_id}', [ParentAbsenceController::class, 'update'])->name('absences.update');
    //     
    //     // Paramètres
    //     Route::get('/parametres', [ParentController::class, 'settings'])->name('settings');
    //     Route::put('/parametres', [ParentProfileController::class, 'update'])->name('settings.update');
    //     Route::put('/mot-de-passe', [ParentProfileController::class, 'updatePassword'])->name('password.update');
    //     Route::post('/photo', [ParentProfileController::class, 'updatePhoto'])->name('photo.update');
    // });
});

Route::prefix('esbtp')->middleware('web')->group(function () {
    // Routes API utilisées par les formulaires
    Route::get('/api/search-parents', [App\Http\Controllers\ESBTPEtudiantController::class, 'searchParents'])->name('esbtp.api.search-parents');
    Route::get('/api/get-classes', [App\Http\Controllers\ESBTPEtudiantController::class, 'getClasses'])->name('esbtp.api.get-classes');
}); 