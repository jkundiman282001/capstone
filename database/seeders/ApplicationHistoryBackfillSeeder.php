<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ApplicationHistory;

class ApplicationHistoryBackfillSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Backfill "Account Created" for all users
        $users = User::all();
        foreach ($users as $user) {
            $exists = ApplicationHistory::where('user_id', $user->id)
                ->where('action', 'Account Created')
                ->exists();
                
            if (!$exists) {
                ApplicationHistory::create([
                    'user_id' => $user->id,
                    'action' => 'Account Created',
                    'description' => 'Your IP Scholar Portal account has been successfully created.',
                    'status' => 'success',
                    'created_at' => $user->created_at,
                    'updated_at' => $user->created_at
                ]);
            }

            // 2. Backfill "Application Submitted" if they have BasicInfo
            if ($user->basicInfo) {
                $appExists = ApplicationHistory::where('user_id', $user->id)
                    ->where('action', 'Application Submitted')
                    ->exists();

                if (!$appExists) {
                    ApplicationHistory::create([
                        'user_id' => $user->id,
                        'action' => 'Application Submitted',
                        'description' => 'Your IP Scholarship application has been successfully submitted.',
                        'status' => 'success',
                        'created_at' => $user->basicInfo->created_at,
                        'updated_at' => $user->basicInfo->updated_at
                    ]);
                }
            }
        }
    }
}
