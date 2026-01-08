<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Staff;
use App\Models\BasicInfo;
use App\Notifications\TransactionNotification;
use App\Notifications\StudentUploadedDocument;
use App\Services\DocumentPriorityService;
use App\Services\ApplicantPriorityService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $requiredTypes = [
            'birth_certificate',
            'income_document',
            'tribal_certificate',
            'endorsement',
            'good_moral',
            'grades',
        ];
        try {
            $request->validate([
                'upload-file' => 'required|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
                'type' => 'required|in:'.implode(',', $requiredTypes),
            ], [
                'upload-file.required' => 'Please select a file to upload.',
                'upload-file.file' => 'The uploaded file is invalid.',
                'upload-file.mimes' => 'Only PDF and image files (JPG, JPEG, PNG, GIF) are allowed.',
                'upload-file.max' => 'File size must not exceed 10MB.',
                'type.required' => 'Document type is required.',
                'type.in' => 'Invalid document type.',
            ]);
        } catch (ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        $file = $request->file('upload-file');
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Store new file using the default disk (S3 in cloud, public locally)
        $disk = config('filesystems.default');
        $path = $file->store('documents', $disk);

        // Update or create document record
        $document = Document::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => $request->type,
            ],
            [
                'filename' => $file->getClientOriginalName(),
                'filepath' => $path,
                'filetype' => $file->getClientMimeType(),
                'filesize' => $file->getSize(),
                'status' => 'pending',
                'submitted_at' => now(),
            ]
        );

        // Calculate document priority (First Come, First Serve)
        $priorityService = new DocumentPriorityService;
        $priorityService->onDocumentUploaded($document);

        // Notify all staff
        /** @var \App\Models\User $student */
        $student = $user;
        $documentType = $request->type;
        /** @var \App\Models\Staff $staff */
        try {
            foreach (Staff::all() as $staff) {
                $staff->notify(new StudentUploadedDocument($student, $documentType));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to notify staff about document upload in DocumentController: ' . $e->getMessage());
        }

        // Notify the student
        $student->notify(new TransactionNotification(
            'transaction',
            'Document Uploaded',
            'You have successfully uploaded the '.str_replace('_', ' ', $documentType).' document.',
            'normal'
        ));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully! Please wait for staff review.',
            ]);
        }

        return back()->with('success', 'Document uploaded successfully! Please wait for staff review.');
    }

    public function show($id)
    {
        try {
            $document = Document::findOrFail($id);
        } catch (Exception $e) {
            Log::error('Document record not found in database', ['id' => $id, 'error' => $e->getMessage()]);
            abort(404, "Document record not found in database.");
        }

        Log::info('Document viewing attempt', [
            'id' => $document->id,
            'filepath' => $document->filepath,
            'user_id' => $document->user_id,
            'auth_id' => Auth::id(),
            'is_staff' => Auth::guard('staff')->check()
        ]);

        // Allow the owner of the document OR any staff member
        if (!Auth::check() && !Auth::guard('staff')->check()) {
            Log::warning('Unauthenticated document access attempt', ['id' => $id]);
            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again to view the document.');
        }

        if ($document->user_id !== Auth::id() && ! Auth::guard('staff')->check()) {
            Log::warning('Unauthorized document access attempt', ['id' => $document->id, 'owner' => $document->user_id, 'current_user' => Auth::id()]);
            abort(403, 'You do not have permission to view this document.');
        }

        $filepath = $document->filepath;
        
        // Use Laravel's Storage facade which is more robust for cloud environments
        // It handles both local and cloud storage drivers seamlessly
        
        // 1. Check the default disk first (configured via FILESYSTEM_DISK)
        $defaultDisk = config('filesystems.default');
        if (Storage::disk($defaultDisk)->exists($filepath)) {
            Log::info("Found document via Storage default disk ({$defaultDisk})", ['path' => $filepath]);
            return Storage::disk($defaultDisk)->response($filepath);
        }

        // 2. Explicitly check S3 if it's not the default but is configured
        if ($defaultDisk !== 's3' && config('filesystems.disks.s3.bucket')) {
            if (Storage::disk('s3')->exists($filepath)) {
                Log::info('Found document via Storage S3 disk', ['path' => $filepath]);
                return Storage::disk('s3')->response($filepath);
            }
        }

        // 3. Check public disk
        if ($defaultDisk !== 'public' && Storage::disk('public')->exists($filepath)) {
            Log::info('Found document via Storage public disk', ['path' => $filepath]);
            return Storage::disk('public')->response($filepath);
        }

        // 4. Check local disk
        if ($defaultDisk !== 'local' && Storage::disk('local')->exists($filepath)) {
            Log::info('Found document via Storage local disk', ['path' => $filepath]);
            return Storage::disk('local')->response($filepath);
        }

        // Fallback: try to see if it's stored without the 'documents/' prefix or with a different prefix
        $trimmedPath = ltrim(str_replace(['public/', 'storage/', 'documents/'], '', $filepath), '/');
        $possiblePaths = [
            $trimmedPath,
            'documents/' . $trimmedPath,
            'public/documents/' . $trimmedPath,
            'storage/documents/' . $trimmedPath
        ];

        foreach ($possiblePaths as $path) {
            // Check cloud first in fallback
            if (config('filesystems.disks.s3.bucket') && Storage::disk('s3')->exists($path)) {
                Log::info('Found via fallback search on S3 disk', ['path' => $path]);
                return Storage::disk('s3')->response($path);
            }
            if (Storage::disk('public')->exists($path)) {
                Log::info('Found via fallback search on public disk', ['path' => $path]);
                return Storage::disk('public')->response($path);
            }
            if (Storage::disk('local')->exists($path)) {
                Log::info('Found via fallback search on local disk', ['path' => $path]);
                return Storage::disk('local')->response($path);
            }
        }

        // Final fallback: try direct filesystem access if Storage facade fails (legacy support)
        $absolutePath = storage_path('app/public/' . $trimmedPath);
        if (file_exists($absolutePath)) {
            Log::info('Found via direct filesystem fallback', ['path' => $absolutePath]);
            return response()->file($absolutePath);
        }

        $baseAbsolutePath = storage_path('app/' . $trimmedPath);
        if (file_exists($baseAbsolutePath)) {
            Log::info('Found via direct base storage fallback', ['path' => $baseAbsolutePath]);
            return response()->file($baseAbsolutePath);
        }

        Log::error('Document not found on any path', [
            'database_path' => $filepath,
            'trimmed_path' => $trimmedPath,
            'absolute_path' => $absolutePath
        ]);

        abort(404, 'File not found on server. If you are using Laravel Cloud, please ensure your storage is persistent or use a cloud disk like S3.');
    }

    public function destroy(Request $request, Document $document)
    {
        // Check if user owns the document
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete file from storage (check all possible disks)
        $disks = ['s3', 'public', 'local'];
        foreach ($disks as $diskName) {
            try {
                if (Storage::disk($diskName)->exists($document->filepath)) {
                    Storage::disk($diskName)->delete($document->filepath);
                    Log::info("Deleted document from {$diskName} disk", ['path' => $document->filepath]);
                }
            } catch (\Exception $e) {
                Log::warning("Failed to check/delete from {$diskName} disk", ['error' => $e->getMessage()]);
            }
        }

        $document->delete();

        // Notify the student
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->notify(new TransactionNotification(
            'transaction',
            'Document Deleted',
            'You have successfully deleted the '.str_replace('_', ' ', $document->type).' document.',
            'normal'
        ));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully.',
            ]);
        }

        return back()->with('success', 'Document deleted successfully.');
    }

    public function index()
    {
        $user = Auth::user();
        $documents = Document::where('user_id', $user->id)->latest()->get();
        $basicInfo = BasicInfo::where('user_id', $user->id)->first();
        $requiredTypes = [
            'birth_certificate' => 'Original or Certified True Copy of Birth Certificate',
            'income_document' => 'Income Tax Return of the parents/guardians or Certificate of Tax Exemption from BIR or Certificate of Indigency signed by the barangay captain',
            'tribal_certificate' => 'Certificate of Tribal Membership/Certificate of Confirmation COC',
            'endorsement' => 'Endorsement of the IPS/IP Traditional Leaders',
            'good_moral' => 'Certificate of Good Moral from the Guidance Counselor',
            'grades' => 'Incoming First Year College (Senior High School Grades), Ongoing college students latest copy of grades',
        ];

        // Calculate priority rank for the student
        $priorityRank = null;
        $acceptancePercent = null;
        $priorityFactors = [];
        $priorityService = new ApplicantPriorityService;
        $prioritizedApplicants = $priorityService->getPrioritizedApplicants();
        $priorityStatistics = $priorityService->getPriorityStatistics();

        // Find the student's rank in the prioritized list
        $priorityScore = null;
        foreach ($prioritizedApplicants as $applicantData) {
            if ($applicantData['user_id'] == $user->id) {
                $priorityRank = $applicantData['priority_rank'] ?? null;
                $priorityScore = $applicantData['priority_score'] ?? null;
                if ($priorityScore !== null) {
                    $acceptancePercent = (int) round($priorityScore);
                }
                $priorityFactors = [
                    'is_priority_ethno' => $applicantData['is_priority_ethno'] ?? false,
                    'is_priority_course' => $applicantData['is_priority_course'] ?? false,
                    'has_approved_tribal_cert' => $applicantData['has_approved_tribal_cert'] ?? false,
                    'has_approved_income_tax' => $applicantData['has_approved_income_tax'] ?? false,
                    'has_approved_grades' => $applicantData['has_approved_grades'] ?? false,
                    'has_all_other_requirements' => $applicantData['has_all_other_requirements'] ?? false,
                ];
                break;
            }
        }

        // Count validated applicants who are not grantees yet
        $validatedNonGranteesCount = BasicInfo::where('application_status', 'validated')
            ->where(function ($q) {
                $q->whereNull('grant_status')
                    ->orWhere('grant_status', '!=', 'grantee')
                    ->orWhere('grant_status', '!=', 'Grantee');
            })
            ->count();

        return view(
            'student.performance',
            compact(
                'documents',
                'requiredTypes',
                'basicInfo',
                'priorityRank',
                'acceptancePercent',
                'priorityFactors',
                'priorityStatistics',
                'priorityScore',
                'validatedNonGranteesCount'
            )
        );
    }
}
