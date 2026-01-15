<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StudentController;
use App\Models\BasicInfo;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/auth', [AuthController::class, 'showFlipForm']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    // Check if user has application, if not redirect to apply
    $hasSubmitted = BasicInfo::where('user_id', $request->user()->id)
        ->whereNotNull('type_assist')
        ->exists();

    if (! $hasSubmitted) {
        return redirect()->route('student.apply')->with('success', 'Email verified! Please complete your scholarship application.');
    }

    return redirect()->route('student.dashboard')->with('success', 'Email verified! Welcome to the portal.');
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        // Check application status for redirect
        $hasSubmitted = BasicInfo::where('user_id', $request->user()->id)
            ->whereNotNull('type_assist')
            ->exists();

        if (! $hasSubmitted) {
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
    Route::get('/student/performance', [StudentController::class, 'performance'])
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
    Route::post('/student/update-gwa', [StudentController::class, 'updateGWA'])->name('student.update-gwa');

    // Application Draft Routes
    Route::post('/student/drafts', [StudentController::class, 'saveDraft'])->name('student.drafts.save');
    Route::get('/student/drafts', [StudentController::class, 'getDrafts'])->name('student.drafts.list');
    Route::get('/student/drafts/{id}', [StudentController::class, 'getDraft'])->name('student.drafts.get');
    Route::delete('/student/drafts/{id}', [StudentController::class, 'deleteDraft'])->name('student.drafts.delete');
    Route::get('/student/apply', function () {
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

        // Check if user is new (hasn't submitted application) - show form directly
        $isNewUser = ! $hasSubmitted;

        // Fetch latest draft for auto-loading
        $latestDraft = \App\Models\ApplicationDraft::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->first();

        return view('student.apply', compact('ethnicities', 'barangays', 'municipalities', 'provinces', 'documents', 'requiredTypes', 'renewalRequiredTypes', 'hasSubmitted', 'submittedApplication', 'canRenew', 'isNewUser', 'latestDraft'));
    })->name('student.apply');
});

Route::post('/documents/upload', [DocumentController::class, 'store'])->name('documents.upload');
Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.view');
Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.delete');
Route::get('/student/profile-pic/{filename}', [StudentController::class, 'showProfilePic'])->name('profile-pic.show');

// Staff Auth Routes (Public)
Route::get('staff/login', [App\Http\Controllers\StaffAuthController::class, 'showForm'])->name('staff.login');
Route::post('staff/login', [App\Http\Controllers\StaffAuthController::class, 'login']);
Route::get('staff/register', [App\Http\Controllers\StaffAuthController::class, 'showForm'])->name('staff.register');
Route::post('staff/register', [App\Http\Controllers\StaffAuthController::class, 'register']);

