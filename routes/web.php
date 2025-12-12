<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LandingController;
use App\Models\BasicInfo;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/auth', [AuthController::class, 'showFlipForm']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    // Check if user has application, if not redirect to apply
    if (!BasicInfo::where('user_id', $request->user()->id)->exists()) {
        return redirect()->route('student.apply')->with('success', 'Email verified! Please complete your scholarship application.');
    }

    return redirect()->route('student.dashboard')->with('success', 'Email verified! Welcome to the portal.');
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        // Check application status for redirect
        if (!BasicInfo::where('user_id', $request->user()->id)->exists()) {
            return redirect()->route('student.apply');
        }
        return redirect()->route('student.dashboard');
    }

    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/student/type-of-assistance', [StudentController::class, 'typeOfAssistance'])->name('student.type_of_assistance');
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/profile', [StudentController::class, 'profile'])->name('student.profile');
    
    // Locked performance tab
    Route::get('/student/performance', [DocumentController::class, 'index'])
        ->middleware('has.applied')
        ->name('student.performance');
        
    Route::get('/student/notifications', [StudentController::class, 'notifications'])->name('student.notifications');
    Route::post('/student/notifications/{id}/mark-read', [StudentController::class, 'markNotificationAsRead'])->name('student.notifications.mark-read');
    Route::post('/student/notifications/mark-all-read', [StudentController::class, 'markAllNotificationsAsRead'])->name('student.notifications.mark-all-read');
    Route::delete('/student/notifications/{id}', [StudentController::class, 'deleteNotification'])->name('student.notifications.delete');
    Route::get('/student/support', [StudentController::class, 'support'])->name('student.support');
    Route::post('/student/apply', [StudentController::class, 'apply'])->name('student.apply');
    Route::post('/student/update-profile-pic', [StudentController::class, 'updateProfilePic'])->name('student.update-profile-pic');
    Route::put('/student/profile', [StudentController::class, 'updateProfile'])->name('student.update-profile');
    Route::post('/student/update-gpa', [StudentController::class, 'updateGPA'])->name('student.update-gpa');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.view');
    
    // Application Draft Routes
    Route::post('/student/drafts', [StudentController::class, 'saveDraft'])->name('student.drafts.save');
    Route::get('/student/drafts', [StudentController::class, 'getDrafts'])->name('student.drafts.list');
    Route::get('/student/drafts/{id}', [StudentController::class, 'getDraft'])->name('student.drafts.get');
    Route::delete('/student/drafts/{id}', [StudentController::class, 'deleteDraft'])->name('student.drafts.delete');
    Route::get('/student/apply', function() {
        $ethnicities = \App\Models\Ethno::all();
        $barangays = \App\Models\Address::query()->select('barangay')->distinct()->where('barangay', '!=', '')->orderBy('barangay')->pluck('barangay');
        $municipalities = \App\Models\Address::query()->select('municipality')->distinct()->where('municipality', '!=', '')->orderBy('municipality')->pluck('municipality');
        $provinces = \App\Models\Address::query()->select('province')->distinct()->where('province', '!=', '')->orderBy('province')->pluck('province');
        
        // Document data
        $user = \Illuminate\Support\Facades\Auth::user();
        $documents = \App\Models\Document::where('user_id', $user->id)->latest()->get();
        
        // Regular application required documents
        $requiredTypes = [
            'birth_certificate' => 'Original or Certified True Copy of Birth Certificate',
            'income_document' => 'Income Tax Return of the parents/guardians or Certificate of Tax Exemption from BIR or Certificate of Indigency signed by the barangay captain',
            'tribal_certificate' => 'Certificate of Tribal Membership/Certificate of Confirmation COC',
            'endorsement' => 'Endorsement of the IPS/IP Traditional Leaders',
            'good_moral' => 'Certificate of Good Moral from the Guidance Counselor',
            'grades' => 'Incoming First Year College (Senior High School Grades), Ongoing college students latest copy of grades',
        ];
        
        // Renewal application required documents
        $renewalRequiredTypes = [
            'certificate_of_enrollment' => 'Certificate of Enrollment',
            'statement_of_account' => 'Statement of Account',
            'gwa_previous_sem' => 'GWA of Previous Semester',
        ];

        // Check if user has already submitted an application
        $hasSubmitted = \App\Models\BasicInfo::where('user_id', $user->id)
            ->whereNotNull('type_assist')
            ->exists();
        
        $submittedApplication = null;
        $canRenew = false;
        if ($hasSubmitted) {
            $submittedApplication = \App\Models\BasicInfo::where('user_id', $user->id)
                ->whereNotNull('type_assist')
                ->first();
            
            // Check if user can renew (must be validated and a grantee)
            if ($submittedApplication) {
                $canRenew = ($submittedApplication->application_status === 'validated' && 
                           strtolower(trim($submittedApplication->grant_status ?? '')) === 'grantee');
            }
        }

        return view('student.apply', compact('ethnicities', 'barangays', 'municipalities', 'provinces', 'documents', 'requiredTypes', 'renewalRequiredTypes', 'hasSubmitted', 'submittedApplication', 'canRenew'));
    })->name('student.apply');
});

