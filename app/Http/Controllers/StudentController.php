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

        // 1. Save Address
        $address = \App\Models\Address::create([
            'barangay' => $request->mailing_barangay,
            'municipality' => $request->mailing_municipality,
            'province' => $request->mailing_province,
        ]);

        // 2. Save Education (all levels as JSON fields)
        $education = \App\Models\Education::create([
            'category' => 'All',
            'school_name' => json_encode([
                'elem' => $request->elem_school,
                'hs' => $request->hs_school,
                'voc' => $request->voc_school,
                'college' => $request->college_school,
                'masteral' => $request->masteral_school,
                'doctorate' => $request->doctorate_school,
            ]),
            'school_type' => json_encode([
                'elem' => $request->elem_type,
                'hs' => $request->hs_type,
                'voc' => $request->voc_type,
                'college' => $request->college_type,
                'masteral' => $request->masteral_type,
                'doctorate' => $request->doctorate_type,
            ]),
            'year_grad' => json_encode([
                'elem' => $request->elem_year,
                'hs' => $request->hs_year,
                'voc' => $request->voc_year,
                'college' => $request->college_year,
                'masteral' => $request->masteral_year,
                'doctorate' => $request->doctorate_year,
            ]),
            'grade_ave' => json_encode([
                'elem' => $request->elem_avg,
                'hs' => $request->hs_avg,
                'voc' => $request->voc_avg,
                'college' => $request->college_avg,
                'masteral' => $request->masteral_avg,
                'doctorate' => $request->doctorate_avg,
            ]),
            'rank' => json_encode([
                'elem' => $request->elem_rank,
                'hs' => $request->hs_rank,
                'voc' => $request->voc_rank,
                'college' => $request->college_rank,
                'masteral' => $request->masteral_rank,
                'doctorate' => $request->doctorate_rank,
            ]),
        ]);

        // 3. Save Siblings
        if ($request->sibling_name) {
            foreach ($request->sibling_name as $i => $name) {
                if ($name) {
                    \App\Models\FamSiblings::create([
                        'name' => $name,
                        'age' => $request->sibling_age[$i] ?? null,
                        'scholarship' => $request->sibling_scholarship[$i] ?? null,
                        'course_year' => $request->sibling_course[$i] ?? null,
                        'present_status' => $request->sibling_status[$i] ?? null,
                    ]);
                }
            }
        }

        // 4. Save Family (father and mother)
        $father = \App\Models\Family::create([
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
        \App\Models\BasicInfo::create([
            'user_id' => $user->id,
            'house_num' => $request->mailing_house_num,
            'birthdate' => $request->birthdate,
            'birthplace' => $request->birthplace,
            'gender' => $request->gender,
            'civil_status' => $request->civil_status,
            'ethno_id' => $request->ethno_id,
            'address_id' => $address->id,
            'education_id' => $education->id,
            'family_id' => $father->id, // or store both father and mother IDs as needed
            'school_pref_id' => $schoolPref->id,
        ]);

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