// Staff Protected Routes (Require Authentication)
Route::middleware(['auth.staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/reports', [StaffDashboardController::class, 'reportsIndex'])->name('staff.reports.index');
    Route::get('/staff/reports/download', [StaffDashboardController::class, 'downloadReport'])->name('staff.reports.download');
    Route::get('/staff/notifications', [StaffDashboardController::class, 'notifications'])->name('staff.notifications');
    Route::post('/staff/notifications/mark-read', [StaffDashboardController::class, 'markNotificationsRead'])->name('staff.notifications.markRead');
    Route::delete('/staff/notifications/{id}', [StaffDashboardController::class, 'deleteNotification'])->name('staff.notifications.delete');
    Route::post('/staff/notifications/mark-all-read', [StaffDashboardController::class, 'markAllNotificationsRead'])->name('staff.notifications.markAllRead');
    Route::get('/staff/applicants/list', [StaffDashboardController::class, 'applicantsList'])->name('staff.applicants.list');
    Route::get('/staff/applicants/search-suggestions', [StaffDashboardController::class, 'searchSuggestions'])->name('staff.applicants.search-suggestions');
    Route::get('/staff/applications/{user}', [StaffDashboardController::class, 'viewApplication'])->name('staff.applications.view');
    Route::post('/staff/applications/{user}/update-status', [StaffDashboardController::class, 'updateApplicationStatus'])->name('staff.applications.update-status');
    Route::delete('/staff/applications/{user}', [StaffDashboardController::class, 'destroyApplicant'])->name('staff.applications.destroy');
    Route::post('/staff/applications/{user}/move-to-pamana', [StaffDashboardController::class, 'moveToPamana'])->name('staff.applications.move-to-pamana');
    Route::post('/staff/applications/{user}/add-to-grantees', [StaffDashboardController::class, 'addToGrantees'])->name('staff.applications.add-to-grantees');
    Route::post('/staff/applications/{user}/add-to-waiting', [StaffDashboardController::class, 'addToWaiting'])->name('staff.applications.add-to-waiting');
    Route::post('/staff/documents/{document}/update-status', [StaffDashboardController::class, 'updateDocumentStatus'])->name('staff.documents.update-status');
    Route::post('staff/logout', [App\Http\Controllers\StaffAuthController::class, 'logout'])->name('staff.logout');
    Route::get('/staff/grades/{user}', [StaffDashboardController::class, 'extractGrades'])->name('staff.grades.extract');
    Route::post('/staff/users/{user}/update-gwa', [StaffDashboardController::class, 'updateGWA'])->name('staff.users.update-gwa');
    Route::get('/staff/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('staff.settings');
    Route::post('/staff/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('staff.settings.update');
    Route::post('/staff/settings/encode-applicant', [App\Http\Controllers\SettingsController::class, 'storeApplicant'])->name('staff.settings.encode-applicant');

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
    Route::get('/staff/priorities/citation-awards', [StaffDashboardController::class, 'citationAwardsPriority'])->name('staff.priorities.citation-awards');
    Route::get('/staff/priorities/social-responsibility', [StaffDashboardController::class, 'socialResponsibilityPriority'])->name('staff.priorities.social-responsibility');
    Route::get('/staff/priorities/other-requirements', [StaffDashboardController::class, 'otherRequirementsPriority'])->name('staff.priorities.other-requirements');

    // Masterlist routes
    Route::get('/staff/masterlist/regular/all', [StaffDashboardController::class, 'masterlistRegular'])->name('staff.masterlist.regular.all');
    Route::get('/staff/masterlist/regular/grantees', [StaffDashboardController::class, 'masterlistRegularGrantees'])->name('staff.masterlist.regular.grantees');
    Route::get('/staff/masterlist/regular/waiting', [StaffDashboardController::class, 'masterlistRegularWaiting'])->name('staff.masterlist.regular.waiting');
    Route::get('/staff/masterlist/pamana', [StaffDashboardController::class, 'masterlistPamana'])->name('staff.masterlist.pamana');
    Route::get('/staff/grantees/report', [StaffDashboardController::class, 'granteesReport'])->name('staff.grantees.report');
    Route::get('/staff/pamana/report', [StaffDashboardController::class, 'pamanaReport'])->name('staff.pamana.report');
    Route::get('/staff/waiting-list/report', [StaffDashboardController::class, 'waitingListReport'])->name('staff.waiting-list.report');
    Route::get('/staff/disqualified/report', [StaffDashboardController::class, 'disqualifiedApplicantsReport'])->name('staff.disqualified.report');
    Route::get('/staff/replacements/report', [StaffDashboardController::class, 'replacementsReport'])->name('staff.replacements.report');
    Route::get('/staff/replacements/grantees', [StaffDashboardController::class, 'replacementGrantees'])->name('staff.replacements.grantees');
    Route::get('/staff/replacements/waiting', [StaffDashboardController::class, 'replacementWaiting'])->name('staff.replacements.waiting');
    Route::post('/staff/replacements', [StaffDashboardController::class, 'storeReplacement'])->name('staff.replacements.store');
    Route::post('/staff/waiting-list/update', [StaffDashboardController::class, 'updateWaitingList'])->name('staff.waiting-list.update');
    Route::post('/staff/grantees/update-grants', [StaffDashboardController::class, 'updateGrants'])->name('staff.grantees.update-grants');

    // Announcements routes
    Route::get('/staff/announcements', [StaffDashboardController::class, 'announcements'])->name('staff.announcements.index');
    Route::post('/staff/announcements', [StaffDashboardController::class, 'storeAnnouncement'])->name('staff.announcements.store');
    Route::delete('/staff/announcements/{id}', [StaffDashboardController::class, 'deleteAnnouncement'])->name('staff.announcements.delete');
});

// Geographic API Routes (Public)
Route::get('/address/barangays', [AddressController::class, 'barangaysByMunicipality'])->name('address.barangays');
Route::get('/address/municipalities', [AddressController::class, 'municipalitiesByProvince'])->name('address.municipalities');
