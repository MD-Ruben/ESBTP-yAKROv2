<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
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
use App\Http\Controllers\ESBTPAttendanceController;
use App\Http\Controllers\ESBTPExamenController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ParentNotificationController;
use App\Http\Controllers\ParentMessageController;
use App\Http\Controllers\ParentPaymentController;
use App\Http\Controllers\ParentSettingsController;

/*
|--------------------------------------------------------------------------
| Routes Web ESBTP-yAKRO
|--------------------------------------------------------------------------
|
| Ce fichier contient les routes essentielles pour le fonctionnement
| de l'application ESBTP-yAKRO, centré sur les fonctionnalités spécifiées.
|
*/

// Route d'accueil
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

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
    Route::get('/finalize', [InstallController::class, 'finalize'])->name('install.finalize.get');
});

// Routes d'authentification simplifiées
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes d'enregistrement
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Routes de réinitialisation de mot de passe
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Routes accessibles uniquement après authentification
Route::middleware(['auth', 'installed'])->group(function () {
    // Dashboard
    // Route::get('/', [DashboardController::class, 'index'])->name('dashboard'); // Commenté pour éviter le conflit avec la route d'accueil
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes pour les fonctionnalités ESBTP
    Route::prefix('esbtp')->name('esbtp.')->group(function () {
        // Routes protégées pour les super-administrateurs
        Route::middleware(['auth', 'role:superAdmin'])->group(function () {
            // Routes pour les filières
            Route::resource('filieres', ESBTPFiliereController::class)
                ->middleware(['permission:view filieres|create filieres|edit filieres|delete filieres']);
            
            // Routes pour les niveaux d'études
            Route::resource('niveaux-etudes', ESBTPNiveauEtudeController::class)
                ->middleware(['permission:view niveaux etudes|create niveau etudes|edit niveau etudes|delete niveau etudes']);
            
            // Routes pour les années universitaires
            Route::resource('annees-universitaires', ESBTPAnneeUniversitaireController::class);
            
            // Routes pour les formations
            Route::resource('formations', ESBTPFormationController::class)
                ->middleware(['permission:view formations|create formations|edit formations|delete formations']);
            
            // Routes pour les classes ESBTP
            Route::resource('classes', ESBTPClasseController::class)
                ->middleware(['permission:view classes|create classe|edit classes|delete classes']);

            // Protéger les routes de création, modification et suppression
            Route::get('classes/{classe}/matieres', [ESBTPClasseController::class, 'matieres'])->name('classes.matieres');
            Route::post('classes/{classe}/matieres', [ESBTPClasseController::class, 'updateMatieres'])->name('classes.update-matieres');
        });
        
        // Routes accessibles pour les secrétaires et super-admins
        Route::middleware(['auth', 'role:secretaire|superAdmin'])->group(function () {
            // Routes pour les étudiants ESBTP
            Route::resource('etudiants', ESBTPEtudiantController::class)
                ->middleware(['permission:view students|create student|edit students|delete students']);
            
            // Routes pour les inscriptions ESBTP - définies ici plutôt que dans le groupe parent
            Route::get('/inscriptions', [ESBTPInscriptionController::class, 'index'])->name('inscriptions.index');
            Route::get('/inscriptions/create', [ESBTPInscriptionController::class, 'create'])->name('inscriptions.create');
            Route::post('/inscriptions', [ESBTPInscriptionController::class, 'store'])->name('inscriptions.store');
            Route::get('/inscriptions/{inscription}', [ESBTPInscriptionController::class, 'show'])->name('inscriptions.show');
            Route::get('/inscriptions/{inscription}/edit', [ESBTPInscriptionController::class, 'edit'])->name('inscriptions.edit');
            Route::put('/inscriptions/{inscription}', [ESBTPInscriptionController::class, 'update'])->name('inscriptions.update');
            Route::delete('/inscriptions/{inscription}', [ESBTPInscriptionController::class, 'destroy'])->name('inscriptions.destroy');
            Route::put('/inscriptions/{inscription}/valider', [ESBTPInscriptionController::class, 'valider'])->name('inscriptions.valider');
            Route::put('/inscriptions/{inscription}/annuler', [ESBTPInscriptionController::class, 'annuler'])->name('inscriptions.annuler');
            Route::get('/inscriptions/getClasses', [ESBTPInscriptionController::class, 'getClasses'])->name('inscriptions.getClasses');
            
            // Routes API utilisées par les formulaires
            Route::get('/api/search-parents', [ESBTPEtudiantController::class, 'searchParents'])->name('esbtp.api.search-parents');
            Route::get('/api/get-classes', [ESBTPEtudiantController::class, 'getClasses'])->name('esbtp.api.get-classes');
            
            // Routes pour les matières - visualisation seulement pour secrétaire
            Route::resource('matieres', ESBTPMatiereController::class)
                ->middleware(['permission:view matieres|create matiere|edit matieres|delete matieres']);
            
            // Routes pour les évaluations - visualisation seulement pour secrétaire
            Route::resource('evaluations', ESBTPEvaluationController::class)
                ->middleware(['permission:view exams|create exam|edit exams|delete exams']);
            
            // Routes pour les notes
            Route::resource('notes', ESBTPNoteController::class)
                ->middleware(['permission:view grades|create grade|edit grades|delete grades']);
            Route::get('evaluations/{evaluation}/saisie-rapide', [ESBTPNoteController::class, 'saisieRapide'])->name('notes.saisie-rapide');
            Route::post('evaluations/{evaluation}/saisie-rapide', [ESBTPNoteController::class, 'enregistrerSaisieRapide'])->name('notes.enregistrer-saisie-rapide');
            
            // Routes pour les bulletins
            Route::resource('bulletins', ESBTPBulletinController::class)
                ->middleware(['permission:view bulletins|generate bulletin|edit bulletins|delete bulletins']);
            Route::get('bulletins/select', [ESBTPBulletinController::class, 'select'])->name('bulletins.select');
            Route::get('bulletins/{bulletin}/pdf', [ESBTPBulletinController::class, 'genererPDF'])->name('bulletins.pdf');
            Route::post('bulletins/generer-classe', [ESBTPBulletinController::class, 'genererClasseBulletins'])->name('bulletins.generer-classe');
            
            // Routes pour les annonces
            Route::resource('annonces', ESBTPAnnonceController::class)
                ->middleware(['permission:send messages']);
            
            // Routes pour les emplois du temps ESBTP
            Route::resource('emplois-temps', ESBTPEmploiTempsController::class)
                ->middleware(['permission:view timetables|create timetable|edit timetables|delete timetables']);
            
            // Routes pour les séances de cours
            Route::resource('seances-cours', ESBTPSeanceCoursController::class);
            
            // Routes pour les présences
            Route::resource('attendances', ESBTPAttendanceController::class)
                ->middleware(['permission:view attendances|create attendance|edit attendances|delete attendances']);
            Route::get('/attendances-rapport-form', [ESBTPAttendanceController::class, 'rapportForm'])->name('attendances.rapport-form');
            Route::post('/attendances-rapport', [ESBTPAttendanceController::class, 'rapport'])->name('attendances.rapport');
        });
        
        // Routes exclusives pour le superAdmin (suppression de ressources)
        Route::middleware(['auth', 'role:superAdmin'])->group(function () {
            // Suppression d'étudiants
            Route::delete('/etudiants/{etudiant}', [ESBTPEtudiantController::class, 'destroy'])->name('etudiants.destroy')
                ->middleware(['permission:delete students']);
            
            // Suppression de bulletins
            Route::delete('bulletins/{bulletin}', [ESBTPBulletinController::class, 'destroy'])->name('bulletins.destroy');
            
            // Routes pour les matières - création, modification, suppression
            Route::get('matieres/create', [ESBTPMatiereController::class, 'create'])->name('matieres.create');
            Route::post('matieres', [ESBTPMatiereController::class, 'store'])->name('matieres.store');
            Route::get('matieres/{matiere}/edit', [ESBTPMatiereController::class, 'edit'])->name('matieres.edit');
            Route::put('matieres/{matiere}', [ESBTPMatiereController::class, 'update'])->name('matieres.update');
            Route::delete('matieres/{matiere}', [ESBTPMatiereController::class, 'destroy'])->name('matieres.destroy');
            
            // Routes pour les évaluations - création, modification, suppression
            Route::get('evaluations/create', [ESBTPEvaluationController::class, 'create'])->name('evaluations.create');
            Route::post('evaluations', [ESBTPEvaluationController::class, 'store'])->name('evaluations.store');
            Route::get('evaluations/{evaluation}/edit', [ESBTPEvaluationController::class, 'edit'])->name('evaluations.edit');
            Route::put('evaluations/{evaluation}', [ESBTPEvaluationController::class, 'update'])->name('evaluations.update');
            Route::delete('evaluations/{evaluation}', [ESBTPEvaluationController::class, 'destroy'])->name('evaluations.destroy');
        });

        // Routes pour l'espace étudiant
        Route::middleware(['auth', 'role:etudiant'])->group(function () {
            Route::get('/mon-profil', [ESBTPEtudiantController::class, 'profile'])->name('mon-profil.index')
                ->middleware(['permission:view own profile']);
            Route::get('/mes-notes', [ESBTPNoteController::class, 'studentGrades'])->name('mes-notes.index')
                ->middleware(['permission:view own grades']);
            Route::get('/mon-emploi-temps', [ESBTPEmploiTempsController::class, 'studentTimetable'])->name('mon-emploi-temps.index')
                ->middleware(['permission:view own timetable']);
            Route::get('/mon-bulletin', [ESBTPBulletinController::class, 'studentBulletins'])->name('mon-bulletin.index')
                ->middleware(['permission:view own bulletin']);
            Route::get('/mes-absences', [ESBTPAttendanceController::class, 'studentAttendance'])->name('mes-absences.index')
                ->middleware(['permission:view own attendances']);
            Route::get('/mes-examens', [ESBTPExamenController::class, 'studentExams'])->name('mes-examens.index')
                ->middleware(['permission:view own exams']);
        });
    });
    
    // Routes pour les paramètres et les rôles
    Route::middleware(['auth', 'role:superAdmin'])->group(function () {
        Route::get('/settings', function() {
            return view('admin.settings.index');
        })->name('settings.index');
        
        Route::get('/roles', function() {
            $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
            return view('admin.roles.index', compact('roles'));
        })->name('roles.index');
    });

    // Routes pour le rôle parent
    Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\ESBTP\ParentController::class, 'dashboard'])->name('dashboard');
        Route::get('/etudiant/{id}', [App\Http\Controllers\ESBTP\ParentController::class, 'showStudent'])->name('student.show');
        
        // Notifications
        Route::get('/notifications', [ParentNotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/{id}', [ParentNotificationController::class, 'show'])->name('notifications.show');
        Route::get('/notifications/{id}/read', [ParentNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::get('/notifications/mark-all-as-read', [ParentNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        
        // Messages
        Route::get('/messages', [ParentMessageController::class, 'index'])->name('messages');
        Route::get('/messages/create', [ParentMessageController::class, 'create'])->name('messages.create');
        Route::post('/messages', [ParentMessageController::class, 'store'])->name('messages.store');
        Route::get('/messages/{id}', [ParentMessageController::class, 'show'])->name('messages.show');
        Route::get('/messages/{id}/reply', [ParentMessageController::class, 'reply'])->name('messages.reply');
        Route::post('/messages/{id}/reply', [ParentMessageController::class, 'storeReply'])->name('messages.store-reply');
        Route::get('/messages/{id}/read', [ParentMessageController::class, 'markAsRead'])->name('messages.read');
        Route::get('/messages/mark-all-as-read', [ParentMessageController::class, 'markAllAsRead'])->name('messages.mark-all-read');
        
        // Paiements
        Route::get('/paiements', [ParentPaymentController::class, 'index'])->name('payments');
        Route::get('/paiements/etudiant/{id}', [ParentPaymentController::class, 'studentHistory'])->name('payments.student');
        Route::get('/paiements/{id}', [ParentPaymentController::class, 'show'])->name('payments.show');
        Route::get('/paiements/{id}/recu', [ParentPaymentController::class, 'downloadReceipt'])->name('payments.download-receipt');
        Route::get('/paiements/nouveau', [ParentPaymentController::class, 'create'])->name('payments.create');
        Route::post('/paiements', [ParentPaymentController::class, 'store'])->name('payments.store');
        
        // Absences
        Route::get('/absences/resume', [App\Http\Controllers\ESBTP\ParentAbsenceController::class, 'summary'])->name('absences.summary');
        Route::get('/absences/etudiant/{etudiant_id}', [App\Http\Controllers\ESBTP\ParentAbsenceController::class, 'index'])->name('absences.index');
        Route::get('/absences/etudiant/{etudiant_id}/absence/{absence_id}', [App\Http\Controllers\ESBTP\ParentAbsenceController::class, 'show'])->name('absences.show');
        Route::get('/absences/etudiant/{etudiant_id}/absence/{absence_id}/justifier', [App\Http\Controllers\ESBTP\ParentAbsenceController::class, 'edit'])->name('absences.edit');
        Route::post('/absences/etudiant/{etudiant_id}/absence/{absence_id}/justifier', [App\Http\Controllers\ESBTP\ParentAbsenceController::class, 'update'])->name('absences.update');
        
        // Bulletins - nouvelles routes pour parents
        Route::get('/bulletins', [App\Http\Controllers\ESBTP\ParentController::class, 'bulletins'])->name('bulletins.index')
            ->middleware(['permission:view children bulletins']);
        Route::get('/bulletins/etudiant/{id}', [App\Http\Controllers\ESBTP\ParentController::class, 'showStudentBulletins'])->name('bulletins.student');
        Route::get('/bulletins/{id}', [App\Http\Controllers\ESBTP\ParentController::class, 'show'])->name('bulletins.show')
            ->middleware(['permission:view children bulletins']);
        Route::get('/bulletins/{id}/pdf', [App\Http\Controllers\ESBTP\ParentController::class, 'downloadPdf'])->name('bulletins.pdf');
        
        // Paramètres du compte
        Route::get('/settings', [ParentSettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings/profile', [ParentSettingsController::class, 'updateProfile'])->name('settings.update');
        Route::put('/settings/password', [ParentSettingsController::class, 'updatePassword'])->name('settings.password.update');
        Route::put('/settings/notifications', [ParentSettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
        Route::put('/settings/photo', [ParentSettingsController::class, 'updatePhoto'])->name('settings.photo.update');
    });
});

// Routes pour le tableau de bord étudiant et enseignant
// Ce bloc de routes est commenté car il est redondant avec les routes définies dans le préfixe 'esbtp'
// Route::middleware(['auth', 'verified'])->group(function () {
//     // Routes pour les étudiants
//     Route::middleware(['role:etudiant'])->group(function () {
//         // Mes notes
//         Route::get('/mes-notes', [App\Http\Controllers\ESBTPNoteController::class, 'studentGrades'])
//             ->name('mes-notes.index');
//             
//         // Mes examens
//         Route::get('/mes-examens', [App\Http\Controllers\ESBTPExamenController::class, 'studentExams'])
//             ->name('mes-examens.index');
//             
//         // Mon bulletin
//         Route::get('/mon-bulletin', [App\Http\Controllers\ESBTPBulletinController::class, 'studentBulletins'])
//             ->name('mon-bulletin.index');
//             
//         // Mes absences
//         Route::get('/mes-absences', [App\Http\Controllers\ESBTPAttendanceController::class, 'studentAttendance'])
//             ->name('mes-absences.index');
//             
//         // Mon emploi du temps
//         Route::get('/mon-emploi-temps', [App\Http\Controllers\ESBTPEmploiTempsController::class, 'studentTimetable'])
//             ->name('mon-emploi-temps.index');
//             
//         // Mon profil
//         Route::get('/mon-profil', [App\Http\Controllers\ESBTPEtudiantController::class, 'profile'])
//             ->name('mon-profil.index');
//     });
// });
