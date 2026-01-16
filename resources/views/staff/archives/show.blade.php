@extends('layouts.app')

@section('content')
@php
    $data = $archive->data;
    // Helper to safely get nested data
    $get = function($key, $default = null) use ($data) {
        return data_get($data, $key, $default);
    };

    $basicInfo = $data['basic_info'] ?? [];
    $documents = collect($data['documents'] ?? []);
    
    // Address data
    $fullAddress = $basicInfo['full_address'] ?? [];
    $mailing = $fullAddress['mailing_address'] ?? null;
    $permanent = $fullAddress['permanent_address'] ?? null;
    $origin = $fullAddress['origin'] ?? null;

    // Education data
    $education = collect($basicInfo['education'] ?? []);

    // Family data
    $family = collect($basicInfo['family'] ?? []);
    $familyFather = $family->where('fam_type', 'father')->first() ?? null;
    $familyMother = $family->where('fam_type', 'mother')->first() ?? null;

    // Siblings data
    $siblings = collect($basicInfo['siblings'] ?? []);

    // School preference data
    $schoolPref = $basicInfo['school_pref'] ?? null;
    
    // Ethno
    $ethno = $data['ethno'] ?? null;

    $requiredTypes = [
        'form' => 'Application Form',
        'pic_2x2' => '2x2 Picture',
        'brgy_cert' => 'Barangay Indigency',
        'cor' => 'Cert. of Registration',
        'grades' => 'Report Card / Grades',
        'valid_id' => 'Valid ID'
    ];
@endphp

