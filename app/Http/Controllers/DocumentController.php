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
        $request->validate([
            'upload-file' => 'required|file|mimes:pdf|max:10240',
            'type' => 'required|in:' . implode(',', $requiredTypes),
        ], [
            'upload-file.mimes' => 'Only PDF files are allowed. Please convert your document to PDF format before uploading.',
            'upload-file.max' => 'File size must not exceed 10MB.',
        ]);

        $file = $request->file('upload-file');
        $user = Auth::user();
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

        // Calculate document priority (First Come, First Serve)
        $priorityService = new \App\Services\DocumentPriorityService();
        $priorityService->onDocumentUploaded($document);

        // Notify all staff
        $student = $user;
        $documentType = $request->type;
        foreach (\App\Models\Staff::all() as $staff) {
            $staff->notify(new \App\Notifications\StudentUploadedDocument($student, $documentType));
        }

        return back()->with('success', 'PDF document uploaded successfully! Please wait for staff review.');
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
