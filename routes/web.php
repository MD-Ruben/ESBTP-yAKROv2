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
use App\Http\Controllers\ExamController;
use App\Http\Controllers\SetupController;

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

// Routes d'installation
Route::get('/setup', [SetupController::class, 'index'])->name('setup');
Route::post('/setup/migrate', [SetupController::class, 'migrate'])->name('setup.migrate');
Route::post('/setup/create-admin', [SetupController::class, 'createAdmin'])->name('setup.create-admin');
Route::post('/setup/finalize', [SetupController::class, 'finalize'])->name('setup.finalize');
Route::get('/setup/check-requirements', [SetupController::class, 'checkRequirements']);

// Route d'accueil
Route::get('/', function () {
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
    
    // Routes pour les présences
    Route::resource('attendances', AttendanceController::class);
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::post('/attendance/mark', [AttendanceController::class, 'mark'])->name('attendance.mark');
    Route::post('/attendance/mark-all', [AttendanceController::class, 'markAll'])->name('attendance.mark-all');
    Route::get('/attendance/mark-page', [AttendanceController::class, 'markPage'])->name('attendance.mark-page');
    Route::get('/attendance/student/{student}', [AttendanceController::class, 'studentDetails'])->name('attendance.student');
    
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
    
    // Routes pour les notes
    Route::resource('grades', GradeController::class);
    Route::get('/grades/report/{student}/{semester?}', [GradeController::class, 'report'])->name('grades.report');
    Route::post('/grades/calculate', [GradeController::class, 'calculate'])->name('grades.calculate');
    Route::get('/grades/bulletin/{student}/{semester}', [GradeController::class, 'bulletin'])->name('grades.bulletin');
    
    // Routes pour les examens
    Route::resource('exams', ExamController::class);
    Route::get('/exam/schedule', [ExamController::class, 'schedule'])->name('exam.schedule');
    
    // Routes pour les emplois du temps
    Route::resource('timetables', TimetableController::class);
    Route::get('/timetable/class/{class}', [TimetableController::class, 'showByClass'])->name('timetable.class');
    Route::get('/timetable/teacher/{teacher}', [TimetableController::class, 'showByTeacher'])->name('timetable.teacher');
});

// Routes pour les parents (accès limité)
Route::middleware(['auth', 'role:parent'])->prefix('parent')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'parentDashboard'])->name('parent.dashboard');
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