<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 p-6 md:p-8 font-sans">
    
    <div class="max-w-[1600px] mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('staff.archives.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-orange-600 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Archives
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                        Archived Data: {{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">
                        Archived on {{ $archive->archived_at->format('F d, Y \a\t h:i A') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Detailed Info -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Required Documents -->
                <div x-data="{ expanded: true }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-6 bg-slate-50 hover:bg-slate-100 transition-colors">
                        <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            Required Documents
                        </h3>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="expanded" class="p-6 border-t border-slate-200">
                        <div class="space-y-3">
                            @foreach($requiredTypes as $typeKey => $typeLabel)
                                @php
                                    $doc = $documents->where('type', $typeKey)->first();
                                    $status = $doc['status'] ?? 'missing';
                                @endphp
                                <div class="space-y-2 p-4 rounded-2xl border-2 {{ 
                                    $status === 'approved' ? 'bg-gradient-to-r from-emerald-50 to-green-50 border-emerald-200' : 
                                    ($status === 'pending' ? 'bg-gradient-to-r from-amber-50 to-orange-50 border-amber-200' : 
                                    ($status === 'rejected' ? 'bg-gradient-to-r from-red-50 to-rose-50 border-red-200' : 'bg-slate-50 border-slate-200 opacity-60')) 
                                }}">
                                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                        <div class="flex items-center gap-3 w-full sm:w-auto flex-1">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{
                                                $status === 'approved' ? 'bg-emerald-500' : 
                                                ($status === 'pending' ? 'bg-amber-500' : 
                                                ($status === 'rejected' ? 'bg-red-500' : 'bg-slate-300'))
                                            }}">
                                                @if($status === 'approved')
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @elseif($status === 'pending')
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @else
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-bold text-slate-900 text-sm">{{ $typeLabel }}</h4>
                                                @if($doc)
                                                    <p class="text-xs font-medium {{
                                                        $status === 'approved' ? 'text-emerald-600' : 
                                                        ($status === 'pending' ? 'text-amber-600' : 'text-red-600')
                                                    }}">
                                                        {{ ucfirst($status) }} • Submitted {{ \Carbon\Carbon::parse($doc['created_at'])->diffForHumans() }}
                                                    </p>
                                                @else
                                                    <p class="text-xs text-slate-500 font-medium">Not submitted</p>
                                                @endif
                                            </div>
                                        </div>
                                        @if($doc)
                                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end flex-shrink-0">
                                                <!-- Note: File access might fail if file was deleted, but we attempt to link to it -->
                                                <a href="{{ route('documents.view', $doc['id']) }}" target="_blank" class="px-4 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold rounded-lg text-xs transition-all flex items-center gap-2">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    Download / View
                                                </a>
                                            </div>
                                        @else
                                            <span class="px-3 py-1.5 bg-slate-200 text-slate-600 font-bold rounded-lg text-xs">Missing</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div x-data="{ expanded: true }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-6 bg-slate-50 hover:bg-slate-100 transition-colors">
                        <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            Personal Information
                        </h3>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="expanded" class="p-6 border-t border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Email</p>
                                <p class="text-sm font-medium text-slate-900 break-all">{{ $data['email'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Contact</p>
                                <p class="text-sm font-medium text-slate-900">{{ $data['contact_num'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Birthdate</p>
                                <p class="text-sm font-medium text-slate-900">{{ $basicInfo['birthdate'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Gender</p>
                                <p class="text-sm font-medium text-slate-900">{{ $basicInfo['gender'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Civil Status</p>
                                <p class="text-sm font-medium text-slate-900">{{ $basicInfo['civil_status'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Birthplace</p>
                                <p class="text-sm font-medium text-slate-900">{{ $basicInfo['birthplace'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div x-data="{ expanded: true }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-6 bg-slate-50 hover:bg-slate-100 transition-colors">
                        <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                            <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            </div>
                            Address Information
                        </h3>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="expanded" class="p-6 border-t border-slate-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($mailing && isset($mailing['address']))
                            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                                <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    Mailing Address
                                </p>
                                <p class="text-sm text-slate-700">
                                    {{ $mailing['house_num'] ?? '' }}, 
                                    {{ $mailing['address']['barangay'] ?? '' }}, 
                                    {{ $mailing['address']['municipality'] ?? '' }}, 
                                    {{ $mailing['address']['province'] ?? '' }}
                                </p>
                            </div>
                            @endif
                            @if($permanent && isset($permanent['address']))
                            <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                                <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    Permanent Address
                                </p>
                                <p class="text-sm text-slate-700">
                                    {{ $permanent['house_num'] ?? '' }}, 
                                    {{ $permanent['address']['barangay'] ?? '' }}, 
                                    {{ $permanent['address']['municipality'] ?? '' }}, 
                                    {{ $permanent['address']['province'] ?? '' }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Education -->
                <div x-data="{ expanded: true }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-6 bg-slate-50 hover:bg-slate-100 transition-colors">
                        <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            Educational Background
                        </h3>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="expanded" class="p-6 border-t border-slate-200">
                        @forelse($education as $index => $edu)
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-5 mb-4 border border-purple-100">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-black">{{ $index + 1 }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-slate-900">{{ $edu['school_name'] ?? 'N/A' }}</h3>
                                        <p class="text-xs text-purple-600 font-medium uppercase">{{ $edu['category'] ?? 'N/A' }}</p>
                                    </div>
                                    @if(isset($edu['year_grad']))
                                        <span class="px-3 py-1.5 bg-purple-600 text-white text-xs font-bold rounded-lg">{{ $edu['year_grad'] }}</span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                    <div class="bg-white rounded-xl p-3 border border-purple-100">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Type</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $edu['school_type'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="bg-white rounded-xl p-3 border border-purple-100">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">GWA</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $edu['grade_ave'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="bg-white rounded-xl p-3 border border-purple-100">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Rank</p>
                                        <p class="text-sm font-bold text-slate-900">{{ isset($edu['rank']) ? '#' . $edu['rank'] : 'N/A' }}</p>
                                    </div>
                                    <div class="bg-white rounded-xl p-3 border border-purple-100">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Year</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $edu['year_grad'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-500">
                                <p class="text-sm font-medium">No educational records available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Family Information -->
                <div x-data="{ expanded: true }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-6 bg-slate-50 hover:bg-slate-100 transition-colors">
                        <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            Family Information
                        </h3>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="expanded" class="p-6 border-t border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Father -->
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <h4 class="font-bold text-slate-900 mb-3 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span> Father's Info
                            </h4>
                            @if($familyFather)
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase">Name</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $familyFather['name'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-xs font-bold text-slate-500 uppercase">Status</p>
                                            <p class="text-sm text-slate-900">{{ $familyFather['status'] ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-500 uppercase">Occupation</p>
                                            <p class="text-sm text-slate-900">{{ $familyFather['occupation'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase">Income</p>
                                        <p class="text-sm text-slate-900">₱{{ number_format((float)($familyFather['income'] ?? 0), 2) }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-slate-400 italic">No information provided</p>
                            @endif
                        </div>

                        <!-- Mother -->
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <h4 class="font-bold text-slate-900 mb-3 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-pink-500"></span> Mother's Info
                            </h4>
                            @if($familyMother)
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase">Name</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $familyMother['name'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-xs font-bold text-slate-500 uppercase">Status</p>
                                            <p class="text-sm text-slate-900">{{ $familyMother['status'] ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-500 uppercase">Occupation</p>
                                            <p class="text-sm text-slate-900">{{ $familyMother['occupation'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase">Income</p>
                                        <p class="text-sm text-slate-900">₱{{ number_format((float)($familyMother['income'] ?? 0), 2) }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-slate-400 italic">No information provided</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Siblings -->
                <div x-data="{ expanded: true }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-6 bg-slate-50 hover:bg-slate-100 transition-colors">
                        <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            Siblings Information
                            <span class="ml-2 px-2 py-0.5 bg-orange-100 text-orange-700 text-xs rounded-full">{{ $siblings->count() }}</span>
                        </h3>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="expanded" class="p-6 border-t border-slate-200">
                        @forelse($siblings as $index => $sibling)
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl p-4 mb-3 border border-orange-100">
                                <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-4 mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-slate-900">{{ $sibling['name'] ?? 'N/A' }}</h3>
                                            <p class="text-xs text-orange-600 font-medium">{{ $sibling['present_status'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase">Age</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $sibling['age'] ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase">Course/Year</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $sibling['course_year'] ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase">Scholarship</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $sibling['scholarship'] ?? 'None' }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 text-sm">No siblings listed</p>
                        @endforelse
                    </div>
                </div>

                <!-- School Preference -->
                @if($schoolPref)
                <div x-data="{ expanded: true }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-6 bg-slate-50 hover:bg-slate-100 transition-colors">
                        <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            School Preference
                        </h3>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div x-show="expanded" class="p-6 border-t border-slate-200 space-y-6">
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
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref['school_name'] ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">School Address</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref['address'] ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Course/Degree</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref['degree'] ?? 'N/A' }}</p>
                                </div>
                                @if(isset($schoolPref['alt_degree']))
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Alternative Course</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref['alt_degree'] }}</p>
                                </div>
                                @endif
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">School Type</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref['school_type'] ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-orange-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Duration</p>
                                    <p class="text-sm font-bold text-slate-900">
                                        @if(isset($schoolPref['num_years']))
                                            {{ $schoolPref['num_years'] }} {{ Str::plural('Year', $schoolPref['num_years']) }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Second Choice -->
                        @if(isset($schoolPref['school_name2']) || isset($schoolPref['degree2']))
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
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref['school_name2'] ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">School Address</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref['address2'] ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Course/Degree</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref['degree2'] ?? 'N/A' }}</p>
                                </div>
                                @if(isset($schoolPref['alt_degree2']))
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Alternative Course</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref['alt_degree2'] }}</p>
                                </div>
                                @endif
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">School Type</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $schoolPref['school_type2'] ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-amber-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Duration</p>
                                    <p class="text-sm font-bold text-slate-900">
                                        @if(isset($schoolPref['num_years2']))
                                            {{ $schoolPref['num_years2'] }} {{ Str::plural('Year', $schoolPref['num_years2']) }}
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
                @if($schoolPref && (isset($schoolPref['ques_answer1']) || isset($schoolPref['ques_answer2'])))
                <div x-data="{ expanded: true }" class="bg-gradient-to-br from-orange-500 to-red-600 rounded-3xl shadow-xl overflow-hidden">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-6 hover:bg-white/5 transition-colors text-white">
                        <h2 class="text-xl font-black flex items-center gap-2">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            </div>
                            Goals & Aspirations
                        </h2>
                        <svg class="w-5 h-5 text-white/70 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="expanded" class="p-6 pt-0 text-white">
                        @if(isset($schoolPref['ques_answer1']))
                            <div class="mb-6">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-bold uppercase tracking-wider opacity-90 mb-2">How will you contribute to your IP community?</h3>
                                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                            <p class="text-base leading-relaxed">{{ $schoolPref['ques_answer1'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($schoolPref['ques_answer2']))
                            <div>
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-bold uppercase tracking-wider opacity-90 mb-2">What are your plans after graduation?</h3>
                                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                            <p class="text-base leading-relaxed">{{ $schoolPref['ques_answer2'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- JSON Data Dump (Collapsed) -->
                <div x-data="{ expanded: false }" class="bg-white rounded-2xl shadow-sm border border-slate-200">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-6 bg-slate-50 hover:bg-slate-100 transition-colors rounded-t-2xl">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            Raw Data Snapshot
                        </h3>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="expanded" class="p-6 border-t border-slate-200">
                        <div class="bg-slate-900 rounded-xl p-4 overflow-x-auto">
                            <pre class="text-xs text-green-400 font-mono">{{ json_encode($archive->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Meta Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-6">
                    <h3 class="font-bold text-slate-900 text-base mb-4">Archive Metadata</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Archived By</label>
                            <div class="text-sm font-medium text-slate-900">{{ $archive->archiver->name ?? 'System' }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Replacement Reason</label>
                            <div class="text-sm font-medium text-slate-900">{{ $archive->replacement->replacement_reason ?? 'N/A' }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Archive Reason</label>
                            <div class="text-sm font-medium text-slate-900">Applicant was replaced</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Replacement ID</label>
                            <div class="text-sm font-medium text-slate-900">#{{ $archive->replacement_id ?? 'N/A' }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">School Year</label>
                            <div class="text-sm font-medium text-slate-900">{{ $archive->replacement->school_year ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
