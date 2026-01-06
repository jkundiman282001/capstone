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

        // Check if there's an existing document of this type for this user
        // Get the most recent one in case there are duplicates
        $existingDocument = Document::where('user_id', $user->id)
            ->where('type', $request->type)
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($existingDocument) {
            // If document exists, update it (resubmission case)
            // Delete old file from storage
            if (Storage::disk('public')->exists($existingDocument->filepath)) {
                Storage::disk('public')->delete($existingDocument->filepath);
            }

            // Store new file
            $path = $file->store('documents', 'public');

            // Update existing document
            $existingDocument->filename = $file->getClientOriginalName();
            $existingDocument->filepath = $path;
            $existingDocument->filetype = $file->getClientMimeType();
            $existingDocument->filesize = $file->getSize();
            $existingDocument->status = 'pending'; // Reset to pending when resubmitted
            $existingDocument->rejection_reason = null; // Clear rejection reason
            $existingDocument->priority_rank = null; // Reset priority rank
            $existingDocument->priority_score = 0; // Reset priority score (default value, not null)
            $existingDocument->submitted_at = now(); // Update submission timestamp
            $existingDocument->touch(); // Update timestamps
            $existingDocument->save();

            $document = $existingDocument;
        } else {
            // Create new document if it doesn't exist
            $path = $file->store('documents', 'public');

            $document = new Document;
            $document->user_id = $user->id;
            $document->filename = $file->getClientOriginalName();
            $document->filepath = $path;
            $document->filetype = $file->getClientMimeType();
            $document->filesize = $file->getSize();
            $document->description = null;
            $document->status = 'pending';
            $document->type = $request->type;
            $document->save();
        }

        // Calculate document priority (First Come, First Serve)
        $priorityService = new DocumentPriorityService;
        $priorityService->onDocumentUploaded($document);

        // Notify all staff
        /** @var \App\Models\User $student */
        $student = $user;
        $documentType = $request->type;
        /** @var \App\Models\Staff $staff */
        foreach (Staff::all() as $staff) {
            $staff->notify(new StudentUploadedDocument($student, $documentType));
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
            $document = Document::find($id);
        } catch (Exception $e) {
            Log::error('Database connection error while viewing document', ['error' => $e->getMessage()]);
            abort(500, "Database connection error. Please ensure your XAMPP MySQL is running.");
        }

        if (!$document) {
            Log::error('Document record not found in database', ['id' => $id]);
            // If the record is missing, it's a 404
            abort(404, "Document record not found in database. If you just pushed your code, remember that uploaded files are NOT saved to Git.");
        }

        Log::info('Document viewing attempt', [
            'id' => $document->id,
            'filepath' => $document->filepath,
            'user_id' => $document->user_id,
            'auth_id' => Auth::id(),
            'is_staff' => Auth::guard('staff')->check()
        ]);

        // Allow the owner of the document OR any staff member
        if ($document->user_id !== Auth::id() && ! Auth::guard('staff')->check()) {
            Log::warning('Unauthorized document access attempt', ['id' => $document->id]);
            abort(403);
        }

        $filepath = $document->filepath;
        // Normalize path: replace backslashes with forward slashes
        $normalizedPath = str_replace('\\', '/', $filepath);
        
        // Remove 'public/' or 'storage/' from the beginning if present for search purposes
        $trimmedPath = preg_replace('/^(public\/|storage\/)/', '', $normalizedPath);
        $trimmedPath = ltrim($trimmedPath, '/');
        
        $path = null;

        // 1. Try the original filepath directly (as absolute or relative to base)
        if (file_exists($filepath)) {
            $path = $filepath;
            Log::info('Found via direct absolute path', ['path' => $path]);
        } elseif (file_exists(base_path($filepath))) {
            $path = base_path($filepath);
            Log::info('Found via base_path', ['path' => $path]);
        }

        // 2. Try relative to storage/app/public
        if (!$path) {
            $publicPath = storage_path('app/public/' . $trimmedPath);
            Log::info('Checking storage/app/public', ['path' => $publicPath]);
            if (file_exists($publicPath)) {
                $path = $publicPath;
                Log::info('Found on storage/app/public', ['path' => $path]);
            }
        }

        // 3. Try relative to storage/app
        if (!$path) {
            $appPath = storage_path('app/' . $trimmedPath);
            Log::info('Checking storage/app', ['path' => $appPath]);
            if (file_exists($appPath)) {
                $path = $appPath;
                Log::info('Found on storage/app', ['path' => $path]);
            }
        }

        // 4. Try relative to public/storage
        if (!$path) {
            $webPath = public_path('storage/' . $trimmedPath);
            Log::info('Checking public/storage', ['path' => $webPath]);
            if (file_exists($webPath)) {
                $path = $webPath;
                Log::info('Found on public/storage', ['path' => $path]);
            }
        }

        // 5. Try just the basename in common folders
        if (!$path) {
            $baseName = basename($filepath);
            $searchPaths = [
                storage_path('app/public/documents/' . $baseName),
                storage_path('app/documents/' . $baseName),
                public_path('storage/documents/' . $baseName),
            ];

            foreach ($searchPaths as $searchPath) {
                Log::info('Checking fallback path', ['path' => $searchPath]);
                if (file_exists($searchPath)) {
                    $path = $searchPath;
                    Log::info('Found on fallback search path', ['path' => $path]);
                    break;
                }
            }
        }

        if ($path) {
            $mimeType = mime_content_type($path);
            $fileName = $document->filename;
            
            Log::info('Serving document', ['path' => $path, 'mime' => $mimeType]);

            // Force correct headers for viewing
            return response()->file($path, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        Log::error('Document not found', ['filepath' => $filepath]);
        abort(404, 'DEBUG: File not found on server. Database path: ' . $filepath . ' - Checked in storage/app/public/' . $trimmedPath);
    }

    public function destroy(Request $request, Document $document)
    {
        // Check if user owns the document
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($document->filepath)) {
            Storage::disk('public')->delete($document->filepath);
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
