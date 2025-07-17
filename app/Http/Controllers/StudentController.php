<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        // In a real app, fetch the student's application status from DB
        $application = null; // Replace with actual application model if exists
        return view('student.dashboard', compact('application'));
    }

    public function apply(Request $request)
    {
        $user = auth()->user();

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
            'address' => $request->school1_address,
            'degree' => $request->school1_course1,
            'school_type' => $request->school1_type,
            'num_years' => $request->school1_years,
            'address2' => $request->school2_address,
            'degree2' => $request->school2_course1,
            'school_type2' => $request->school2_type,
            'num_years2' => $request->school2_years,
            'ques_answer1' => $request->contribution,
            'ques_answer2' => $request->plans_after_grad,
        ]);

        // 6. Save Basic Info (linking all the above)
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

        $request->session()->flash('status', 'Your IP Scholarship application has been submitted!');
        return redirect()->route('student.dashboard');
    }

    public function profile(Request $request)
    {
        // In a real app, fetch the student's profile data from DB
        $student = Auth::user(); // Get the authenticated user
        return view('student.profile', compact('student'));
    }

    public function performance(Request $request)
    {
        // In a real app, fetch the student's performance data from DB
        $student = Auth::user(); // Get the authenticated user
        return view('student.performance', compact('student'));
    }

    public function notifications(Request $request)
    {
        // In a real app, fetch the student's notifications from DB
        $student = Auth::user(); // Get the authenticated user
        
        // Mock notification data - in real app, fetch from database
        $notifications = [
            [
                'id' => 1,
                'type' => 'application_status',
                'title' => 'Application Status Updated',
                'message' => 'Your scholarship application has been reviewed and is currently under evaluation.',
                'is_read' => false,
                'created_at' => now()->subHours(2),
                'priority' => 'high'
            ],
            [
                'id' => 2,
                'type' => 'requirement_reminder',
                'title' => 'Document Required',
                'message' => 'Please submit your Certificate of Low Income within 7 days to complete your application.',
                'is_read' => false,
                'created_at' => now()->subDays(1),
                'priority' => 'urgent'
            ],
            [
                'id' => 3,
                'type' => 'general',
                'title' => 'Welcome to IP Scholar Portal',
                'message' => 'Thank you for joining the IP Scholar Portal. We\'re here to support your academic journey!',
                'is_read' => true,
                'created_at' => now()->subDays(3),
                'priority' => 'normal'
            ],
            [
                'id' => 4,
                'type' => 'deadline',
                'title' => 'Application Deadline Reminder',
                'message' => 'The scholarship application deadline is approaching. Please ensure all requirements are submitted.',
                'is_read' => false,
                'created_at' => now()->subDays(2),
                'priority' => 'high'
            ],
            [
                'id' => 5,
                'type' => 'system',
                'title' => 'System Maintenance Notice',
                'message' => 'The portal will be under maintenance on Saturday from 2:00 AM to 6:00 AM.',
                'is_read' => true,
                'created_at' => now()->subDays(4),
                'priority' => 'normal'
            ]
        ];
        
        return view('student.notifications', compact('student', 'notifications'));
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