<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\ApplicationHistory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // We use a try-catch to ensure migration doesn't fail if models aren't fully loaded
        // though in a migration this is usually fine.
        
        $users = User::all();
        foreach ($users as $user) {
            // 1. Backfill "Account Created"
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
            
            // 3. Backfill "Document Uploaded" if they have documents
            if ($user->documents && $user->documents->count() > 0) {
                foreach ($user->documents as $doc) {
                    $docExists = ApplicationHistory::where('user_id', $user->id)
                        ->where('action', 'Document Uploaded')
                        ->where('description', 'like', '%' . $doc->document_type . '%')
                        ->exists();
                        
                    if (!$docExists) {
                        ApplicationHistory::create([
                            'user_id' => $user->id,
                            'action' => 'Document Uploaded',
                            'description' => "Uploaded: " . ucwords(str_replace('_', ' ', $doc->document_type)),
                            'status' => 'success',
                            'created_at' => $doc->created_at,
                            'updated_at' => $doc->updated_at
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Usually, we don't delete data in down() for backfills, 
        // but we could delete records created by this migration if needed.
    }
};
