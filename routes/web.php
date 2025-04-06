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
use App\Http\Controllers\ESBTPPaiementController;
use App\Http\Controllers\ESBTPNotificationController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\StudentProgressionController;

/*
|--------------------------------------------------------------------------
| Routes Web ESBTP-yAKRO
|--------------------------------------------------------------------------
|
| Ce fichier contient les routes essentielles pour le fonctionnement
| de l'application ESBTP-yAKRO, centré sur les fonctionnalités spécifiées.
|
*/

// Test route for debugging
Route::get('/test-emploi-temps-show', function () {
    $controller = new ESBTPEmploiTempsController();
    $emploiTemps = \App\Models\ESBTPEmploiTemps::find(1);

    if (!$emploiTemps) {
        return response()->json(['error' => 'Emploi du temps not found'], 404);
    }

    return $controller->show($emploiTemps);
});

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

    // Routes pour la gestion du profil admin
    Route::middleware(['role:superAdmin|secretaire'])->group(function () {
        Route::get('/admin/profile', [AdminProfileController::class, 'index'])->name('admin.profile');
        Route::put('/admin/profile/update', [AdminProfileController::class, 'updateProfile'])->name('admin.profile.update');
        Route::put('/admin/profile/update-professional', [AdminProfileController::class, 'updateProfessionalInfo'])->name('admin.profile.update.professional');
        Route::put('/admin/profile/update-password', [AdminProfileController::class, 'updatePassword'])->name('admin.password.update');
    });

    // Routes pour les fonctionnalités ESBTP
    Route::prefix('esbtp')->name('esbtp.')->group(function () {
        // Routes protégées pour les super-administrateurs
        Route::middleware(['auth', 'role:superAdmin'])->group(function () {
            // Routes pour les filières
            Route::resource('filieres', ESBTPFiliereController::class)
                ->middleware(['permission:view_filieres|create_filieres|edit_filieres|delete_filieres']);

            // Routes pour les niveaux d'études
            Route::resource('niveaux-etudes', ESBTPNiveauEtudeController::class)
                ->middleware(['permission:view_niveaux_etudes|create_niveaux_etudes|edit_niveaux_etudes|delete_niveaux_etudes']);

            // Routes pour les années universitaires
            Route::resource('annees-universitaires', ESBTPAnneeUniversitaireController::class);

            // Routes pour les secrétaires
            Route::resource('secretaires', \App\Http\Controllers\ESBTP\SecretaireAdminController::class);

            // Dashboard superAdmin
            Route::get('/dashboard', [App\Http\Controllers\ESBTP\SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');

            // Routes de modification des classes - réservées aux superAdmin
            Route::resource('classes', ESBTPClasseController::class)
                ->parameters(['classes' => 'classe'])
                ->except(['index', 'show'])
                ->names([
                    'create' => 'classes.create',
                    'store' => 'classes.store',
                    'edit' => 'classes.edit',
                    'update' => 'classes.update',
                    'destroy' => 'classes.destroy'
                ])
                ->middleware(['permission:create_classe|create classes|edit_classes|edit classes|delete_classes|delete classes']);
        });

        // Routes accessibles aux superAdmin et secrétaires
        Route::middleware(['auth', 'role:superAdmin|secretaire'])->group(function () {
            // Routes pour les classes ESBTP - index et show avec permission view_classes
            Route::get('classes', [ESBTPClasseController::class, 'index'])
                ->name('classes.index')
                ->middleware(['permission:view_classes|view classes']);

            Route::get('classes/{classe}', [ESBTPClasseController::class, 'show'])
                ->name('classes.show')
                ->middleware(['permission:view_classes|view classes']);

            // Routes de l'API pour récupérer les matières d'une classe - accessible aux superAdmin et secrétaires
            Route::get('classes/{classe}/matieres', [ESBTPClasseController::class, 'getMatieres'])
                ->name('classes.matieres')
                ->middleware(['permission:view_classes|view classes']);

            // Routes pour les matières
            Route::name('matieres.')->prefix('matieres')->group(function () {
                Route::get('/json', [ESBTPMatiereController::class, 'getMatieresJson'])
                    ->name('json')
                    ->middleware(['permission:view_matieres|view matieres']);
                Route::delete('/bulk-delete', [ESBTPMatiereController::class, 'bulkDelete'])
                    ->name('bulk-delete')
                    ->middleware(['permission:delete_matieres|delete matieres']);
                Route::get('attach-to-classes', [ESBTPMatiereController::class, 'attachToClasses'])
                    ->name('attach-to-classes')
                    ->middleware(['permission:view_matieres|view matieres']);
                Route::post('process-attach-to-classes', [ESBTPMatiereController::class, 'processAttachToClasses'])
                    ->name('process-attach-to-classes')
                    ->middleware(['permission:edit_matieres|edit matieres']);
            });

            // Routes CRUD pour les matières
            Route::resource('matieres', ESBTPMatiereController::class)
                ->names([
                    'index' => 'matieres.index',
                    'create' => 'matieres.create',
                    'store' => 'matieres.store',
                    'show' => 'matieres.show',
                    'edit' => 'matieres.edit',
                    'update' => 'matieres.update',
                    'destroy' => 'matieres.destroy'
                ])
                ->middleware(['permission:view_matieres|view matieres']);

            // Routes pour les emplois du temps ESBTP (définies individuellement)
            Route::get('emploi-temps', [ESBTPEmploiTempsController::class, 'index'])
                ->name('emploi-temps.index')
                ->middleware(['permission:view_timetables']);

            Route::get('emploi-temps/create', [ESBTPEmploiTempsController::class, 'create'])
                ->name('emploi-temps.create')
                ->middleware(['permission:create_timetable']);

            Route::post('emploi-temps', [ESBTPEmploiTempsController::class, 'store'])
                ->name('emploi-temps.store')
                ->middleware(['permission:create_timetable']);

            Route::get('emploi-temps/{emploi_temp}', [ESBTPEmploiTempsController::class, 'show'])
                ->name('emploi-temps.show')
                ->middleware(['permission:view_timetables']);

            Route::get('emploi-temps/{emploi_temp}/edit', [ESBTPEmploiTempsController::class, 'edit'])
                ->name('emploi-temps.edit')
                ->middleware(['permission:edit_timetables']);

            Route::put('emploi-temps/{emploi_temp}', [ESBTPEmploiTempsController::class, 'update'])
                ->name('emploi-temps.update')
                ->middleware(['permission:edit_timetables']);

            Route::delete('emploi-temps/{emploi_temp}', [ESBTPEmploiTempsController::class, 'destroy'])
                ->name('emploi-temps.destroy')
                ->middleware(['permission:delete_timetables']);

            Route::get('emploi-temps/{emploi_temp}/export-pdf', [ESBTPEmploiTempsController::class, 'generatePdf'])
                ->name('emploi-temps.export-pdf')
                ->middleware(['permission:view_timetables']);

            // Routes pour les résultats
            Route::get('resultats', [ESBTPBulletinController::class, 'resultats'])
                ->name('resultats.index')
                ->middleware(['permission:view own bulletin|view_bulletins']);
            Route::get('resultats/classe/{classe}', [ESBTPBulletinController::class, 'resultatClasse'])
                ->name('resultats.classe')
                ->middleware(['permission:view own bulletin|view_bulletins']);
            Route::get('resultats/etudiant/{etudiant}', [ESBTPBulletinController::class, 'resultatEtudiant'])
                ->name('resultats.etudiant')
                ->middleware(['permission:view own bulletin|view_bulletins']);
            Route::get('resultats/historique/classes', [ESBTPBulletinController::class, 'resultats'])
                ->name('resultats.historique.classes')
                ->middleware(['permission:view own bulletin|view_bulletins']);

            // Routes pour les annonces
            Route::resource('annonces', ESBTPAnnonceController::class)
                ->middleware(['permission:send_messages']);

            // Routes pour les séances de cours
            Route::resource('seances-cours', ESBTPSeanceCoursController::class)
                ->parameters(['seances-cours' => 'seance']);

            // Routes pour les présences
            Route::get('/attendances', [ESBTPAttendanceController::class, 'index'])->name('attendances.index')
                ->middleware('permission:view attendances');
            Route::get('/attendances/create', [ESBTPAttendanceController::class, 'create'])->name('attendances.create')
                ->middleware('permission:create attendance');
            Route::post('/attendances', [ESBTPAttendanceController::class, 'store'])->name('attendances.store')
                ->middleware('permission:create attendance');
            Route::get('/attendances/{attendance}/edit', [ESBTPAttendanceController::class, 'edit'])->name('attendances.edit')
                ->middleware('permission:edit attendances');
            Route::put('/attendances/{attendance}', [ESBTPAttendanceController::class, 'update'])->name('attendances.update')
                ->middleware('permission:edit attendances');
            Route::delete('/attendances/{attendance}', [ESBTPAttendanceController::class, 'destroy'])->name('attendances.destroy')
                ->middleware('permission:delete attendances');
            Route::get('/attendances/rapport', [ESBTPAttendanceController::class, 'rapportForm'])->name('attendances.rapport-form')
                ->middleware('permission:view attendances');
            Route::post('/attendances/rapport', [ESBTPAttendanceController::class, 'rapport'])->name('attendances.rapport')
                ->middleware('permission:view attendances');

            // Paiements
            Route::get('/paiements', [App\Http\Controllers\ESBTPPaiementController::class, 'index'])->name('paiements.index');
            Route::get('/paiements/create', [App\Http\Controllers\ESBTPPaiementController::class, 'create'])->name('paiements.create');
            Route::post('/paiements', [App\Http\Controllers\ESBTPPaiementController::class, 'store'])->name('paiements.store');
            Route::get('/paiements/{paiement}', [App\Http\Controllers\ESBTPPaiementController::class, 'show'])->name('paiements.show');
            Route::get('/paiements/{paiement}/edit', [App\Http\Controllers\ESBTPPaiementController::class, 'edit'])->name('paiements.edit');
            Route::put('/paiements/{paiement}', [App\Http\Controllers\ESBTPPaiementController::class, 'update'])->name('paiements.update');
            Route::get('/paiements/{paiement}/valider', [App\Http\Controllers\ESBTPPaiementController::class, 'valider'])->name('paiements.valider');
            Route::post('/paiements/{paiement}/rejeter', [App\Http\Controllers\ESBTPPaiementController::class, 'rejeter'])->name('paiements.rejeter');
            Route::get('/paiements/{paiement}/recu', [App\Http\Controllers\ESBTPPaiementController::class, 'genererRecu'])->name('paiements.recu');
            Route::get('/paiements/etudiant/{etudiant}', [App\Http\Controllers\ESBTPPaiementController::class, 'paiementsEtudiant'])->name('paiements.etudiant');

            // Routes ESBTP Bulletins
            Route::prefix('bulletins')->name('bulletins.')->group(function () {
                Route::get('/', [ESBTPBulletinController::class, 'index'])->name('index');
                Route::get('/create', [ESBTPBulletinController::class, 'create'])->name('create');
                Route::post('/', [ESBTPBulletinController::class, 'store'])->name('store');
                Route::get('/{bulletin}', [ESBTPBulletinController::class, 'show'])->name('show');
                Route::get('/{bulletin}/edit', [ESBTPBulletinController::class, 'edit'])->name('edit');
                Route::put('/{bulletin}', [ESBTPBulletinController::class, 'update'])->name('update');
                Route::delete('/{bulletin}', [ESBTPBulletinController::class, 'destroy'])->name('destroy');
                Route::get('/select', [ESBTPBulletinController::class, 'select'])->name('select');

                // Route pour la signature des bulletins
                Route::post('bulletins/{bulletin}/signer/{role}', [ESBTPBulletinController::class, 'signer'])
                    ->name('bulletins.signer')
                    ->middleware(['permission:edit_bulletins']);
                // Route pour basculer la publication d'un bulletin
                Route::put('bulletins/{bulletin}/toggle-publication', [ESBTPBulletinController::class, 'togglePublication'])
                    ->name('bulletins.toggle-publication')
                    ->middleware(['permission:edit_bulletins']);

                // Route pour les bulletins en attente
                Route::get('pending', [ESBTPBulletinController::class, 'pending'])
                    ->name('pending')
                    ->middleware(['permission:view_bulletins']);

                // Routes pour la prévisualisation et modification des moyennes
                Route::get('/moyennes-preview', [ESBTPBulletinController::class, 'previewMoyennes'])->name('moyennes-preview');
                Route::post('/moyennes-update', [ESBTPBulletinController::class, 'updateMoyennes'])->name('moyennes-update');
            });

            // Route for today's timetable - moved outside bulletins group
            Route::get('timetables/today', [ESBTPEmploiTempsController::class, 'today'])->name('timetables.today');
        });

        // Routes accessibles pour les secrétaires et super-admins
        Route::middleware(['auth', 'role:secretaire|superAdmin'])->group(function () {
            // Nouvelle route pour la vue fusionnée des étudiants et inscriptions
            Route::get('/etudiants-inscriptions', [ESBTPEtudiantController::class, 'indexFusionne'])
                ->name('etudiants-inscriptions.index')
                ->middleware(['permission:view_students|view_inscriptions']);

            // Routes pour les étudiants ESBTP avec toutes les actions CRUD
            Route::resource('etudiants', ESBTPEtudiantController::class)
                ->middleware(['permission:view_students|create_students|edit_students|delete_students']);

            // Routes pour réinitialiser le mot de passe d'un étudiant
            Route::get('/etudiants/{etudiant}/reset-password', [ESBTPEtudiantController::class, 'resetPassword'])
                ->name('etudiants.reset-password')
                ->middleware(['permission:edit_students']);

            // Route pour générer un certificat de scolarité
            Route::get('/etudiants/{etudiant}/certificat', [ESBTPEtudiantController::class, 'genererCertificat'])
                ->name('etudiants.certificat')
                ->middleware(['permission:view_students']);

            // Routes pour les inscriptions ESBTP
            Route::get('/inscriptions', [ESBTPInscriptionController::class, 'index'])->name('inscriptions.index');
            Route::get('/inscriptions/create', [ESBTPInscriptionController::class, 'create'])->name('inscriptions.create');
            Route::get('/inscriptions/getClasses', [ESBTPInscriptionController::class, 'getClasses'])->name('inscriptions.getClasses');
            Route::post('/inscriptions', [ESBTPInscriptionController::class, 'store'])->name('inscriptions.store');
            Route::get('/inscriptions/{inscription}', [ESBTPInscriptionController::class, 'show'])->name('inscriptions.show');
            Route::get('/inscriptions/{inscription}/edit', [ESBTPInscriptionController::class, 'edit'])->name('inscriptions.edit');
            Route::put('/inscriptions/{inscription}', [ESBTPInscriptionController::class, 'update'])->name('inscriptions.update');
            Route::delete('/inscriptions/{inscription}', [ESBTPInscriptionController::class, 'destroy'])->name('inscriptions.destroy');
            Route::put('/inscriptions/{inscription}/valider', [ESBTPInscriptionController::class, 'valider'])->name('inscriptions.valider');
            Route::put('/inscriptions/{inscription}/annuler', [ESBTPInscriptionController::class, 'annuler'])->name('inscriptions.annuler');

            // Routes API utilisées par les formulaires
            Route::get('classes/{classe}/matieres', [ESBTPClasseController::class, 'getMatieres'])
                ->name('classes.matieres')
                ->middleware(['permission:view_classes|view classes']);

            // Routes pour les évaluations - visualisation seulement pour secrétaire
            Route::resource('evaluations', ESBTPEvaluationController::class)
                ->names([
                    'index' => 'evaluations.index',
                    'create' => 'evaluations.create',
                    'store' => 'evaluations.store',
                    'show' => 'evaluations.show',
                    'edit' => 'evaluations.edit',
                    'update' => 'evaluations.update',
                    'destroy' => 'evaluations.destroy'
                ])
                ->middleware(['permission:view_exams|create_exam|edit_exams|delete_exams']);

            // Ajout des routes spécifiques pour les évaluations
            Route::patch('/evaluations/{evaluation}/update-status', [ESBTPEvaluationController::class, 'updateStatus'])
                ->name('evaluations.update-status')
                ->middleware(['permission:edit_exams']);

            Route::patch('/evaluations/{evaluation}/toggle-published', [ESBTPEvaluationController::class, 'togglePublished'])
                ->name('evaluations.toggle-published')
                ->middleware(['permission:edit_exams']);

            Route::patch('/evaluations/{evaluation}/toggle-notes-published', [ESBTPEvaluationController::class, 'toggleNotesPublished'])
                ->name('evaluations.toggle-notes-published')
                ->middleware(['permission:edit_exams']);

            // Routes pour les notes
            Route::resource('notes', ESBTPNoteController::class)
                ->middleware(['permission:view_grades|create_grade|edit_grades|delete_grades']);
            Route::get('evaluations/{evaluation}/saisie-rapide', [ESBTPNoteController::class, 'saisieRapide'])->name('notes.saisie-rapide');
            Route::post('notes/store-batch', [ESBTPNoteController::class, 'enregistrerSaisieRapide'])->name('notes.store-batch');
        });

        // Espace étudiant - routes accessibles pour les étudiants
        Route::middleware(['auth', 'role:etudiant'])->group(function () {
            Route::get('/mon-profil', [ESBTPEtudiantController::class, 'profile'])
                ->name('mon-profil.index')
                ->middleware(['permission:view own profile|view_students']);

            Route::get('/mes-notes', [ESBTPNoteController::class, 'studentGrades'])
                ->name('mes-notes.index')
                ->middleware(['permission:view own grades|view_grades']);

            Route::get('/mon-emploi-temps', [ESBTPEmploiTempsController::class, 'studentTimetable'])
                ->name('mon-emploi-temps.index')
                ->middleware(['permission:view own timetable|view_timetables']);

            // Routes pour l'affichage des classes (lecture seule) pour les étudiants
            Route::get('/student-classes', [ESBTPClasseController::class, 'index'])
                ->name('student.classes.index')
                ->middleware(['permission:view_classes|view classes']);
            Route::get('/student-classes/{classe}', [ESBTPClasseController::class, 'show'])
                ->name('student.classes.show')
                ->middleware(['permission:view_classes|view classes']);

            Route::get('/mon-bulletin', [ESBTPBulletinController::class, 'studentBulletins'])
                ->name('mon-bulletin.index')
                ->middleware(['permission:view own bulletin|view_bulletins']);

            // Route pour accéder à la page des absences
            Route::get('/esbtp/mes-absences', [ESBTPAttendanceController::class, 'studentAttendance'])
                ->name('esbtp.mes-absences.index')
                ->middleware(['permission:view own attendances|view_attendances']);

            // Route pour justifier une absence
            Route::post('/esbtp/mes-absences/{absenceId}/justify', [ESBTPAttendanceController::class, 'justifyAbsence'])
                ->name('esbtp.mes-absences.justify')
                ->middleware(['permission:view own attendances|view_attendances']);

            // Route de debug pour les absences (accessible uniquement en développement)
            Route::get('/mes-absences/debug', [ESBTPAttendanceController::class, 'studentAttendance'])
                ->name('mes-absences.debug')
                ->middleware(['role:etudiant'])
                ->defaults('debug', true);

            Route::get('/mes-evaluations', [ESBTPEvaluationController::class, 'studentEvaluations'])
                ->name('mes-evaluations.index')
                ->middleware(['permission:view own exams|view_exams']);

            // Routes pour les notifications des étudiants
            Route::get('/mes-notifications', [ESBTPNotificationController::class, 'index'])
                ->name('mes-notifications.index');
            Route::post('/mes-notifications/{id}/read', [ESBTPNotificationController::class, 'markAsRead'])
                ->name('mes-notifications.read');
            Route::post('/mes-notifications/mark-all-read', [ESBTPNotificationController::class, 'markAllAsRead'])
                ->name('mes-notifications.markAllAsRead');
            Route::get('/notifications/unread-count', [ESBTPNotificationController::class, 'getUnreadCount'])
                ->name('notifications.unreadCount');

            // Routes pour les messages des étudiants
            Route::get('/mes-messages', [ESBTPAnnonceController::class, 'studentMessages'])
                ->name('mes-messages.index');
            Route::post('/mes-messages/{id}/read', [ESBTPAnnonceController::class, 'markAsRead'])
                ->name('mes-messages.read');
            Route::post('/mes-messages/mark-all-read', [ESBTPAnnonceController::class, 'markAllAsRead'])
                ->name('mes-messages.mark-all-read');
        });

        // Routes exclusives pour le superAdmin (suppression de ressources)
        Route::middleware(['auth', 'role:superAdmin'])->group(function () {
            // Suppression d'étudiants
            Route::delete('/etudiants/{etudiant}', [ESBTPEtudiantController::class, 'destroy'])->name('etudiants.destroy')
                ->middleware(['permission:delete_students']);

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

            // Route de suppression des emplois du temps
            Route::delete('emploi-temps/{emploi_temp}', [ESBTPEmploiTempsController::class, 'destroy'])
                ->name('emploi-temps.destroy')
                ->middleware(['permission:delete_timetables']);
        });

        // Emploi du temps routes
        Route::get('/emploi-temps/{emploi_temp}/add-session', [ESBTPEmploiTempsController::class, 'addSession'])
            ->name('emploi-temps.add-session');
        Route::post('/emploi-temps/{emploi_temp}/store-session', [ESBTPEmploiTempsController::class, 'storeSession'])
            ->name('emploi-temps.store-session');

        // Traitement des justifications d'absence (pour les administrateurs et secrétaires)
        Route::post('/attendances/{absenceId}/process-justification', [ESBTPAttendanceController::class, 'processJustification'])
            ->name('attendances.process-justification')
            ->middleware('permission:edit_attendances|edit attendances');
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

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [ESBTPNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/{id}/read', [ESBTPNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/mark-all-read', [ESBTPNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::get('/unread-count', [ESBTPNotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    });

    // Vérifier si la route pour le tableau de bord étudiant existe déjà
    // Si elle n'existe pas, l'ajouter
    Route::middleware(['auth', 'role:etudiant'])->prefix('dashboard')->group(function () {
        Route::get('/etudiant', [DashboardController::class, 'studentDashboard'])->name('dashboard.etudiant');
    });

    // Student Progression Routes
    Route::prefix('esbtp')->middleware(['auth', 'role:superAdmin|secretaire'])->group(function () {
        Route::get('/progression', [StudentProgressionController::class, 'index'])->name('esbtp.progression.index');
        Route::get('/api/progression/recommendations/{classe}/{annee}', [StudentProgressionController::class, 'getRecommendations'])->name('esbtp.progression.recommendations');
        Route::post('/api/progression/process', [StudentProgressionController::class, 'processProgression'])->name('esbtp.progression.process');
    });

    // Route spécifique pour l'URL /esbtp/mes-absences
    Route::middleware(['auth', 'role:etudiant'])->group(function () {
        // Cette route est redondante et sera commentée pour éviter les conflits
        Route::get('/esbtp/mes-absences', [ESBTPAttendanceController::class, 'studentAttendance'])
            ->name('esbtp.mes-absences.index')
            ->middleware(['permission:view own attendances|view_attendances']);
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

// API routes for ESBTP
Route::prefix('esbtp/api')->name('esbtp.api.')->middleware(['auth'])->group(function () {
    Route::get('classes/{id}/matieres', [ESBTPClasseController::class, 'getMatieresForApi'])->name('classes.matieres.api');
    Route::get('classes/{id}', [ESBTPClasseController::class, 'getClasseById'])->name('classes.get');
    Route::get('get-classes', [ESBTPInscriptionController::class, 'getClasses'])->name('get-classes');
    Route::get('search-parents', [ESBTPEtudiantController::class, 'searchParents'])->name('search-parents');
});

// Route for activating all timetables
Route::post('esbtp/activate-all-timetables', [App\Http\Controllers\ESBTPEmploiTempsController::class, 'activateAll'])
    ->name('esbtp.emploi-temps.activate-all')
    ->middleware(['auth', 'role:superAdmin']);

// Route for setting a timetable as current
Route::post('esbtp/emploi-temps/{id}/set-current', [App\Http\Controllers\ESBTPEmploiTempsController::class, 'setCurrent'])
    ->name('esbtp.emploi-temps.set-current')
    ->middleware(['auth', 'role:superAdmin|secretaire']);

// Routes pour les évaluations
Route::prefix('esbtp/evaluations')->name('esbtp.evaluations.')->group(function () {
    Route::get('/', [ESBTPEvaluationController::class, 'index'])->name('index');
    Route::get('/create', [ESBTPEvaluationController::class, 'create'])->name('create');
    Route::post('/', [ESBTPEvaluationController::class, 'store'])->name('store');
    Route::get('/{evaluation}', [ESBTPEvaluationController::class, 'show'])->name('show');
    Route::get('/{evaluation}/edit', [ESBTPEvaluationController::class, 'edit'])->name('edit');
    Route::put('/{evaluation}', [ESBTPEvaluationController::class, 'update'])->name('update');
    Route::delete('/{evaluation}', [ESBTPEvaluationController::class, 'destroy'])->name('destroy');
    Route::patch('/{evaluation}/toggle-published', [ESBTPEvaluationController::class, 'togglePublished'])->name('toggle-published');
    Route::patch('/{evaluation}/toggle-notes-published', [ESBTPEvaluationController::class, 'toggleNotesPublished'])->name('toggle-notes-published');
    Route::patch('/{evaluation}/update-status', [ESBTPEvaluationController::class, 'updateStatus'])->name('update-status');
    Route::get('/{evaluation}/pdf', [ESBTPEvaluationController::class, 'generatePdf'])->name('pdf');
});

// Routes pour les notifications des étudiants
Route::prefix('esbtp')->name('esbtp.')->middleware(['auth', 'role:etudiant'])->group(function () {
    Route::get('/mes-notifications', [ESBTPNotificationController::class, 'index'])->name('mes-notifications.index');
    Route::post('/mes-notifications/{id}/read', [ESBTPNotificationController::class, 'markAsRead'])->name('mes-notifications.read');
    Route::post('/mes-notifications/mark-all-read', [ESBTPNotificationController::class, 'markAllAsRead'])->name('mes-notifications.markAllAsRead');
    Route::get('/notifications/unread-count', [ESBTPNotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
});

// Ajouter la route pour générer le PDF d'une évaluation
Route::get('/evaluations/{evaluation}/pdf', [ESBTPEvaluationController::class, 'generatePdf'])
    ->name('evaluations.pdf');

// Route pour l'index des bulletins ESBTP
Route::get('/esbtp/bulletins', [ESBTPBulletinController::class, 'index'])->name('esbtp.bulletins.index');

// Route spéciale pour la sélection des bulletins
Route::get('/esbtp/bulletins/select', [ESBTPBulletinController::class, 'select'])
    ->name('esbtp.bulletins.select')
    ->middleware(['auth']);

// Route spéciale pour la génération de PDF de bulletins - placée ici pour éviter les conflits
Route::get('/esbtp-special/bulletins-pdf', [ESBTPBulletinController::class, 'genererPDFParParams'])->name('esbtp.bulletins.pdf-params');

// Routes spéciales pour la prévisualisation et modification des moyennes
Route::get('/esbtp-special/bulletins/moyennes-preview', [ESBTPBulletinController::class, 'previewMoyennes'])->name('esbtp.bulletins.moyennes-preview');
Route::post('/esbtp-special/bulletins/moyennes-update', [ESBTPBulletinController::class, 'updateMoyennes'])->name('esbtp.bulletins.moyennes-update');

// Routes spéciales pour la configuration des matières et l'édition des professeurs
Route::get('/esbtp-special/bulletins/config-matieres', [ESBTPBulletinController::class, 'configMatieresTypeFormation'])->name('esbtp.bulletins.config-matieres');
Route::post('/esbtp-special/bulletins/save-config-matieres', [ESBTPBulletinController::class, 'saveConfigMatieresTypeFormation'])->name('esbtp.bulletins.save-config-matieres');
Route::get('/esbtp-special/bulletins/edit-professeurs', [ESBTPBulletinController::class, 'editProfesseurs'])->name('esbtp.bulletins.edit-professeurs');
Route::post('/esbtp-special/bulletins/save-professeurs', [ESBTPBulletinController::class, 'saveProfesseurs'])->name('esbtp.bulletins.save-professeurs');
Route::get('/esbtp-special/bulletins/generate', [ESBTPBulletinController::class, 'generate'])->name('esbtp.bulletins.generate');
