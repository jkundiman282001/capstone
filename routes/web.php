<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return view('student.dashboard');
})->name('home');

Route::get('/auth', [AuthController::class, 'showFlipForm']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/student/type-of-assistance', [StudentController::class, 'typeOfAssistance'])->name('student.type_of_assistance');
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::get('/student/performance', [DocumentController::class, 'index'])->name('student.performance');
    Route::get('/student/notifications', [StudentController::class, 'notifications'])->name('student.notifications');
    Route::get('/student/support', [StudentController::class, 'support'])->name('student.support');
    Route::post('/student/apply', [StudentController::class, 'apply'])->name('student.apply');
    Route::post('/student/update-profile-pic', [StudentController::class, 'updateProfilePic'])->name('student.update-profile-pic');
    Route::get('/student/apply', function() {
        $ethnicities = \App\Models\Ethno::all();
        $barangays = \App\Models\Address::query()->select('barangay')->distinct()->where('barangay', '!=', '')->orderBy('barangay')->pluck('barangay');
        $municipalities = \App\Models\Address::query()->select('municipality')->distinct()->where('municipality', '!=', '')->orderBy('municipality')->pluck('municipality');
        $provinces = \App\Models\Address::query()->select('province')->distinct()->where('province', '!=', '')->orderBy('province')->pluck('province');
        return view('student.apply', compact('ethnicities', 'barangays', 'municipalities', 'provinces'));
    });
});

Route::post('/documents/upload', [DocumentController::class, 'store'])->name('documents.upload');

// Staff Auth Routes (Public)
Route::get('staff/login', [App\Http\Controllers\StaffAuthController::class, 'showForm'])->name('staff.login');
Route::post('staff/login', [App\Http\Controllers\StaffAuthController::class, 'login']);
Route::get('staff/register', [App\Http\Controllers\StaffAuthController::class, 'showForm'])->name('staff.register');
Route::post('staff/register', [App\Http\Controllers\StaffAuthController::class, 'register']);

// Staff Protected Routes (Require Authentication)
Route::middleware(['auth.staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/reports/download', [StaffDashboardController::class, 'downloadReport'])->name('staff.reports.download');
    Route::post('/staff/feedback', [StaffDashboardController::class, 'submitFeedback'])->name('staff.feedback');
    Route::post('/staff/notifications/mark-read', [App\Http\Controllers\StaffDashboardController::class, 'markNotificationsRead'])->name('staff.notifications.markRead');
    Route::get('/staff/applicants/list', [StaffDashboardController::class, 'applicantsList'])->name('staff.applicants.list');
    Route::get('/staff/applications/{user}', [StaffDashboardController::class, 'viewApplication'])->name('staff.applications.view');
    Route::post('/staff/documents/{document}/update-status', [StaffDashboardController::class, 'updateDocumentStatus'])->name('staff.documents.update-status');
    Route::post('staff/logout', [App\Http\Controllers\StaffAuthController::class, 'logout'])->name('staff.logout');
    Route::get('/staff/grades/{user}', [StaffDashboardController::class, 'extractGrades'])->name('staff.grades.extract');
});

// Geographic API Routes (Public)
Route::get('/address/barangays', [AddressController::class, 'barangaysByMunicipality'])->name('address.barangays');
Route::get('/address/municipalities', [AddressController::class, 'municipalitiesByProvince'])->name('address.municipalities');
