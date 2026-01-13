<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\BasicInfo;
use App\Models\Document;
use App\Models\Education;
use App\Models\Ethno;
use App\Models\Family;
use App\Models\FamSiblings;
use App\Models\FullAddress;
use App\Models\MailingAddress;
use App\Models\Origin;
use App\Models\PermanentAddress;
use App\Models\SchoolPref;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Services\DocumentPriorityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ManualApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_PH');
        $ethnos = Ethno::all();
        if ($ethnos->isEmpty()) {
            $ethnos = collect([Ethno::create(['ethnicity' => 'Tagalog']), Ethno::create(['ethnicity' => 'Ilocano']), Ethno::create(['ethnicity' => 'Bisaya'])]);
        }

        $documentTypes = [
            'birth_certificate' => 'Birth Certificate',
            'income_document' => 'Income Document',
            'tribal_certificate' => 'Tribal Certificate',
            'endorsement' => 'Endorsement',
            'good_moral' => 'Good Moral',
            'grades' => 'Grades'
        ];

        $attainmentOptions = [
            'None',
            'Elementary/Primary School',
            'High School (no diploma)',
            'High School Diploma or GED',
            'College, No Degree',
            'College Graduate',
            'Associate Degree',
            'Bachelor\'s Degree',
            'Master\'s Degree',
            'Professional Degree',
            'Doctorate Degree',
            'Trade/Technical/Vocational Training/Certificate'
        ];

        $incomeOptions = [
            'Below - ₱50,000',
            '₱50,001 – ₱100,000',
            '₱100,001 – ₱150,000',
            '₱150,001 – ₱200,000',
            '₱200,001 – ₱300,000',
            '₱300,001 – ₱400,000',
            '₱400,001 – ₱500,000',
            '₱500,001 - Above'
        ];

        // Fetch all existing addresses once
        $allAddresses = Address::all();
        if ($allAddresses->isEmpty()) {
            // Fallback if no addresses exist yet (though they should from migrations)
            $allAddresses = collect([
                Address::create(['barangay' => 'Aplaya', 'municipality' => 'Digos', 'province' => 'Davao del Sur']),
                Address::create(['barangay' => 'San Jose', 'municipality' => 'Digos', 'province' => 'Davao del Sur'])
            ]);
        }

        for ($i = 0; $i < 20; $i++) {
            try {
                DB::beginTransaction();

                // Determine status distribution
                $statusRoll = $faker->numberBetween(1, 100);
                $appStatus = 'pending';
                $grantStatus = null;

                if ($statusRoll <= 40) { // 40% Pending
                    $appStatus = 'pending';
                } elseif ($statusRoll <= 70) { // 30% Validated/Grantee
                    $appStatus = 'validated';
                    $grantStatus = $faker->randomElement([null, 'grantee', 'grantee']); // Bias toward grantee for validated
                } elseif ($statusRoll <= 90) { // 20% Rejected
                    $appStatus = 'rejected';
                    $grantStatus = null;
                } else { // 10% Terminated (Rejected + was Grantee)
                    $appStatus = 'rejected';
                    $grantStatus = 'grantee';
                }

                // 1. Create User
                $user = User::create([
                    'first_name' => $faker->firstName,
                    'middle_name' => $faker->lastName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'contact_num' => '09' . $faker->numerify('#########'),
                    'ethno_id' => $ethnos->random()->id,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'role' => 'student',
                ]);

                // 2. Use Existing Addresses
                $mailingAddr = $allAddresses->random();
                $mailing = MailingAddress::create([
                    'address_id' => $mailingAddr->id,
                    'house_num' => $faker->buildingNumber,
                ]);

                $permanentAddr = $allAddresses->random();
                $permanent = PermanentAddress::create([
                    'address_id' => $permanentAddr->id,
                    'house_num' => $faker->buildingNumber,
                ]);

                $originAddr = $allAddresses->random();
                $origin = Origin::create([
                    'address_id' => $originAddr->id,
                    'house_num' => $faker->buildingNumber,
                ]);

                $fullAddress = FullAddress::create([
                    'mailing_address_id' => $mailing->id,
                    'permanent_address_id' => $permanent->id,
                    'origin_id' => $origin->id,
                ]);

                // 3. Create School Preference
                $essay1 = $faker->randomElement([
                    "I want to help my community by providing education and health support. As a member of the tribe, I value our indigenous culture and ancestral lands. I will lead mentor programs for the youth.",
                    "My contribution to the IP community involves volunteer work in environment conservation. I will serve my people by teaching them about livelihood opportunities while preserving our ancestral heritage.",
                    "I plan to support the tribe through educational initiatives. I will return to my community to help my tribe develop sustainable livelihood projects for the youth and lead cultural awareness programs."
                ]);
                
                $essay2 = $faker->randomElement([
                    "After graduation, I will give back to my community. I plan to work as a teacher in our tribal school to support the education of indigenous children and contribute to my tribe's development.",
                    "I will return to my ancestral lands to serve as a health worker. My goal is to improve the health and well-being of our community members and mentor the next generation of leaders.",
                    "I intend to lead projects that focus on livelihood and environment. I will serve my people by applying my knowledge to help my community grow while respecting our indigenous culture."
                ]);

                $schoolPref = SchoolPref::create([
                    'school_name' => $faker->company . ' University',
                    'address' => $faker->address,
                    'degree' => $faker->jobTitle,
                    'alt_degree' => $faker->jobTitle,
                    'school_type' => $faker->randomElement(['Public', 'Private']),
                    'num_years' => $faker->randomElement(['4', '5']),
                    'school_name2' => $faker->company . ' College',
                    'address2' => $faker->address,
                    'degree2' => $faker->jobTitle,
                    'alt_degree2' => $faker->jobTitle,
                    'school_type2' => $faker->randomElement(['Public', 'Private']),
                    'num_years2' => $faker->randomElement(['4', '5']),
                    'ques_answer1' => $essay1,
                    'ques_answer2' => $essay2,
                ]);

                // 4. Create Basic Info
                $basicInfo = BasicInfo::create([
                    'user_id' => $user->id,
                    'full_address_id' => $fullAddress->id,
                    'school_pref_id' => $schoolPref->id,
                    'house_num' => $mailing->house_num,
                    'birthdate' => $faker->date('Y-m-d', '-18 years'),
                    'birthplace' => $faker->city,
                    'gender' => $faker->randomElement(['Male', 'Female']),
                    'civil_status' => $faker->randomElement(['Single', 'Married']),
                    'type_assist' => 'Regular',
                    'assistance_for' => 'College',
                    'application_status' => $appStatus,
                    'grant_status' => $grantStatus,
                ]);

                // 5. Create Education Records
                $levels = [
                    ['cat' => 1, 'school' => 'Elementary School'],
                    ['cat' => 2, 'school' => 'High School'],
                    ['cat' => 3, 'school' => 'Vocational School'],
                    ['cat' => 4, 'school' => 'College'],
                ];

                foreach ($levels as $level) {
                    Education::create([
                        'basic_info_id' => $basicInfo->id,
                        'category' => $level['cat'],
                        'school_name' => $faker->company . ' ' . $level['school'],
                        'school_type' => $faker->randomElement(['Public', 'Private']),
                        'year_grad' => $faker->year,
                        'grade_ave' => $faker->randomFloat(2, 85, 98),
                        'rank' => $faker->numberBetween(1, 50),
                    ]);
                }

                // 6. Create Family Records
                // Clean income values for seeder
                $fatherIncomeStr = $faker->randomElement($incomeOptions);
                $dash = strpos($fatherIncomeStr, '–') !== false ? '–' : (strpos($fatherIncomeStr, '-') !== false ? '-' : null);
                $fatherIncome = $fatherIncomeStr;
                if ($dash && strpos($fatherIncome, 'Below') === false) {
                    $parts = explode($dash, $fatherIncome);
                    $fatherIncome = $parts[0];
                }
                $fatherIncome = (int) preg_replace('/[^0-9]/', '', $fatherIncome);

                $motherIncomeStr = $faker->randomElement($incomeOptions);
                $dash = strpos($motherIncomeStr, '–') !== false ? '–' : (strpos($motherIncomeStr, '-') !== false ? '-' : null);
                $motherIncome = $motherIncomeStr;
                if ($dash && strpos($motherIncome, 'Below') === false) {
                    $parts = explode($dash, $motherIncome);
                    $motherIncome = $parts[0];
                }
                $motherIncome = (int) preg_replace('/[^0-9]/', '', $motherIncome);

                Family::create([
                    'basic_info_id' => $basicInfo->id,
                    'fam_type' => 'father',
                    'name' => $faker->name('male'),
                    'status' => 'Living',
                    'address' => $faker->address,
                    'occupation' => $faker->jobTitle,
                    'educational_attainment' => $faker->randomElement($attainmentOptions),
                    'office_address' => $faker->address,
                    'income' => $fatherIncome,
                    'ethno_id' => $ethnos->random()->id,
                ]);

                Family::create([
                    'basic_info_id' => $basicInfo->id,
                    'fam_type' => 'mother',
                    'name' => $faker->name('female'),
                    'status' => 'Living',
                    'address' => $faker->address,
                    'occupation' => $faker->jobTitle,
                    'educational_attainment' => $faker->randomElement($attainmentOptions),
                    'office_address' => $faker->address,
                    'income' => $motherIncome,
                    'ethno_id' => $ethnos->random()->id,
                ]);

                // 7. Create Sibling Records
                for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                    FamSiblings::create([
                        'basic_info_id' => $basicInfo->id,
                        'name' => $faker->name,
                        'age' => $faker->numberBetween(5, 25),
                        'scholarship' => $faker->randomElement(['None', 'Private', 'Government']),
                        'course_year' => $faker->jobTitle,
                        'present_status' => $faker->randomElement(['Studying', 'Working', 'Unemployed']),
                    ]);
                }

                // 8. Create Document Records
                foreach ($documentTypes as $type => $label) {
                    $filename = "dummy_{$type}.pdf";
                    $status = $faker->randomElement(['pending', 'approved', 'approved', 'pending']); // Bias towards approved for testing
                    $document = Document::create([
                        'user_id' => $user->id,
                        'type' => $type,
                        'filename' => $filename,
                        'filepath' => "documents/{$filename}",
                        'filetype' => 'application/pdf',
                        'filesize' => $faker->numberBetween(100000, 500000),
                        'status' => $status,
                        'submitted_at' => now(),
                    ]);

                    // Call priority service
                    try {
                        $priorityService = new DocumentPriorityService();
                        $priorityService->calculateDocumentPriority($document);
                    } catch (\Exception $e) {
                        // Ignore if service fails
                    }

                    // Log Transaction
                    TransactionHistory::create([
                        'user_id' => $user->id,
                        'action' => 'Document Uploaded',
                        'description' => "Uploaded: {$label} (File: {$filename})",
                        'status' => 'info',
                        'metadata' => [
                            'document_id' => $document->id,
                            'type' => $type,
                            'filename' => $filename
                        ]
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("Failed to seed applicant: " . $e->getMessage());
            }
        }
        
        $this->command->info("Successfully seeded 20 manual applicants.");
    }
}
