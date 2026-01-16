@extends('layouts.app')

@section('content')
@php
    $data = $archive->data;
    $user = $archive->user; // The user model (might be deleted/modified, but we rely on archive data mostly)
    
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

<div class="min-h-screen bg-slate-50 p-6 md:p-8 font-sans">
    <div class="max-w-[1600px] mx-auto space-y-6">
        
        <!-- Navigation & Breadcrumbs -->
        <nav class="flex items-center text-sm font-medium text-slate-500">
            <a href="{{ route('staff.archives.index') }}" class="hover:text-slate-900 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Archives
            </a>
            <svg class="w-4 h-4 mx-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-slate-900">Record #{{ $archive->id }}</span>
        </nav>

        <!-- Archive Status Banner -->
        <div class="bg-white rounded-xl shadow-sm border-l-4 border-amber-500 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider rounded-full">Archived Record</span>
                    <span class="text-slate-400 text-sm">•</span>
                    <span class="text-slate-600 font-medium text-sm">{{ $archive->archived_at->format('F d, Y \a\t h:i A') }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900">
                    {{ $data['first_name'] ?? '' }} {{ $data['middle_name'] ?? '' }} {{ $data['last_name'] ?? '' }}
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Archived by <span class="font-bold text-slate-700">{{ $archive->archiver->name ?? 'System' }}</span>
                </p>
            </div>
            <div class="flex items-center gap-8 bg-slate-50 px-6 py-4 rounded-xl border border-slate-100">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Reason</p>
                    <p class="font-bold text-slate-900">Applicant Replaced</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Replacement Reason</p>
                    <p class="font-medium text-slate-900 max-w-xs truncate" title="{{ $archive->replacement->replacement_reason ?? '' }}">
                        {{ $archive->replacement->replacement_reason ?? 'N/A' }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">School Year</p>
                    <p class="font-mono font-bold text-slate-900">{{ $archive->replacement->school_year ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Column: Profile & Personal (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Profile Card -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="h-32 bg-gradient-to-r from-slate-800 to-slate-900"></div>
                    <div class="px-6 pb-6 relative">
                        <div class="-mt-16 mb-4">
                            @if(isset($data['profile_pic']) && $data['profile_pic'] !== 'profile_pics/default.png')
                                <!-- We try to use the path if it exists, otherwise default -->
                                <img src="{{ asset('storage/'.$data['profile_pic']) }}" onerror="this.src='{{ asset('images/default-avatar.png') }}'" class="w-32 h-32 rounded-2xl border-4 border-white shadow-md bg-white object-cover">
                            @else
                                <div class="w-32 h-32 rounded-2xl border-4 border-white shadow-md bg-slate-100 flex items-center justify-center text-4xl font-black text-slate-300">
                                    {{ substr($data['first_name'] ?? '', 0, 1) }}{{ substr($data['last_name'] ?? '', 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">{{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</h2>
                                <p class="text-slate-500 font-medium text-sm">{{ $data['email'] ?? 'No email' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 py-4 border-t border-slate-100">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase font-bold">Phone</p>
                                    <p class="text-sm font-semibold text-slate-700">{{ $data['contact_num'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase font-bold">Birthdate</p>
                                    <p class="text-sm font-semibold text-slate-700">{{ $basicInfo['birthdate'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase font-bold">Gender</p>
                                    <p class="text-sm font-semibold text-slate-700">{{ $basicInfo['gender'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase font-bold">Civil Status</p>
                                    <p class="text-sm font-semibold text-slate-700">{{ $basicInfo['civil_status'] ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100">
                                <p class="text-xs text-slate-400 uppercase font-bold mb-2">Ethnicity / IP Group</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-purple-50 text-purple-700 text-sm font-bold">
                                    {{ $ethno['ethnicity'] ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="pt-4 border-t border-slate-100 space-y-3">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase font-bold mb-1">Permanent Address</p>
                                    <p class="text-sm text-slate-700 leading-snug">
                                        {{ $permanent['house_num'] ?? '' }} {{ $permanent['address']['barangay'] ?? '' }}, 
                                        {{ $permanent['address']['municipality'] ?? '' }}, {{ $permanent['address']['province'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Raw Data Toggle -->
                <div x-data="{ showRaw: false }" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <button @click="showRaw = !showRaw" class="flex items-center justify-between w-full text-sm font-bold text-slate-500 hover:text-slate-900">
                        <span>Raw JSON Data</span>
                        <svg class="w-4 h-4" :class="showRaw ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="showRaw" class="mt-4 overflow-hidden">
                        <pre class="text-[10px] bg-slate-900 text-green-400 p-3 rounded-lg overflow-x-auto font-mono max-h-64">{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>

            <!-- Right Column: Data Tables (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Education & School -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h3 class="font-bold text-slate-900">Educational History</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3 font-bold">School Name</th>
                                    <th class="px-6 py-3 font-bold">Type</th>
                                    <th class="px-6 py-3 font-bold">Year Graduated</th>
                                    <th class="px-6 py-3 font-bold">GWA</th>
                                    <th class="px-6 py-3 font-bold">Rank</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($education as $edu)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-slate-900">
                                        {{ $edu['school_name'] ?? 'N/A' }}
                                        <div class="text-xs text-slate-500 font-normal">{{ $edu['category'] ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-3 text-slate-600">{{ $edu['school_type'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 text-slate-600">{{ $edu['year_grad'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 font-bold text-slate-900">{{ $edu['grade_ave'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 text-slate-600">{{ isset($edu['rank']) ? '#'.$edu['rank'] : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-slate-400 italic">No education records found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- School Preference -->
                @if($schoolPref)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h3 class="font-bold text-slate-900">School & Course Preferences</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded bg-slate-900 text-white flex items-center justify-center text-xs font-bold">1</span>
                                <h4 class="font-bold text-slate-900">First Choice</h4>
                            </div>
                            <div class="pl-8 space-y-1">
                                <p class="text-sm font-semibold text-slate-900">{{ $schoolPref['school_name'] ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500">{{ $schoolPref['address'] ?? '' }}</p>
                                <div class="mt-2 p-3 bg-slate-50 rounded-lg border border-slate-100">
                                    <p class="text-xs text-slate-400 uppercase font-bold mb-1">Degree / Course</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $schoolPref['degree'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        @if(isset($schoolPref['school_name2']) || isset($schoolPref['degree2']))
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold">2</span>
                                <h4 class="font-bold text-slate-900">Second Choice</h4>
                            </div>
                            <div class="pl-8 space-y-1">
                                <p class="text-sm font-semibold text-slate-900">{{ $schoolPref['school_name2'] ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500">{{ $schoolPref['address2'] ?? '' }}</p>
                                <div class="mt-2 p-3 bg-slate-50 rounded-lg border border-slate-100">
                                    <p class="text-xs text-slate-400 uppercase font-bold mb-1">Degree / Course</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $schoolPref['degree2'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Family & Siblings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Parents -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-900">Family Background</h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <!-- Father -->
                            <div class="p-4">
                                <p class="text-xs font-bold text-slate-400 uppercase mb-2">Father</p>
                                @if($familyFather)
                                    <p class="font-bold text-slate-900">{{ $familyFather['name'] ?? 'N/A' }}</p>
                                    <p class="text-sm text-slate-600">{{ $familyFather['occupation'] ?? 'N/A' }}</p>
                                    <p class="text-sm text-slate-600 mt-1">Annual Income: <span class="font-medium text-slate-900">₱{{ number_format((float)($familyFather['income'] ?? 0), 2) }}</span></p>
                                @else
                                    <p class="text-sm text-slate-400 italic">No record</p>
                                @endif
                            </div>
                            <!-- Mother -->
                            <div class="p-4">
                                <p class="text-xs font-bold text-slate-400 uppercase mb-2">Mother</p>
                                @if($familyMother)
                                    <p class="font-bold text-slate-900">{{ $familyMother['name'] ?? 'N/A' }}</p>
                                    <p class="text-sm text-slate-600">{{ $familyMother['occupation'] ?? 'N/A' }}</p>
                                    <p class="text-sm text-slate-600 mt-1">Annual Income: <span class="font-medium text-slate-900">₱{{ number_format((float)($familyMother['income'] ?? 0), 2) }}</span></p>
                                @else
                                    <p class="text-sm text-slate-400 italic">No record</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Siblings -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900">Siblings</h3>
                            <span class="text-xs font-bold bg-slate-200 text-slate-600 px-2 py-1 rounded">{{ $siblings->count() }}</span>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-50 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2">Name</th>
                                        <th class="px-4 py-2">Age</th>
                                        <th class="px-4 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($siblings as $sibling)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-slate-900">{{ $sibling['name'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $sibling['age'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-slate-600 text-xs">{{ $sibling['present_status'] ?? 'N/A' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-slate-400 italic">No siblings listed</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-bold text-slate-900">Submitted Documents</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3 font-bold">Document Type</th>
                                    <th class="px-6 py-3 font-bold">Status</th>
                                    <th class="px-6 py-3 font-bold">Submission Date</th>
                                    <th class="px-6 py-3 font-bold text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($requiredTypes as $typeKey => $typeLabel)
                                    @php
                                        $doc = $documents->where('type', $typeKey)->first();
                                        $status = $doc['status'] ?? 'missing';
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-3">
                                            <span class="font-medium text-slate-900">{{ $typeLabel }}</span>
                                        </td>
                                        <td class="px-6 py-3">
                                            @if($status === 'approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                    Approved
                                                </span>
                                            @elseif($status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                    Pending
                                                </span>
                                            @elseif($status === 'rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Rejected
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                                    Missing
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-slate-600">
                                            @if($doc)
                                                {{ \Carbon\Carbon::parse($doc['created_at'])->format('M d, Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            @if($doc)
                                                <a href="{{ route('documents.view', $doc['id']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium text-xs">
                                                    View File
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Essays (if any) -->
                @if($schoolPref && (isset($schoolPref['ques_answer1']) || isset($schoolPref['ques_answer2'])))
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-bold text-slate-900">Goals & Aspirations</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        @if(isset($schoolPref['ques_answer1']))
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">How will you contribute to your IP community?</h4>
                            <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100">
                                {{ $schoolPref['ques_answer1'] }}
                            </p>
                        </div>
                        @endif

                        @if(isset($schoolPref['ques_answer2']))
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">What are your plans after graduation?</h4>
                            <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100">
                                {{ $schoolPref['ques_answer2'] }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