Route::post('/documents/upload', [DocumentController::class, 'store'])->name('documents.upload');
Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.delete');

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
    Route::post('/staff/applications/{user}/update-status', [StaffDashboardController::class, 'updateApplicationStatus'])->name('staff.applications.update-status');
    Route::post('/staff/applications/{user}/move-to-pamana', [StaffDashboardController::class, 'moveToPamana'])->name('staff.applications.move-to-pamana');
    Route::post('/staff/applications/{user}/add-to-grantees', [StaffDashboardController::class, 'addToGrantees'])->name('staff.applications.add-to-grantees');
    Route::post('/staff/applications/{user}/add-to-waiting', [StaffDashboardController::class, 'addToWaiting'])->name('staff.applications.add-to-waiting');
    Route::post('/staff/documents/{document}/update-status', [StaffDashboardController::class, 'updateDocumentStatus'])->name('staff.documents.update-status');
    Route::post('staff/logout', [App\Http\Controllers\StaffAuthController::class, 'logout'])->name('staff.logout');
    Route::get('/staff/grades/{user}', [StaffDashboardController::class, 'extractGrades'])->name('staff.grades.extract');
    Route::post('/staff/users/{user}/update-gpa', [StaffDashboardController::class, 'updateGPA'])->name('staff.users.update-gpa');
    
    // Document priority routes (First Come, First Serve)
    Route::post('/staff/documents/recalculate-priorities', [StaffDashboardController::class, 'recalculateDocumentPriorities'])->name('staff.documents.recalculate-priorities');
    Route::get('/staff/documents/prioritized', [StaffDashboardController::class, 'getPrioritizedDocuments'])->name('staff.documents.prioritized');
    Route::get('/staff/documents/priority-statistics', [StaffDashboardController::class, 'getDocumentPriorityStatistics'])->name('staff.documents.priority-statistics');
    Route::get('/staff/priorities/applicants', [StaffDashboardController::class, 'applicantPriority'])->name('staff.priorities.applicants');
    Route::get('/staff/priorities/documents', [StaffDashboardController::class, 'documentPriority'])->name('staff.priorities.documents');
    Route::get('/staff/priorities/ip', [StaffDashboardController::class, 'ipPriority'])->name('staff.priorities.ip');
    Route::get('/staff/priorities/courses', [StaffDashboardController::class, 'coursePriority'])->name('staff.priorities.courses');
    Route::get('/staff/priorities/tribal-certificate', [StaffDashboardController::class, 'tribalCertificatePriority'])->name('staff.priorities.tribal-certificate');
    Route::get('/staff/priorities/income-tax', [StaffDashboardController::class, 'incomeTaxPriority'])->name('staff.priorities.income-tax');
    Route::get('/staff/priorities/academic-performance', [StaffDashboardController::class, 'academicPerformancePriority'])->name('staff.priorities.academic-performance');
    Route::get('/staff/priorities/other-requirements', [StaffDashboardController::class, 'otherRequirementsPriority'])->name('staff.priorities.other-requirements');
    
    // Masterlist routes
    Route::get('/staff/masterlist/regular/all', [StaffDashboardController::class, 'masterlistRegular'])->name('staff.masterlist.regular.all');
    Route::get('/staff/masterlist/regular/grantees', [StaffDashboardController::class, 'masterlistRegularGrantees'])->name('staff.masterlist.regular.grantees');
    Route::get('/staff/masterlist/regular/waiting', [StaffDashboardController::class, 'masterlistRegularWaiting'])->name('staff.masterlist.regular.waiting');
    Route::get('/staff/masterlist/pamana', [StaffDashboardController::class, 'masterlistPamana'])->name('staff.masterlist.pamana');
    Route::get('/staff/grantees/report', [StaffDashboardController::class, 'granteesReport'])->name('staff.grantees.report');
    
    // Announcements routes
    Route::get('/staff/announcements', [StaffDashboardController::class, 'announcements'])->name('staff.announcements.index');
    Route::post('/staff/announcements', [StaffDashboardController::class, 'storeAnnouncement'])->name('staff.announcements.store');
});

// Geographic API Routes (Public)
Route::get('/address/barangays', [AddressController::class, 'barangaysByMunicipality'])->name('address.barangays');
Route::get('/address/municipalities', [AddressController::class, 'municipalitiesByProvince'])->name('address.municipalities');
