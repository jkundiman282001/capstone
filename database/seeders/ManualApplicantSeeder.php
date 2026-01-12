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

        for ($i = 0; $i < 10; $i++) {
            try {
                DB::beginTransaction();

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

                // 2. Create Addresses
                $mailingAddr = Address::firstOrCreate([
                    'barangay' => $faker->streetName,
                    'municipality' => 'Digos City',
                    'province' => 'Davao del Sur',
                ]);
                $mailing = MailingAddress::create([
                    'address_id' => $mailingAddr->id,
                    'house_num' => $faker->buildingNumber,
                ]);

                $permanentAddr = Address::firstOrCreate([
                    'barangay' => $faker->streetName,
                    'municipality' => 'Digos City',
                    'province' => 'Davao del Sur',
                ]);
                $permanent = PermanentAddress::create([
                    'address_id' => $permanentAddr->id,
                    'house_num' => $faker->buildingNumber,
                ]);

                $originAddr = Address::firstOrCreate([
                    'barangay' => $faker->streetName,
                    'municipality' => 'Digos City',
                    'province' => 'Davao del Sur',
                ]);
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
                    'ques_answer1' => $faker->paragraph,
                    'ques_answer2' => $faker->paragraph,
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
                    'type_assist' => 'Manual',
                    'assistance_for' => 'Tuition',
                    'application_status' => 'pending',
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
                Family::create([
                    'basic_info_id' => $basicInfo->id,
                    'fam_type' => 'father',
                    'name' => $faker->name('male'),
                    'status' => 'Living',
                    'address' => $faker->address,
                    'occupation' => $faker->jobTitle,
                    'educational_attainment' => 'College Graduate',
                    'office_address' => $faker->address,
                    'income' => $faker->numberBetween(10000, 50000),
                    'ethno_id' => $ethnos->random()->id,
                ]);

                Family::create([
                    'basic_info_id' => $basicInfo->id,
                    'fam_type' => 'mother',
                    'name' => $faker->name('female'),
                    'status' => 'Living',
                    'address' => $faker->address,
                    'occupation' => $faker->jobTitle,
                    'educational_attainment' => 'College Graduate',
                    'office_address' => $faker->address,
                    'income' => $faker->numberBetween(10000, 50000),
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
                    $document = Document::create([
                        'user_id' => $user->id,
                        'type' => $type,
                        'filename' => $filename,
                        'filepath' => "documents/{$filename}",
                        'filetype' => 'application/pdf',
                        'filesize' => $faker->numberBetween(100000, 500000),
                        'status' => 'pending',
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
        
        $this->command->info("Successfully seeded 10 manual applicants.");
    }
}
