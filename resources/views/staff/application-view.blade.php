@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 p-6">
    
    <div class="max-w-[1800px] mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 rounded-xl shadow-sm border border-slate-200 transition-all text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Application Header Card -->
        <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-8 mb-6">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <!-- Profile Section -->
                <div class="flex items-center gap-5">
                    <div class="relative">
                        @if($user->profile_pic)
                            <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="{{ $user->first_name }}" class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-xl">
                        @else
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center text-white font-black text-3xl border-4 border-white shadow-xl">
                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg border-2 border-white">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-slate-900 mb-1">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</h1>
                        <p class="text-sm text-slate-500 font-medium mb-3">Application ID: #NCIP-{{ date('Y') }}-{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-100">
                                ✅ Active
                            </span>
                            <span class="px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-100">
                                💻 {{ $schoolPref->degree ?? 'N/A' }}
                            </span>
                            <span class="px-3 py-1.5 bg-purple-50 text-purple-700 text-xs font-bold rounded-lg border border-purple-100">
                                🏔️ {{ $ethno->ethnicity ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Status & Action Section -->
                <div class="flex flex-col items-end gap-4">
                    @php
                        $appStatus = $basicInfo->application_status ?? 'pending';
                        $isValidated = $appStatus === 'validated';
                    @endphp
                    
                    <!-- Status Badge -->
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Application Status</p>
                        <div class="px-6 py-3 rounded-2xl {{ $isValidated ? 'bg-gradient-to-r from-emerald-500 to-green-600' : 'bg-gradient-to-r from-amber-500 to-orange-600' }} shadow-lg">
                            <p class="text-2xl font-black text-white">{{ $isValidated ? 'Validated' : 'Pending' }}</p>
                        </div>
                    </div>

                    <!-- Action Button -->
                    @if($isValidated)
                        <button onclick="updateApplicationStatus('pending')" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg text-sm">
                            Set to Pending
                        </button>
                    @else
                        <button onclick="updateApplicationStatus('validated')" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg text-sm">
                            Approve Application
                        </button>
                    @endif
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-bold text-slate-700">Document Completion</span>
                    <span class="text-2xl font-black text-emerald-600">{{ $progressPercent }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden shadow-inner">
                    <div class="bg-gradient-to-r from-emerald-500 via-green-500 to-teal-500 h-3 rounded-full transition-all duration-1000" style="width: {{ $progressPercent }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-500 mt-2">
                    <span>✅ {{ $approvedCount }} of {{ $totalRequired }} approved</span>
                    <span>⏳ {{ $totalRequired - $approvedCount }} pending</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Approved</p>
                        <p class="text-3xl font-black text-emerald-600">{{ $documents->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pending</p>
                        <p class="text-3xl font-black text-amber-600">{{ $documents->where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-amber-50 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Missing</p>
                        <p class="text-3xl font-black text-red-600">{{ $totalRequired - $documents->whereIn('type', array_keys($requiredTypes))->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-red-50 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Required</p>
                        <p class="text-3xl font-black text-blue-600">{{ $totalRequired }}</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content with Sidebar Navigation -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Main Content Area (9 columns) -->
            <div class="lg:col-span-9 space-y-6">
                
                <!-- Documents Section -->
                <div id="documents-section" class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6 scroll-mt-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            Required Documents
                        </h2>
                        <button onclick="recalculateDocumentPriorities()" class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl text-xs font-bold transition-all flex items-center gap-2 border border-blue-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Recalculate
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach($requiredTypes as $typeKey => $typeLabel)
                            @php
                                $uploaded = $documents->firstWhere('type', $typeKey);
                                $status = $uploaded ? $uploaded->status : 'missing';
                            @endphp
                            
                            @if($status === 'approved')
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-green-50 border-2 border-emerald-200 rounded-2xl hover:shadow-md transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">{{ $typeLabel }}</h4>
                                            <p class="text-xs text-emerald-600 font-medium">Approved • {{ $uploaded->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <button onclick="viewDocument('{{ asset('storage/' . $uploaded->filepath) }}', '{{ $uploaded->filename }}', '{{ $uploaded->filetype }}')" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold rounded-lg text-xs transition-all">
                                        View
                                    </button>
                                </div>
                            @elseif($status === 'pending')
                                @php $priorityRank = $uploaded->priority_rank ?? null; @endphp
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl hover:shadow-md transition-all relative">
                                    @if($priorityRank)
                                        <div class="absolute top-2 right-2">
                                            <span class="px-2 py-1 bg-amber-600 text-white text-[10px] font-black rounded-lg">Rank #{{ $priorityRank }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">{{ $typeLabel }}</h4>
                                            <p class="text-xs text-amber-600 font-medium">Pending Review • {{ $uploaded->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button onclick="viewDocument('{{ asset('storage/' . $uploaded->filepath) }}', '{{ $uploaded->filename }}', '{{ $uploaded->filetype }}')" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold rounded-lg text-xs transition-all">View</button>
                                        <button onclick="updateDocumentStatus({{ $uploaded->id }}, 'approved')" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 font-bold rounded-lg text-xs transition-all">Accept</button>
                                        <button onclick="updateDocumentStatus({{ $uploaded->id }}, 'rejected')" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 font-bold rounded-lg text-xs transition-all">Reject</button>
                                    </div>
                                </div>
                            @elseif($status === 'rejected')
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 rounded-2xl hover:shadow-md transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">{{ $typeLabel }}</h4>
                                            <p class="text-xs text-red-600 font-medium">Rejected • {{ $uploaded->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <button onclick="viewDocument('{{ asset('storage/' . $uploaded->filepath) }}', '{{ $uploaded->filename }}', '{{ $uploaded->filetype }}')" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold rounded-lg text-xs transition-all">View</button>
                                </div>
                            @else
                                <div class="flex items-center justify-between p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl opacity-60">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-slate-300 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-700 text-sm">{{ $typeLabel }}</h4>
                                            <p class="text-xs text-slate-500 font-medium">Not submitted</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1.5 bg-slate-200 text-slate-600 font-bold rounded-lg text-xs">Missing</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Personal Information -->
                <div id="personal-section" class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6 scroll-mt-6">
                    <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                        <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        Personal Information
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Email</p>
                            <p class="text-sm font-medium text-slate-900">{{ $user->email }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Contact</p>
                            <p class="text-sm font-medium text-slate-900">{{ $user->contact_num ?? 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Birthdate</p>
                            <p class="text-sm font-medium text-slate-900">{{ $basicInfo->birthdate ?? 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Gender</p>
                            <p class="text-sm font-medium text-slate-900">{{ $basicInfo->gender ?? 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Civil Status</p>
                            <p class="text-sm font-medium text-slate-900">{{ $basicInfo->civil_status ?? 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Birthplace</p>
                            <p class="text-sm font-medium text-slate-900">{{ $basicInfo->birthplace ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div id="address-section" class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6 scroll-mt-6">
                    <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                        <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        </div>
                        Address Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($mailing && $mailing->address)
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Mailing Address
                            </p>
                            <p class="text-sm text-slate-700">{{ $mailing->house_num ?? '' }}, {{ $mailing->address->barangay ?? '' }}, {{ $mailing->address->municipality ?? '' }}, {{ $mailing->address->province ?? '' }}</p>
                        </div>
                        @endif
                        @if($permanent && $permanent->address)
                        <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                            <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                Permanent Address
                            </p>
                            <p class="text-sm text-slate-700">{{ $permanent->house_num ?? '' }}, {{ $permanent->address->barangay ?? '' }}, {{ $permanent->address->municipality ?? '' }}, {{ $permanent->address->province ?? '' }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Education Section -->
                <div id="education-section" class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6 scroll-mt-6">
                    <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-2">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        Educational Background
                    </h2>

                    @forelse($education as $index => $edu)
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-5 mb-4 border border-purple-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-black">{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-slate-900">{{ $edu->school_name ?? 'N/A' }}</h3>
                                    <p class="text-xs text-purple-600 font-medium uppercase">{{ $edu->category ?? 'N/A' }}</p>
                                </div>
                                @if($edu->year_grad)
                                    <span class="px-3 py-1.5 bg-purple-600 text-white text-xs font-bold rounded-lg">{{ $edu->year_grad }}</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="bg-white rounded-xl p-3 border border-purple-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Type</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $edu->school_type ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-purple-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">GWA</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $edu->grade_ave ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-purple-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Rank</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $edu->rank ? '#' . $edu->rank : 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-purple-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Year</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $edu->year_grad ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <p class="text-sm font-medium">No educational records available</p>
                        </div>
                    @endforelse
                </div>

                <!-- Family Information Section -->
                <div id="family-section" class="grid grid-cols-1 md:grid-cols-2 gap-6 scroll-mt-6">
                    <!-- Father's Information -->
                    <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6">
                        <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            Father's Information
                        </h2>

                        @if($familyFather)
                            <div class="space-y-3">
                                <div class="pb-3 border-b border-slate-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Name</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $familyFather->name ?? 'N/A' }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Status</p>
                                        <p class="text-sm font-medium text-slate-900">{{ $familyFather->status ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Occupation</p>
                                        <p class="text-sm font-medium text-slate-900">{{ $familyFather->occupation ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="pb-3 border-b border-slate-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Address</p>
                                    <p class="text-xs text-slate-700">{{ $familyFather->address ?? 'N/A' }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Education</p>
                                        <p class="text-sm font-medium text-slate-900">{{ $familyFather->educational_attainment ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Income</p>
                                        <p class="text-sm font-medium text-slate-900">
                                            @if($familyFather->income)
                                                ₱{{ number_format((float) $familyFather->income, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @if($familyFather->ethno)
                                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-100">
                                        <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Ethnicity</p>
                                        <p class="text-sm font-bold text-amber-900">{{ $familyFather->ethno->ethnicity ?? 'N/A' }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-center py-4 text-slate-400 text-sm">No information provided</p>
                        @endif
                    </div>

                    <!-- Mother's Information -->
                    <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6">
                        <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                            <div class="w-7 h-7 bg-pink-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            Mother's Information
                        </h2>

                        @if($familyMother)
                            <div class="space-y-3">
                                <div class="pb-3 border-b border-slate-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Name</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $familyMother->name ?? 'N/A' }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Status</p>
                                        <p class="text-sm font-medium text-slate-900">{{ $familyMother->status ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Occupation</p>
                                        <p class="text-sm font-medium text-slate-900">{{ $familyMother->occupation ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="pb-3 border-b border-slate-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Address</p>
                                    <p class="text-xs text-slate-700">{{ $familyMother->address ?? 'N/A' }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Education</p>
                                        <p class="text-sm font-medium text-slate-900">{{ $familyMother->educational_attainment ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Income</p>
                                        <p class="text-sm font-medium text-slate-900">
                                            @if($familyMother->income)
                                                ₱{{ number_format((float) $familyMother->income, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @if($familyMother->ethno)
                                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-100">
                                        <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Ethnicity</p>
                                        <p class="text-sm font-bold text-amber-900">{{ $familyMother->ethno->ethnicity ?? 'N/A' }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-center py-4 text-slate-400 text-sm">No information provided</p>
                        @endif
                    </div>
                </div>

                <!-- Siblings Section -->
                <div id="siblings-section" class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6 scroll-mt-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            Siblings Information
                        </h2>
                        <span class="px-4 py-2 bg-orange-50 text-orange-700 rounded-xl text-sm font-bold border border-orange-100">
                            {{ $siblings->count() }} {{ Str::plural('Sibling', $siblings->count()) }}
                        </span>
                    </div>

                    @forelse($siblings as $index => $sibling)
                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl p-5 mb-4 border border-orange-100">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-black">{{ $index + 1 }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900">{{ $sibling->name ?? 'N/A' }}</h3>
                                        <p class="text-xs text-orange-600 font-medium">Sibling #{{ $index + 1 }}</p>
                                    </div>
                                </div>
                                @if($sibling->present_status)
                                    <span class="px-3 py-1.5 rounded-lg text-xs font-bold
                                        @if(str_contains($sibling->present_status, 'Studying')) bg-emerald-100 text-emerald-700 border border-emerald-200
                                        @elseif(str_contains($sibling->present_status, 'Working')) bg-blue-100 text-blue-700 border border-blue-200
                                        @else bg-slate-100 text-slate-700 border border-slate-200
                                        @endif">
                                        {{ $sibling->present_status }}
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Age</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $sibling->age ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Course/Year</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $sibling->course_year ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Scholarship</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $sibling->scholarship ?? 'None' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Status</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $sibling->present_status ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <p class="text-sm font-medium">No siblings listed</p>
                        </div>
                    @endforelse
                </div>

                <!-- School Preference -->
                @if($schoolPref)
                <div id="school-section" class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6 scroll-mt-6">
                    <h2 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                        <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        School Preference
                    </h2>
                    
                    <div class="space-y-6">
                        <!-- First Choice -->
                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl p-5 border border-orange-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-black text-lg">1</span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900">First Choice</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">School Name</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref->school_name ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">School Address</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref->address ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Course/Degree</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref->degree ?? 'N/A' }}</p>
                                </div>
                                @if($schoolPref->alt_degree)
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Alternative Course</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref->alt_degree }}</p>
                                </div>
                                @endif
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">School Type</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref->school_type ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Duration</p>
                                    <p class="text-sm font-bold text-slate-900">
                                        @if($schoolPref->num_years)
                                            {{ $schoolPref->num_years }} {{ Str::plural('Year', $schoolPref->num_years) }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Second Choice -->
                        @if($schoolPref->school_name2 || $schoolPref->degree2)
                        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-2xl p-5 border border-amber-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-amber-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-black text-lg">2</span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900">Second Choice</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">School Name</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref->school_name2 ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">School Address</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref->address2 ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Course/Degree</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref->degree2 ?? 'N/A' }}</p>
                                </div>
                                @if($schoolPref->alt_degree2)
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Alternative Course</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref->alt_degree2 }}</p>
                                </div>
                                @endif
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">School Type</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref->school_type2 ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Duration</p>
                                    <p class="text-sm font-bold text-slate-900">
                                        @if($schoolPref->num_years2)
                                            {{ $schoolPref->num_years2 }} {{ Str::plural('Year', $schoolPref->num_years2) }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Essay Questions / Goals & Aspirations -->
                @if($schoolPref && ($schoolPref->ques_answer1 || $schoolPref->ques_answer2))
                <div id="essay-section" class="bg-gradient-to-br from-orange-500 to-red-600 rounded-3xl shadow-xl p-6 text-white scroll-mt-6">
                    <h2 class="text-xl font-black mb-6 flex items-center gap-2">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        </div>
                        Goals & Aspirations
                    </h2>

                    @if($schoolPref->ques_answer1)
                        <div class="mb-6">
                            <div class="flex items-start gap-3 mb-3">
                                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-bold uppercase tracking-wider opacity-90 mb-2">How will you contribute to your IP community?</h3>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-base leading-relaxed">{{ $schoolPref->ques_answer1 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($schoolPref->ques_answer2)
                        <div>
                            <div class="flex items-start gap-3 mb-3">
                                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-bold uppercase tracking-wider opacity-90 mb-2">What are your plans after graduation?</h3>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-base leading-relaxed">{{ $schoolPref->ques_answer2 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @endif

            </div>

            <!-- Sticky Navigation Sidebar (3 columns) -->
            <div class="lg:col-span-3">
                <div class="sticky top-6">
                    <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4">Quick Navigation</h3>
                        <nav class="space-y-2">
                            <a href="#documents-section" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-50 transition-all text-sm font-medium text-slate-700 hover:text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Documents
                            </a>
                            <a href="#personal-section" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-50 transition-all text-sm font-medium text-slate-700 hover:text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Personal Info
                            </a>
                            <a href="#address-section" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-50 transition-all text-sm font-medium text-slate-700 hover:text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                Address
                            </a>
                            <a href="#education-section" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-50 transition-all text-sm font-medium text-slate-700 hover:text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                Education
                            </a>
                            <a href="#family-section" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-50 transition-all text-sm font-medium text-slate-700 hover:text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Family
                            </a>
                            <a href="#siblings-section" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-50 transition-all text-sm font-medium text-slate-700 hover:text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Siblings
                            </a>
                            <a href="#school-section" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-50 transition-all text-sm font-medium text-slate-700 hover:text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                School Preference
                            </a>
                            <a href="#essay-section" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-50 transition-all text-sm font-medium text-slate-700 hover:text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                Essay Questions
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Viewer Modal -->
<div id="documentModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-[95vw] h-[95vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <h3 id="modalTitle" class="text-lg font-bold text-slate-900"></h3>
            <button onclick="closeDocumentModal()" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="flex-1 p-4 overflow-hidden">
            <div id="documentViewer" class="w-full h-full rounded-2xl overflow-hidden"></div>
        </div>
        <div class="flex items-center justify-between p-6 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center gap-2">
                <button onclick="downloadDocument()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-all">Download</button>
                <button onclick="printDocument()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition-all">Print</button>
            </div>
            <button onclick="closeDocumentModal()" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-xl text-sm font-bold transition-all">Close</button>
        </div>
    </div>
</div>

<script>
let currentDocumentUrl = '';
let currentDocumentName = '';

// Smooth scroll for navigation links
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Update active state
            document.querySelectorAll('.nav-link').forEach(l => {
                l.classList.remove('bg-orange-50', 'text-orange-600');
            });
            this.classList.add('bg-orange-50', 'text-orange-600');
        }
    });
});

function viewDocument(url, filename, filetype) {
    currentDocumentUrl = url;
    currentDocumentName = filename;
    
    const modal = document.getElementById('documentModal');
    const modalTitle = document.getElementById('modalTitle');
    const documentViewer = document.getElementById('documentViewer');
    
    modalTitle.textContent = filename;
    documentViewer.innerHTML = '';
    
    if (filetype === 'application/pdf') {
        const iframe = document.createElement('iframe');
        iframe.src = url;
        iframe.className = 'w-full h-full border-0 rounded-2xl';
        iframe.title = filename;
        documentViewer.appendChild(iframe);
    } else {
        const message = document.createElement('div');
        message.className = 'flex items-center justify-center h-full text-gray-500';
        message.innerHTML = `
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-lg font-semibold mb-2">Non-PDF File</p>
                <p class="text-sm mb-4">Only PDF files can be previewed</p>
                <button onclick="downloadDocument()" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">Download to View</button>
            </div>
        `;
        documentViewer.appendChild(message);
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDocumentModal() {
    document.getElementById('documentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function updateDocumentStatus(documentId, newStatus) {
    if (!confirm(`Are you sure you want to ${newStatus} this document?`)) return;

    fetch(`/staff/documents/${documentId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Document ${newStatus} successfully!`);
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
    const iframe = document.querySelector('#documentViewer iframe');
    if (iframe) {
        iframe.contentWindow.print();
    }
}

function recalculateDocumentPriorities() {
    if (!confirm('Recalculate document priorities for all pending documents?')) return;

    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    button.disabled = true;

    fetch('{{ route("staff.documents.recalculate-priorities") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Document priorities recalculated successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error recalculating priorities');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function updateApplicationStatus(status) {
    if (!confirm(`Are you sure you want to ${status === 'validated' ? 'approve' : 'set to pending'} this application?`)) return;

    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="w-4 h-4 animate-spin inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
    button.disabled = true;

    fetch('{{ route("staff.applications.update-status", $user->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Application status updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating application status');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

document.getElementById('documentModal').addEventListener('click', function(e) {
    if (e.target === this) closeDocumentModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDocumentModal();
});
</script>
@endsection
