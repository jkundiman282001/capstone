<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

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

        // Notify all staff
        $student = $user;
        $documentType = $request->type;
        foreach (\App\Models\Staff::all() as $staff) {
            $staff->notify(new \App\Notifications\StudentUploadedDocument($student, $documentType));
        }

        return back()->with('success', 'PDF document uploaded successfully! Please wait for staff review.');
    }

    public function index()
    {
        $user = Auth::user();
        $documents = Document::where('user_id', $user->id)->latest()->get();
        $basicInfo = \App\Models\BasicInfo::where('user_id', $user->id)->first();
        $requiredTypes = [
            'birth_certificate' => 'Certified Birth Certificate',
            'income_document' => 'Income Tax Return/Tax Exemption/Indigency',
            'tribal_certificate' => 'Certificate of Tribal Membership/Confirmation',
            'endorsement' => 'Endorsement of the IPS/IP Traditional Leaders',
            'good_moral' => 'Certificate of Good Moral',
            'grades' => 'Latest Copy of Grades',
        ];
        return view('student.performance', compact('documents', 'requiredTypes', 'basicInfo'));
    }
}
