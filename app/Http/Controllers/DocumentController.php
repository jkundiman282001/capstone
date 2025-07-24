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
            'upload-file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'type' => 'required|in:' . implode(',', $requiredTypes),
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

        return back()->with('success', 'Document uploaded successfully!');
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
