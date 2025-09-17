@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Enhanced Application Header -->
    <div class="bg-white/95 rounded-2xl shadow-2xl p-8 mb-8 relative overflow-hidden backdrop-blur-sm">
        <!-- Cultural Pattern Overlay -->
        <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
            <div class="w-full h-full bg-gradient-to-br from-gold to-sunrise-orange rounded-full"></div>
        </div>
        
        <div class="flex items-center justify-between mb-8 relative z-10">
            <div class="flex items-center space-x-6">
                <div class="relative">
                    @if($user->profile_pic)
                        <div class="w-20 h-20 rounded-2xl overflow-hidden shadow-xl floating">
                            <img src="{{ asset('storage/' . $user->profile_pic) }}" 
                                 alt="{{ $user->first_name }} {{ $user->last_name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-20 h-20 bg-gradient-to-br from-amber-700 via-green-600 to-yellow-400 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-xl floating">
                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-amber-700 text-sm">🌟</span>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-amber-700 mb-2">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</h2>
                    <p class="text-gray-600 text-lg">Application ID: #NCIP-{{ date('Y') }}-{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</p>
                    <div class="flex items-center space-x-3 mt-3">
                        <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-bold rounded-full border border-green-200">
                            ✅ Active
                        </span>
                        <span class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-bold rounded-full border border-blue-200">
                            💻 {{ $schoolPref->degree ?? 'N/A' }}
                        </span>
                        <span class="px-4 py-2 bg-yellow-100 text-amber-700 text-sm font-bold rounded-full border border-yellow-200">
                            🏔️ {{ $ethno->ethnicity ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right bg-gradient-to-br from-orange-100 to-yellow-100 p-6 rounded-2xl border border-orange-200">
                <p class="text-base text-gray-600 font-medium">Application Status</p>
                <p class="text-2xl font-bold text-orange-600 mt-1">Under Review</p>
                <p class="text-sm text-gray-500">Under Review</p>
            </div>
        </div>

        <!-- Enhanced Progress Bar -->
        <div class="mb-8 relative z-10">
            <div class="flex justify-between items-center mb-4">
                <span class="text-lg font-bold text-amber-700 flex items-center">
                    <span class="mr-2">📄</span> Document Completion
                </span>
                <span class="text-xl font-bold text-green-600">{{ $progressPercent }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-2xl h-6 shadow-inner">
                <div class="bg-gradient-to-r from-green-500 via-orange-500 to-yellow-400 h-6 rounded-2xl transition-all duration-1000 shadow-lg" style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="flex justify-between text-sm text-gray-600 mt-3">
                <span class="flex items-center"><span class="mr-1">✅</span> {{ $approvedCount }} of {{ $totalRequired }} documents submitted</span>
                <span class="flex items-center"><span class="mr-1">⏳</span> {{ $totalRequired - $approvedCount }} pending</span>
            </div>
        </div>
    </div>

    <!-- Enhanced Quick Stats with Cultural Icons -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/95 rounded-2xl shadow-xl p-6 relative overflow-hidden backdrop-blur-sm hover:scale-105 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-20 h-20 bg-green-100 rounded-full -mr-10 -mt-10"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-base font-bold text-gray-700 mb-1">Approved</p>
                    <p class="text-sm text-gray-500">Approved</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $documents->where('status', 'approved')->count() }}</p>
                </div>
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center">
                    <span class="text-2xl">✅</span>
                </div>
            </div>
        </div>

        <div class="bg-white/95 rounded-2xl shadow-xl p-6 relative overflow-hidden backdrop-blur-sm hover:scale-105 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-20 h-20 bg-red-100 rounded-full -mr-10 -mt-10"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-base font-bold text-gray-700 mb-1">Missing</p>
                    <p class="text-sm text-gray-500">Missing</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $totalRequired - $documents->whereIn('type', array_keys($requiredTypes))->count() }}</p>
                </div>
                <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center">
                    <span class="text-2xl">❌</span>
                </div>
            </div>
        </div>

        <div class="bg-white/95 rounded-2xl shadow-xl p-6 relative overflow-hidden backdrop-blur-sm hover:scale-105 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-20 h-20 bg-orange-100 rounded-full -mr-10 -mt-10"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-base font-bold text-gray-700 mb-1">In Review</p>
                    <p class="text-sm text-gray-500">In Review</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $documents->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center">
                    <span class="text-2xl">⏳</span>
                </div>
            </div>
        </div>

        <div class="bg-white/95 rounded-2xl shadow-xl p-6 relative overflow-hidden backdrop-blur-sm hover:scale-105 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100 rounded-full -mr-10 -mt-10"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-base font-bold text-gray-700 mb-1">Total</p>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalRequired }}</p>
                </div>
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <span class="text-2xl">📊</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Documents Section -->
    <div class="bg-white/95 rounded-2xl shadow-2xl p-8 mb-8 backdrop-blur-sm">
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-amber-700 flex items-center">
                <span class="mr-3">📋</span> Required Documents
            </h3>
        </div>

        <div class="space-y-4">
            @foreach($requiredTypes as $typeKey => $typeLabel)
                @php
                    $uploaded = $documents->firstWhere('type', $typeKey);
                    $status = $uploaded ? $uploaded->status : 'missing';
                    
                    // Get appropriate icon for each document type
                    $icon = match($typeKey) {
                        'birth_certificate' => '📋',
                        'income_document' => '💰',
                        'tribal_certificate' => '🏔️',
                        'endorsement' => '✅',
                        'good_moral' => '🤝',
                        'grades' => '📚',
                        default => '📄'
                    };
                @endphp
                
                @if($status === 'approved')
                    <!-- Approved Document -->
                    <div class="flex items-center justify-between p-6 bg-gradient-to-r from-green-100 to-green-50 border-2 border-green-300 rounded-2xl hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-green-600 rounded-2xl flex items-center justify-center">
                                <span class="text-white text-xl">✅</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-700 text-lg">{{ $typeLabel }}</h4>
                                <p class="text-green-600 font-medium">Approved • Uploaded {{ $uploaded->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-4 py-2 bg-green-600 text-white font-bold rounded-xl">✓ Accepted</span>
                            <button onclick="viewDocument('{{ asset('storage/' . $uploaded->filepath) }}', '{{ $uploaded->filename }}', '{{ $uploaded->filetype }}')" class="px-4 py-2 bg-blue-100 text-blue-700 font-semibold rounded-xl hover:bg-blue-200 transition-colors">View</button>
                        </div>
                    </div>
                @elseif($status === 'pending')
                    <!-- Pending Document -->
                    <div class="flex items-center justify-between p-6 bg-gradient-to-r from-orange-100 to-orange-50 border-2 border-orange-300 rounded-2xl hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center">
                                <span class="text-white text-xl">⏳</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-700 text-lg">{{ $typeLabel }}</h4>
                                <p class="text-orange-600 font-medium">Pending Review • Uploaded {{ $uploaded->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-4 py-2 bg-orange-500 text-white font-bold rounded-xl">Pending</span>
                            <button onclick="viewDocument('{{ asset('storage/' . $uploaded->filepath) }}', '{{ $uploaded->filename }}', '{{ $uploaded->filetype }}')" class="px-4 py-2 bg-blue-100 text-blue-700 font-semibold rounded-xl hover:bg-blue-200 transition-colors">View</button>
                            <button onclick="updateDocumentStatus({{ $uploaded->id }}, 'approved')" class="px-4 py-2 bg-green-100 text-green-700 font-semibold rounded-xl hover:bg-green-200 transition-colors">Accept</button>
                            <button onclick="updateDocumentStatus({{ $uploaded->id }}, 'rejected')" class="px-4 py-2 bg-red-100 text-red-700 font-semibold rounded-xl hover:bg-red-200 transition-colors">Reject</button>
                        </div>
                    </div>
                @elseif($status === 'rejected')
                    <!-- Rejected Document -->
                    <div class="flex items-center justify-between p-6 bg-gradient-to-r from-red-100 to-red-50 border-2 border-red-300 rounded-2xl hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-red-600 rounded-2xl flex items-center justify-center">
                                <span class="text-white text-xl">✗</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-700 text-lg">{{ $typeLabel }}</h4>
                                <p class="text-red-600 font-medium">Rejected • Uploaded {{ $uploaded->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-4 py-2 bg-red-600 text-white font-bold rounded-xl">Rejected</span>
                            <button onclick="viewDocument('{{ asset('storage/' . $uploaded->filepath) }}', '{{ $uploaded->filename }}', '{{ $uploaded->filetype }}')" class="px-4 py-2 bg-blue-100 text-blue-700 font-semibold rounded-xl hover:bg-blue-200 transition-colors">View</button>
                        </div>
                    </div>
                @else
                    <!-- Missing Document -->
                    <div class="flex items-center justify-between p-6 bg-gradient-to-r from-red-100 to-red-50 border-2 border-red-300 rounded-2xl hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-red-600 rounded-2xl flex items-center justify-center">
                                <span class="text-white text-xl">{{ $icon }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-700 text-lg">{{ $typeLabel }}</h4>
                                <p class="text-red-600 font-medium">Missing • {{ $typeLabel }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-4 py-2 bg-red-600 text-white font-bold rounded-xl">Missing</span>
                        </div>
                    </div>
                @endif
                @if($typeKey === 'grades' && $uploaded)
                    <button onclick="showGradesViewer(this)" data-user-id="{{ $user->id }}" class="px-4 py-2 bg-purple-100 text-purple-700 font-semibold rounded-xl hover:bg-purple-200 transition-colors">View Grades</button>
                @endif
            @endforeach
        </div>
    </div>

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

<!-- Grades Viewer Modal -->
<div id="gradesModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-2">
    <div class="bg-white rounded-lg shadow-xl w-[90vw] max-w-2xl flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Extracted Grades Text</h3>
            <button onclick="closeGradesModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="flex-1 p-6 overflow-auto">
            <div class="mb-4 text-gray-600 text-sm">Below is the raw extracted text from the student's grades PDF.</div>
            <pre id="gradesText" class="bg-gray-100 p-4 rounded text-sm text-gray-800 whitespace-pre-wrap">Loading...</pre>
        </div>
        <div class="flex items-center justify-end p-4 border-t border-gray-200">
            <button onclick="closeGradesModal()" class="px-4 py-2 bg-gray-600 text-white rounded text-sm font-semibold hover:bg-gray-700 transition">Close</button>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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

function showGradesViewer(btn) {
    const userId = btn ? btn.getAttribute('data-user-id') : null;
    document.getElementById('gradesModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (!userId) return;
    const gradesText = document.getElementById('gradesText');
    gradesText.textContent = 'Loading...';
    fetch(`/staff/grades/${userId}`)
        .then(res => {
            if (!res.ok) {
                return res.json().then(data => {
                    throw new Error(data.error || 'Failed to contact AI service.');
                }).catch(() => {
                    throw new Error('Failed to contact AI service.');
                });
            }
            return res.json();
        })
        .then(data => {
            if (data.success && typeof data.text === 'string') {
                gradesText.textContent = data.text.trim() ? data.text : 'No text found in document.';
            } else {
                gradesText.textContent = data.error || 'Failed to extract text.';
            }
        })
        .catch((error) => {
            console.error('Error contacting AI service:', error);
            gradesText.textContent = (error.message || 'Error contacting AI service.') + '\nClick Retry below.';
            gradesText.insertAdjacentHTML('afterend', `<button onclick='retryGradesViewer(${userId})' class='mt-2 px-4 py-2 bg-purple-100 text-purple-700 rounded'>Retry</button>`);
        });
}

function retryGradesViewer(userId) {
    // Simulate a button object for showGradesViewer
    showGradesViewer({ getAttribute: () => userId });
}
// Optional: Close modal with Escape key
if (!window._gradesModalEscapeListener) {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeGradesModal();
        }
    });
    window._gradesModalEscapeListener = true;
}
</script>
@endsection
