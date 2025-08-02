@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Scholarship Application (Read-Only)</h1>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">Personal Info</h2>
        <div><strong>Type of Assistance:</strong> {{ $basicInfo->type_assist }}</div>
        <div><strong>First Name:</strong> {{ $user->first_name }}</div>
        <div><strong>Middle Name:</strong> {{ $user->middle_name }}</div>
        <div><strong>Last Name:</strong> {{ $user->last_name }}</div>
        <div><strong>Email:</strong> {{ $user->email }}</div>
        <div><strong>Contact Number:</strong> {{ $user->contact_num }}</div>
        <div><strong>Date of Birth:</strong> {{ $basicInfo->birthdate }}</div>
        <div><strong>Place of Birth:</strong> {{ $basicInfo->birthplace }}</div>
        <div><strong>Gender:</strong> {{ $basicInfo->gender }}</div>
        <div><strong>Civil Status:</strong> {{ $basicInfo->civil_status }}</div>
        <div><strong>Ethnolinguistic Group:</strong> {{ $ethno->ethnicity ?? '' }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">Address</h2>
        <div class="mb-2"><strong>Mailing Address:</strong> {{ $mailing->house_num ?? '' }}, {{ $mailing->address->barangay ?? '' }}, {{ $mailing->address->municipality ?? '' }}, {{ $mailing->address->province ?? '' }}</div>
        <div class="mb-2"><strong>Permanent Address:</strong> {{ $permanent->house_num ?? '' }}, {{ $permanent->address->barangay ?? '' }}, {{ $permanent->address->municipality ?? '' }}, {{ $permanent->address->province ?? '' }}</div>
        <div class="mb-2"><strong>Place of Origin:</strong> {{ $origin->house_num ?? '' }}, {{ $origin->address->barangay ?? '' }}, {{ $origin->address->municipality ?? '' }}, {{ $origin->address->province ?? '' }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">Education</h2>
        @foreach($education as $edu)
            <div class="mb-2">
                <strong>Category:</strong> {{ $edu->category }}<br>
                <strong>School Name:</strong> {{ $edu->school_name }}<br>
                <strong>School Type:</strong> {{ $edu->school_type }}<br>
                <strong>Year Graduate:</strong> {{ $edu->year_grad }}<br>
                <strong>Grade Average:</strong> {{ $edu->grade_ave }}<br>
                <strong>Rank:</strong> {{ $edu->rank }}
            </div>
        @endforeach
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">Family</h2>
        <div class="mb-2"><strong>Father's Name:</strong> {{ $familyFather->name ?? '' }}</div>
        <div class="mb-2"><strong>Father's Status:</strong> {{ $familyFather->status ?? '' }}</div>
        <div class="mb-2"><strong>Father's Address:</strong> {{ $familyFather->address ?? '' }}</div>
        <div class="mb-2"><strong>Father's Occupation:</strong> {{ $familyFather->occupation ?? '' }}</div>
        <div class="mb-2"><strong>Father's Office Address:</strong> {{ $familyFather->office_address ?? '' }}</div>
        <div class="mb-2"><strong>Father's Educational Attainment:</strong> {{ $familyFather->educational_attainment ?? '' }}</div>
        <div class="mb-2"><strong>Father's Ethnolinguistic Group:</strong> {{ $familyFather->ethno->ethnicity ?? '' }}</div>
        <div class="mb-2"><strong>Father's Income:</strong> {{ $familyFather->income ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Name:</strong> {{ $familyMother->name ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Status:</strong> {{ $familyMother->status ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Address:</strong> {{ $familyMother->address ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Occupation:</strong> {{ $familyMother->occupation ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Office Address:</strong> {{ $familyMother->office_address ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Educational Attainment:</strong> {{ $familyMother->educational_attainment ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Ethnolinguistic Group:</strong> {{ $familyMother->ethno->ethnicity ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Income:</strong> {{ $familyMother->income ?? '' }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">Siblings</h2>
        @forelse($siblings as $sibling)
            <div class="mb-2 border-b pb-2">
                <strong>Name:</strong> {{ $sibling->name }}<br>
                <strong>Age:</strong> {{ $sibling->age }}<br>
                <strong>Scholarship:</strong> {{ $sibling->scholarship }}<br>
                <strong>Course/Year Level:</strong> {{ $sibling->course_year }}<br>
                <strong>Status:</strong> {{ $sibling->present_status }}
            </div>
        @empty
            <div class="text-gray-400">No siblings listed.</div>
        @endforelse
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">School Preference</h2>
        <div class="mb-2"><strong>First Preference Address:</strong> {{ $schoolPref->address ?? '' }}</div>
        <div class="mb-2"><strong>First Preference Degree/Course:</strong> {{ $schoolPref->degree ?? '' }}</div>
        <div class="mb-2"><strong>First Preference School Type:</strong> {{ $schoolPref->school_type ?? '' }}</div>
        <div class="mb-2"><strong>First Preference No. of Years:</strong> {{ $schoolPref->num_years ?? '' }}</div>
        <div class="mb-2"><strong>Second Preference Address:</strong> {{ $schoolPref->address2 ?? '' }}</div>
        <div class="mb-2"><strong>Second Preference Degree/Course:</strong> {{ $schoolPref->degree2 ?? '' }}</div>
        <div class="mb-2"><strong>Second Preference School Type:</strong> {{ $schoolPref->school_type2 ?? '' }}</div>
        <div class="mb-2"><strong>Second Preference No. of Years:</strong> {{ $schoolPref->num_years2 ?? '' }}</div>
        <div class="mb-2"><strong>Contribution to Community:</strong> {{ $schoolPref->ques_answer1 ?? '' }}</div>
        <div class="mb-2"><strong>Plans After Graduation:</strong> {{ $schoolPref->ques_answer2 ?? '' }}</div>
    </div>

    <!-- Documents Section -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-4">Uploaded Documents</h2>
        
        <!-- PDF Notice -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded-lg mb-4">
            <div class="flex items-center gap-2 text-sm text-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>File Format:</strong> Only PDF files are accepted for document uploads (max 10MB)</span>
            </div>
        </div>
        
        @php
            $totalRequired = count($requiredTypes);
            $approvedCount = $documents->whereIn('type', array_keys($requiredTypes))->where('status', 'approved')->count();
            $progressPercent = $totalRequired > 0 ? round(($approvedCount / $totalRequired) * 100) : 0;
        @endphp
        
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700">Document Approval Progress</span>
                <span class="text-sm font-bold text-blue-600">{{ $progressPercent }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
            </div>
        </div>

        <div class="space-y-4">
            @foreach($requiredTypes as $typeKey => $typeLabel)
                @php
                    $uploaded = $documents->firstWhere('type', $typeKey);
                    $status = $uploaded ? $uploaded->status : 'missing';
                @endphp
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($status === 'approved')
                                <span class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-bold">✓</span>
                            @elseif($status === 'pending')
                                <span class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-white text-sm font-bold">!</span>
                            @elseif($status === 'rejected')
                                <span class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center text-white text-sm font-bold">✗</span>
                            @else
                                <span class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center text-white text-sm font-bold">×</span>
                            @endif
                            <div>
                                <span class="font-semibold text-gray-900">{{ $typeLabel }}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    @if($status === 'approved')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Approved</span>
                                    @elseif($status === 'pending')
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">Pending</span>
                                    @elseif($status === 'rejected')
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">Rejected</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">Missing</span>
                                    @endif
                                    @if($uploaded)
                                        <span class="text-gray-500 text-xs">• Uploaded {{ $uploaded->created_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($uploaded)
                                <button onclick="viewDocument('{{ asset('storage/' . $uploaded->filepath) }}', '{{ $uploaded->filename }}', '{{ $uploaded->filetype }}')" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm font-semibold hover:bg-blue-700 transition">View</button>
                                @if($uploaded->status === 'pending')
                                    <button onclick="updateDocumentStatus({{ $uploaded->id }}, 'approved')" class="px-3 py-1.5 bg-green-600 text-white rounded text-sm font-semibold hover:bg-green-700 transition">Accept</button>
                                    <button onclick="updateDocumentStatus({{ $uploaded->id }}, 'rejected')" class="px-3 py-1.5 bg-red-600 text-white rounded text-sm font-semibold hover:bg-red-700 transition">Reject</button>
                                @elseif($uploaded->status === 'approved')
                                    <span class="px-3 py-1.5 bg-green-100 text-green-800 rounded text-sm font-semibold">✓ Accepted</span>
                                @elseif($uploaded->status === 'rejected')
                                    <span class="px-3 py-1.5 bg-red-100 text-red-800 rounded text-sm font-semibold">✗ Rejected</span>
                                    <button onclick="updateDocumentStatus({{ $uploaded->id }}, 'approved')" class="px-3 py-1.5 bg-green-600 text-white rounded text-sm font-semibold hover:bg-green-700 transition">Accept</button>
                                @endif
                                <span class="text-xs text-gray-500 ml-2">{{ $uploaded->filename }}</span>
                            @else
                                <span class="text-gray-400 text-sm">Not uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
</div>

<!-- Document Viewer Modal -->
<div id="documentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-2">
    <div class="bg-white rounded-lg shadow-xl w-[95vw] h-[95vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900"></h3>
            <button onclick="closeDocumentModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Content -->
        <div class="flex-1 p-2 overflow-hidden">
            <div id="documentViewer" class="w-full h-full"></div>
        </div>
        
        <!-- PDF Notice -->
        <div class="px-4 py-2 bg-blue-50 border-t border-gray-200">
            <div class="flex items-center gap-2 text-sm text-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Note: Only PDF files are accepted for document uploads</span>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-between p-4 border-t border-gray-200">
            <div class="flex items-center gap-2">
                <button onclick="downloadDocument()" class="px-4 py-2 bg-green-600 text-white rounded text-sm font-semibold hover:bg-green-700 transition">Download</button>
                <button onclick="printDocument()" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-semibold hover:bg-blue-700 transition">Print</button>
            </div>
            <button onclick="closeDocumentModal()" class="px-4 py-2 bg-gray-600 text-white rounded text-sm font-semibold hover:bg-gray-700 transition">Close</button>
        </div>
    </div>
</div>

<script>
let currentDocumentUrl = '';
let currentDocumentName = '';

function viewDocument(url, filename, filetype) {
    currentDocumentUrl = url;
    currentDocumentName = filename;
    
    const modal = document.getElementById('documentModal');
    const modalTitle = document.getElementById('modalTitle');
    const documentViewer = document.getElementById('documentViewer');
    
    modalTitle.textContent = filename;
    
    // Clear previous content
    documentViewer.innerHTML = '';
    
    // Handle different file types
    if (filetype.startsWith('image/')) {
        // Image files - show message that only PDFs are accepted
        const message = document.createElement('div');
        message.className = 'flex items-center justify-center h-full text-gray-500';
        message.innerHTML = `
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="text-lg font-semibold mb-2">Non-PDF File Detected</p>
                <p class="text-sm mb-4">This file is not in PDF format. Only PDF files are accepted for document uploads.</p>
                <button onclick="downloadDocument()" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Download to View</button>
            </div>
        `;
        documentViewer.appendChild(message);
    } else if (filetype === 'application/pdf') {
        // PDF files
        const iframe = document.createElement('iframe');
        iframe.src = url;
        iframe.className = 'w-full h-full border-0';
        iframe.title = filename;
        documentViewer.appendChild(iframe);
    } else {
        // Other file types (doc, docx, etc.) - show download message
        const message = document.createElement('div');
        message.className = 'flex items-center justify-center h-full text-gray-500';
        message.innerHTML = `
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="text-lg font-semibold mb-2">Non-PDF File Detected</p>
                <p class="text-sm mb-4">This file is not in PDF format. Only PDF files are accepted for document uploads.</p>
                <button onclick="downloadDocument()" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Download to View</button>
            </div>
        `;
        documentViewer.appendChild(message);
    }
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDocumentModal() {
    const modal = document.getElementById('documentModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function updateDocumentStatus(documentId, newStatus) {
    if (!confirm(`Are you sure you want to ${newStatus} this document?`)) {
        return;
    }

    fetch(`/staff/documents/${documentId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(`Document ${newStatus} successfully!`);
            // Reload the page to show updated status
            location.reload();
        } else {
            alert('Error updating document status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating document status');
    });
}

function downloadDocument() {
    if (currentDocumentUrl) {
        const link = document.createElement('a');
        link.href = currentDocumentUrl;
        link.download = currentDocumentName;
        link.click();
    }
}

function printDocument() {
    const documentViewer = document.getElementById('documentViewer');
    const iframe = documentViewer.querySelector('iframe');
    const img = documentViewer.querySelector('img');
    
    if (iframe) {
        iframe.contentWindow.print();
    } else if (img) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head><title>${currentDocumentName}</title></head>
                <body>
                    <img src="${img.src}" style="max-width: 100%; height: auto;">
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}

// Close modal when clicking outside
document.getElementById('documentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDocumentModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDocumentModal();
    }
});
</script>
@endsection
