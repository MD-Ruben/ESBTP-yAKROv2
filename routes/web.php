<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CertificateTypeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AbsenceJustificationController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\ExamController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Installation Routes
Route::prefix('install')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('install.index');
    Route::get('/database', [InstallController::class, 'database'])->name('install.database');
    Route::post('/database', [InstallController::class, 'setupDatabase'])->name('install.setup-database');
    Route::get('/migration', [InstallController::class, 'migration'])->name('install.migration');
    Route::post('/migration', [InstallController::class, 'runMigration'])->name('install.run-migration');
    Route::post('/check-migrations', [InstallController::class, 'checkMigrations'])->name('install.check-migrations');
    Route::get('/admin', [InstallController::class, 'admin'])->name('install.admin');
    Route::post('/admin', [InstallController::class, 'setupAdmin'])->name('install.setup-admin');
    Route::get('/finish', [InstallController::class, 'finish'])->name('install.finish');
    Route::post('/finish', [InstallController::class, 'finishInstall'])->name('install.finish-install');
});

// Routes d'accueil
Route::get('/', function () {
    // Utiliser le helper d'installation pour vérifier si l'application est installée
    $installationStatus = \App\Helpers\InstallationHelper::getInstallationStatus();
    $hasAdminUser = \App\Helpers\InstallationHelper::hasAdminUser();
    
    // Journaliser l'état pour le débogage
    \Log::info("Welcome route - Installation status: " . 
              ($installationStatus['installed'] ? "Installed" : "Not installed") . 
              ", Match: {$installationStatus['match_percentage']}%, Admin user: " . 
              ($hasAdminUser ? "Yes" : "No"));
    
    // Si l'application n'est pas installée du tout ou s'il n'y a pas d'utilisateur admin, 
    // rediriger vers l'installation
    if (!$installationStatus['installed'] || !$hasAdminUser) {
        // Journaliser l'état pour le débogage
        \Log::info("Welcome route - Redirecting to install: " . 
                  (!$installationStatus['installed'] ? "Not installed" : "No admin user"));
        
        // Rediriger vers la page d'installation
        return redirect()->route('install.index');
    }
    
    // Même si les migrations ne correspondent pas à 100%, afficher la page d'accueil
    return view('welcome');
})->name('welcome');

