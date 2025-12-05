<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
                'type' => 'required|in:' . implode(',', $requiredTypes),
            ], [
                'upload-file.required' => 'Please select a file to upload.',
                'upload-file.file' => 'The uploaded file is invalid.',
                'upload-file.mimes' => 'Only PDF and image files (JPG, JPEG, PNG, GIF) are allowed.',
                'upload-file.max' => 'File size must not exceed 10MB.',
                'type.required' => 'Document type is required.',
                'type.in' => 'Invalid document type.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $file = $request->file('upload-file');
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

            $document = new Document();
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
        $priorityService = new \App\Services\DocumentPriorityService();
        $priorityService->onDocumentUploaded($document);

        // Notify all staff
        $student = $user;
        $documentType = $request->type;
        foreach (\App\Models\Staff::all() as $staff) {
            $staff->notify(new \App\Notifications\StudentUploadedDocument($student, $documentType));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully! Please wait for staff review.'
            ]);
        }
        
        return back()->with('success', 'Document uploaded successfully! Please wait for staff review.');
    }

    public function show(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->filepath)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($document->filepath));
    }

    public function destroy(Document $document)
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

        return back()->with('success', 'Document deleted successfully.');
    }

    public function index()
    {
        $user = Auth::user();
        $documents = Document::where('user_id', $user->id)->latest()->get();
        $basicInfo = \App\Models\BasicInfo::where('user_id', $user->id)->first();
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
        $priorityService = new \App\Services\ApplicantPriorityService();
        $prioritizedApplicants = $priorityService->getPrioritizedApplicants();
        $priorityStatistics = $priorityService->getPriorityStatistics();
        
        // Find the student's rank in the prioritized list
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

        return view(
            'student.performance',
            compact(
                'documents',
                'requiredTypes',
                'basicInfo',
                'priorityRank',
                'acceptancePercent',
                'priorityFactors',
                'priorityStatistics'
            )
        );
    }
}
