@extends('layouts.student')

@section('title', 'Scholarship Application - IP Scholar Portal')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
        body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        color: #334155;
    }
    
    .form-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .step-nav {
        position: sticky;
        top: 6rem;
    }

    .step-item {
        position: relative;
        padding-left: 3rem;
        padding-bottom: 2rem;
        border-left: 2px solid #e2e8f0;
    }

    .step-item:last-child {
        border-left: none;
        padding-bottom: 0;
    }

    .step-number {
        position: absolute;
        left: -1.05rem;
        top: 0;
        width: 2rem;
        height: 2rem;
        background-color: #fff;
        border: 2px solid #cbd5e1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        color: #64748b;
        transition: all 0.3s ease;
    }

    .step-item.active .step-number {
        background-color: #ea580c;
        border-color: #ea580c;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.1);
    }

    .step-item.completed .step-number {
        background-color: #22c55e;
        border-color: #22c55e;
            color: #fff;
    }
    
    .step-item.completed {
        border-left-color: #22c55e;
    }

    .step-content {
        margin-top: -0.25rem;
    }

    .step-title {
        font-weight: 600;
        font-size: 1rem;
        color: #94a3b8;
        margin-bottom: 0.25rem;
        transition: color 0.3s ease;
    }

    .step-item.active .step-title {
        color: #0f172a;
    }
    
    .step-item.completed .step-title {
        color: #0f172a;
    }

    .step-desc {
        font-size: 0.875rem;
        color: #94a3b8;
    }

    .main-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1);
        border: 1px solid #f1f5f9;
    }

    .form-header {
        padding: 2rem;
        border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
        justify-content: space-between;
    }

    .form-body {
        padding: 2.5rem;
    }
    
    .form-footer {
        padding: 1.5rem 2.5rem;
        background-color: #f8fafc;
        border-top: 1px solid #f1f5f9;
        border-radius: 0 0 1rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .input-group {
        margin-bottom: 1.5rem;
    }

    .input-label {
        display: block;
        font-size: 0.875rem;
            font-weight: 500;
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        color: #1e293b;
        background-color: #fff;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
    }

    .btn {
        display: inline-flex;
            align-items: center;
            justify-content: center;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #ea580c;
            color: #fff;
        border: 1px solid transparent;
    }

    .btn-primary:hover {
        background-color: #c2410c;
    }

    .btn-outline {
        background-color: #fff;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .btn-outline:hover {
        background-color: #f8fafc;
        color: #1e293b;
        border-color: #94a3b8;
    }

    .section-heading {
        font-size: 1.25rem;
            font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .section-heading svg {
        color: #ea580c;
    }

    /* Animations */
    .fade-enter {
        opacity: 0;
        transform: translateY(10px);
    }
    .fade-enter-active {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.3s ease, transform 0.3s ease;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen pt-24 pb-20">
    <!-- Application Hub (Landing) -->
    <div id="application-hub" class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Application Hub</h1>
                <p class="text-slate-500 text-sm mt-1">Manage your scholarship applications</p>
    </div>
                    </div>

        <!-- Start New Section (Google Docs Style) -->
        <div class="bg-slate-50 rounded-xl p-8 mb-10 border border-slate-200">
            <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-4">Start a new application</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <!-- New Application Card -->
                <button type="button" onclick="startNewApplication()" class="group text-left">
                    <div class="aspect-[3/4] bg-white rounded-lg border border-slate-200 shadow-sm group-hover:border-orange-400 group-hover:shadow-md transition-all flex items-center justify-center mb-3 relative overflow-hidden">
                        <div class="w-12 h-12 rounded-full bg-white border border-slate-100 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    </div>
                    <div class="font-medium text-slate-900 text-sm group-hover:text-orange-700 transition-colors">Scholarship Application</div>
                    <div class="text-xs text-slate-500">NCIP Educational Assistance</div>
                </button>
                    </div>
                    </div>

        <!-- Recent Documents Section -->
        <div>
            <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-4">Recent drafts</h2>
            <div id="hub-recent-drafts" class="grid grid-cols-1 gap-4">
                <!-- Drafts populated by JS -->
                <div class="text-center py-12 bg-white rounded-xl border border-dashed border-slate-300 text-slate-400 text-sm">
                    No recent drafts found
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container (Hidden Initially) -->
    <div id="application-form-view" class="form-container hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Sidebar: Navigation -->
            <div class="lg:col-span-3">
                <div class="step-nav">
                    <div class="mb-8">
                        <button onclick="returnToHub()" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-orange-600 transition-colors mb-4">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Back to Hub
                        </button>
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" alt="NCIP" class="h-10 w-10">
                    <div>
                                <h1 class="text-lg font-bold text-slate-900 leading-tight">Scholarship<br>Application</h1>
                    </div>
                    </div>
                        <p class="text-sm text-slate-500 mt-2">Complete all steps to submit your application.</p>
                </div>

                    <div class="steps-wrapper">
                        @foreach([
                            ['title' => 'Personal Information', 'desc' => 'Basic details'],
                            ['title' => 'Address Details', 'desc' => 'Contact info'],
                            ['title' => 'Education', 'desc' => 'Academic history'],
                            ['title' => 'Family Background', 'desc' => 'Parent & sibling info'],
                            ['title' => 'School Preference', 'desc' => 'Intended studies'],
                            ['title' => 'Document Requirements', 'desc' => 'Upload files']
                        ] as $index => $step)
                        <div class="step-item {{ $index === 0 ? 'active' : '' }}" id="nav-step-{{ $index + 1 }}">
                            <div class="step-number">
                                @if($index === 0) 1 @else {{ $index + 1 }} @endif
                            </div>
                            <div class="step-content">
                                <div class="step-title">{{ $step['title'] }}</div>
                                <div class="step-desc">{{ $step['desc'] }}</div>
                            </div>
                        </div>
                        @endforeach
            </div>

                    <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Need Assistance?</p>
                                <p class="text-xs text-blue-700 mt-1">Contact support at (02) 888-1234 or email support@ncip.gov.ph</p>
                </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content: Form -->
            <div class="lg:col-span-9">
                <form method="POST" action="{{ route('student.apply') }}" id="applicationForm" class="main-card" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Header -->
                    <div class="form-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900" id="form-section-title">Personal Information</h2>
                            <p class="text-sm text-slate-500 mt-1">Step <span id="current-step-num">1</span> of 6</p>
                            <p class="text-xs text-slate-400 mt-2">Your progress auto-saves on this device.</p>
                </div>
                        <button type="button" id="clearDraftBtn" class="text-xs font-semibold text-slate-400 hover:text-orange-600 transition-colors">
                            Clear saved draft
                        </button>
                    </div>

                    <!-- Error Display -->
                @if ($errors->any())
                        <div class="p-4 mx-8 mt-6 bg-red-50 border border-red-100 rounded-lg">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                        </div>
                        </div>
                    @endif

                    <!-- Body -->
                    <div class="form-body">
                        <!-- Step 1 -->
                        <div class="form-step" id="step-1">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="input-label">Type of Assistance <span class="text-red-500">*</span></label>
                                    <div class="flex gap-6 p-4 border border-slate-200 rounded-lg bg-slate-50/50">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="type_of_assistance[]" value="Regular" class="w-4 h-4 text-orange-600 border-slate-300 rounded focus:ring-orange-500 check-assist" {{ in_array('Regular', old('type_of_assistance', [])) ? 'checked' : '' }}>
                                            <span class="ml-2 text-slate-700">Regular Scholarship</span>
                                </label>
                                        <label class="flex items-center cursor-not-allowed">
                                            <input type="checkbox" name="type_of_assistance[]" value="Merit-Based" class="w-4 h-4 text-orange-600 border-slate-300 rounded focus:ring-orange-500 check-assist" disabled>
                                            <span class="ml-2 text-slate-400">Merit-Based <span class="text-xs">(Locked)</span></span>
                                    </label>
                                        <label class="flex items-center cursor-not-allowed">
                                            <input type="checkbox" name="type_of_assistance[]" value="PDAF" class="w-4 h-4 text-orange-600 border-slate-300 rounded focus:ring-orange-500 check-assist" disabled>
                                            <span class="ml-2 text-slate-400">PDAF <span class="text-xs">(Locked)</span></span>
                                    </label>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="input-label">Assistance For</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 border border-slate-200 rounded-lg bg-white">
                                        @foreach (['Post-study' => true, 'College' => false, 'Highschool' => true, 'Elementary' => true] as $option => $locked)
                                            <label class="flex items-center text-sm text-slate-{{ $locked ? '400 cursor-not-allowed' : '700' }}">
                                                <input type="checkbox" name="assistance_for[]" value="{{ $option }}" class="w-4 h-4 text-orange-600 border-slate-300 rounded focus:ring-orange-500" {{ $locked ? 'disabled' : '' }} {{ in_array($option, old('assistance_for', [])) ? 'checked' : '' }}>
                                                <span class="ml-2">{{ $option }} @if($locked)<span class="text-xs text-slate-400">(Locked)</span>@endif</span>
                                    </label>
                                        @endforeach
                                </div>
                            </div>

                                <div class="input-group">
                                    <label class="input-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control bg-slate-50" required value="{{ old('first_name', auth()->user()->first_name ?? '') }}" readonly>
                            </div>
                                <div class="input-group">
                                    <label class="input-label">Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control bg-slate-50" value="{{ old('middle_name', auth()->user()->middle_name ?? '') }}" readonly>
                            </div>
                                <div class="input-group">
                                    <label class="input-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control bg-slate-50" required value="{{ old('last_name', auth()->user()->last_name ?? '') }}" readonly>
                            </div>
                                <div class="input-group">
                                    <label class="input-label">Email Address</label>
                                    <input type="email" name="email" class="form-control bg-slate-50" required value="{{ old('email', auth()->user()->email ?? '') }}" readonly>
                            </div>
                                <div class="input-group">
                                    <label class="input-label">Contact Number</label>
                                    <input type="text" name="contact_num" class="form-control bg-slate-50" required value="{{ old('contact_num', auth()->user()->contact_num ?? '') }}" readonly>
                            </div>
                                <div class="input-group">
                                    <label class="input-label">Date of Birth</label>
                                    <input type="date" name="birthdate" class="form-control" required value="{{ old('birthdate') }}">
                            </div>
                                <div class="input-group">
                                    <label class="input-label">Place of Birth</label>
                                    <input type="text" name="birthplace" class="form-control" required value="{{ old('birthplace') }}">
                            </div>
                                <div class="input-group">
                                    <label class="input-label">Gender</label>
                                    <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                                <div class="input-group">
                                    <label class="input-label">Civil Status</label>
                                    <select name="civil_status" class="form-control" required>
                                        <option value="">Select Status</option>
                                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                </select>
                            </div>
                                <div class="input-group">
                                    <label class="input-label">Ethnolinguistic Group</label>
                                    @php $ethno = $ethnicities->firstWhere('id', auth()->user()->ethno_id); @endphp
                                    <input type="text" class="form-control bg-slate-50" value="{{ $ethno->ethnicity ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>

                        <!-- Step 2 -->
                        <div class="form-step hidden" id="step-2">
                            @foreach(['mailing' => 'Mailing Address', 'permanent' => 'Permanent Address', 'origin' => 'Place of Origin'] as $prefix => $title)
                            <div class="mb-8 {{ !$loop->last ? 'pb-8 border-b border-slate-100' : '' }}">
                                <h3 class="section-heading">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657L12 22l5.657-5.343z"/></svg>
                                    {{ $title }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="input-group">
                                        <label class="input-label">Province</label>
                                        <input type="text" name="{{ $prefix }}_province" class="form-control bg-slate-50" value="Davao del Sur" readonly>
                        </div>
                                    <div class="input-group">
                                        <label class="input-label">Municipality</label>
                                        <select name="{{ $prefix }}_municipality" class="form-control muni-select" data-prefix="{{ $prefix }}" required>
                                <option value="">Select Municipality</option>
                                @foreach($municipalities as $municipality)
                                                <option value="{{ $municipality }}" {{ old($prefix.'_municipality') == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                                @endforeach
                            </select>
                                    </div>
                                    <div class="input-group">
                                        <label class="input-label">Barangay</label>
                                        <select name="{{ $prefix }}_barangay" class="form-control brgy-select" id="{{ $prefix }}_barangay" required>
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                                <option value="{{ $barangay }}" {{ old($prefix.'_barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                @endforeach
                            </select>
                        </div>
                                    <div class="input-group">
                                        <label class="input-label">House No. / Street</label>
                                        <input type="text" name="{{ $prefix }}_house_num" class="form-control" value="{{ old($prefix.'_house_num') }}">
                                    </div>
                                </div>
                            </div>
                                @endforeach
                        </div>

                        <!-- Step 3 -->
                        <div class="form-step hidden" id="step-3">
                            @php
                                $yearOptions = range((int)date('Y'), (int)date('Y') - 60);
                                $gwaOptions = range(75, 100);
                                $rankOptions = [
                                    'Valedictorian','Salutatorian','With Highest Honors','With High Honors','With Honors',
                                    'Top 10','Dean\'s Lister','Academic Awardee','None'
                                ];
                            @endphp
                            <div class="space-y-8">
                                @foreach([
                                    ['key' => 'elem', 'label' => 'Elementary', 'cat' => 1, 'required' => true],
                                    ['key' => 'hs', 'label' => 'High School', 'cat' => 2, 'required' => true],
                                    ['key' => 'voc', 'label' => 'Vocational', 'cat' => 3, 'required' => false],
                                    ['key' => 'college', 'label' => 'College', 'cat' => 4, 'required' => false],
                                    ['key' => 'masteral', 'label' => 'Masteral', 'cat' => 5, 'required' => false],
                                    ['key' => 'doctorate', 'label' => 'Doctorate', 'cat' => 6, 'required' => false]
                                ] as $level)
                                <div class="p-5 border border-slate-200 rounded-lg bg-slate-50/30">
                                    <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xs font-bold">{{ $level['cat'] }}</span>
                                        {{ $level['label'] }}
                                        @if($level['required']) <span class="text-red-500">*</span> @endif
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="input-label">School Name</label>
                                            <input type="text" name="{{ $level['key'] }}_school" class="form-control" value="{{ old($level['key'].'_school') }}" {{ $level['required'] ? 'required' : '' }}>
                                        </div>
                                        <div>
                                            <label class="input-label">Type</label>
                                            <select name="{{ $level['key'] }}_type" class="form-control" {{ $level['required'] ? 'required' : '' }}>
                                                <option value="">Select</option>
                                                <option value="Public" {{ old($level['key'].'_type') == 'Public' ? 'selected' : '' }}>Public</option>
                                                <option value="Private" {{ old($level['key'].'_type') == 'Private' ? 'selected' : '' }}>Private</option>
                            </select>
                                        </div>
                                        <div>
                                            <label class="input-label">Year Graduated</label>
                                            <select name="{{ $level['key'] }}_year" class="form-control" {{ $level['required'] ? 'required' : '' }}>
                                                <option value="">Select Year</option>
                                                @foreach($yearOptions as $year)
                                                    <option value="{{ $year }}" {{ old($level['key'].'_year') == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                @endforeach
                            </select>
                                        </div>
                                        <div>
                                            <label class="input-label">GWA</label>
                                            <select name="{{ $level['key'] }}_avg" class="form-control" {{ $level['required'] ? 'required' : '' }}>
                                                <option value="">Select GWA</option>
                                                @foreach($gwaOptions as $gwa)
                                                    <option value="{{ $gwa }}" {{ old($level['key'].'_avg') == $gwa ? 'selected' : '' }}>
                                                        {{ $gwa }}
                                                    </option>
                                @endforeach
                            </select>
                        </div>
                                        <div>
                                            <label class="input-label">Rank/Honors</label>
                                            <select name="{{ $level['key'] }}_rank" class="form-control">
                                                <option value="">Select Rank/Honor</option>
                                                @foreach($rankOptions as $rank)
                                                    <option value="{{ $rank }}" {{ old($level['key'].'_rank') == $rank ? 'selected' : '' }}>
                                                        {{ $rank }}
                                                    </option>
                                @endforeach
                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                        </div>
                    </div>

                        <!-- Step 4 -->
                        <div class="form-step hidden" id="step-4">
                            <h3 class="section-heading">Parents Information</h3>
                            
                            @foreach(['father' => 'Father', 'mother' => 'Mother'] as $parent => $label)
                            <div class="mb-8 pb-8 border-b border-slate-100">
                                <h4 class="font-semibold text-slate-700 mb-4 uppercase text-sm tracking-wide">{{ $label }}'s Details</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="input-label">Status</label>
                                        <div class="flex gap-4">
                                            <label class="flex items-center">
                                                <input type="radio" name="{{ $parent }}_status" value="Living" class="text-orange-600 focus:ring-orange-500" checked>
                                                <span class="ml-2 text-sm">Living</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" name="{{ $parent }}_status" value="Deceased" class="text-orange-600 focus:ring-orange-500">
                                                <span class="ml-2 text-sm">Deceased</span>
                                            </label>
                        </div>
                            </div>
                                    <div class="input-group">
                                        <label class="input-label">Name</label>
                                        <input type="text" name="{{ $parent }}_name" class="form-control">
                            </div>
                                    <div class="md:col-span-2">
                                    <label class="input-label">Address</label>
                                    <input type="text" name="{{ $parent }}_address" class="form-control">
                                </div>
                                    <div class="input-group">
                                        <label class="input-label">Occupation</label>
                                        <input type="text" name="{{ $parent }}_occupation" class="form-control">
                                </div>
                                    <div class="input-group">
                                        <label class="input-label">Educational Attainment</label>
                                        <input type="text" name="{{ $parent }}_education" class="form-control">
                                </div>
                                    <div class="md:col-span-2">
                                    <label class="input-label">Office Address</label>
                                    <input type="text" name="{{ $parent }}_office_address" class="form-control">
                            </div>
                                    <div class="input-group">
                                        <label class="input-label">Annual Income</label>
                                        <input type="text" name="{{ $parent }}_income" class="form-control">
                            </div>
                                    <div class="input-group">
                                        <label class="input-label">IP Group</label>
                                        <select name="{{ $parent }}_ethno" class="form-control">
                                            <option value="">Select</option>
                                            @foreach($ethnicities as $ethno)
                                                <option value="{{ $ethno->id }}">{{ $ethno->ethnicity }}</option>
                                            @endforeach
                                </select>
                            </div>
                                </div>
                                </div>
                            @endforeach

                            <h3 class="section-heading flex items-center justify-between">
                                <span>Siblings</span>
                                <button type="button" class="btn btn-outline btn-sm" onclick="openSiblingModal()">+ Add Sibling</button>
                            </h3>
                            <div id="siblings-list" class="space-y-4 mb-4">
                                <p id="siblings-empty" class="p-6 text-sm text-slate-500 text-center border border-dashed border-slate-300 rounded-2xl">No siblings added yet.</p>
                                </div>
                            </div>

                        <!-- Step 5 -->
                        <div class="form-step hidden" id="step-5">
                            <h3 class="section-heading">School Preference</h3>
                            
@php
    $courseOptions = [
        'Agriculture',
        'Aqua-Culture and Fisheries',
        'Anthropology',
        'Business Administration (Accounting, Marketing, Management, Economics, Entrepreneurship)',
        'Civil Engineering',
        'Community Development',
        'Criminology',
        'Education',
        'Foreign Service',
        'Forestry and Environment Studies (Forestry, Environmental Science, Agro-Forestry)',
        'Geodetic Engineering',
        'Geology',
        'Law',
        'Medicine and Allied Health Sciences (Nursing, Midwifery, Medical Technology, etc.)',
        'Mechanical Engineering',
        'Mining Engineering',
        'Social Sciences (AB courses)',
        'Social Work',
        'BS Information Technology',
        'BS Computer Science',
        'BS Accountancy',
        'BS Nursing',
        'BS Education',
        'BA Political Science',
        'Other',
    ];
@endphp
@foreach(['school1' => 'First Choice', 'school2' => 'Second Choice'] as $key => $label)
                            <div class="p-5 border border-slate-200 rounded-lg bg-slate-50/50 mb-6">
                                <h4 class="font-semibold text-slate-800 mb-4">{{ $label }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                        <label class="input-label">School Name</label>
                                        <input type="text" name="{{ $key }}_name" class="form-control" value="{{ old($key.'_name') }}" required>
                            </div>
                            <div>
                                        <label class="input-label">School Address</label>
                                        <input type="text" name="{{ $key }}_address" class="form-control" value="{{ old($key.'_address') }}" required>
                                    </div>
                                    <div>
                                        <label class="input-label">Course/Degree (Primary)</label>
                                        <select name="{{ $key }}_course1" class="form-control" required>
                                            <option value="">Select Course</option>
                                            @foreach($courseOptions as $course)
                                                <option value="{{ $course }}" {{ old($key.'_course1') == $course ? 'selected' : '' }}>{{ $course }}</option>
                                            @endforeach
                                </select>
                            </div>
                            <div>
                                        <label class="input-label">Course/Degree (Alternate)</label>
                                        <select name="{{ $key }}_course_alt" class="form-control">
                                            <option value="">Select Course</option>
                                            @foreach($courseOptions as $course)
                                                <option value="{{ $course }}" {{ old($key.'_course_alt') == $course ? 'selected' : '' }}>{{ $course }}</option>
                                            @endforeach
                                        </select>
                            </div>
                            <div>
                                        <label class="input-label">Type</label>
                                        <select name="{{ $key }}_type" class="form-control" required>
                                            <option value="Public" {{ old($key.'_type') == 'Public' ? 'selected' : '' }}>Public</option>
                                            <option value="Private" {{ old($key.'_type') == 'Private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                                    <div>
                                        <label class="input-label">Duration (Years)</label>
                                        <input type="text" name="{{ $key }}_years" class="form-control" value="{{ old($key.'_years') }}" required>
                                </div>
                                </div>
                                </div>
                            @endforeach

                            <div class="mt-8">
                                <h3 class="section-heading">Essay</h3>
                                <div class="space-y-6">
                            <div>
                                        <label class="input-label">How will you contribute to your IP community?</label>
                                        <textarea name="contribution" class="form-control" rows="4" required></textarea>
                            </div>
                            <div>
                                        <label class="input-label">What are your plans after graduation?</label>
                                        <textarea name="plans_after_grad" class="form-control" rows="4" required></textarea>
                            </div>
                                </div>
                                </div>
                                </div>
                        
                        <!-- Step 6: Document Requirements -->
                        <div class="form-step hidden" id="step-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="section-heading mb-0">Document Requirements</h3>
                            </div>

                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-8 flex gap-3">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <span class="font-semibold">Upload Instructions:</span> Please upload clear PDF files or images (JPG, PNG) of your documents. Maximum file size is 10MB per file.
                            </div>
                            </div>

                            @if(session('success'))
                                <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6 text-sm font-medium flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($requiredTypes as $typeKey => $typeLabel)
                                    @php
                                        $uploaded = $documents->firstWhere('type', $typeKey);
                                        $status = $uploaded ? $uploaded->status : 'missing';
                                    @endphp
                                    
                                    <div class="relative border border-slate-200 rounded-xl bg-white hover:border-orange-200 transition-all duration-200 shadow-sm group overflow-hidden flex flex-col h-full">
                                        @if($uploaded)
                                            <div class="flex flex-1 items-stretch">
                                                <!-- Left Content -->
                                                <div class="p-5 flex-1 flex flex-col justify-center">
                                                    <h4 class="font-semibold text-slate-900 text-sm leading-tight mb-1">{{ $typeLabel }}</h4>
                                                    <div class="text-xs text-slate-500 flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        Uploaded {{ $uploaded->created_at->diffForHumans() }}
                                </div>
                                </div>

                                                <!-- Right Actions (Bookmark Navigation) -->
                                                <div class="flex flex-col w-12 border-l border-slate-100">
                                                    <!-- View -->
                                                    @if($status !== 'rejected')
                                                    <a href="{{ route('documents.view', $uploaded) }}" target="_blank" class="flex-1 flex items-center justify-center bg-green-50 text-green-600 hover:bg-green-100 transition-colors relative group/btn" title="View Document">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </a>
                                                    
                                                    <!-- Delete -->
                                                    <form action="{{ route('documents.delete', $uploaded->id) }}" method="POST" class="flex-1 flex flex-col border-t border-slate-100" onsubmit="return confirm('Are you sure you want to discard and delete this document?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex-1 w-full flex items-center justify-center bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Discard & Delete">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </form>
                                                    @endif
                            </div>
                        </div>
                                        @else
                                            <div class="p-5 flex-1 flex flex-col">
                                                <div class="flex justify-between items-start mb-4">
                                                    <div class="pr-2">
                                                        <h4 class="font-semibold text-slate-900 text-sm leading-tight mb-1">{{ $typeLabel }}</h4>
                                                        <div class="text-xs text-slate-400 italic">Required document</div>
                    </div>
                        </div>

                                                <div class="mt-auto">
                                                    <div class="doc-upload-container">
                                                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer bg-slate-50 hover:bg-orange-50 hover:border-orange-300 transition-colors relative group/upload">
                                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                <svg class="w-6 h-6 mb-2 text-slate-400 group-hover/upload:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                                <p class="text-xs text-slate-500"><span class="font-semibold">Click to upload</span> PDF or Image</p>
                                                            </div>
                                                            <input type="file" name="documents[{{ $typeKey }}]" class="doc-file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".pdf,.jpg,.jpeg,.png,.gif">
                                </label>
                                                        <div class="file-name-display text-xs text-slate-600 mt-2 text-center truncate hidden px-2"></div>
                            </div>
                        </div>
                            </div>
                                        @endif
                                    </div>
                                @endforeach
                        </div>
                                        </div>
                                        </div>

                    <!-- Footer -->
                    <div class="form-footer">
                        <button type="button" class="btn btn-outline" id="prevBtn" style="display: none">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Back
                        </button>
                        <button type="button" class="btn btn-outline ml-3" id="saveDraftBtn">
                            Save as Draft
                        </button>
                        <div class="ml-auto">
                            <button type="button" class="btn btn-primary" id="nextBtn">
                                Next Step
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <button type="submit" class="btn btn-primary bg-green-600 hover:bg-green-700 border-green-600" id="submitBtn" style="display: none">
                                Submit Application
                            </button>
                                        </div>
                                        </div>
                </form>
                                        </div>
                                    </div>
    </div>
</div>

<!-- Sibling Modal -->
<div id="siblingModalBackdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden"></div>
<div id="siblingModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-6 relative">
        <button type="button" class="absolute right-4 top-4 text-slate-400 hover:text-slate-600" onclick="closeSiblingModal()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <h3 class="text-xl font-semibold text-slate-800 mb-4">Add Sibling</h3>
        <div class="space-y-4">
                                    <div>
                <label class="input-label">Name <span class="text-red-500">*</span></label>
                <input type="text" id="modal_sibling_name" class="form-control" placeholder="Juan Dela Cruz">
                                    </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                    <label class="input-label">Age</label>
                    <input type="number" id="modal_sibling_age" class="form-control" placeholder="18">
                                    </div>
                                    <div>
                    <label class="input-label">Scholarship (if any)</label>
                    <input type="text" id="modal_sibling_scholarship" class="form-control" placeholder="NCIP Scholar">
                </div>
                                    </div>
                                    <div>
                <label class="input-label">Course / Year Level</label>
                <input type="text" id="modal_sibling_course" class="form-control" placeholder="BSIT - 2nd Year">
                                    </div>
                                    <div>
                <label class="input-label">Present Status</label>
                <select id="modal_sibling_status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="None">None</option>
                    <option value="Stopped/Undergraduate">Stopped/Undergraduate</option>
                    <option value="Undergraduate/Married">Undergraduate/Married</option>
                    <option value="Graduated/Married">Graduated/Married</option>
                    <option value="Graduate/Working (Single)">Graduate/Working (Single)</option>
                </select>
                                    </div>
                                </div>
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" class="btn btn-outline" onclick="closeSiblingModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveSiblingFromModal()">Save Sibling</button>
                        </div>
                        </div>
</div>

@push('scripts')
                        <script>
    let currentStep = parseInt(localStorage.getItem('apply_current_step')) || 1;
    const totalSteps = 6;
    const form = document.getElementById('applicationForm');
    const siblingStatusOptions = [
        'None',
        'Stopped/Undergraduate',
        'Undergraduate/Married',
        'Graduated/Married',
        'Graduate/Working (Single)',
    ];
    
    function updateUI() {
        // Save step
        localStorage.setItem('apply_current_step', currentStep);

        // Hide all steps
        document.querySelectorAll('.form-step').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('fade-enter-active');
        });
        
        // Show current step
        const currentStepEl = document.getElementById('step-' + currentStep);
        currentStepEl.classList.remove('hidden');
        // Trigger animation
        setTimeout(() => currentStepEl.classList.add('fade-enter-active'), 10);

        // Update sidebar nav
        document.querySelectorAll('.step-item').forEach((el, idx) => {
            el.classList.remove('active', 'completed');
            if (idx + 1 === currentStep) el.classList.add('active');
            if (idx + 1 < currentStep) el.classList.add('completed');
        });

        // Update header info
        document.getElementById('current-step-num').textContent = currentStep;
        
        const titles = [
            'Personal Information', 
            'Address Details', 
            'Educational Background', 
            'Family Information', 
            'School Preference',
            'Document Requirements'
        ];
        document.getElementById('form-section-title').textContent = titles[currentStep - 1];

        // Buttons
        document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'inline-flex';
        document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'inline-flex';
        document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-flex' : 'none';
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (validateStep(currentStep)) {
            currentStep++;
            updateUI();
        }
    });

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateUI();
        }
    });

    function validateStep(step) {
        const stepEl = document.getElementById('step-' + step);
        const requiredInputs = stepEl.querySelectorAll('[required]');
        let isValid = true;

        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('border-red-500');
                input.addEventListener('input', () => input.classList.remove('border-red-500'), { once: true });
            }
        });

        if (step === 1) {
            // Checkbox validation
            const checkboxes = document.querySelectorAll('.check-assist');
            const isChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (!isChecked) {
                isValid = false;
                alert('Please select at least one type of assistance.');
            }
        }

        if (!isValid) {
            alert('Please fill in all required fields.');
        }
        return isValid;
    }

    // Checkbox exclusive logic
    document.querySelectorAll('.check-assist').forEach(cb => {
        cb.addEventListener('change', function() {
            if(this.checked) {
                document.querySelectorAll('.check-assist').forEach(other => {
                    if(other !== this) other.checked = false;
                });
            }
        });
    });

    // Sibling logic
    window.addSibling = function(data = {}) {
        const container = document.getElementById('siblings-list');
        const item = document.createElement('div');
        item.className = 'sibling-item rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3';

        item.innerHTML = `
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-sm font-semibold text-orange-600">
                    <span class="sibling-index"></span>
                    <span>Siblings</span>
                    </div>
                <button type="button" class="text-xs font-semibold text-red-500 hover:text-red-700" onclick="removeSibling(this)">Remove</button>
                        </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="text-sm">
                    <div class="text-slate-400 uppercase tracking-wide text-[11px] mb-1">Name</div>
                    <div class="font-semibold text-slate-900 leading-snug sibling-display-name">${data.name ?? ''}</div>
                                </div>
                <div class="text-sm">
                    <div class="text-slate-400 uppercase tracking-wide text-[11px] mb-1">Age</div>
                    <div class="font-medium text-slate-900 leading-snug sibling-display-age">${data.age ?? ''}</div>
                                </div>
                <div class="text-sm">
                    <div class="text-slate-400 uppercase tracking-wide text-[11px] mb-1">Scholarship</div>
                    <div class="font-medium text-slate-900 leading-snug sibling-display-scholarship">${data.scholarship ?? '—'}</div>
                            </div>
                <div class="text-sm">
                    <div class="text-slate-400 uppercase tracking-wide text-[11px] mb-1">Course / Year Level</div>
                    <div class="font-medium text-slate-900 leading-snug sibling-display-course">${data.course ?? '—'}</div>
                        </div>
                <div class="text-sm md:col-span-2">
                    <div class="text-slate-400 uppercase tracking-wide text-[11px] mb-1">Present Status</div>
                    <div class="font-medium text-slate-900 leading-snug sibling-display-status">${data.status ?? '—'}</div>
                                </div>
                                </div>
            <input type="hidden" name="sibling_name[]" value="${data.name ?? ''}">
            <input type="hidden" name="sibling_age[]" value="${data.age ?? ''}">
            <input type="hidden" name="sibling_scholarship[]" value="${data.scholarship ?? ''}">
            <input type="hidden" name="sibling_course[]" value="${data.course ?? ''}">
            <input type="hidden" name="sibling_status[]" value="${data.status ?? ''}">
        `;
        container.appendChild(item);
        refreshSiblingState();
        document.dispatchEvent(new Event('apply:sibling-changed'));
    }
    
    window.removeSibling = function(btn) {
        const list = document.getElementById('siblings-list');
        btn.closest('.sibling-item').remove();
        refreshSiblingState();
        document.dispatchEvent(new Event('apply:sibling-changed'));
    }

    // Location logic
    const setupLocation = (prefix) => {
        const muniSelect = document.querySelector(`[name="${prefix}_municipality"]`);
        const brgySelect = document.getElementById(`${prefix}_barangay`);
        
        if(muniSelect && brgySelect) {
            muniSelect.addEventListener('change', function() {
                fetch(`/address/barangays?municipality=${this.value}`)
                    .then(r => r.json())
                    .then(data => {
                        brgySelect.innerHTML = '<option value="">Select Barangay</option>';
                        data.forEach(b => {
                            const opt = document.createElement('option');
                            opt.value = b;
                            opt.textContent = b;
                            brgySelect.appendChild(opt);
                        });
                    });
            });
        }
    };
    ['mailing', 'permanent', 'origin'].forEach(setupLocation);

    // Draft persistence using localStorage
    (function() {
        const formEl = document.getElementById('applicationForm');
        const storageKey = 'apply_drafts_v2'; // dictionary {id: data}
        const currentIdKey = 'apply_current_draft_id';

        const storageAvailable = (() => {
            try {
                const testKey = '__storage_test';
                localStorage.setItem(testKey, '1');
                localStorage.removeItem(testKey);
                return true;
            } catch (err) {
                return false;
            }
        })();

        if (!formEl || !storageAvailable) {
            return;
        }

        let saveTimeout;
        const scheduleDraftSave = () => {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(saveDraft, 400);
        };

        function saveDraft() {
            const drafts = JSON.parse(localStorage.getItem(storageKey) || '{}');
            let currentId = localStorage.getItem(currentIdKey);
            if (!currentId) {
                currentId = Date.now().toString();
                localStorage.setItem(currentIdKey, currentId);
            }

            const data = {};
            const elements = formEl.querySelectorAll('input, select, textarea');
            elements.forEach(el => {
                if (!el.name || el.name === '_token' || el.type === 'file') {
                    return;
                }
                if (el.type === 'checkbox') {
                    if (!data[el.name]) {
                        data[el.name] = [];
                    }
                    if (el.checked) {
                        data[el.name].push(el.value);
                    }
                } else if (el.type === 'radio') {
                    if (el.checked) {
                        data[el.name] = el.value;
                    } else if (!data[el.name]) {
                        data[el.name] = '';
                    }
                } else if (el.name.endsWith('[]')) {
                    if (!data[el.name]) {
                        data[el.name] = [];
                    }
                    data[el.name].push(el.value);
                } else {
                    data[el.name] = el.value;
                }
            });
            
            // Metadata
            data._timestamp = new Date().toISOString();
            data._id = currentId;
            // Try to construct a name
            const fname = data['first_name'] || '';
            const lname = data['last_name'] || '';
            data._name = (fname || lname) ? `${fname} ${lname}`.trim() + ' - Scholarship Application' : 'Untitled Application';

            drafts[currentId] = data;
            localStorage.setItem(storageKey, JSON.stringify(drafts));
            
            updateDraftUI(data._timestamp);
        }

        function updateDraftUI(timestamp) {
            // This only updates the side panel within the form view
            const container = document.getElementById('drafts-container');
            const timeEl = document.getElementById('draft-timestamp');
            if (!container || !timeEl) return;

            if (timestamp) {
                container.classList.remove('hidden');
                const date = new Date(timestamp);
                const timeStr = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const dateStr = date.toLocaleDateString();
                timeEl.textContent = `Last saved: ${dateStr} at ${timeStr}`;
            } else {
                container.classList.add('hidden');
            }
        }

        function ensureSiblingEntries(count) {
            const list = document.getElementById('siblings-list');
            if (!list) return;
            // Clear existing first to match exact count/order if needed, or just add missing
            // Handled in restoreDraft logic below.
            while (list.querySelectorAll('.sibling-item').length < count) {
                addSibling();
            }
        }

        function restoreDraft() {
            const currentId = localStorage.getItem(currentIdKey);
            const drafts = JSON.parse(localStorage.getItem(storageKey) || '{}');
            const data = currentId ? drafts[currentId] : null;

            if (!data) {
                formEl.reset();
                document.getElementById('siblings-list').innerHTML = '<p id="siblings-empty" class="p-6 text-sm text-slate-500 text-center border border-dashed border-slate-300 rounded-2xl">No siblings added yet.</p>';
                refreshSiblingState();
                updateDraftUI(null);
                return;
            }

            updateDraftUI(data._timestamp);

            // Reset sibling list
            const siblingList = document.getElementById('siblings-list');
            siblingList.innerHTML = '';
            document.getElementById('siblings-empty').classList.remove('hidden');

            Object.entries(data).forEach(([name, value]) => {
                if (name.startsWith('_')) return;
                if (Array.isArray(value) && name.startsWith('sibling_') && name.endsWith('[]')) {
                    ensureSiblingEntries(value.length);
                }
            });

            Object.entries(data).forEach(([name, value]) => {
                if (name === '_token' || name.startsWith('_')) {
                    return;
                }
                let fields = formEl.querySelectorAll(`[name="${name}"]`);
                if (!fields.length) {
                    return;
                }

                const fieldType = fields[0].type;
                if (fieldType === 'checkbox') {
                    fields.forEach(field => {
                        field.checked = Array.isArray(value) ? value.includes(field.value) : value === field.value;
                    });
                    return;
                }

                if (fieldType === 'radio') {
                    fields.forEach(field => {
                        field.checked = value === field.value;
                    });
                    return;
                }

                if (Array.isArray(value)) {
                    fields.forEach((field, index) => {
                        field.value = value[index] ?? '';
                    });
                    return;
                }

                fields.forEach(field => {
                    field.value = value ?? '';
                });
            });
            refreshSiblingState();
        }

        // Listeners
        formEl.addEventListener('input', scheduleDraftSave, true);
        formEl.addEventListener('change', scheduleDraftSave, true);
        document.addEventListener('apply:sibling-changed', scheduleDraftSave);
        document.addEventListener('apply:restore-draft-needed', restoreDraft);

        // Restore on load if ID is set
        restoreDraft();

        const clearDraftBtn = document.getElementById('clearDraftBtn');
        clearDraftBtn?.addEventListener('click', () => {
            if (confirm('Delete this draft permanently?')) {
                const currentId = localStorage.getItem(currentIdKey);
                if (currentId) {
                    const drafts = JSON.parse(localStorage.getItem(storageKey) || '{}');
                    delete drafts[currentId];
                    localStorage.setItem(storageKey, JSON.stringify(drafts));
                    localStorage.removeItem(currentIdKey);
                }
                location.reload(); // Go back to Hub
            }
        });

        formEl.addEventListener('submit', () => {
            const currentId = localStorage.getItem(currentIdKey);
            if (currentId) {
                const drafts = JSON.parse(localStorage.getItem(storageKey) || '{}');
                delete drafts[currentId];
                localStorage.setItem(storageKey, JSON.stringify(drafts));
                localStorage.removeItem(currentIdKey);
            }
            localStorage.removeItem('apply_current_step');
        });

        // Expose save function for the manual button
        window.saveDraftManual = function() {
            saveDraft();
            alert('Application draft saved successfully!');
            if (window.returnToHub) {
                window.returnToHub();
            }
        };
    })();

    // Manual Save Draft Button
    document.getElementById('saveDraftBtn')?.addEventListener('click', () => {
        if (window.saveDraftManual) {
            window.saveDraftManual();
        }
    });

    // Continue Draft Logic (Sidebar)
    document.getElementById('continueDraftBtn')?.addEventListener('click', () => {
        const header = document.querySelector('.form-header');
        if (header) {
            header.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const card = document.querySelector('.main-card');
            if (card) {
                card.classList.add('ring-4', 'ring-orange-100', 'transition-all', 'duration-500');
                setTimeout(() => {
                    card.classList.remove('ring-4', 'ring-orange-100');
                }, 1000);
            }
        }
    });

    // Sibling modal logic
    const siblingModal = document.getElementById('siblingModal');
    const siblingBackdrop = document.getElementById('siblingModalBackdrop');

    window.openSiblingModal = function() {
        siblingModal.classList.remove('hidden');
        siblingModal.classList.add('flex');
        siblingBackdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    window.closeSiblingModal = function() {
        siblingModal.classList.add('hidden');
        siblingModal.classList.remove('flex');
        siblingBackdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        clearSiblingModal();
    };

    function clearSiblingModal() {
        document.getElementById('modal_sibling_name').value = '';
        document.getElementById('modal_sibling_age').value = '';
        document.getElementById('modal_sibling_scholarship').value = '';
        document.getElementById('modal_sibling_course').value = '';
        document.getElementById('modal_sibling_status').value = '';
    }

    window.saveSiblingFromModal = function() {
        const name = document.getElementById('modal_sibling_name').value.trim();
        if (!name) {
            alert('Name is required');
            return;
        }

        const siblingData = {
            name,
            age: document.getElementById('modal_sibling_age').value,
            scholarship: document.getElementById('modal_sibling_scholarship').value,
            course: document.getElementById('modal_sibling_course').value,
            status: document.getElementById('modal_sibling_status').value,
        };

        addSibling(siblingData);
        closeSiblingModal();
    };

    function updateSiblingLabels() {
        const items = document.querySelectorAll('#siblings-list .sibling-item .sibling-index');
        items.forEach((node, index) => {
            node.textContent = `${index + 1}.`;
        });
    }

    function refreshSiblingState() {
        const list = document.getElementById('siblings-list');
        const hasItems = list.children.length > 0;
        document.getElementById('siblings-empty').classList.toggle('hidden', hasItems);
        updateSiblingLabels();
    }

    // Document Upload Logic
    const uploadAllBtn = document.getElementById('upload-all-btn');
    
    // File selection feedback
    document.querySelectorAll('.doc-file-input').forEach(input => {
        input.addEventListener('change', function() {
            const container = this.closest('.doc-upload-container');
            const label = container.querySelector('label');
            const fileNameDisplay = container.querySelector('.file-name-display');
            const clickText = label.querySelector('p');
            const icon = label.querySelector('svg');

            if (this.files && this.files[0]) {
                const file = this.files[0];
                // Update visual state
                label.classList.remove('bg-slate-50', 'border-slate-300');
                label.classList.add('bg-orange-50', 'border-orange-400');
                
                // Update text and icon
                icon.classList.remove('text-slate-400');
                icon.classList.add('text-orange-500');
                
                clickText.innerHTML = `<span class="font-semibold text-orange-700">Selected:</span> ${file.name}`;
                
                // Optional: Show file size
                const size = (file.size / 1024 / 1024).toFixed(2); // MB
                fileNameDisplay.textContent = `${size} MB`;
                fileNameDisplay.classList.remove('hidden');
                    } else {
                // Reset state
                label.classList.add('bg-slate-50', 'border-slate-300');
                label.classList.remove('bg-orange-50', 'border-orange-400');
                icon.classList.add('text-slate-400');
                icon.classList.remove('text-orange-500');
                clickText.innerHTML = `<span class="font-semibold">Click to upload</span> PDF`;
                fileNameDisplay.classList.add('hidden');
            }
        });
    });

    if (uploadAllBtn) {
        uploadAllBtn.addEventListener('click', async function() {
            const containers = Array.from(document.querySelectorAll('.doc-upload-container'));
            const uploadsToProcess = containers.filter(container => {
                const fileInput = container.querySelector('.doc-file-input');
                return fileInput && fileInput.files.length > 0;
            });

            if (uploadsToProcess.length === 0) {
                alert('Please select at least one document (PDF or Image) before uploading.');
                return;
            }

            uploadAllBtn.disabled = true;
            const originalText = uploadAllBtn.innerHTML;
            uploadAllBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Uploading...';

            let hasErrors = false;
            // Get CSRF token
            const csrfToken = document.querySelector('input[name="_token"]').value;

            for (const container of uploadsToProcess) {
                const fileInput = container.querySelector('.doc-file-input');
                const typeInput = container.querySelector('input[name="type"]');
                const action = container.dataset.action;

                const formData = new FormData();
                formData.append('upload-file', fileInput.files[0]);
                formData.append('type', typeInput.value);
                formData.append('_token', csrfToken);

                try {
                    const response = await fetch(action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        hasErrors = true;
                    }
                } catch (error) {
                    hasErrors = true;
                }
            }

            if (hasErrors) {
                alert('Some documents failed to upload. Please review the selections and try again.');
            } else {
                alert('All selected documents were uploaded successfully!');
            }

            // Reload to update status
            window.location.reload();
        });
    }

    // Init
    // Check if we should show the form or hub based on localStorage
    ( function initView() {
        // New multi-draft keys
        const DRAFTS_STORAGE_KEY = 'apply_drafts_v2';
        const CURRENT_DRAFT_KEY = 'apply_current_draft_id';
        const hubView = document.getElementById('application-hub');
        const formView = document.getElementById('application-form-view');
        const draftsContainer = document.getElementById('hub-recent-drafts');
        
        if (!hubView || !formView) return;

        if (!localStorage.getItem(DRAFTS_STORAGE_KEY)) {
            localStorage.setItem(DRAFTS_STORAGE_KEY, JSON.stringify({}));
        }

        function getDraftsArray() {
            const draftsObj = JSON.parse(localStorage.getItem(DRAFTS_STORAGE_KEY) || '{}');
            return Object.values(draftsObj);
        }

        function renderDraftsList() {
            const drafts = getDraftsArray().sort((a, b) => new Date(b._timestamp || 0) - new Date(a._timestamp || 0));

            if (drafts.length > 0) {
                draftsContainer.innerHTML = drafts.map(draft => {
                    const timestamp = draft._timestamp ? new Date(draft._timestamp) : new Date();
                    const timeStr = timestamp.toLocaleDateString() + ' ' + timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    const title = draft._name || 'Scholarship Application';

                    return `
                    <button type="button" onclick="continueDraft('${draft._id}')" class="flex items-center p-4 bg-white border border-slate-200 rounded-xl shadow-sm hover:border-orange-300 hover:shadow-md transition-all group text-left w-full mb-3">
                        <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-100 mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-slate-900 text-sm group-hover:text-orange-700 transition-colors">${title}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Last edited ${timeStr}</p>
                        </div>
                        <div class="text-xs font-medium text-slate-400 bg-slate-50 px-2 py-1 rounded group-hover:bg-orange-50 group-hover:text-orange-600 transition-colors">
                            Open
                        </div>
                    </button>
                    `;
                }).join('');
            } else {
                draftsContainer.innerHTML = `
                    <div class="text-center py-12 bg-white rounded-xl border border-dashed border-slate-300 text-slate-400 text-sm">
                        No recent drafts found
                    </div>
                `;
            }
        }

        renderDraftsList();
        
        // Functions for view switching
        window.startNewApplication = function() {
            const newDraftId = Date.now().toString();
            localStorage.setItem('apply_current_draft_id', newDraftId);
            
            // Initialize the draft in storage immediately
            const drafts = JSON.parse(localStorage.getItem(DRAFTS_STORAGE_KEY) || '{}');
            drafts[newDraftId] = {  
                _id: newDraftId,
                _timestamp: new Date().toISOString(),
                _name: 'Untitled Application'
            };
            localStorage.setItem(DRAFTS_STORAGE_KEY, JSON.stringify(drafts));
            
            // Trigger form show which will restore (and thus clear) the form for the new ID
            showForm();
        };

        window.continueDraft = function(draftId) {
            if (!draftId) return;
            localStorage.setItem('apply_current_draft_id', draftId);
            showForm();
        };

        window.returnToHub = function() {
            hubView.classList.remove('hidden');
            formView.classList.add('hidden');
            
            // Re-render drafts list to show any updates
            renderDraftsList();
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        function showForm() {
            hubView.classList.add('hidden');
            formView.classList.remove('hidden');
            // Trigger restore logic
            document.dispatchEvent(new Event('apply:restore-draft-needed'));
            updateUI(); // Ensure correct step is shown
        }
    })();

</script>
@endpush 
@endsection
