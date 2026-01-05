<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\BasicInfo;
use App\Models\Ethno;
use App\Models\Family;
use App\Models\FullAddress;
use App\Models\Origin;
use App\Models\SchoolPref;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first ethnicity if exists, or create one
        $ethno = Ethno::first();
        if (! $ethno) {
            $ethno = Ethno::create(['ethnicity' => 'Igorot']);
        }

        $applicants = [
            [
                'user' => [
                    'first_name' => 'Maria',
                    'middle_name' => 'Santos',
                    'last_name' => 'Dela Cruz',
                    'email' => 'maria.delacruz@example.com',
                    'password' => Hash::make('password123'),
                    'contact_num' => '09123456789',
                    'ethno_id' => $ethno->id,
                    'course' => 'BS Nursing',
                    'email_verified_at' => now(),
                ],
                'basic_info' => [
                    'house_num' => '123',
                    'birthdate' => '2000-05-15',
                    'birthplace' => 'Baguio City',
                    'gender' => 'Female',
                    'civil_status' => 'Single',
                    'type_assist' => 'Regular',
                    'assistance_for' => 'Tuition,Books',
                    'application_status' => 'validated',
                ],
                'address' => [
                    'barangay' => 'Camp 7',
                    'municipality' => 'Baguio City',
                    'province' => 'Benguet',
                ],
                'school_pref' => [
                    'school_name' => 'University of the Cordilleras',
                    'address' => 'Baguio City',
                    'degree' => 'BS Nursing',
                    'school_type' => 'Private',
                    'num_years' => '4',
                    'ques_answer1' => 'I want to contribute to healthcare in indigenous communities.',
                    'ques_answer2' => 'After graduation, I plan to work in rural health centers serving indigenous communities.',
                ],
            ],
            [
                'user' => [
                    'first_name' => 'Juan',
                    'middle_name' => 'Reyes',
                    'last_name' => 'Garcia',
                    'email' => 'juan.garcia@example.com',
                    'password' => Hash::make('password123'),
                    'contact_num' => '09123456790',
                    'ethno_id' => $ethno->id,
                    'course' => 'BS Education',
                    'email_verified_at' => now(),
                ],
                'basic_info' => [
                    'house_num' => '456',
                    'birthdate' => '2001-08-20',
                    'birthplace' => 'La Trinidad',
                    'gender' => 'Male',
                    'civil_status' => 'Single',
                    'type_assist' => 'Regular',
                    'assistance_for' => 'Tuition',
                    'application_status' => 'validated',
                ],
                'address' => [
                    'barangay' => 'Pico',
                    'municipality' => 'La Trinidad',
                    'province' => 'Benguet',
                ],
                'school_pref' => [
                    'school_name' => 'Benguet State University',
                    'address' => 'La Trinidad, Benguet',
                    'degree' => 'BS Education',
                    'school_type' => 'State University',
                    'num_years' => '4',
                    'ques_answer1' => 'I want to teach and preserve indigenous culture through education.',
                    'ques_answer2' => 'I plan to become a teacher and return to my community to educate the youth.',
                ],
            ],
            [
                'user' => [
                    'first_name' => 'Ana',
                    'middle_name' => 'Lopez',
                    'last_name' => 'Fernandez',
                    'email' => 'ana.fernandez@example.com',
                    'password' => Hash::make('password123'),
                    'contact_num' => '09123456791',
                    'ethno_id' => $ethno->id,
                    'course' => 'BS Information Technology',
                    'email_verified_at' => now(),
                ],
                'basic_info' => [
                    'house_num' => '789',
                    'birthdate' => '2002-03-10',
                    'birthplace' => 'Itogon',
                    'gender' => 'Female',
                    'civil_status' => 'Single',
                    'type_assist' => 'Pamana',
                    'assistance_for' => 'Tuition,Books,Allowance',
                    'application_status' => 'validated',
                ],
                'address' => [
                    'barangay' => 'Poblacion',
                    'municipality' => 'Itogon',
                    'province' => 'Benguet',
                ],
                'school_pref' => [
                    'school_name' => 'Saint Louis University',
                    'address' => 'Baguio City',
                    'degree' => 'BS Information Technology',
                    'school_type' => 'Private',
                    'num_years' => '4',
                    'ques_answer1' => 'I want to use technology to help preserve and promote indigenous culture.',
                    'ques_answer2' => 'I plan to develop digital solutions for indigenous communities after graduation.',
                ],
            ],
            [
                'user' => [
                    'first_name' => 'Carlos',
                    'middle_name' => 'Villanueva',
                    'last_name' => 'Ramos',
                    'email' => 'carlos.ramos@example.com',
                    'password' => Hash::make('password123'),
                    'contact_num' => '09123456792',
                    'ethno_id' => $ethno->id,
                    'course' => 'BS Accountancy',
                    'email_verified_at' => now(),
                ],
                'basic_info' => [
                    'house_num' => '321',
                    'birthdate' => '1999-11-25',
                    'birthplace' => 'Baguio City',
                    'gender' => 'Male',
                    'civil_status' => 'Single',
                    'type_assist' => 'Regular',
                    'assistance_for' => 'Tuition,Books',
                    'application_status' => 'validated',
                ],
                'address' => [
                    'barangay' => 'Irisan',
                    'municipality' => 'Baguio City',
                    'province' => 'Benguet',
                ],
                'school_pref' => [
                    'school_name' => 'University of the Philippines Baguio',
                    'address' => 'Baguio City',
                    'degree' => 'BS Accountancy',
                    'school_type' => 'State University',
                    'num_years' => '4',
                    'ques_answer1' => 'I want to help indigenous communities with financial literacy and management.',
                    'ques_answer2' => 'I plan to work as an accountant and assist indigenous organizations with their finances.',
                ],
            ],
            [
                'user' => [
                    'first_name' => 'Rosa',
                    'middle_name' => 'Torres',
                    'last_name' => 'Mendoza',
                    'email' => 'rosa.mendoza@example.com',
                    'password' => Hash::make('password123'),
                    'contact_num' => '09123456793',
                    'ethno_id' => $ethno->id,
                    'course' => 'BS Computer Science',
                    'email_verified_at' => now(),
                ],
                'basic_info' => [
                    'house_num' => '654',
                    'birthdate' => '2001-07-12',
                    'birthplace' => 'Sablan',
                    'gender' => 'Female',
                    'civil_status' => 'Single',
                    'type_assist' => 'Regular',
                    'assistance_for' => 'Tuition',
                    'application_status' => 'validated',
                ],
                'address' => [
                    'barangay' => 'Bagong',
                    'municipality' => 'Sablan',
                    'province' => 'Benguet',
                ],
                'school_pref' => [
                    'school_name' => 'Cordillera Career Development College',
                    'address' => 'La Trinidad, Benguet',
                    'degree' => 'BS Computer Science',
                    'school_type' => 'Private',
                    'num_years' => '4',
                    'ques_answer1' => 'I want to use computer science skills to create solutions for indigenous communities.',
                    'ques_answer2' => 'I plan to develop software applications that help preserve indigenous languages and culture.',
                ],
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($applicants as $applicantData) {
            // Check if user already exists, if so skip
            $existingUser = User::where('email', $applicantData['user']['email'])->first();
            if ($existingUser) {
                $this->command->warn("User {$applicantData['user']['email']} already exists. Skipping...");
                $skipped++;

                continue;
            }

            // Create User
            $user = User::create($applicantData['user']);

            // Create Address (same for mailing, permanent, and origin)
            $address = Address::firstOrCreate([
                'barangay' => $applicantData['address']['barangay'],
                'municipality' => $applicantData['address']['municipality'],
                'province' => $applicantData['address']['province'],
            ]);

            // Create Mailing Address
            $mailingAddressId = DB::table('mailing_address')->insertGetId([
                'address_id' => $address->id,
                'house_num' => $applicantData['basic_info']['house_num'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Permanent Address
            $permanentAddressId = DB::table('permanent_address')->insertGetId([
                'address_id' => $address->id,
                'house_num' => $applicantData['basic_info']['house_num'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Origin Address
            $originId = DB::table('origin')->insertGetId([
                'address_id' => $address->id,
                'house_num' => $applicantData['basic_info']['house_num'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Full Address
            $fullAddress = FullAddress::create([
                'mailing_address_id' => $mailingAddressId,
                'permanent_address_id' => $permanentAddressId,
                'origin_id' => $originId,
            ]);

            // Create School Preference
            $schoolPref = SchoolPref::create($applicantData['school_pref']);

            // Create Basic Info
            $basicInfo = BasicInfo::create([
                'user_id' => $user->id,
                'full_address_id' => $fullAddress->id,
                'house_num' => $applicantData['basic_info']['house_num'],
                'birthdate' => $applicantData['basic_info']['birthdate'],
                'birthplace' => $applicantData['basic_info']['birthplace'],
                'gender' => $applicantData['basic_info']['gender'],
                'civil_status' => $applicantData['basic_info']['civil_status'],
                'school_pref_id' => $schoolPref->id,
                'type_assist' => $applicantData['basic_info']['type_assist'],
                'assistance_for' => $applicantData['basic_info']['assistance_for'],
                'application_status' => $applicantData['basic_info']['application_status'],
            ]);

            // Create Family records (Father and Mother)
            Family::create([
                'basic_info_id' => $basicInfo->id,
                'fam_type' => 'father',
                'name' => $user->last_name.', Father',
                'address' => $address->barangay.', '.$address->municipality,
                'occupation' => 'Farmer',
                'office_address' => $address->municipality.', '.$address->province,
                'educational_attainment' => 'High School',
                'ethno_id' => $ethno->id,
                'income' => '15000',
                'status' => 'Living',
            ]);

            Family::create([
                'basic_info_id' => $basicInfo->id,
                'fam_type' => 'mother',
                'name' => $user->last_name.', Mother',
                'address' => $address->barangay.', '.$address->municipality,
                'occupation' => 'Housewife',
                'office_address' => 'N/A',
                'educational_attainment' => 'Elementary',
                'ethno_id' => $ethno->id,
                'income' => '0',
                'status' => 'Living',
            ]);

            $created++;
        }

        $this->command->info("Seeder completed! Created: {$created}, Skipped: {$skipped}");
    }
}
