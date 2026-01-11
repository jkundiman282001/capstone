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
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact_num' => 'required|string|max:20',
            'ethno_id' => 'required|exists:ethno,id',
            'course' => 'required|string|max:255',
            'gender' => 'required|string',
            'birthdate' => 'required|date',
            'birthplace' => 'required|string|max:255',
            'civil_status' => 'required|string',
            'province' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'house_num' => 'nullable|string|max:255',
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
                'course' => $validated['course'],
                'password' => Hash::make('password123'), // Default password
                'email_verified_at' => now(),
            ]);

            // 2. Create Address
            $address = Address::firstOrCreate([
                'barangay' => $validated['barangay'],
                'municipality' => $validated['municipality'],
                'province' => $validated['province'],
            ]);

            // 3. Create Address Links (Mailing, Permanent, Origin)
            $mailingId = DB::table('mailing_address')->insertGetId([
                'address_id' => $address->id,
                'house_num' => $validated['house_num'] ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $permanentId = DB::table('permanent_address')->insertGetId([
                'address_id' => $address->id,
                'house_num' => $validated['house_num'] ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $originId = DB::table('origin')->insertGetId([
                'address_id' => $address->id,
                'house_num' => $validated['house_num'] ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Create Full Address
            $fullAddress = FullAddress::create([
                'mailing_address_id' => $mailingId,
                'permanent_address_id' => $permanentId,
                'origin_id' => $originId,
            ]);

            // 5. Create School Preference (Empty default)
            $schoolPref = SchoolPref::create([
                'school1_name' => 'N/A',
                'school1_course' => $validated['course'],
            ]);

            // 6. Create Basic Info
            BasicInfo::create([
                'user_id' => $user->id,
                'full_address_id' => $fullAddress->id,
                'school_pref_id' => $schoolPref->id,
                'house_num' => $validated['house_num'] ?? '',
                'birthdate' => $validated['birthdate'],
                'birthplace' => $validated['birthplace'],
                'gender' => $validated['gender'],
                'civil_status' => $validated['civil_status'],
                'type_assist' => 'Regular', // Default
                'assistance_for' => 'Tuition', // Default
                'application_status' => 'pending', // Default
            ]);

            // 7. Create Family Records (Defaults)
            Family::create([
                'basic_info_id' => $user->basicInfo->id,
                'fam_type' => 'father',
                'name' => 'N/A',
                'status' => 'Living',
            ]);

            Family::create([
                'basic_info_id' => $user->basicInfo->id,
                'fam_type' => 'mother',
                'name' => 'N/A',
                'status' => 'Living',
            ]);

            DB::commit();

            return redirect()->route('staff.settings')->with('success', 'Applicant encoded successfully! Default password is "password123".');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to encode applicant: ' . $e->getMessage()])->withInput();
        }
    }
}
