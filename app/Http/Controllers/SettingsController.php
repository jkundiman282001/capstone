<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Ethno;
use App\Models\Address;
use App\Models\FullAddress;
use App\Models\SchoolPref;
use App\Models\BasicInfo;
use App\Models\Family;
use App\Models\Education;
use App\Models\FamSiblings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $maxSlots = \App\Models\Setting::get('max_slots', 120);

        // Get all student users for the deletion management
        $applicants = User::orderBy('last_name')
            ->get();

        // Data for manual encoding form
        $ethnicities = \App\Models\Ethno::all();
        $barangays = \App\Models\Address::query()->select('barangay')->distinct()->where('barangay', '!=', '')->orderBy('barangay')->pluck('barangay');
        $municipalities = \App\Models\Address::query()->select('municipality')->distinct()->where('municipality', '!=', '')->orderBy('municipality')->pluck('municipality');
        $provinces = \App\Models\Address::query()->select('province')->distinct()->where('province', '!=', '')->orderBy('province')->pluck('province');

        return view('staff.settings', compact('maxSlots', 'applicants', 'ethnicities', 'barangays', 'municipalities', 'provinces'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'max_slots' => ['required', 'integer', 'min:1'],
        ]);

        Setting::set('max_slots', $validated['max_slots']);

        return redirect()->route('staff.settings')->with('success', 'Settings updated successfully!');
    }

    public function storeApplicant(Request $request)
    {
        $validated = $request->validate([
            // Personal Info
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact_num' => 'required|string|max:20',
            'ethno_id' => 'required|exists:ethno,id',
            'gender' => 'required|string',
            'birthdate' => 'required|date',
            'birthplace' => 'required|string|max:255',
            'civil_status' => 'required|string',

            // Address - Mailing
            'mailing_municipality' => 'required|string',
            'mailing_barangay' => 'required|string',
            'mailing_house_num' => 'nullable|string',
            // Address - Permanent
            'permanent_municipality' => 'required|string',
            'permanent_barangay' => 'required|string',
            'permanent_house_num' => 'nullable|string',
            // Address - Origin
            'origin_municipality' => 'required|string',
            'origin_barangay' => 'required|string',
            'origin_house_num' => 'nullable|string',

            // Education
            'elem_school' => 'required|string',
            'elem_type' => 'required|string',
            'elem_year' => 'required|string',
            'elem_avg' => 'required|string',
            'elem_rank' => 'nullable|string',
            'hs_school' => 'required|string',
            'hs_type' => 'required|string',
            'hs_year' => 'required|string',
            'hs_avg' => 'required|string',
            'hs_rank' => 'nullable|string',
            'voc_school' => 'nullable|string',
            'voc_type' => 'nullable|string',
            'voc_year' => 'nullable|string',
            'voc_avg' => 'nullable|string',
            'voc_rank' => 'nullable|string',
            'college_school' => 'nullable|string',
            'college_type' => 'nullable|string',
            'college_year' => 'nullable|string',
            'college_avg' => 'nullable|string',
            'college_rank' => 'nullable|string',

            // Parents
            'father_status' => 'required|string',
            'father_name' => 'required|string',
            'father_address' => 'nullable|string',
            'father_occupation' => 'nullable|string',
            'father_education' => 'nullable|string',
            'father_office_address' => 'nullable|string',
            'father_income' => 'nullable|string',
            'father_ethno' => 'nullable|exists:ethno,id',
            'mother_status' => 'required|string',
            'mother_name' => 'required|string',
            'mother_address' => 'nullable|string',
            'mother_occupation' => 'nullable|string',
            'mother_education' => 'nullable|string',
            'mother_office_address' => 'nullable|string',
            'mother_income' => 'nullable|string',
            'mother_ethno' => 'nullable|exists:ethno,id',

            // School Preference
            'school1_name' => 'required|string',
            'school1_address' => 'required|string',
            'school1_course1' => 'required|string',
            'school1_course1_other' => 'nullable|string',
            'school1_course_alt' => 'nullable|string',
            'school1_course_alt_other' => 'nullable|string',
            'school1_type' => 'required|string',
            'school1_years' => 'required|string',
            'school2_name' => 'required|string',
            'school2_address' => 'required|string',
            'school2_course1' => 'required|string',
            'school2_course1_other' => 'nullable|string',
            'school2_course_alt' => 'nullable|string',
            'school2_course_alt_other' => 'nullable|string',
            'school2_type' => 'required|string',
            'school2_years' => 'required|string',

            // Essay
            'contribution' => 'required|string',
            'plans_after_grad' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create User
            $user = User::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'contact_num' => $validated['contact_num'],
                'ethno_id' => $validated['ethno_id'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // 2. Create Addresses and Link them
            $mailingAddr = Address::firstOrCreate([
                'barangay' => $validated['mailing_barangay'],
                'municipality' => $validated['mailing_municipality'],
                'province' => 'Davao del Sur',
            ]);
            $mailingId = DB::table('mailing_address')->insertGetId([
                'address_id' => $mailingAddr->id,
                'house_num' => $validated['mailing_house_num'] ?? '',
                'created_at' => now(), 'updated_at' => now(),
            ]);

            $permanentAddr = Address::firstOrCreate([
                'barangay' => $validated['permanent_barangay'],
                'municipality' => $validated['permanent_municipality'],
                'province' => 'Davao del Sur',
            ]);
            $permanentId = DB::table('permanent_address')->insertGetId([
                'address_id' => $permanentAddr->id,
                'house_num' => $validated['permanent_house_num'] ?? '',
                'created_at' => now(), 'updated_at' => now(),
            ]);

            $originAddr = Address::firstOrCreate([
                'barangay' => $validated['origin_barangay'],
                'municipality' => $validated['origin_municipality'],
                'province' => 'Davao del Sur',
            ]);
            $originId = DB::table('origin')->insertGetId([
                'address_id' => $originAddr->id,
                'house_num' => $validated['origin_house_num'] ?? '',
                'created_at' => now(), 'updated_at' => now(),
            ]);

            $fullAddress = FullAddress::create([
                'mailing_address_id' => $mailingId,
                'permanent_address_id' => $permanentId,
                'origin_id' => $originId,
            ]);

            // 3. Create School Preference
            $school1_course = $validated['school1_course1'] === 'Other' ? $validated['school1_course1_other'] : $validated['school1_course1'];
            $school1_alt = $validated['school1_course_alt'] === 'Other' ? $validated['school1_course_alt_other'] : $validated['school1_course_alt'];
            $school2_course = $validated['school2_course1'] === 'Other' ? $validated['school2_course1_other'] : $validated['school2_course1'];
            $school2_alt = $validated['school2_course_alt'] === 'Other' ? $validated['school2_course_alt_other'] : $validated['school2_course_alt'];

            $schoolPref = SchoolPref::create([
                'school_name' => $validated['school1_name'],
                'address' => $validated['school1_address'],
                'degree' => $school1_course,
                'alt_degree' => $school1_alt,
                'school_type' => $validated['school1_type'],
                'num_years' => $validated['school1_years'],
                'school_name2' => $validated['school2_name'],
                'address2' => $validated['school2_address'],
                'degree2' => $school2_course,
                'alt_degree2' => $school2_alt,
                'school_type2' => $validated['school2_type'],
                'num_years2' => $validated['school2_years'],
                'ques_answer1' => $validated['contribution'],
                'ques_answer2' => $validated['plans_after_grad'],
            ]);

            // 4. Create Basic Info
            $basicInfo = BasicInfo::create([
                'user_id' => $user->id,
                'full_address_id' => $fullAddress->id,
                'school_pref_id' => $schoolPref->id,
                'house_num' => $validated['mailing_house_num'] ?? '',
                'birthdate' => $validated['birthdate'],
                'birthplace' => $validated['birthplace'],
                'gender' => $validated['gender'],
                'civil_status' => $validated['civil_status'],
                'type_assist' => 'Regular',
                'assistance_for' => 'Tuition',
                'application_status' => 'pending',
            ]);

            // 5. Create Education Records
            $levels = [
                ['key' => 'elem', 'cat' => 1],
                ['key' => 'hs', 'cat' => 2],
                ['key' => 'voc', 'cat' => 3],
                ['key' => 'college', 'cat' => 4],
            ];

            foreach ($levels as $level) {
                if (!empty($validated[$level['key'] . '_school'])) {
                    Education::create([
                        'basic_info_id' => $basicInfo->id,
                        'category' => $level['cat'],
                        'school_name' => $validated[$level['key'] . '_school'],
                        'school_type' => $validated[$level['key'] . '_type'],
                        'year_grad' => $validated[$level['key'] . '_year'],
                        'grade_ave' => $validated[$level['key'] . '_avg'],
                        'rank' => $validated[$level['key'] . '_rank'],
                    ]);
                }
            }

            // 6. Create Family Records
            Family::create([
                'basic_info_id' => $basicInfo->id,
                'fam_type' => 'father',
                'name' => $validated['father_name'],
                'status' => $validated['father_status'],
                'address' => $validated['father_address'],
                'occupation' => $validated['father_occupation'],
                'educational_attainment' => $validated['father_education'],
                'office_address' => $validated['father_office_address'],
                'income' => $validated['father_income'],
                'ethno_id' => $validated['father_ethno'],
            ]);

            Family::create([
                'basic_info_id' => $basicInfo->id,
                'fam_type' => 'mother',
                'name' => $validated['mother_name'],
                'status' => $validated['mother_status'],
                'address' => $validated['mother_address'],
                'occupation' => $validated['mother_occupation'],
                'educational_attainment' => $validated['mother_education'],
                'office_address' => $validated['mother_office_address'],
                'income' => $validated['mother_income'],
                'ethno_id' => $validated['mother_ethno'],
            ]);

            DB::commit();

            return redirect()->route('staff.settings')->with('success', 'Applicant encoded successfully! Default password is "password123".');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to encode applicant: ' . $e->getMessage()])->withInput();
        }
    }
}
