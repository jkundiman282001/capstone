<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\BasicInfo;
use App\Models\ApplicationDraft;
use App\Models\TransactionHistory;
use App\Notifications\TransactionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\FullAddress;
use App\Models\SchoolPref;
use App\Models\Education;
use App\Models\Family;
use App\Models\FamSiblings;
use App\Models\Staff;
use App\Notifications\StudentSubmittedApplication;
use App\Notifications\StudentUploadedDocument;
use App\Services\DocumentPriorityService;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        // In a real app, fetch the student's application status from DB
        $application = null; // Replace with actual application model if exists
        $hasApplied = false;
        $applicationStatus = 'pending';
        $rejectionReason = null;
        $grantStatus = null;

        // Check if user is authenticated
        if ($request->user()) {
            $hasApplied = BasicInfo::where('user_id', $request->user()->id)->exists();

            // Get application status and rejection reason if exists
            if ($hasApplied) {
                $basicInfo = BasicInfo::where('user_id', $request->user()->id)->first();
                $applicationStatus = $basicInfo->application_status ?? 'pending';
                $rejectionReason = $basicInfo->application_rejection_reason ?? null;
                $grantStatus = $basicInfo->grant_status ? strtolower(trim($basicInfo->grant_status)) : null;
            }
        }

        // Get statistics matching landing page
        $maxSlots = \App\Models\Setting::get('max_slots', 120);
        $validatedCount = BasicInfo::where('application_status', 'validated')->count();
        $availableSlots = max(0, $maxSlots - $validatedCount);
        $isFull = $availableSlots === 0;

        // Count applicants who have applied (have type_assist filled)
        $applicantsApplied = BasicInfo::whereNotNull('type_assist')->count();

        // Count applicants who are approved/validated
        $applicantsApproved = $validatedCount;

        // Count pending applications (applied but not yet reviewed/approved)
        $applicantsPending = BasicInfo::whereNotNull('type_assist')
            ->where(function ($query) {
                $query->whereNull('application_status')
                    ->orWhere('application_status', 'pending');
            })
            ->count();

        $stats = [
            'slotsLeft' => $availableSlots,
            'applicantsApplied' => $applicantsApplied,
            'applicantsApproved' => $applicantsApproved,
            'applicantsPending' => $applicantsPending,
            'maxSlots' => $maxSlots,
            'availableSlots' => $availableSlots,
            'isFull' => $isFull,
        ];

        // Fetch announcements from database
        $announcements = \App\Models\Announcement::orderBy('created_at', 'desc')
            ->take(6) // Show latest 6 announcements
            ->get();

        return view('student.dashboard', compact('application', 'hasApplied', 'applicationStatus', 'rejectionReason', 'grantStatus', 'announcements', 'stats'));
    }

    public function showRenewalForm(Request $request)
    {
        $user = auth()->user();

        // Check if user has submitted an application
        $existingApplication = BasicInfo::where('user_id', $user->id)
            ->whereNotNull('type_assist')
            ->first();

        // Check eligibility: Must be validated and a grantee
        if (! $existingApplication ||
            $existingApplication->application_status !== 'validated' ||
            strtolower(trim($existingApplication->grant_status ?? '')) !== 'grantee') {
            return redirect()->route('student.dashboard')
                ->with('error', 'You are not eligible for scholarship renewal. Only validated grantees can renew their scholarship.');
        }

        // Renewal application required documents
        $renewalRequiredTypes = [
            'certificate_of_enrollment' => 'Certificate of Enrollment',
            'statement_of_account' => 'Statement of Account',
            'gwa_previous_sem' => 'GWA of Previous Semester',
        ];

        // Fetch user documents
        $documents = Document::where('user_id', $user->id)->latest()->get();

        return view('student.renew', compact('user', 'renewalRequiredTypes', 'documents', 'existingApplication'));
    }

    public function submitRenewal(Request $request)
    {
        $user = auth()->user();

        // Check eligibility
        $existingApplication = BasicInfo::where('user_id', $user->id)
            ->whereNotNull('type_assist')
            ->first();

        if (! $existingApplication ||
            $existingApplication->application_status !== 'validated' ||
            strtolower(trim($existingApplication->grant_status ?? '')) !== 'grantee') {
            return redirect()->route('student.dashboard')
                ->with('error', 'You are not eligible for scholarship renewal.');
        }

        $request->validate([
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'gpa' => 'nullable|numeric|min:0|max:100',
            'target_year_level' => 'required|integer|min:1|max:5',
        ]);

        try {
            DB::beginTransaction();

            $updateData = [];
            
            // Update GWA if provided
            if ($request->has('gpa') && $request->gpa) {
                $updateData['gpa'] = $request->gpa;
            }

            // Update Target Year Level
            if ($request->has('target_year_level')) {
                $updateData['target_year_level'] = $request->target_year_level;
            }

            if (!empty($updateData)) {
                $existingApplication->update($updateData);
            }

            // Handle document uploads
            $hasUploads = false;
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    if (! $file) {
                        continue;
                    }
                    $hasUploads = true;

                    $disk = config('filesystems.default');
                    $path = $file->store('documents', $disk);

                    // Update or create document record
                    $document = Document::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'type' => $type,
                        ],
                        [
                            'filename' => $file->getClientOriginalName(),
                            'filepath' => $path,
                            'filetype' => $file->getClientMimeType(),
                            'filesize' => $file->getSize(),
                            'status' => 'pending',
                            'submitted_at' => now(),
                        ]
                    );

                    $priorityService = new \App\Services\DocumentPriorityService;
                    $priorityService->onDocumentUploaded($document);

                    // Notify staff about each document
                    try {
                        foreach (\App\Models\Staff::all() as $staff) {
                            $staff->notify(new \App\Notifications\StudentUploadedDocument($user, $type));
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to notify staff about document upload: ' . $e->getMessage());
                    }
                }
            }

            if (!$hasUploads && !$request->has('gpa')) {
                 return redirect()->back()->with('error', 'Please upload at least one document or update your GWA.');
            }

            // Notify all staff about renewal submission
            try {
                foreach (\App\Models\Staff::all() as $staff) {
                    $staff->notify(new \App\Notifications\StudentSubmittedApplication($user));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to notify staff about renewal submission: ' . $e->getMessage());
            }

            // Notify the student
            $user->notify(new TransactionNotification(
                'transaction',
                'Renewal Submitted',
                'Your scholarship renewal application has been successfully submitted and is now pending review.',
                'normal'
            ));

            DB::commit();
            $message = 'Your scholarship renewal application has been submitted!';
            $request->session()->flash('status', $message);

            return redirect()->route('student.dashboard');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Scholarship renewal submission failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while submitting your renewal: ' . $e->getMessage());
        }
    }

    public function apply(Request $request)
    {
        $user = auth()->user();

        // Check if this is a renewal application
        $isRenewal = $request->has('is_renewal') && $request->input('is_renewal') == '1';

        // Check if user has already submitted an application
        $hasSubmitted = BasicInfo::where('user_id', $user->id)
            ->whereNotNull('type_assist')
            ->exists();

        // Allow renewal if user is a validated grantee, otherwise block
        if ($hasSubmitted && ! $isRenewal) {
            return redirect()->route('student.apply')
                ->with('error', 'You have already submitted an application. You cannot submit another application.');
        }

        // For renewals, check if user is eligible (validated grantee)
        if ($isRenewal) {
            $existingApplication = BasicInfo::where('user_id', $user->id)
                ->whereNotNull('type_assist')
                ->first();

            if (! $existingApplication ||
                $existingApplication->application_status !== 'validated' ||
                strtolower(trim($existingApplication->grant_status ?? '')) !== 'grantee') {
                return redirect()->route('student.apply')
                    ->with('error', 'You are not eligible for scholarship renewal. Only validated grantees can renew their scholarship.');
            }
        }

        $request->validate([
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            // Basic Info
            'birthdate' => 'required_if:is_renewal,0|date',
            'birthplace' => 'required_if:is_renewal,0|string|max:255',
            'gender' => 'required_if:is_renewal,0|string|in:Male,Female',
            'civil_status' => 'required_if:is_renewal,0|string|max:50',

            // Addresses
            'mailing_barangay' => 'required_if:is_renewal,0|string|max:255',
            'mailing_municipality' => 'required_if:is_renewal,0|string|max:255',
            'mailing_province' => 'required_if:is_renewal,0|string|max:255',

            'permanent_barangay' => 'required_if:is_renewal,0|string|max:255',
            'permanent_municipality' => 'required_if:is_renewal,0|string|max:255',
            'permanent_province' => 'required_if:is_renewal,0|string|max:255',

            'origin_barangay' => 'required_if:is_renewal,0|string|max:255',
            'origin_municipality' => 'required_if:is_renewal,0|string|max:255',
            'origin_province' => 'required_if:is_renewal,0|string|max:255',

            // School Pref
            'school1_name' => 'required_if:is_renewal,0|string|max:255',
            'school1_address' => 'required_if:is_renewal,0|string|max:255',
            'school1_course1' => 'required_if:is_renewal,0|string|max:255',
            'school1_type' => 'required_if:is_renewal,0|string|max:50',
            'school1_years' => 'required_if:is_renewal,0|integer',

            'contribution' => 'required_if:is_renewal,0|string',
            'plans_after_grad' => 'required_if:is_renewal,0|string',

            // Parent Info (Optional but must be strings if provided)
            'father_name' => 'nullable|string|max:255',
            'father_address' => 'nullable|string|max:255',
            'father_education' => 'nullable|string|max:255',
            'father_income' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_address' => 'nullable|string|max:255',
            'mother_education' => 'nullable|string|max:255',
            'mother_income' => 'nullable|string|max:255',
            'gpa' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

        // For renewals, only process document uploads and skip form data
        if ($isRenewal) {
            // Update GWA if provided in renewal
            if ($request->has('gpa') && $request->gpa) {
                $existingApplication = BasicInfo::where('user_id', $user->id)
                    ->whereNotNull('type_assist')
                    ->first();
                if ($existingApplication) {
                    $existingApplication->update(['gpa' => $request->gpa]);
                }
            }

            // Handle document uploads for renewal
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    if (! $file) {
                        continue;
                    }

                    $disk = config('filesystems.default');
                    $path = $file->store('documents', $disk);

                    // Update or create document record
                    $document = Document::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'type' => $type,
                        ],
                        [
                            'filename' => $file->getClientOriginalName(),
                            'filepath' => $path,
                            'filetype' => $file->getClientMimeType(),
                            'filesize' => $file->getSize(),
                            'status' => 'pending',
                            'submitted_at' => now(),
                        ]
                    );

                    $priorityService = new \App\Services\DocumentPriorityService;
                    $priorityService->onDocumentUploaded($document);

                    // Notify staff (handle missing table gracefully)
                    try {
                        foreach (\App\Models\Staff::all() as $staff) {
                            $staff->notify(new \App\Notifications\StudentUploadedDocument($user, $type));
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to notify staff about document upload: ' . $e->getMessage());
                    }
                }
            }

            // Notify all staff about renewal submission
            try {
                // Update GWA if provided in renewal
                if ($request->has('gpa') && $request->gpa) {
                    $existingApplication = BasicInfo::where('user_id', $user->id)
                        ->whereNotNull('type_assist')
                        ->first();
                    if ($existingApplication) {
                        $existingApplication->update(['gpa' => $request->gpa]);
                    }
                }

                foreach (\App\Models\Staff::all() as $staff) {
                    $staff->notify(new \App\Notifications\StudentSubmittedApplication($user));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to notify staff about renewal submission: ' . $e->getMessage());
            }

            // Notify the student
            $user->notify(new TransactionNotification(
                'transaction',
                'Renewal Submitted',
                'Your scholarship renewal application has been successfully submitted and is now pending review.',
                'normal'
            ));

            DB::commit();
            $message = 'Your scholarship renewal application has been submitted!';
            $request->session()->flash('status', $message);

            return redirect()->route('student.dashboard');
        }

        // Get the selected type of assistance (only one allowed)
        $typeAssist = null;
        if ($request->has('type_of_assistance') && is_array($request->type_of_assistance) && count($request->type_of_assistance) > 0) {
            $typeAssist = $request->type_of_assistance[0];
        }

        // 1. Save Mailing Address (reuse if exists)
        $mailingAddress = \App\Models\Address::firstOrCreate([
            'barangay' => $request->mailing_barangay,
            'municipality' => $request->mailing_municipality,
            'province' => $request->mailing_province,
        ]);
        $mailing = \DB::table('mailing_address')->insertGetId([
            'address_id' => $mailingAddress->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \DB::table('mailing_address')->where('id', $mailing)->update(['house_num' => $request->mailing_house_num]);

        // 2. Save Permanent Address (reuse if exists)
        $permanentAddress = \App\Models\Address::firstOrCreate([
            'barangay' => $request->permanent_barangay,
            'municipality' => $request->permanent_municipality,
            'province' => $request->permanent_province,
        ]);
        $permanent = \DB::table('permanent_address')->insertGetId([
            'address_id' => $permanentAddress->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \DB::table('permanent_address')->where('id', $permanent)->update(['house_num' => $request->permanent_house_num]);

        // 3. Save Origin Address (reuse if exists)
        $originAddress = \App\Models\Address::firstOrCreate([
            'barangay' => $request->origin_barangay,
            'municipality' => $request->origin_municipality,
            'province' => $request->origin_province,
        ]);
        $origin = \DB::table('origin')->insertGetId([
            'address_id' => $originAddress->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \DB::table('origin')->where('id', $origin)->update(['house_num' => $request->origin_house_num]);

        // 4. Save Full Address (connect the three address tables)
        $fullAddressId = \DB::table('full_address')->insertGetId([
            'mailing_address_id' => $mailing,
            'permanent_address_id' => $permanent,
            'origin_id' => $origin,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Save School Preference
        $course1 = $request->school1_course1;
        if ($course1 === 'Other' && $request->has('school1_course1_other')) {
            $course1 = $request->school1_course1_other;
        }
        $course1Alt = $request->school1_course_alt;
        if ($course1Alt === 'Other' && $request->has('school1_course_alt_other')) {
            $course1Alt = $request->school1_course_alt_other;
        }
        $course2 = $request->school2_course1;
        if ($course2 === 'Other' && $request->has('school2_course1_other')) {
            $course2 = $request->school2_course1_other;
        }
        $course2Alt = $request->school2_course_alt;
        if ($course2Alt === 'Other' && $request->has('school2_course_alt_other')) {
            $course2Alt = $request->school2_course_alt_other;
        }

        $schoolPref = \App\Models\SchoolPref::create([
            'school_name' => $request->school1_name,
            'address' => $request->school1_address,
            'degree' => $course1,
            'alt_degree' => $course1Alt,
            'school_type' => $request->school1_type,
            'num_years' => $request->school1_years,
            'school_name2' => $request->school2_name,
            'address2' => $request->school2_address,
            'degree2' => $course2,
            'alt_degree2' => $course2Alt,
            'school_type2' => $request->school2_type,
            'num_years2' => $request->school2_years,
            'ques_answer1' => $request->contribution,
            'ques_answer2' => $request->plans_after_grad,
        ]);

        // 6. Save Basic Info (linking all the above)
        $assistanceFor = null;
        if (is_array($request->assistance_for) && count($request->assistance_for) > 0) {
            $assistanceFor = implode(',', $request->assistance_for);
        }

        $basicInfo = \App\Models\BasicInfo::updateOrCreate(
            ['user_id' => $user->id],
            [
            'house_num' => $request->mailing_house_num,
            'birthdate' => $request->birthdate,
            'birthplace' => $request->birthplace,
            'gender' => $request->gender,
            'civil_status' => $request->civil_status,
            'full_address_id' => $fullAddressId,
            // 'family_id' => will be set after creating family records
            'school_pref_id' => $schoolPref->id,
            'type_assist' => $typeAssist,
            'assistance_for' => $assistanceFor,
            'gpa' => $request->gpa,
        ]);

        // 2. Save Education (each level as a separate row, linked to BasicInfo)
        $educationLevels = [
            1 => ['school' => $request->elem_school, 'type' => $request->elem_type, 'year' => $request->elem_year, 'avg' => $request->elem_avg, 'rank' => $request->elem_rank],
            2 => ['school' => $request->hs_school, 'type' => $request->hs_type, 'year' => $request->hs_year, 'avg' => $request->hs_avg, 'rank' => $request->hs_rank],
            3 => ['school' => $request->voc_school, 'type' => $request->voc_type, 'year' => $request->voc_year, 'avg' => $request->voc_avg, 'rank' => $request->voc_rank],
            4 => ['school' => $request->college_school, 'type' => $request->college_type, 'year' => $request->college_year, 'avg' => $request->college_avg, 'rank' => $request->college_rank],
        ];

        $educationIds = [];
        foreach ($educationLevels as $category => $data) {
            if (! empty($data['school'])) { // Only save if school name is provided
                $education = \App\Models\Education::create([
                    'basic_info_id' => $basicInfo->id,
                    'category' => $category,
                    'school_name' => $data['school'],
                    'school_type' => $data['type'],
                    'year_grad' => $data['year'],
                    'grade_ave' => $data['avg'],
                    'rank' => $data['rank'],
                ]);
                $educationIds[$category] = $education->id;
            }
        }

        // Optionally, set education_id in BasicInfo to the highest level's ID (if any)

        // 4. Save Family (father and mother)
        // Handle "Other" occupation for father
        $fatherOccupation = $request->father_occupation;
        if ($fatherOccupation === 'Other' && $request->has('father_occupation_other')) {
            $fatherOccupation = $request->father_occupation_other;
        }

        // Handle "Other" occupation for mother
        $motherOccupation = $request->mother_occupation;
        if ($motherOccupation === 'Other' && $request->has('mother_occupation_other')) {
            $motherOccupation = $request->mother_occupation_other;
        }

        // Clean income values - remove commas and non-numeric characters, convert to integer
        // Handle range strings from dropdown by taking the first numeric part
        $fatherIncome = $request->father_income;
        if ($fatherIncome) {
            // Handle both en-dash (–) and regular dash (-)
            $dash = strpos($fatherIncome, '–') !== false ? '–' : (strpos($fatherIncome, '-') !== false ? '-' : null);
            if ($dash && strpos($fatherIncome, 'Below') === false) {
                $parts = explode($dash, $fatherIncome);
                $fatherIncome = $parts[0];
            }
            $fatherIncome = (int) preg_replace('/[^0-9]/', '', $fatherIncome);
        } else {
            $fatherIncome = 0;
        }

        $motherIncome = $request->mother_income;
        if ($motherIncome) {
            // Handle both en-dash (–) and regular dash (-)
            $dash = strpos($motherIncome, '–') !== false ? '–' : (strpos($motherIncome, '-') !== false ? '-' : null);
            if ($dash && strpos($motherIncome, 'Below') === false) {
                $parts = explode($dash, $motherIncome);
                $motherIncome = $parts[0];
            }
            $motherIncome = (int) preg_replace('/[^0-9]/', '', $motherIncome);
        } else {
            $motherIncome = 0;
        }

        $father = \App\Models\Family::create([
            'basic_info_id' => $basicInfo->id,
            'name' => $request->father_name,
            'address' => $request->father_address,
            'occupation' => $fatherOccupation,
            'office_address' => $request->father_office_address,
            'educational_attainment' => $request->father_education,
            'ethno_id' => $request->father_ethno,
            'income' => $fatherIncome,
            'status' => $request->father_status,
            'fam_type' => 'father',
        ]);
        $mother = \App\Models\Family::create([
            'basic_info_id' => $basicInfo->id,
            'name' => $request->mother_name,
            'address' => $request->mother_address,
            'occupation' => $motherOccupation,
            'office_address' => $request->mother_office_address,
            'educational_attainment' => $request->mother_education,
            'ethno_id' => $request->mother_ethno,
            'income' => $motherIncome,
            'status' => $request->mother_status,
            'fam_type' => 'mother',
        ]);

        // 3. Save Siblings
        $createdSiblingIds = [];
        if ($request->sibling_name) {
            foreach ($request->sibling_name as $i => $name) {
                if ($name) {
                    $sibling = \App\Models\FamSiblings::create([
                        'basic_info_id' => $basicInfo->id,
                        'name' => $name,
                        'age' => $request->sibling_age[$i] ?? null,
                        'scholarship' => $request->sibling_scholarship[$i] ?? null,
                        'course_year' => $request->sibling_course[$i] ?? null,
                        'present_status' => $request->sibling_status[$i] ?? null,
                    ]);
                    $createdSiblingIds[] = $sibling->id;
                }
            }
        }

        $message = $isRenewal
            ? 'Your scholarship renewal application has been submitted!'
            : 'Your IP Scholarship application has been submitted!';
        $request->session()->flash('status', $message);

        // Handle document uploads submitted with the form
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $type => $file) {
                if (! $file) {
                    continue;
                }

                $disk = config('filesystems.default');
                $path = $file->store('documents', $disk);

                // Update or create document record
                $document = Document::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'type' => $type,
                    ],
                    [
                        'filename' => $file->getClientOriginalName(),
                        'filepath' => $path,
                        'filetype' => $file->getClientMimeType(),
                        'filesize' => $file->getSize(),
                        'status' => 'pending',
                        'submitted_at' => now(),
                    ]
                );

                $priorityService = new \App\Services\DocumentPriorityService;
                $priorityService->onDocumentUploaded($document);

                // Log Transaction History
                TransactionHistory::create([
                    'user_id' => $user->id,
                    'action' => 'Document Uploaded',
                    'description' => 'Uploaded: ' . ucwords(str_replace('_', ' ', $type)) . ' (File: ' . $file->getClientOriginalName() . ')',
                    'status' => 'info',
                    'metadata' => [
                        'document_id' => $document->id,
                        'type' => $type,
                        'filename' => $file->getClientOriginalName()
                    ]
                ]);

                // Notify staff (handle missing table gracefully)
                try {
                    foreach (\App\Models\Staff::all() as $staff) {
                        $staff->notify(new \App\Notifications\StudentUploadedDocument($user, $type));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to notify staff about document upload: ' . $e->getMessage());
                }
            }
        }

        // Notify all staff
        try {
            foreach (\App\Models\Staff::all() as $staff) {
                $staff->notify(new \App\Notifications\StudentSubmittedApplication($user));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to notify staff about application submission: ' . $e->getMessage());
        }

        // Notify the student
        $user->notify(new TransactionNotification(
            'transaction',
            'Application Submitted',
            'Your scholarship application has been successfully submitted and is now pending review.',
            'normal'
        ));

        // Log Transaction History
        TransactionHistory::create([
            'user_id' => $user->id,
            'action' => 'Application Submitted',
            'description' => 'Your scholarship application has been successfully submitted and is now pending review.',
            'status' => 'success',
        ]);

        DB::commit();

        return redirect()->route('student.dashboard');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Scholarship application submission failed: ' . $e->getMessage(), [
            'user_id' => $user->id,
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()
            ->withInput()
            ->with('error', 'An error occurred while submitting your application: ' . $e->getMessage());
    }
}

    /**
     * Save application draft
     */
    public function saveDraft(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'draft_id' => 'nullable|exists:application_drafts,id',
                'name' => 'nullable|string|max:255',
                'current_step' => 'required|integer|min:1|max:6',
                'form_data' => 'nullable|array',
            ]);

            $draftId = $request->input('draft_id');
            $name = $request->input('name');
            $formData = $request->input('form_data', []);

            // Generate name from form data if not provided
            if (! $name && is_array($formData)) {
                $firstName = $formData['first_name'] ?? '';
                $lastName = $formData['last_name'] ?? '';
                $name = trim($firstName.' '.$lastName);
                $name = $name ?: 'Untitled Application';
                if ($name !== 'Untitled Application') {
                    $name .= ' - Scholarship Application';
                }
            }

            if ($draftId) {
                // Update existing draft
                $draft = ApplicationDraft::where('id', $draftId)
                    ->where('user_id', $user->id)
                    ->first();

                if (! $draft) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Draft not found or you do not have permission to update it.',
                    ], 404);
                }

                $draft->update([
                    'name' => $name,
                    'current_step' => $request->input('current_step'),
                    'form_data' => $formData,
                ]);
            } else {
                // Create new draft
                $draft = ApplicationDraft::create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'current_step' => $request->input('current_step'),
                    'form_data' => $formData,
                ]);
            }

            return response()->json([
                'success' => true,
                'draft' => [
                    'id' => $draft->id,
                    'name' => $draft->name,
                    'current_step' => $draft->current_step,
                    'updated_at' => $draft->updated_at ? $draft->updated_at->toISOString() : now()->toISOString(),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving draft', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the draft: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all drafts for the current user
     */
    public function getDrafts(Request $request)
    {
        $user = auth()->user();

        $drafts = ApplicationDraft::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($draft) {
                return [
                    'id' => $draft->id,
                    'name' => $draft->name,
                    'current_step' => $draft->current_step,
                    'updated_at' => $draft->updated_at->toISOString(),
                    'created_at' => $draft->created_at->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'drafts' => $drafts,
        ]);
    }

    /**
     * Get a specific draft
     */
    public function getDraft($id, Request $request)
    {
        try {
            $user = auth()->user();

            $draft = ApplicationDraft::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (! $draft) {
                return response()->json([
                    'success' => false,
                    'message' => 'Draft not found or you do not have permission to access it.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'draft' => [
                    'id' => $draft->id,
                    'name' => $draft->name,
                    'current_step' => $draft->current_step ?? 1,
                    'form_data' => $draft->form_data ?? [],
                    'updated_at' => $draft->updated_at ? $draft->updated_at->toISOString() : now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading draft', [
                'draft_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading the draft: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a draft
     */
    public function deleteDraft($id, Request $request)
    {
        $user = auth()->user();

        $draft = ApplicationDraft::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $draft->delete();

        return response()->json([
            'success' => true,
            'message' => 'Draft deleted successfully',
        ]);
    }

    public function profile(Request $request)
    {
        // In a real app, fetch the student's profile data from DB
        $student = Auth::user()->load('documents', 'basicInfo'); // Get the authenticated user with documents and basic info

        // Check application status from basic_info table
        $basicInfo = $student->basicInfo;



        $applicationStatus = $basicInfo ? ($basicInfo->application_status ?? 'pending') : 'pending';
        $rejectionReason = $basicInfo ? ($basicInfo->application_rejection_reason ?? null) : null;

        // Get GWA from basic_info table (entered by admin)
        $currentGWA = $basicInfo ? ($basicInfo->gpa ?? null) : null;
        $grantStatus = $basicInfo ? ($basicInfo->grant_status ?? null) : null;

        $enableRenewButton = \App\Models\Setting::get('enable_renew_button', 1);

        return view('student.profile', compact('student', 'applicationStatus', 'rejectionReason', 'currentGWA', 'grantStatus', 'enableRenewButton'));
    }

    public function performance(Request $request)
    {
        $student = Auth::user(); // Get the authenticated user

        // Security check: Ensure user has submitted an application
        $hasSubmitted = \App\Models\BasicInfo::where('user_id', $student->id)
            ->whereNotNull('type_assist')
            ->exists();

        if (! $hasSubmitted) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You must submit your application before viewing this page.');
        }

        $basicInfo = \App\Models\BasicInfo::where('user_id', $student->id)->first();

        // Acceptance chance calculation moved to view
        $priorityRank = null;
        $priorityFactors = [];
        $priorityStatistics = [];

        // Get masterlist: validated applicants who are not yet grantees (waiting list)
        // Masterlist = validated applicants (approved) who are waiting for grant (not yet grantees)
        $masterlistApplicants = \App\Models\User::with([
            'basicInfo',
            'ethno',
            'documents',
            'basicInfo.schoolPref',
        ])
            ->whereHas('basicInfo', function ($query) {
                $query->where('application_status', 'validated')
                    ->where(function ($q) {
                        $q->whereNull('grant_status')
                            ->orWhere('grant_status', '!=', 'grantee');
                    });
            })
            ->get();

        // Get student's application status
        $studentApplicationStatus = $basicInfo ? ($basicInfo->application_status ?? null) : null;
        $studentGrantStatus = $basicInfo ? ($basicInfo->grant_status ?? null) : null;
        $isStudentValidated = ($studentApplicationStatus === 'validated');
        // Handle case variations and whitespace for grant_status
        $isStudentGrantee = $studentGrantStatus && strtolower(trim($studentGrantStatus)) === 'grantee';
        $isStudentInMasterlist = $isStudentValidated && ! $isStudentGrantee;

        // Initialize student priority score
        $studentPriorityScore = null;

        // Check if student has documents uploaded (indicator they've applied)
        $hasDocuments = $student->documents()->exists();

        // Calculate priority factors for display (if student has applied OR is validated OR has documents)
        // Also check if basicInfo exists OR student has documents
        if (($basicInfo && ($basicInfo->type_assist || $isStudentValidated)) || $hasDocuments) {
            $student->load(['ethno', 'documents', 'basicInfo.schoolPref', 'basicInfo.education']);

            // Get priority factors
            $priorityService = new \App\Services\ApplicantPriorityService;
            $breakdown = $priorityService->calculateApplicantPriority($student);
            $priorityFactors = [
                'is_priority_ethno' => $breakdown['is_priority_ethno'] ?? false,
                'ip_rubric_score' => $breakdown['ip_rubric_score'] ?? 0,
                'academic_rubric_score' => $breakdown['academic_rubric_score'] ?? 0,
                'has_approved_income_tax' => $breakdown['has_approved_income_tax'] ?? false,
                'awards_rubric_score' => $breakdown['awards_rubric_score'] ?? 0,
                'social_responsibility_rubric_score' => $breakdown['social_responsibility_rubric_score'] ?? 0,
            ];

            // Calculate student's priority score for weighted lottery
            $studentPriorityScore = $breakdown['priority_score'] ?? 0;
        }

        // ============================================
        // ACCEPTANCE CHANCE CALCULATION
        // ============================================
        // Improved calculation that handles cases where slots > applicants
        // Uses applicants waiting for approval (validated but not yet grantees)
        // Rules:
        // 1. If student is a grantee → 100%
        // 2. If slots_left >= total_applicants_waiting:
        //    - If validated: 95% (high but not 100% due to remaining uncertainty)
        //    - If not validated: 85% (lower due to validation requirement)
        // 3. Otherwise → (slots_left / total_applicants_waiting) * 100
        // ============================================

        // Initialize acceptance chance
        $acceptanceChance = 0.0;

        // Step 1: Get basic information
        $maxSlots = \App\Models\Setting::get('max_slots', 120);
        // Use case-insensitive comparison to count grantees
        $granteesCount = BasicInfo::where('application_status', 'validated')
            ->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'")
            ->count();
        $slotsLeft = max(0, $maxSlots - $granteesCount);

        // Step 2: Count total applicants waiting for approval (validated but not yet grantees)
        // This represents applicants who are in the queue waiting for grant approval
        // Exclude grantees by checking that grant_status is not 'grantee' or 'Grantee'
        // Use case-insensitive comparison and handle NULL values

        // First, get all validated applicants with their grant_status for debugging
        $allValidatedApplicants = BasicInfo::where('application_status', 'validated')
            ->with('user:id,first_name,last_name')
            ->get(['id', 'user_id', 'application_status', 'grant_status']);

        // Log all validated applicants for debugging
        \Log::info('All validated applicants:', [
            'total' => $allValidatedApplicants->count(),
            'applicants' => $allValidatedApplicants->map(function ($app) {
                return [
                    'user_id' => $app->user_id,
                    'name' => $app->user ? $app->user->first_name.' '.$app->user->last_name : 'N/A',
                    'grant_status' => $app->grant_status,
                    'grant_status_lower' => $app->grant_status ? strtolower(trim($app->grant_status)) : 'NULL',
                ];
            })->toArray(),
        ]);

        // Count applicants waiting (not grantees)
        // Method: Count all validated, then subtract grantees (more reliable)
        $allValidatedCount = BasicInfo::where('application_status', 'validated')->count();

        // Try multiple ways to count grantees to debug
        $granteesCountMethod1 = BasicInfo::where('application_status', 'validated')
            ->where(function ($q) {
                $q->where('grant_status', 'grantee')
                    ->orWhere('grant_status', 'Grantee');
            })
            ->count();

        // Try case-insensitive approach
        $granteesCountMethod2 = BasicInfo::where('application_status', 'validated')
            ->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'")
            ->count();

        // Get all grant_status values for debugging
        $allGrantStatuses = BasicInfo::where('application_status', 'validated')
            ->whereNotNull('grant_status')
            ->pluck('grant_status')
            ->toArray();

        // Use the case-insensitive method
        $granteesCountForWaiting = $granteesCountMethod2;
        $totalApplicantsWaiting = $allValidatedCount - $granteesCountForWaiting;

        // Debug logging
        \Log::info('Total Applicants Waiting Calculation:', [
            'all_validated_count' => $allValidatedCount,
            'grantees_count_method1' => $granteesCountMethod1,
            'grantees_count_method2' => $granteesCountMethod2,
            'grantees_count_used' => $granteesCountForWaiting,
            'total_applicants_waiting' => $totalApplicantsWaiting,
            'calculation' => "{$allValidatedCount} - {$granteesCountForWaiting} = {$totalApplicantsWaiting}",
            'all_grant_statuses' => $allGrantStatuses,
            'all_validated_applicants' => $allValidatedApplicants->map(function ($app) {
                return [
                    'user_id' => $app->user_id,
                    'grant_status' => $app->grant_status,
                    'grant_status_lower' => $app->grant_status ? strtolower(trim($app->grant_status)) : 'NULL',
                ];
            })->toArray(),
        ]);

        // Get the actual applicants waiting for debugging
        // Simple approach: get all validated, exclude grantees using whereNotIn
        $applicantsWaiting = BasicInfo::where('application_status', 'validated')
            ->where(function ($q) {
                // Include NULL or values not in ['grantee', 'Grantee']
                $q->whereNull('grant_status')
                    ->orWhereNotIn('grant_status', ['grantee', 'Grantee']);
            })
            ->with('user:id,first_name,last_name')
            ->get(['id', 'user_id', 'application_status', 'grant_status']);

        \Log::info('Applicants waiting for approval (not grantees):', [
            'total' => $totalApplicantsWaiting,
            'applicants' => $applicantsWaiting->map(function ($app) {
                return [
                    'user_id' => $app->user_id,
                    'name' => $app->user ? $app->user->first_name.' '.$app->user->last_name : 'N/A',
                    'grant_status' => $app->grant_status,
                ];
            })->toArray(),
        ]);

        // Set priority statistics for display (calculation will be done in the view)
        $priorityStatistics = [
            'total_applicants' => $totalApplicantsWaiting, // Total applicants waiting for approval (validated but not yet grantees)
            'slots_left' => $slotsLeft,
            'grantees_count' => $granteesCount,
            'max_slots' => $maxSlots,
            'is_student_validated' => $isStudentValidated,
            'student_grant_status' => $studentGrantStatus,
            // Debug data: applicants waiting for approval
            'applicants_waiting_debug' => $applicantsWaiting->map(function ($app) {
                return [
                    'user_id' => $app->user_id,
                    'name' => $app->user ? $app->user->first_name.' '.$app->user->last_name : 'N/A',
                    'grant_status' => $app->grant_status ?? 'NULL',
                ];
            })->toArray(),
            // Debug data: all validated applicants
            'all_validated_debug' => $allValidatedApplicants->map(function ($app) {
                return [
                    'user_id' => $app->user_id,
                    'name' => $app->user ? $app->user->first_name.' '.$app->user->last_name : 'N/A',
                    'grant_status' => $app->grant_status ?? 'NULL',
                ];
            })->toArray(),
        ];

        // Calculate rank if student is in masterlist
        if ($isStudentInMasterlist) {
            $priorityService = new \App\Services\ApplicantPriorityService;
            $studentScore = $studentPriorityScore ?? 0;
            $rank = 1;

            foreach ($masterlistApplicants as $applicant) {
                if ($applicant->id === $student->id) {
                    continue; // Skip self
                }
                $applicantScore = $this->calculateStudentPriorityScore($applicant, $priorityService);
                if ($applicantScore > $studentScore) {
                    $rank++;
                }
            }

            $priorityRank = $rank;
        }

        // Get documents for the view
        $documents = $student->documents ?? collect();
        
        // Fetch permanent transaction history
        try {
            $transactionHistories = TransactionHistory::where('user_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error fetching transaction histories: ' . $e->getMessage());
            $transactionHistories = collect();
        }

        $requiredTypes = [
            'birth_certificate' => 'Original or Certified True Copy of Birth Certificate',
            'income_document' => 'Income Tax Return of the parents/guardians or Certificate of Tax Exemption from BIR or Certificate of Indigency signed by the barangay captain',
            'tribal_certificate' => 'Certificate of Tribal Membership/Certificate of Confirmation COC',
            'endorsement' => 'Endorsement of the IPS/IP Traditional Leaders',
            'good_moral' => 'Certificate of Good Moral from the Guidance Counselor',
            'grades' => 'Incoming First Year College (Senior High School Grades), Ongoing college students latest copy of grades',
        ];

        $renewalRequiredTypes = [
            'certificate_of_enrollment' => 'Certificate of Enrollment',
            'statement_of_account' => 'Statement of Account',
            'gwa_previous_sem' => 'GWA of Previous Semester'
        ];

        return view('student.performance', compact(
            'student',
            'basicInfo',
            'priorityRank',
            'priorityFactors',
            'priorityStatistics',
            'documents',
            'requiredTypes',
            'renewalRequiredTypes',
            'transactionHistories'
        ));
    }

    /**
     * Calculate student's priority score
     */
    private function calculateStudentPriorityScore($student, $priorityService): float
    {
        try {
            $breakdown = $priorityService->calculateApplicantPriority($student);

            return (float) ($breakdown['priority_score'] ?? 0.0);
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Check if student is in priority ethno group
     */
    private function checkPriorityEthno($student): bool
    {
        $ethnicity = optional($student->ethno)->ethnicity ?? null;
        if (! $ethnicity) {
            return false;
        }

        $priorityGroups = ["b'laan", 'bagobo', 'kalagan', 'kaulo'];

        return in_array(strtolower(trim($ethnicity)), $priorityGroups, true);
    }

    /**
     * Check if student's course is priority
     */
    private function checkPriorityCourse($student): bool
    {
        $basicInfo = $student->basicInfo;
        $schoolPref = $basicInfo?->schoolPref;
        $courseName = $schoolPref->degree ?? $schoolPref->degree2 ?? $student->course ?? null;

        if (! $courseName) {
            return false;
        }

        $priorityCourses = [
            'Agriculture', 'Aqua-Culture and Fisheries', 'Anthropology',
            'Business Administration (Accounting, Marketing, Management, Economics, Entrepreneurship)',
            'Civil Engineering', 'Community Development', 'Criminology', 'Education',
            'Foreign Service', 'Forestry and Environment Studies (Forestry, Environmental Science, Agro-Forestry)',
            'Geodetic Engineering', 'Geology', 'Law',
            'Medicine and Allied Health Sciences (Nursing, Midwifery, Medical Technology, etc.)',
            'Mechanical Engineering', 'Mining Engineering', 'Social Sciences (AB courses)', 'Social Work',
        ];

        $courseName = trim($courseName);

        return in_array($courseName, $priorityCourses, true);
    }

    /**
     * Check if student has all other requirements
     */
    private function checkAllOtherRequirements($student): bool
    {
        $otherRequiredTypes = ['birth_certificate', 'endorsement', 'good_moral'];
        $approvedDocs = $student->documents()
            ->whereIn('type', $otherRequiredTypes)
            ->where('status', 'approved')
            ->get();

        $hasBirthCert = $approvedDocs->where('type', 'birth_certificate')->isNotEmpty();
        $hasEndorsement = $approvedDocs->where('type', 'endorsement')->isNotEmpty();
        $hasGoodMoral = $approvedDocs->where('type', 'good_moral')->isNotEmpty();

        return $hasBirthCert && $hasEndorsement && $hasGoodMoral;
    }

    /**
     * Calculate acceptance chance using weighted-lottery (probabilistic) method
     *
     * Implementation: Approximation with replacement
     *
     * Formula:
     * 1. Weight: w_i = score_i / sum(all scores)
     * 2. Chance: Chance_i ≈ 1 - (1 - w_i)^S
     *
     * Where S = number of slots remaining
     *
     * This gives each applicant a probability based on their priority score
     * relative to all other applicants' scores. Higher scores = higher probability.
     *
     * @param  float  $studentScore  The applicant's priority score
     * @param  array  $allScores  Array of all applicants' priority scores
     * @param  int  $slotsLeft  Number of slots remaining
     * @return float Acceptance chance percentage (0-100)
     */
    private function calculateWeightedLotteryChance(float $studentScore, array $allScores, int $slotsLeft): float
    {
        // If no slots left, chance is 0
        if ($slotsLeft <= 0) {
            return 0.0;
        }

        // If student has no score, chance is 0
        if ($studentScore <= 0) {
            return 0.0;
        }

        // Calculate sum of all scores
        $totalScoreSum = array_sum($allScores);

        // Avoid division by zero
        if ($totalScoreSum <= 0) {
            return 0.0;
        }

        // Step 1: Calculate weight (w_i = score_i / sum(all scores))
        $weight = $studentScore / $totalScoreSum;

        // Ensure weight is between 0 and 1
        $weight = max(0.0, min(1.0, $weight));

        // Step 2: Calculate chance using approximation formula
        // Chance_i ≈ 1 - (1 - w_i)^S
        // Where S = slots_left
        $chance = 1 - pow(1 - $weight, $slotsLeft);

        // Convert to percentage and ensure it's between 0 and 100
        $chancePercent = $chance * 100;

        return round(max(0.0, min(100.0, $chancePercent)), 2);
    }

    public function notifications(Request $request)
    {
        $student = Auth::user();

        // Fetch real notifications from database
        $notifications = $student->notifications()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;

                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'general',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'is_read' => $notification->read_at !== null,
                    'created_at' => $notification->created_at,
                    'priority' => $data['priority'] ?? 'normal',
                    'rejection_reason' => $data['rejection_reason'] ?? null,
                ];
            });

        return view('student.notifications', compact('student', 'notifications'));
    }

    public function markNotificationAsRead($id)
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $id)->first();

            if ($notification && ! $notification->read_at) {
                $notification->markAsRead();

                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Notification not found or already read'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred'], 500);
        }
    }

    public function markAllNotificationsAsRead()
    {
        try {
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();

            return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred'], 500);
        }
    }

    public function deleteNotification($id)
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $id)->first();

            if ($notification) {
                $notification->delete();

                return response()->json(['success' => true, 'message' => 'Notification deleted']);
            }

            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred'], 500);
        }
    }

    public function support(Request $request)
    {
        $student = Auth::user();

        return view('student.support', compact('student'));
    }

    public function updateProfilePic(Request $request)
    {
        $request->validate([
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $user = Auth::user();

            $disk = config('filesystems.default');
            // Delete old profile picture if exists
            if ($user->profile_pic) {
                try {
                    if (Storage::disk($disk)->exists($user->profile_pic)) {
                        Storage::disk($disk)->delete($user->profile_pic);
                    } elseif (Storage::disk('public')->exists($user->profile_pic)) {
                        Storage::disk('public')->delete($user->profile_pic);
                    }
                } catch (\Throwable $e) {
                    Log::warning("Could not delete old profile picture: " . $e->getMessage());
                }
            }

            // Store new profile picture
            $path = $request->file('profile_pic')->store('profile-pics', $disk);

            // Update user profile
            $user->update(['profile_pic' => $path]);

            // Notify the student
            $user->notify(new \App\Notifications\TransactionNotification(
                'profile_update',
                'Profile Picture Updated',
                'Your profile picture has been successfully updated in your account settings.',
                'normal'
            ));

            return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated successfully',
                    'profile_pic_url' => route('profile-pic.show', ['filename' => basename($path)], false),
                ]);
        } catch (\Throwable $e) {
            Log::error("Failed to update profile picture: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile picture. Please try again later.',
            ], 500);
        }
    }

    /**
     * Serve the profile picture directly from storage.
     * This bypasses symlink issues on cloud hosting.
     */
    public function showProfilePic($filename)
    {
        try {
            $path = 'profile-pics/'.$filename;
            $disk = config('filesystems.default');
            
            \Log::info("Serving profile pic: {$path} from disk: {$disk}");

            // 1. Check default disk
            if (Storage::disk($disk)->exists($path)) {
                \Log::info("Found on default disk: {$disk}");
                
                $file = Storage::disk($disk)->get($path);
                $mimeType = Storage::disk($disk)->mimeType($path);
                
                return response($file, 200)->header('Content-Type', $mimeType);
            }

            // 2. Fallback to public disk
            if ($disk !== 'public' && Storage::disk('public')->exists($path)) {
                \Log::info("Found on public disk");
                
                $file = Storage::disk('public')->get($path);
                $mimeType = Storage::disk('public')->mimeType($path);
                
                return response($file, 200)->header('Content-Type', $mimeType);
            }
            
            \Log::warning("Profile pic not found on any disk: {$path}");

            // 3. Fallback to default avatar
            $defaultPath = public_path('images/default-avatar.png');
            if (file_exists($defaultPath)) {
                return response()->file($defaultPath, [
                    'Content-Type' => 'image/png',
                    'Cache-Control' => 'public, max-age=3600'
                ]);
            }

            // Absolute fallback: 1x1 transparent GIF
            return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'), 200)
                ->header('Content-Type', 'image/gif');
        } catch (\Throwable $e) {
            Log::error("Error serving profile picture: " . $e->getMessage());
            
            // Fallback to default avatar on any storage error
            $defaultPath = public_path('images/default-avatar.png');
            if (file_exists($defaultPath)) {
                return response()->file($defaultPath);
            }
            
            // Absolute fallback: 1x1 transparent GIF
            return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'), 200)
                ->header('Content-Type', 'image/gif');
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_num' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'current_year_level' => 'nullable|string|in:1st,2nd,3rd,4th,5th',
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'contact_num' => $validated['contact_num'],
            'email' => $validated['email'],
        ]);

        // Update or create basic_info record for current_year_level
        $basicInfo = $user->basicInfo;
        if ($basicInfo) {
            $basicInfo->update([
                'current_year_level' => $validated['current_year_level'] ?? null,
            ]);
        } else {
            // Create basic_info if it doesn't exist
            \App\Models\BasicInfo::create([
                'user_id' => $user->id,
                'current_year_level' => $validated['current_year_level'] ?? null,
            ]);
        }

        // Notify the student
        $user->notify(new \App\Notifications\TransactionNotification(
            'profile_update',
            'Profile Information Updated',
            'Your personal information has been successfully updated in your profile.',
            'normal'
        ));

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updateGWA(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'gwa' => 'required|numeric|min:75|max:100',
        ], [
            'gwa.required' => 'GWA value is required.',
            'gwa.numeric' => 'GWA must be a number.',
            'gwa.min' => 'GWA must be at least 75.',
            'gwa.max' => 'GWA cannot exceed 100.',
        ]);

        // Get or create basic_info record
        $basicInfo = $user->basicInfo;

        if (! $basicInfo) {
            // Create basic_info if it doesn't exist
            $basicInfo = BasicInfo::create([
                'user_id' => $user->id,
            ]);
        }

        // Update GWA in basic_info table
        $basicInfo->gpa = $validated['gwa'];
        $basicInfo->save();

        // Notify the student
        $user->notify(new \App\Notifications\TransactionNotification(
            'update',
            'GWA Updated',
            'Your academic GWA has been successfully updated to '.$validated['gwa'].'.',
            'normal'
        ));

        return response()->json([
            'success' => true,
            'message' => 'GWA updated successfully.',
            'gwa' => $validated['gwa'],
        ]);
    }

    public function typeOfAssistance()
    {
        $types = \App\Models\TypeAssist::all();

        return view('student.type_of_assistance', compact('types'));
    }

    public function showApplicationForm(Request $request)
    {
        $ethnicities = \App\Models\Ethno::all();
        $userId = auth()->id();
        $family_father = \App\Models\Family::where('fam_type', 'father')->where('user_id', $userId)->first();
        $family_mother = \App\Models\Family::where('fam_type', 'mother')->where('user_id', $userId)->first();
        $siblings = \App\Models\FamSiblings::all();
        $school_pref = \App\Models\SchoolPref::latest()->first();

        // ...fetch other data as needed
        return view('student.apply', compact('ethnicities', 'family_father', 'family_mother', 'siblings', 'school_pref'));
    }
}