// Routes d'authentification
// Auth::routes();
// Routes d'authentification explicites
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Password Reset Routes
Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes pour les rôles et permissions
    Route::get('/roles', function () {
        return view('admin.roles.index', ['roles' => \Spatie\Permission\Models\Role::with('permissions')->get()]);
    })->name('roles.index');
    
    Route::get('/permissions', function () {
        return view('admin.permissions.index', ['permissions' => \Spatie\Permission\Models\Permission::all()]);
    })->name('permissions.index');
    
    // Routes pour les paramètres
    Route::get('/settings', function () {
        return view('admin.settings.index');
    })->name('settings.index');
    
    // Routes pour le profil utilisateur
    Route::put('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Routes pour les étudiants
    Route::resource('students', StudentController::class);
    Route::get('/students/{student}/attendance', [StudentController::class, 'attendance'])->name('students.attendance');
    Route::get('/students/{student}/grades', [StudentController::class, 'grades'])->name('students.grades');
    Route::get('/student/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::get('/student/timetable', [StudentController::class, 'timetable'])->name('student.timetable');
    Route::get('/student/certificates', [StudentController::class, 'certificates'])->name('student.certificates');
    
    // Routes pour les enseignants
    Route::resource('teachers', TeacherController::class);
    Route::get('/teachers/{teacher}/attendance', [TeacherController::class, 'attendance'])->name('teachers.attendance');
    Route::get('/teachers/{teacher}/subjects', [TeacherController::class, 'subjects'])->name('teachers.subjects');
    Route::get('/teacher/profile', [TeacherController::class, 'profile'])->name('teacher.profile');
    Route::get('/teacher/classes', [TeacherController::class, 'classes'])->name('teacher.classes');
    
    // Routes pour les classes (réservées aux superadmins)
    Route::resource('classes', ClassController::class);
    Route::post('/classes/{class}/assign-teacher', [ClassController::class, 'assignTeacher'])->name('classes.assign-teacher');
    Route::post('/classes/{class}/unassign-teacher', [ClassController::class, 'unassignTeacher'])->name('classes.unassign-teacher');
    
    // Routes pour les présences
    Route::resource('attendances', AttendanceController::class);
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::post('/attendance/mark', [AttendanceController::class, 'mark'])->name('attendance.mark');
    Route::post('/attendance/mark-all', [AttendanceController::class, 'markAll'])->name('attendance.mark-all');
    Route::get('/attendance/mark-page', [AttendanceController::class, 'markPage'])->name('attendance.mark-page');
    Route::get('/attendance/student/{student}', [AttendanceController::class, 'studentDetails'])->name('attendance.student');
    
    // Routes pour les justifications d'absence
    Route::resource('absences/justifications', AbsenceJustificationController::class)->except(['edit', 'update']);
    Route::post('/absences/justifications/{justification}/process', [AbsenceJustificationController::class, 'process'])->name('absences.justifications.process');
    
    // Routes alternatives pour les justifications d'absence (plus simples)
    Route::get('/justifications', [AbsenceJustificationController::class, 'index'])->name('justifications.index');
    Route::get('/justifications/create', [AbsenceJustificationController::class, 'create'])->name('justifications.create');
    Route::post('/justifications', [AbsenceJustificationController::class, 'store'])->name('justifications.store');
    Route::get('/justifications/{justification}', [AbsenceJustificationController::class, 'show'])->name('justifications.show');
    Route::delete('/justifications/{justification}', [AbsenceJustificationController::class, 'destroy'])->name('justifications.destroy');
    Route::post('/justifications/{justification}/process', [AbsenceJustificationController::class, 'process'])->name('justifications.process');
    
    // Routes pour les présences des enseignants
    Route::resource('teacher-attendances', TeacherAttendanceController::class);
    Route::get('/teacher-attendance/report', [TeacherAttendanceController::class, 'report'])->name('teacher-attendance.report');
    Route::post('/teacher-attendance/mark', [TeacherAttendanceController::class, 'mark'])->name('teacher-attendance.mark');
    Route::post('/teacher-attendance/mark-all', [TeacherAttendanceController::class, 'markAll'])->name('teacher-attendance.mark-all');
    
    // Routes pour les certificats
    Route::resource('certificates', CertificateController::class);
    Route::resource('certificate-types', CertificateTypeController::class);
    Route::get('/certificates/generate/{student}', [CertificateController::class, 'generate'])->name('certificates.generate');
    Route::get('/certificates/download/{certificate}', [CertificateController::class, 'download'])->name('certificates.download');
    Route::post('/certificate/revoke/{certificate}', [CertificateController::class, 'revoke'])->name('certificate.revoke');
    
    // Routes pour les notifications
    Route::resource('notifications', NotificationController::class);
    Route::post('/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    
    // Routes pour la messagerie interne
    Route::resource('messages', MessageController::class);
    Route::get('/messages/inbox', [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::post('/messages/send-to-group', [MessageController::class, 'sendToGroup'])->name('messages.send-to-group');
    Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::post('/messages/{message}/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.mark-as-read');
    
    // Routes pour les notes
    Route::resource('grades', GradeController::class);
    Route::get('/grades/report/{student}/{semester?}', [GradeController::class, 'report'])->name('grades.report');
    Route::post('/grades/calculate', [GradeController::class, 'calculate'])->name('grades.calculate');
    Route::get('/grades/bulletin/{student}/{semester}', [GradeController::class, 'bulletin'])->name('grades.bulletin');
    Route::get('/grades/bulletin-select', [GradeController::class, 'selectBulletin'])->name('grades.bulletin.select');
    
    // Routes pour les emplois du temps
    Route::resource('timetables', TimetableController::class);
    Route::get('/timetable/class/{class}', [TimetableController::class, 'showByClass'])->name('timetable.class');
    Route::get('/timetable/teacher/{teacher}', [TimetableController::class, 'showByTeacher'])->name('timetable.teacher');
    
    // Routes pour les fonctionnalités ESBTP
    Route::prefix('esbtp')->name('esbtp.')->group(function () {
        // Routes pour les cycles de formation
        Route::resource('cycles', App\Http\Controllers\ESBTPCycleController::class);
        Route::post('/cycles/{cycle}/restore', [App\Http\Controllers\ESBTPCycleController::class, 'restore'])->name('cycles.restore');
        Route::delete('/cycles/{cycle}/force-delete', [App\Http\Controllers\ESBTPCycleController::class, 'forceDelete'])->name('cycles.force-delete');
        
        // Routes pour les filières
        Route::resource('filieres', App\Http\Controllers\ESBTPFiliereController::class);
        
        // Routes pour les niveaux d'études
        Route::resource('niveaux-etudes', App\Http\Controllers\ESBTPNiveauEtudeController::class);
        
        // Routes pour les années universitaires
        Route::resource('annees-universitaires', App\Http\Controllers\ESBTPAnneeUniversitaireController::class);
        
        // Routes pour les salles
        Route::resource('salles', App\Http\Controllers\ESBTPSalleController::class);
        
        // Routes pour les classes ESBTP
        Route::resource('classes', ClassController::class);
        
        // Routes pour les étudiants ESBTP
        Route::resource('etudiants', App\Http\Controllers\ESBTPEtudiantController::class);
        
        // Routes pour les inscriptions ESBTP
        Route::resource('inscriptions', App\Http\Controllers\ESBTPInscriptionController::class);
        
        // Routes pour les matières et unités d'enseignement
        Route::resource('matieres', App\Http\Controllers\ESBTPTeachingElementController::class);
        Route::resource('unites-enseignement', App\Http\Controllers\ESBTPTeachingUnitController::class);
        
        // Routes pour les enseignants ESBTP
        Route::resource('enseignants', TeacherController::class);
        
        // Routes pour les emplois du temps ESBTP
        Route::resource('emplois-temps', TimetableController::class);
        
        // Routes pour les présences ESBTP
        Route::resource('presences', AttendanceController::class);
        
        // Routes pour les évaluations ESBTP
        Route::resource('evaluations', ExamController::class);
        
        // Routes pour les notes ESBTP
        Route::resource('notes', GradeController::class);
        
        // Routes pour les bulletins
        Route::get('/bulletins', [GradeController::class, 'selectBulletin'])->name('bulletins.index');
        
        // Routes pour les résultats
        Route::get('/resultats', [GradeController::class, 'index'])->name('resultats.index');
        
        // Routes pour la messagerie
        Route::resource('messages', MessageController::class);
        
        // Routes pour les notifications
        Route::resource('notifications', NotificationController::class);
        
        // Routes pour les annonces
        Route::get('/annonces', [NotificationController::class, 'index'])->name('annonces.index');
        
        // Routes pour l'espace étudiant
        Route::get('/mon-profil', [StudentController::class, 'profile'])->name('mon-profil.index');
        Route::get('/mes-notes', [GradeController::class, 'index'])->name('mes-notes.index');
        Route::get('/mes-absences', [AttendanceController::class, 'index'])->name('mes-absences.index');
        Route::get('/mes-paiements', [App\Http\Controllers\ESBTPPaiementController::class, 'index'])->name('mes-paiements.index');
        Route::get('/mon-emploi-temps', [TimetableController::class, 'index'])->name('mon-emploi-temps.index');
        
        // Routes pour les spécialités
        Route::resource('specialties', App\Http\Controllers\ESBTPSpecialtyController::class);
        Route::post('/specialties/{specialty}/restore', [App\Http\Controllers\ESBTPSpecialtyController::class, 'restore'])->name('specialties.restore');
        Route::delete('/specialties/{specialty}/force-delete', [App\Http\Controllers\ESBTPSpecialtyController::class, 'forceDelete'])->name('specialties.force-delete');
        
        // Routes pour les partenariats
        Route::resource('partnerships', App\Http\Controllers\ESBTPPartnershipController::class);
        Route::post('/partnerships/{partnership}/restore', [App\Http\Controllers\ESBTPPartnershipController::class, 'restore'])->name('partnerships.restore');
        Route::delete('/partnerships/{partnership}/force-delete', [App\Http\Controllers\ESBTPPartnershipController::class, 'forceDelete'])->name('partnerships.force-delete');
        
        // Routes pour la formation continue
        Route::resource('continuing-education', App\Http\Controllers\ESBTPContinuingEducationController::class);
        Route::post('/continuing-education/{continuingEducation}/restore', [App\Http\Controllers\ESBTPContinuingEducationController::class, 'restore'])->name('continuing-education.restore');
        Route::delete('/continuing-education/{continuingEducation}/force-delete', [App\Http\Controllers\ESBTPContinuingEducationController::class, 'forceDelete'])->name('continuing-education.force-delete');
        
        // Routes pour les départements
        Route::resource('departments', App\Http\Controllers\ESBTPDepartmentController::class);
        Route::post('/departments/{department}/restore', [App\Http\Controllers\ESBTPDepartmentController::class, 'restore'])->name('departments.restore');
        Route::delete('/departments/{department}/force-delete', [App\Http\Controllers\ESBTPDepartmentController::class, 'forceDelete'])->name('departments.force-delete');
        
        // Routes pour les années d'études
        Route::resource('study-years', App\Http\Controllers\ESBTPStudyYearController::class);
        Route::post('/study-years/{studyYear}/restore', [App\Http\Controllers\ESBTPStudyYearController::class, 'restore'])->name('study-years.restore');
        Route::delete('/study-years/{studyYear}/force-delete', [App\Http\Controllers\ESBTPStudyYearController::class, 'forceDelete'])->name('study-years.force-delete');
        
        // Routes pour les semestres
        Route::resource('semesters', App\Http\Controllers\ESBTPSemesterController::class);
        Route::post('/semesters/{semester}/restore', [App\Http\Controllers\ESBTPSemesterController::class, 'restore'])->name('semesters.restore');
        Route::delete('/semesters/{semester}/force-delete', [App\Http\Controllers\ESBTPSemesterController::class, 'forceDelete'])->name('semesters.force-delete');
    });

    // Routes pour les paiements
    Route::prefix('esbtp/paiements')->name('esbtp.paiements.')->group(function () {
        Route::get('/', [App\Http\Controllers\ESBTPPaiementController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\ESBTPPaiementController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ESBTPPaiementController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\ESBTPPaiementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\ESBTPPaiementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\ESBTPPaiementController::class, 'update'])->name('update');
        Route::get('/{id}/valider', [App\Http\Controllers\ESBTPPaiementController::class, 'valider'])->name('valider');
        Route::post('/{id}/rejeter', [App\Http\Controllers\ESBTPPaiementController::class, 'rejeter'])->name('rejeter');
        Route::get('/{id}/recu', [App\Http\Controllers\ESBTPPaiementController::class, 'genererRecu'])->name('recu');
        Route::get('/etudiant/{etudiantId}', [App\Http\Controllers\ESBTPPaiementController::class, 'paiementsEtudiant'])->name('etudiant');
    });

    // Routes API pour les paiements
    Route::prefix('esbtp/api')->name('esbtp.api.')->group(function () {
        Route::get('/etudiants/search', [App\Http\Controllers\ESBTPEtudiantController::class, 'search'])->name('etudiants.search');
        Route::get('/etudiants/inscriptions', [App\Http\Controllers\ESBTPEtudiantController::class, 'getInscriptions'])->name('etudiants.inscriptions');
    });
});

// Routes pour les parents (accès limité)
Route::middleware(['auth', 'role:parent'])->prefix('parent')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'parentDashboard'])->name('parent.dashboard');
    Route::get('/profile', [DashboardController::class, 'parentProfile'])->name('parent.profile');
    Route::get('/children', [DashboardController::class, 'children'])->name('parent.children');
    Route::get('/child/{student}/grades', [StudentController::class, 'parentViewGrades'])->name('parent.child.grades');
    Route::get('/child/{student}/attendance', [StudentController::class, 'parentViewAttendance'])->name('parent.child.attendance');
    Route::get('/child/{student}/timetable', [StudentController::class, 'parentViewTimetable'])->name('parent.child.timetable');
});

// Routes pour l'API mobile (si nécessaire)
Route::prefix('api/v1')->middleware('auth:api')->group(function () {
    Route::get('/student/profile', [StudentController::class, 'apiProfile']);
    Route::get('/student/grades', [StudentController::class, 'apiGrades']);
    Route::get('/student/attendance', [StudentController::class, 'apiAttendance']);
    Route::get('/student/timetable', [StudentController::class, 'apiTimetable']);
    Route::get('/student/notifications', [NotificationController::class, 'apiNotifications']);
});

// Routes API pour les sélecteurs dépendants
Route::prefix('api')->group(function () {
    Route::get('/sections/by-class/{classId}', function ($classId) {
        $sections = App\Models\Section::where('class_id', $classId)->get();
        return response()->json($sections);
    });
});
