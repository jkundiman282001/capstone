<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\BasicInfo;
use App\Models\Document;
use App\Models\ApplicationDraft;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        // In a real app, fetch the student's application status from DB
        $application = null; // Replace with actual application model if exists
        $hasApplied = false;
        $applicationStatus = 'pending';
        $rejectionReason = null;
        
        // Check if user is authenticated
        if ($request->user()) {
            $hasApplied = BasicInfo::where('user_id', $request->user()->id)->exists();
            
            // Get application status and rejection reason if exists
            if ($hasApplied) {
                $basicInfo = BasicInfo::where('user_id', $request->user()->id)->first();
                $applicationStatus = $basicInfo->application_status ?? 'pending';
                $rejectionReason = $basicInfo->application_rejection_reason ?? null;
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
            ->where(function($query) {
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

        return view('student.dashboard', compact('application', 'hasApplied', 'applicationStatus', 'rejectionReason', 'announcements', 'stats'));
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
        if ($hasSubmitted && !$isRenewal) {
            return redirect()->route('student.apply')
                ->with('error', 'You have already submitted an application. You cannot submit another application.');
        }
        
        // For renewals, check if user is eligible (validated grantee)
        if ($isRenewal) {
            $existingApplication = BasicInfo::where('user_id', $user->id)
                ->whereNotNull('type_assist')
                ->first();
            
            if (!$existingApplication || 
                $existingApplication->application_status !== 'validated' || 
                strtolower(trim($existingApplication->grant_status ?? '')) !== 'grantee') {
                return redirect()->route('student.apply')
                    ->with('error', 'You are not eligible for scholarship renewal. Only validated grantees can renew their scholarship.');
            }
        }

        $request->validate([
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
        ]);

        // For renewals, only process document uploads and skip form data
        if ($isRenewal) {
            // Handle document uploads for renewal
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    if (!$file) {
                        continue;
                    }

                    $path = $file->store('documents', 'public');

                    // Check if document already exists for this user and type
                    $existingDocument = Document::where('user_id', $user->id)
                        ->where('type', $type)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($existingDocument) {
                        // Update existing document
                        if (Storage::disk('public')->exists($existingDocument->filepath)) {
                            Storage::disk('public')->delete($existingDocument->filepath);
                        }
                        
                        $existingDocument->filename = $file->getClientOriginalName();
                        $existingDocument->filepath = $path;
                        $existingDocument->filetype = $file->getClientMimeType();
                        $existingDocument->filesize = $file->getSize();
                        $existingDocument->status = 'pending';
                        $existingDocument->rejection_reason = null;
                        $existingDocument->priority_rank = null;
                        $existingDocument->priority_score = 0;
                        $existingDocument->submitted_at = now();
                        $existingDocument->save();
                        
                        $document = $existingDocument;
                    } else {
                        // Create new document
                        $document = new Document();
                        $document->user_id = $user->id;
                        $document->filename = $file->getClientOriginalName();
                        $document->filepath = $path;
                        $document->filetype = $file->getClientMimeType();
                        $document->filesize = $file->getSize();
                        $document->description = null;
                        $document->status = 'pending';
                        $document->type = $type;
                        $document->save();
                    }

                    $priorityService = new \App\Services\DocumentPriorityService();
                    $priorityService->onDocumentUploaded($document);

                    foreach (\App\Models\Staff::all() as $staff) {
                        $staff->notify(new \App\Notifications\StudentUploadedDocument($user, $type));
                    }
                }
            }

            // Notify all staff about renewal submission
            foreach (\App\Models\Staff::all() as $staff) {
                $staff->notify(new \App\Notifications\StudentSubmittedApplication($user));
            }

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
        $schoolPref = \App\Models\SchoolPref::create([
            'school_name' => $request->school1_name,
            'address' => $request->school1_address,
            'degree' => $request->school1_course1,
            'alt_degree' => $request->school1_course_alt,
            'school_type' => $request->school1_type,
            'num_years' => $request->school1_years,
            'school_name2' => $request->school2_name,
            'address2' => $request->school2_address,
            'degree2' => $request->school2_course1,
            'alt_degree2' => $request->school2_course_alt,
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

        $basicInfo = \App\Models\BasicInfo::create([
            'user_id' => $user->id,
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
        ]);

        // 2. Save Education (each level as a separate row, linked to BasicInfo)
        $educationLevels = [
            1 => ['school' => $request->elem_school, 'type' => $request->elem_type, 'year' => $request->elem_year, 'avg' => $request->elem_avg, 'rank' => $request->elem_rank],
            2 => ['school' => $request->hs_school, 'type' => $request->hs_type, 'year' => $request->hs_year, 'avg' => $request->hs_avg, 'rank' => $request->hs_rank],
            3 => ['school' => $request->voc_school, 'type' => $request->voc_type, 'year' => $request->voc_year, 'avg' => $request->voc_avg, 'rank' => $request->voc_rank],
            4 => ['school' => $request->college_school, 'type' => $request->college_type, 'year' => $request->college_year, 'avg' => $request->college_avg, 'rank' => $request->college_rank],
            5 => ['school' => $request->masteral_school, 'type' => $request->masteral_type, 'year' => $request->masteral_year, 'avg' => $request->masteral_avg, 'rank' => $request->masteral_rank],
            6 => ['school' => $request->doctorate_school, 'type' => $request->doctorate_type, 'year' => $request->doctorate_year, 'avg' => $request->doctorate_avg, 'rank' => $request->doctorate_rank],
        ];

        $educationIds = [];
        foreach ($educationLevels as $category => $data) {
            if (!empty($data['school'])) { // Only save if school name is provided
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
        $father = \App\Models\Family::create([
            'basic_info_id' => $basicInfo->id,
            'name' => $request->father_name,
            'address' => $request->father_address,
            'occupation' => $request->father_occupation,
            'office_address' => $request->father_office_address,
            'educational_attainment' => $request->father_education,
            'ethno_id' => $request->father_ethno,
            'income' => $request->father_income,
            'status' => $request->father_status,
            'fam_type' => 'father',
        ]);
        $mother = \App\Models\Family::create([
            'basic_info_id' => $basicInfo->id,
            'name' => $request->mother_name,
            'address' => $request->mother_address,
            'occupation' => $request->mother_occupation,
            'office_address' => $request->mother_office_address,
            'educational_attainment' => $request->mother_education,
            'ethno_id' => $request->mother_ethno,
            'income' => $request->mother_income,
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
                if (!$file) {
                    continue;
                }

                $path = $file->store('documents', 'public');

                $document = new Document();
                $document->user_id = $user->id;
                $document->filename = $file->getClientOriginalName();
                $document->filepath = $path;
                $document->filetype = $file->getClientMimeType();
                $document->filesize = $file->getSize();
                $document->description = null;
                $document->status = 'pending';
                $document->type = $type;
                $document->save();

                $priorityService = new \App\Services\DocumentPriorityService();
                $priorityService->onDocumentUploaded($document);

                foreach (\App\Models\Staff::all() as $staff) {
                    $staff->notify(new \App\Notifications\StudentUploadedDocument($user, $type));
                }
            }
        }

        // Notify all staff
        foreach (\App\Models\Staff::all() as $staff) {
            $staff->notify(new \App\Notifications\StudentSubmittedApplication($user));
        }

        return redirect()->route('student.dashboard');
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
            if (!$name && is_array($formData)) {
                $firstName = $formData['first_name'] ?? '';
                $lastName = $formData['last_name'] ?? '';
                $name = trim($firstName . ' ' . $lastName);
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

                if (!$draft) {
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
                'message' => 'An error occurred while saving the draft: ' . $e->getMessage(),
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

            if (!$draft) {
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
                'message' => 'An error occurred while loading the draft: ' . $e->getMessage(),
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

        // Get GPA from basic_info table (entered by admin)
        $currentGPA = $basicInfo ? ($basicInfo->gpa ?? null) : null;

        return view('student.profile', compact('student', 'applicationStatus', 'rejectionReason', 'currentGPA'));
    }

    public function performance(Request $request)
    {
        $student = Auth::user(); // Get the authenticated user
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
            'basicInfo.schoolPref'
        ])
        ->whereHas('basicInfo', function($query) {
            $query->where('application_status', 'validated')
                  ->where(function($q) {
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
        $isStudentInMasterlist = $isStudentValidated && !$isStudentGrantee;
        
        // Initialize student priority score
        $studentPriorityScore = null;
        
        // Check if student has documents uploaded (indicator they've applied)
        $hasDocuments = $student->documents()->exists();
        
        // Calculate priority factors for display (if student has applied OR is validated OR has documents)
        // Also check if basicInfo exists OR student has documents
        if (($basicInfo && ($basicInfo->type_assist || $isStudentValidated)) || $hasDocuments) {
            $student->load(['ethno', 'documents', 'basicInfo.schoolPref', 'basicInfo.education']);
            
            // Get priority factors
            $priorityService = new \App\Services\ApplicantPriorityService();
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
            'applicants' => $allValidatedApplicants->map(function($app) {
                return [
                    'user_id' => $app->user_id,
                    'name' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'N/A',
                    'grant_status' => $app->grant_status,
                    'grant_status_lower' => $app->grant_status ? strtolower(trim($app->grant_status)) : 'NULL'
                ];
            })->toArray()
        ]);
        
        // Count applicants waiting (not grantees)
        // Method: Count all validated, then subtract grantees (more reliable)
        $allValidatedCount = BasicInfo::where('application_status', 'validated')->count();
        
        // Try multiple ways to count grantees to debug
        $granteesCountMethod1 = BasicInfo::where('application_status', 'validated')
            ->where(function($q) {
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
            'all_validated_applicants' => $allValidatedApplicants->map(function($app) {
                return [
                    'user_id' => $app->user_id,
                    'grant_status' => $app->grant_status,
                    'grant_status_lower' => $app->grant_status ? strtolower(trim($app->grant_status)) : 'NULL'
                ];
            })->toArray()
        ]);
        
        // Get the actual applicants waiting for debugging
        // Simple approach: get all validated, exclude grantees using whereNotIn
        $applicantsWaiting = BasicInfo::where('application_status', 'validated')
            ->where(function($q) {
                // Include NULL or values not in ['grantee', 'Grantee']
                $q->whereNull('grant_status')
                  ->orWhereNotIn('grant_status', ['grantee', 'Grantee']);
            })
            ->with('user:id,first_name,last_name')
            ->get(['id', 'user_id', 'application_status', 'grant_status']);
        
        \Log::info('Applicants waiting for approval (not grantees):', [
            'total' => $totalApplicantsWaiting,
            'applicants' => $applicantsWaiting->map(function($app) {
                return [
                    'user_id' => $app->user_id,
                    'name' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'N/A',
                    'grant_status' => $app->grant_status,
                ];
            })->toArray()
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
            'applicants_waiting_debug' => $applicantsWaiting->map(function($app) {
                return [
                    'user_id' => $app->user_id,
                    'name' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'N/A',
                    'grant_status' => $app->grant_status ?? 'NULL',
                ];
            })->toArray(),
            // Debug data: all validated applicants
            'all_validated_debug' => $allValidatedApplicants->map(function($app) {
                return [
                    'user_id' => $app->user_id,
                    'name' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'N/A',
                    'grant_status' => $app->grant_status ?? 'NULL',
                ];
            })->toArray(),
        ];
        
        // Calculate rank if student is in masterlist
        if ($isStudentInMasterlist) {
            $priorityService = new \App\Services\ApplicantPriorityService();
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
        $requiredTypes = [
            'birth_certificate' => 'Original or Certified True Copy of Birth Certificate',
            'income_document' => 'Income Tax Return of the parents/guardians or Certificate of Tax Exemption from BIR or Certificate of Indigency signed by the barangay captain',
            'tribal_certificate' => 'Certificate of Tribal Membership/Certificate of Confirmation COC',
            'endorsement' => 'Endorsement of the IPS/IP Traditional Leaders',
            'good_moral' => 'Certificate of Good Moral from the Guidance Counselor',
            'grades' => 'Incoming First Year College (Senior High School Grades), Ongoing college students latest copy of grades',
        ];
        
        return view('student.performance', compact(
            'student', 
            'basicInfo', 
            'priorityRank', 
            'priorityFactors',
            'priorityStatistics',
            'documents',
            'requiredTypes'
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
        if (!$ethnicity) return false;
        
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
        
        if (!$courseName) return false;
        
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
     * @param float $studentScore The applicant's priority score
     * @param array $allScores Array of all applicants' priority scores
     * @param int $slotsLeft Number of slots remaining
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
            
            if ($notification && !$notification->read_at) {
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
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        // Delete old profile picture if exists
        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        // Store new profile picture
        $path = $request->file('profile_pic')->store('profile-pics', 'public');
        
        // Update user profile
        $user->update(['profile_pic' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Profile picture updated successfully',
            'profile_pic_url' => Storage::url($path)
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_num' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
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

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updateGPA(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'gpa' => 'required|numeric|min:1.0|max:5.0'
        ], [
            'gpa.required' => 'GPA value is required.',
            'gpa.numeric' => 'GPA must be a number.',
            'gpa.min' => 'GPA must be at least 1.0.',
            'gpa.max' => 'GPA cannot exceed 5.0.',
        ]);
        
        // Get or create basic_info record
        $basicInfo = $user->basicInfo;
        
        if (!$basicInfo) {
            // Create basic_info if it doesn't exist
            $basicInfo = BasicInfo::create([
                'user_id' => $user->id,
            ]);
        }
        
        // Update GPA in basic_info table
        $basicInfo->gpa = $validated['gpa'];
        $basicInfo->save();
        
        return response()->json([
            'success' => true,
            'message' => 'GPA updated successfully.',
            'gpa' => $validated['gpa'],
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