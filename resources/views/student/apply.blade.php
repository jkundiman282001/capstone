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
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-top: 1px solid #f1f5f9;
        border-radius: 0 0 1rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
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

    #saveDraftBtn {
        position: relative;
        z-index: 10;
        pointer-events: auto;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: #ffffff;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    #saveDraftBtn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    #saveDraftBtn:active {
        transform: translateY(0);
    }

    #saveDraftBtn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    #nextBtn {
        background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
        color: #ffffff;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 2px 4px rgba(234, 88, 12, 0.2);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    #nextBtn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(234, 88, 12, 0.3);
        background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%);
    }

    #nextBtn:active {
        transform: translateY(0);
    }

    #nextBtn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    #submitBtn {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: #ffffff;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 2px 4px rgba(34, 197, 94, 0.2);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    #submitBtn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(34, 197, 94, 0.3);
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
    }

    #submitBtn:active {
        transform: translateY(0);
    }

    #submitBtn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
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

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out;
    }

    .gradient-text {
        background: linear-gradient(135deg, #F97316 0%, #EA580C 50%, #DC2626 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Toast Notification */
    .toast-container {
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 9999;
        pointer-events: none;
    }

    .toast {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        min-width: 320px;
        max-width: 400px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        pointer-events: auto;
        animation: slideInRight 0.3s ease-out;
        border-left: 4px solid #22c55e;
    }

    .toast.success {
        border-left-color: #22c55e;
    }

    .toast.error {
        border-left-color: #ef4444;
    }

    .toast-icon {
        flex-shrink: 0;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toast.success .toast-icon {
        background: #dcfce7;
        color: #16a34a;
    }

    .toast-content {
        flex: 1;
    }

    .toast-title {
        font-weight: 600;
        font-size: 0.95rem;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .toast-message {
        font-size: 0.875rem;
        color: #64748b;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .toast.hiding {
        animation: slideOutRight 0.3s ease-in forwards;
    }

    /* Success Modal Styles */
    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-scaleIn {
        animation: scaleIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #successModal button:hover {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
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

        @if($hasSubmitted)
        <!-- Lock Message -->
        <div class="bg-gradient-to-r from-amber-50 via-orange-50/50 to-amber-50 border-l-4 border-amber-500 rounded-2xl shadow-xl p-8 mb-8 animate-fadeIn">
            <div class="flex items-start gap-6">
                <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-black text-amber-900 mb-3 gradient-text">Application Already Submitted</h3>
                    <p class="text-amber-800 text-base mb-4 leading-relaxed">
                        You have already submitted a scholarship application. You cannot create or submit another application at this time.
                    </p>
                    @if($submittedApplication)
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-5 border-2 border-amber-200 shadow-inner mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm font-bold text-amber-900">
                                <span>Application Status:</span> 
                                <span class="capitalize ml-2 px-3 py-1 bg-amber-100 rounded-lg">{{ $submittedApplication->application_status ?? 'Pending Review' }}</span>
                            </p>
                        </div>
                        @if($submittedApplication->application_status === 'rejected' && $submittedApplication->application_rejection_reason)
                        <div class="mt-3 pt-3 border-t border-amber-200">
                            <p class="text-sm text-amber-800">
                                <span class="font-bold">Rejection Reason:</span> 
                                <span class="ml-2">{{ $submittedApplication->application_rejection_reason }}</span>
                            </p>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="mt-6">
                        <a href="{{ route('student.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-xl hover:from-amber-700 hover:to-orange-700 transition-all duration-300 text-sm font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Start New Section (Google Docs Style) -->
        <div class="bg-slate-50 rounded-xl p-8 mb-10 border border-slate-200 {{ $hasSubmitted && !$canRenew ? 'opacity-60' : '' }}">
            <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-4">Start a new application</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <!-- New Application Card -->
                <button type="button" onclick="{{ $hasSubmitted ? 'void(0)' : 'startNewApplication()' }}" 
                    class="group text-left {{ $hasSubmitted ? 'cursor-not-allowed opacity-50' : '' }}" 
                    {{ $hasSubmitted ? 'disabled' : '' }}>
                    <div class="aspect-[3/4] bg-white rounded-lg border border-slate-200 shadow-sm {{ $hasSubmitted ? '' : 'group-hover:border-orange-400 group-hover:shadow-md' }} transition-all flex items-center justify-center mb-3 relative overflow-hidden">
                        <div class="w-12 h-12 rounded-full bg-white border border-slate-100 flex items-center justify-center shadow-sm {{ $hasSubmitted ? '' : 'group-hover:scale-110' }} transition-transform">
                            @if($hasSubmitted)
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            @else
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            @endif
                        </div>
                    </div>
                    <div class="font-medium text-slate-900 text-sm {{ $hasSubmitted ? '' : 'group-hover:text-orange-700' }} transition-colors">
                        {{ $hasSubmitted ? 'Application Locked' : 'Scholarship Application' }}
                    </div>
                    <div class="text-xs text-slate-500">{{ $hasSubmitted ? 'Already submitted' : 'NCIP Educational Assistance' }}</div>
                </button>

                <!-- Renewal Application Card -->
                @if($canRenew)
                <button type="button" onclick="startRenewalApplication()" class="group text-left">
                    <div class="aspect-[3/4] bg-white rounded-lg border border-slate-200 shadow-sm group-hover:border-blue-400 group-hover:shadow-md transition-all flex items-center justify-center mb-3 relative overflow-hidden">
                        <div class="w-12 h-12 rounded-full bg-white border border-slate-100 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                    </div>
                    <div class="font-medium text-slate-900 text-sm group-hover:text-blue-700 transition-colors">Scholarship Renewal</div>
                    <div class="text-xs text-slate-500">Renew your scholarship</div>
                </button>
                @endif
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

                    <div class="steps-wrapper" id="steps-wrapper">
                        <!-- Regular Application Steps -->
                        <div id="regular-steps">
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
                        
                        <!-- Renewal Application Steps (Hidden by default) -->
                        <div id="renewal-steps" style="display: none;">
                            <div class="step-item active" id="nav-step-renewal">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <div class="step-title">Document Requirements</div>
                                    <div class="step-desc">Upload renewal documents</div>
                                </div>
                            </div>
                        </div>
            </div>

                    <div class="mt-8 bg-gradient-to-r from-blue-50 via-cyan-50/50 to-blue-50 border-l-4 border-blue-500 rounded-xl shadow-lg p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-base font-black text-blue-900 mb-2">Need Assistance?</p>
                                <p class="text-sm text-blue-800 leading-relaxed">Contact support at (02) 888-1234 or email support@ncip.gov.ph</p>
                </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content: Form -->
            <div class="lg:col-span-9">
                <form method="POST" action="{{ route('student.apply') }}" id="applicationForm" class="main-card {{ $hasSubmitted && !$canRenew ? 'opacity-60 pointer-events-none' : '' }}" enctype="multipart/form-data" {{ $hasSubmitted && !$canRenew ? 'onsubmit="return false;"' : '' }}>
                    @csrf
                    
                    <!-- Header -->
                    <div class="form-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900" id="form-section-title">Personal Information</h2>
                            <p class="text-sm text-slate-500 mt-1">Step <span id="current-step-num">1</span> of 6</p>
                            <p class="text-xs text-slate-400 mt-2">Your progress auto-saves on this device.</p>
                            <input type="hidden" name="is_renewal" id="is_renewal_input" value="0">
                </div>
                        <button type="button" id="clearDraftBtn" class="text-xs font-semibold text-slate-400 hover:text-orange-600 transition-colors">
                            Clear saved draft
                        </button>
                    </div>


                    <!-- Error Display -->
                @if ($errors->any())
                        <div class="mx-8 mt-6 bg-gradient-to-r from-red-50 via-red-50/50 to-red-50 border-l-4 border-red-500 rounded-xl shadow-lg p-6 animate-fadeIn">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-black text-red-900 mb-3">There were errors with your submission</h3>
                                    <ul class="space-y-2">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2 text-sm text-red-800">
                                    <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                        </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mx-8 mt-6 bg-gradient-to-r from-red-50 via-red-50/50 to-red-50 border-l-4 border-red-500 rounded-xl shadow-lg p-6 animate-fadeIn">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-black text-red-900">{{ session('error') }}</h3>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($hasSubmitted)
                        <div class="mx-8 mt-6 bg-gradient-to-r from-amber-50 via-orange-50/50 to-amber-50 border-l-4 border-amber-500 rounded-xl shadow-lg p-6 animate-fadeIn">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-black text-amber-900 mb-2">Application Locked</h3>
                                    <p class="text-sm text-amber-800 leading-relaxed">You have already submitted an application. This form is read-only.</p>
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
                            @foreach(['mailing' => 'Mailing Address', 'permanent' => 'Permanent Address', 'origin' => 'Place of Origin/Place of Birth'] as $prefix => $title)
                            <div class="mb-8 {{ !$loop->last ? 'pb-8 border-b border-slate-100' : '' }}">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="section-heading mb-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657L12 22l5.657-5.343z"/></svg>
                                    {{ $title }}
                                </h3>
                                    @if($prefix !== 'mailing')
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" id="same_as_mailing_{{ $prefix }}" class="w-4 h-4 text-orange-600 border-slate-300 rounded focus:ring-orange-500 same-as-mailing-checkbox" data-target-prefix="{{ $prefix }}">
                                        <span class="text-sm font-medium text-slate-700 group-hover:text-orange-600 transition-colors">Same as Mailing Address</span>
                                    </label>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="address-fields-{{ $prefix }}">
                                    <div class="input-group">
                                        <label class="input-label">Province</label>
                                        <input type="text" name="{{ $prefix }}_province" class="form-control bg-slate-50" value="Davao del Sur" readonly>
                        </div>
                                    <div class="input-group">
                                        <label class="input-label">Municipality</label>
                                        <select name="{{ $prefix }}_municipality" class="form-control muni-select" data-prefix="{{ $prefix }}" id="{{ $prefix }}_municipality" required>
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
                                        <input type="text" name="{{ $prefix }}_house_num" class="form-control" id="{{ $prefix }}_house_num" value="{{ old($prefix.'_house_num') }}">
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
                                        <select name="{{ $parent }}_occupation" id="{{ $parent }}_occupation" class="form-control occupation-select">
                                            <option value="">Select Occupation</option>
                                            <option value="Farmer">Farmer</option>
                                            <option value="Fisherman">Fisherman</option>
                                            <option value="Teacher">Teacher</option>
                                            <option value="Nurse">Nurse</option>
                                            <option value="Doctor">Doctor</option>
                                            <option value="Engineer">Engineer</option>
                                            <option value="Accountant">Accountant</option>
                                            <option value="Business Owner">Business Owner</option>
                                            <option value="Entrepreneur">Entrepreneur</option>
                                            <option value="Government Employee">Government Employee</option>
                                            <option value="Police Officer">Police Officer</option>
                                            <option value="Soldier">Soldier</option>
                                            <option value="Driver">Driver</option>
                                            <option value="Construction Worker">Construction Worker</option>
                                            <option value="Carpenter">Carpenter</option>
                                            <option value="Electrician">Electrician</option>
                                            <option value="Plumber">Plumber</option>
                                            <option value="Mechanic">Mechanic</option>
                                            <option value="Barangay Official">Barangay Official</option>
                                            <option value="Barangay Health Worker">Barangay Health Worker</option>
                                            <option value="Social Worker">Social Worker</option>
                                            <option value="Lawyer">Lawyer</option>
                                            <option value="Judge">Judge</option>
                                            <option value="Architect">Architect</option>
                                            <option value="Veterinarian">Veterinarian</option>
                                            <option value="Dentist">Dentist</option>
                                            <option value="Pharmacist">Pharmacist</option>
                                            <option value="Midwife">Midwife</option>
                                            <option value="Medical Technologist">Medical Technologist</option>
                                            <option value="Physical Therapist">Physical Therapist</option>
                                            <option value="Chef">Chef</option>
                                            <option value="Cook">Cook</option>
                                            <option value="Waiter/Waitress">Waiter/Waitress</option>
                                            <option value="Housekeeper">Housekeeper</option>
                                            <option value="Security Guard">Security Guard</option>
                                            <option value="Janitor">Janitor</option>
                                            <option value="Salesperson">Salesperson</option>
                                            <option value="Cashier">Cashier</option>
                                            <option value="Store Manager">Store Manager</option>
                                            <option value="Bank Teller">Bank Teller</option>
                                            <option value="Bank Manager">Bank Manager</option>
                                            <option value="Real Estate Agent">Real Estate Agent</option>
                                            <option value="Insurance Agent">Insurance Agent</option>
                                            <option value="Call Center Agent">Call Center Agent</option>
                                            <option value="IT Professional">IT Professional</option>
                                            <option value="Programmer">Programmer</option>
                                            <option value="Web Developer">Web Developer</option>
                                            <option value="Graphic Designer">Graphic Designer</option>
                                            <option value="Photographer">Photographer</option>
                                            <option value="Journalist">Journalist</option>
                                            <option value="Writer">Writer</option>
                                            <option value="Tour Guide">Tour Guide</option>
                                            <option value="Travel Agent">Travel Agent</option>
                                            <option value="Hotel Staff">Hotel Staff</option>
                                            <option value="Flight Attendant">Flight Attendant</option>
                                            <option value="Pilot">Pilot</option>
                                            <option value="Seafarer">Seafarer</option>
                                            <option value="OFW (Overseas Filipino Worker)">OFW (Overseas Filipino Worker)</option>
                                            <option value="Domestic Helper">Domestic Helper</option>
                                            <option value="Caregiver">Caregiver</option>
                                            <option value="Tricycle Driver">Tricycle Driver</option>
                                            <option value="Jeepney Driver">Jeepney Driver</option>
                                            <option value="Taxi Driver">Taxi Driver</option>
                                            <option value="Delivery Rider">Delivery Rider</option>
                                            <option value="Barber">Barber</option>
                                            <option value="Hairdresser">Hairdresser</option>
                                            <option value="Tailor">Tailor</option>
                                            <option value="Dressmaker">Dressmaker</option>
                                            <option value="Vendor">Vendor</option>
                                            <option value="Market Vendor">Market Vendor</option>
                                            <option value="Sari-Sari Store Owner">Sari-Sari Store Owner</option>
                                            <option value="Retailer">Retailer</option>
                                            <option value="Factory Worker">Factory Worker</option>
                                            <option value="Laborer">Laborer</option>
                                            <option value="Mason">Mason</option>
                                            <option value="Welder">Welder</option>
                                            <option value="Baker">Baker</option>
                                            <option value="Gardener">Gardener</option>
                                            <option value="Freelancer">Freelancer</option>
                                            <option value="Self-Employed">Self-Employed</option>
                                            <option value="Unemployed">Unemployed</option>
                                            <option value="Retired">Retired</option>
                                            <option value="Student">Student</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <input type="text" name="{{ $parent }}_occupation_other" id="{{ $parent }}_occupation_other" class="form-control mt-3 hidden" placeholder="Please specify occupation">
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
        // Priority Courses
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
        // Related/Relevant Courses (Mid-scale)
        'Agricultural Engineering',
        'Agribusiness',
        'Agricultural Economics',
        'Animal Science',
        'Crop Science',
        'Agricultural Technology',
        'Marine Biology',
        'Fisheries',
        'Aquaculture',
        'Marine Science',
        'Oceanography',
        'Sociology',
        'Cultural Studies',
        'Ethnic Studies',
        'Archaeology',
        'Business Management',
        'Marketing',
        'Economics',
        'Entrepreneurship',
        'Finance',
        'Human Resource Management',
        'Operations Management',
        'Structural Engineering',
        'Environmental Engineering',
        'Construction Engineering',
        'Transportation Engineering',
        'Rural Development',
        'Urban Planning',
        'Public Administration',
        'Development Studies',
        'Criminal Justice',
        'Forensic Science',
        'Law Enforcement',
        'Security Management',
        'Elementary Education',
        'Secondary Education',
        'Special Education',
        'Educational Administration',
        'Curriculum Development',
        'International Relations',
        'Diplomatic Studies',
        'International Studies',
        'Environmental Science',
        'Ecology',
        'Conservation',
        'Natural Resource Management',
        'Environmental Management',
        'Surveying',
        'Geomatics',
        'Land Surveying',
        'Geographic Information Systems',
        'Geological Engineering',
        'Geophysics',
        'Earth Science',
        'Legal Studies',
        'Jurisprudence',
        'Constitutional Law',
        'Public Health',
        'Health Sciences',
        'Medical Laboratory Science',
        'Radiologic Technology',
        'Physical Therapy',
        'Occupational Therapy',
        'Pharmacy',
        'Industrial Engineering',
        'Manufacturing Engineering',
        'Automotive Engineering',
        'Aerospace Engineering',
        'Mineral Processing',
        'Mining Technology',
        'Psychology',
        'History',
        'Philosophy',
        'Literature',
        'Communication Arts',
        'Journalism',
        'Human Services',
        'Community Services',
        'Social Welfare',
        'Counseling',
        // Excluded Courses (Low-scale)
        'BS Information Technology',
        'BS Computer Science',
        'BS Accountancy',
        'BS Nursing',
        'BS Education',
        'BA Political Science',
        // Other Common Courses
        'Architecture',
        'Chemical Engineering',
        'Electrical Engineering',
        'Electronics Engineering',
        'Computer Engineering',
        'Information Systems',
        'Data Science',
        'Statistics',
        'Mathematics',
        'Physics',
        'Chemistry',
        'Biology',
        'Biochemistry',
        'Biotechnology',
        'Food Technology',
        'Nutrition',
        'Hotel and Restaurant Management',
        'Tourism',
        'Hospitality Management',
        'Fine Arts',
        'Music',
        'Theater Arts',
        'Dance',
        'Sports Science',
        'Physical Education',
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

                            <div class="bg-gradient-to-r from-blue-50 via-cyan-50/50 to-blue-50 border-l-4 border-blue-500 rounded-xl shadow-lg p-6 mb-8 animate-fadeIn">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-base font-black text-blue-900 mb-2">Upload Instructions</h4>
                                        <p class="text-sm text-blue-800 leading-relaxed">
                                            Please upload clear PDF files or images (JPG, PNG) of your documents. Maximum file size is 10MB per file.
                                        </p>
                                    </div>
                            </div>
                            </div>

                            @if(session('success'))
                                <div class="bg-gradient-to-r from-green-50 via-emerald-50/50 to-green-50 border-l-4 border-green-500 rounded-xl shadow-lg p-6 mb-6 animate-fadeIn">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-md">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-black text-green-900">{{ session('success') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Regular Application Documents -->
                            <div id="regular-documents" class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                                    @if($typeKey === 'grades')
                                                    <div class="mb-3">
                                                        <label class="input-label text-xs">GPA (Grade Point Average) <span class="text-red-500">*</span></label>
                                                        <input type="number" name="gpa" id="gpa-input-grades" step="0.01" min="1.0" max="5.0" class="form-control text-sm" placeholder="Enter GPA (1.0 - 5.0)">
                                                        <p class="text-xs text-slate-500 mt-1">Scale: 1.0 - 5.0 (Philippine Grading System)</p>
                                                    </div>
                                                    @endif
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

                            <!-- Renewal Application Documents (Hidden by default) -->
                            <div id="renewal-documents" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                                @foreach($renewalRequiredTypes as $typeKey => $typeLabel)
                                    @php
                                        $uploaded = $documents->firstWhere('type', $typeKey);
                                        $status = $uploaded ? $uploaded->status : 'missing';
                                    @endphp
                                    
                                    <div class="relative border border-slate-200 rounded-xl bg-white hover:border-blue-200 transition-all duration-200 shadow-sm group overflow-hidden flex flex-col h-full">
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

                                                <!-- Right Actions -->
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
                                                    @if($typeKey === 'gwa_previous_sem')
                                                    <div class="mb-3">
                                                        <label class="input-label text-xs">GPA (Grade Point Average) <span class="text-red-500">*</span></label>
                                                        <input type="number" name="gpa" id="gpa-input-renewal" step="0.01" min="1.0" max="5.0" class="form-control text-sm" placeholder="Enter GPA (1.0 - 5.0)">
                                                        <p class="text-xs text-slate-500 mt-1">Scale: 1.0 - 5.0 (Philippine Grading System)</p>
                                                    </div>
                                                    @endif
                                                    <div class="doc-upload-container">
                                                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer bg-slate-50 hover:bg-blue-50 hover:border-blue-300 transition-colors relative group/upload">
                                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                <svg class="w-6 h-6 mb-2 text-slate-400 group-hover/upload:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
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
                                        </div>

                    <!-- Footer -->
                    <div class="form-footer">
                        <button type="button" class="btn btn-outline" id="prevBtn" style="display: none" {{ $hasSubmitted ? 'disabled' : '' }}>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Back
                        </button>
                        <div class="ml-auto flex items-center gap-2">
                            <button type="button" id="saveDraftBtn" {{ $hasSubmitted ? 'disabled' : '' }}>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            Save as Draft
                        </button>
                            <button type="button" id="nextBtn" {{ $hasSubmitted ? 'disabled' : '' }}>
                                Next Step
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                            <button type="submit" id="submitBtn" style="display: none" {{ $hasSubmitted ? 'disabled' : '' }}>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Submit Application
                            </button>
                                        </div>
                                        </div>
                </form>
                                        </div>
                                    </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toastContainer" class="toast-container"></div>

<!-- Custom Success Modal -->
<div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center px-4 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm animate-fadeIn"></div>
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 z-10 transform scale-95 animate-scaleIn">
        <div class="text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-3" id="successModalTitle">Success!</h3>
            <p class="text-base text-slate-600 mb-6" id="successModalMessage">Your action was completed successfully.</p>
            <button onclick="closeSuccessModal()" class="w-full px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                OK
            </button>
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
    // Check if application is locked
    const isApplicationLocked = {{ $hasSubmitted ? 'true' : 'false' }};
    
    // Set initial document visibility (regular documents shown, renewal hidden)
    document.addEventListener('DOMContentLoaded', function() {
        const regularDocs = document.getElementById('regular-documents');
        const renewalDocs = document.getElementById('renewal-documents');
        if (regularDocs) regularDocs.style.display = 'grid';
        if (renewalDocs) renewalDocs.style.display = 'none';
        
        // Disable all form inputs if application is locked
        if (isApplicationLocked) {
            const form = document.getElementById('applicationForm');
            if (form) {
                const inputs = form.querySelectorAll('input, select, textarea, button');
                inputs.forEach(input => {
                    if (input.type !== 'hidden' && input.id !== 'clearDraftBtn') {
                        input.disabled = true;
                    }
                });
            }
        }
    });
    
    // Define global functions IMMEDIATELY at the top to ensure they're available
    window.startNewApplication = function() {
        // Prevent starting new application if locked
        if (isApplicationLocked) {
            alert('You have already submitted an application. You cannot create a new one.');
            return;
        }
        
        const hubView = document.getElementById('application-hub');
        const formView = document.getElementById('application-form-view');
        
        if (!hubView || !formView) {
            console.error('Application hub or form view not found');
            return;
        }
        
        // Clear current draft ID
        window.currentDraftId = null;
        window.isRenewal = false;
        
        // Reset to step 1 for new application
        if (typeof currentStep !== 'undefined') {
            currentStep = 1;
        }
        
        // Trigger form show
        hubView.classList.add('hidden');
        formView.classList.remove('hidden');
        
        // Reset form
        const formEl = document.getElementById('applicationForm');
        if (formEl) {
            formEl.reset();
            const siblingList = document.getElementById('siblings-list');
            if (siblingList) {
                siblingList.innerHTML = '<p id="siblings-empty" class="p-6 text-sm text-slate-500 text-center border border-dashed border-slate-300 rounded-2xl">No siblings added yet.</p>';
            }
            refreshSiblingState();
        }
        
        // Update form header for new application
        const formHeader = document.querySelector('.form-header h2');
        if (formHeader) {
            formHeader.textContent = 'Scholarship Application';
        }
        
        // Set renewal flag
        const renewalInput = document.getElementById('is_renewal_input');
        if (renewalInput) {
            renewalInput.value = '0';
        }
        
        // Remove renewal notice if exists
        const renewalNotice = document.getElementById('renewal-notice');
        if (renewalNotice) {
            renewalNotice.remove();
        }
        
        // Show regular documents, hide renewal documents
        const regularDocs = document.getElementById('regular-documents');
        const renewalDocs = document.getElementById('renewal-documents');
        if (regularDocs) regularDocs.style.display = 'grid';
        if (renewalDocs) renewalDocs.style.display = 'none';
        
        // Show regular steps, hide renewal steps
        const regularSteps = document.getElementById('regular-steps');
        const renewalSteps = document.getElementById('renewal-steps');
        if (regularSteps) regularSteps.style.display = 'block';
        if (renewalSteps) renewalSteps.style.display = 'none';
        
        if (typeof updateUI === 'function') {
            updateUI();
        }
    };

    window.startRenewalApplication = function() {
        const hubView = document.getElementById('application-hub');
        const formView = document.getElementById('application-form-view');
        
        if (!hubView || !formView) {
            console.error('Application hub or form view not found');
            return;
        }
        
        // Clear current draft ID
        window.currentDraftId = null;
        window.isRenewal = true;
        
        // Set renewal mode - go directly to document step (step 6)
        if (typeof currentStep !== 'undefined') {
            currentStep = 6;
        }
        
        // Trigger form show
        hubView.classList.add('hidden');
        formView.classList.remove('hidden');
        
        // Show renewal steps, hide regular steps
        const regularSteps = document.getElementById('regular-steps');
        const renewalSteps = document.getElementById('renewal-steps');
        if (regularSteps) regularSteps.style.display = 'none';
        if (renewalSteps) renewalSteps.style.display = 'block';
        
        // Show renewal documents, hide regular documents
        const regularDocs = document.getElementById('regular-documents');
        const renewalDocs = document.getElementById('renewal-documents');
        if (regularDocs) regularDocs.style.display = 'none';
        if (renewalDocs) renewalDocs.style.display = 'grid';
        
        // Hide all form steps except step 6
        document.querySelectorAll('.form-step').forEach(step => {
            step.classList.add('hidden');
        });
        const step6 = document.getElementById('step-6');
        if (step6) {
            step6.classList.remove('hidden');
        }
        
        // Update form header for renewal
        const formHeader = document.querySelector('.form-header h2');
        if (formHeader) {
            formHeader.textContent = 'Scholarship Renewal - Document Upload';
        }
        
        // Update step counter
        const stepNum = document.getElementById('current-step-num');
        if (stepNum) {
            stepNum.textContent = '1';
        }
        
        // Update section title
        const sectionTitle = document.getElementById('form-section-title');
        if (sectionTitle) {
            sectionTitle.textContent = 'Document Requirements';
        }
        
        // Set renewal flag
        const renewalInput = document.getElementById('is_renewal_input');
        if (renewalInput) {
            renewalInput.value = '1';
        }
        
        // Hide navigation buttons (no prev/next needed for renewal)
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) nextBtn.style.display = 'none';
        if (submitBtn) {
            submitBtn.style.display = 'inline-flex';
            submitBtn.disabled = false;
        }
        
        // Remove pointer-events-none from form if it exists (for renewals)
        const form = document.getElementById('applicationForm');
        if (form) {
            form.classList.remove('pointer-events-none');
            form.removeAttribute('onsubmit');
            // Re-enable all form inputs
            const inputs = form.querySelectorAll('input, select, textarea, button');
            inputs.forEach(input => {
                if (input.type !== 'hidden' && input.id !== 'clearDraftBtn') {
                    input.disabled = false;
                }
            });
        }
        
        // Show renewal notice
        const formBody = document.querySelector('.form-body');
        if (formBody && !document.getElementById('renewal-notice')) {
            const notice = document.createElement('div');
            notice.id = 'renewal-notice';
            notice.className = 'mb-6 bg-gradient-to-r from-blue-50 via-cyan-50/50 to-blue-50 border-l-4 border-blue-500 rounded-xl shadow-lg p-6 animate-fadeIn';
            notice.innerHTML = `
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-base font-black text-blue-900 mb-2">Scholarship Renewal</h4>
                        <p class="text-sm text-blue-800 leading-relaxed">Please upload the required renewal documents: Certificate of Enrollment, Statement of Account, and GWA of Previous Semester.</p>
                    </div>
                </div>
            `;
            formBody.insertBefore(notice, formBody.firstChild);
        }
        
        // Update sidebar title
        const sidebarTitle = document.querySelector('.step-nav h1');
        if (sidebarTitle) {
            sidebarTitle.innerHTML = 'Scholarship<br>Renewal';
        }
        
        const sidebarDesc = document.querySelector('.step-nav p');
        if (sidebarDesc) {
            sidebarDesc.textContent = 'Upload required renewal documents.';
        }
        
        // Setup file input listeners for renewal documents
        setTimeout(() => {
            if (typeof setupFileInputListeners === 'function') {
                setupFileInputListeners();
            }
        }, 100);
    };

    window.continueDraft = function(draftId) {
        if (!draftId) {
            console.error('No draft ID provided');
            return;
        }
        
        console.log('Loading draft:', draftId);
        
        const hubView = document.getElementById('application-hub');
        const formView = document.getElementById('application-form-view');
        
        if (!hubView || !formView) {
            console.error('Application hub or form view not found');
            return;
        }
        
        window.currentDraftId = draftId;
        hubView.classList.add('hidden');
        formView.classList.remove('hidden');
        
        // Load draft from server
        const url = `/student/drafts/${draftId}`;
        console.log('Fetching draft from:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(err => {
                    console.error('Error response:', err);
                    throw new Error(err.message || `HTTP error! status: ${response.status}`);
                }).catch(parseError => {
                    // If JSON parsing fails, throw with status
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(result => {
            console.log('Draft loaded successfully:', result);
            if (result.success && result.draft) {
                if (typeof window.restoreDraftFromData === 'function') {
                    window.restoreDraftFromData(result.draft);
                } else {
                    console.error('restoreDraftFromData function not available');
                    alert('Error: Draft restoration function not available. Please refresh the page.');
                    return;
                }
                if (typeof updateUI === 'function') {
                    updateUI();
                }
            } else {
                console.error('Failed to load draft:', result);
                alert('Failed to load draft: ' + (result.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading draft:', error);
            alert('Error loading draft: ' + error.message);
        });
    };

    window.returnToHub = function() {
        const hubView = document.getElementById('application-hub');
        const formView = document.getElementById('application-form-view');
        const draftsContainer = document.getElementById('hub-recent-drafts');
        
        if (!hubView || !formView) return;
        
        hubView.classList.remove('hidden');
        formView.classList.add('hidden');
        
        // Re-render drafts list to show any updates
        if (draftsContainer && window.renderDraftsList) {
            window.renderDraftsList();
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Sibling modal functions - define early to ensure availability
    window.openSiblingModal = function() {
        const siblingModal = document.getElementById('siblingModal');
        const siblingBackdrop = document.getElementById('siblingModalBackdrop');
        
        if (!siblingModal || !siblingBackdrop) {
            console.error('Sibling modal elements not found');
            return;
        }
        
        siblingModal.classList.remove('hidden');
        siblingModal.classList.add('flex');
        siblingBackdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    window.closeSiblingModal = function() {
        const siblingModal = document.getElementById('siblingModal');
        const siblingBackdrop = document.getElementById('siblingModalBackdrop');
        
        if (!siblingModal || !siblingBackdrop) {
            console.error('Sibling modal elements not found');
            return;
        }
        
        siblingModal.classList.add('hidden');
        siblingModal.classList.remove('flex');
        siblingBackdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        
        // Clear modal inputs
        const nameInput = document.getElementById('modal_sibling_name');
        const ageInput = document.getElementById('modal_sibling_age');
        const scholarshipInput = document.getElementById('modal_sibling_scholarship');
        const courseInput = document.getElementById('modal_sibling_course');
        const statusInput = document.getElementById('modal_sibling_status');
        
        if (nameInput) nameInput.value = '';
        if (ageInput) ageInput.value = '';
        if (scholarshipInput) scholarshipInput.value = '';
        if (courseInput) courseInput.value = '';
        if (statusInput) statusInput.value = '';
    };

    window.saveSiblingFromModal = function() {
        const nameInput = document.getElementById('modal_sibling_name');
        if (!nameInput) {
            console.error('Sibling name input not found');
            return;
        }
        
        const name = nameInput.value.trim();
        if (!name) {
            alert('Name is required');
            return;
        }

        const siblingData = {
            name,
            age: document.getElementById('modal_sibling_age')?.value || '',
            scholarship: document.getElementById('modal_sibling_scholarship')?.value || '',
            course: document.getElementById('modal_sibling_course')?.value || '',
            status: document.getElementById('modal_sibling_status')?.value || '',
        };

        if (typeof window.addSibling === 'function') {
            window.addSibling(siblingData);
        }
        window.closeSiblingModal();
    };

    let currentStep = 1;
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
        // Step is tracked in currentStep variable and saved with draft
        
        // Check if in renewal mode
        const isRenewalMode = window.isRenewal === true;

        // Hide all steps
        document.querySelectorAll('.form-step').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('fade-enter-active');
        });
        
        // For renewal mode, only show step 6 (documents)
        if (isRenewalMode) {
            const step6 = document.getElementById('step-6');
            if (step6) {
                step6.classList.remove('hidden');
                setTimeout(() => step6.classList.add('fade-enter-active'), 10);
            }
            
            // Hide regular documents, show renewal documents
            const regularDocs = document.getElementById('regular-documents');
            const renewalDocs = document.getElementById('renewal-documents');
            if (regularDocs) regularDocs.style.display = 'none';
            if (renewalDocs) renewalDocs.style.display = 'grid';
            
            // Update sidebar nav for renewal (only one step)
            const renewalStep = document.getElementById('nav-step-renewal');
            if (renewalStep) {
                document.querySelectorAll('.step-item').forEach(el => {
                    el.classList.remove('active', 'completed');
                });
                renewalStep.classList.add('active');
            }
            
            // Update header info for renewal
            const stepNum = document.getElementById('current-step-num');
            if (stepNum) stepNum.textContent = '1';
            
            const sectionTitle = document.getElementById('form-section-title');
            if (sectionTitle) sectionTitle.textContent = 'Document Requirements';
            
            // Hide navigation buttons, show submit
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            if (prevBtn) prevBtn.style.display = 'none';
            if (nextBtn) nextBtn.style.display = 'none';
            if (submitBtn) {
                submitBtn.style.display = 'inline-flex';
                submitBtn.disabled = false;
            }
            
            // Remove pointer-events-none from form if it exists (for renewals)
            const form = document.getElementById('applicationForm');
            if (form) {
                form.classList.remove('pointer-events-none');
                form.removeAttribute('onsubmit');
                // Re-enable all form inputs
                const inputs = form.querySelectorAll('input, select, textarea, button');
                inputs.forEach(input => {
                    if (input.type !== 'hidden' && input.id !== 'clearDraftBtn') {
                        input.disabled = false;
                    }
                });
            }
        } else {
            // Hide renewal documents, show regular documents
            const regularDocs = document.getElementById('regular-documents');
            const renewalDocs = document.getElementById('renewal-documents');
            if (regularDocs) regularDocs.style.display = 'grid';
            if (renewalDocs) renewalDocs.style.display = 'none';
            // Regular application flow
            const currentStepEl = document.getElementById('step-' + currentStep);
            if (currentStepEl) {
                currentStepEl.classList.remove('hidden');
                setTimeout(() => currentStepEl.classList.add('fade-enter-active'), 10);
            }

            // Update sidebar nav (regular steps)
            document.querySelectorAll('#regular-steps .step-item').forEach((el, idx) => {
                el.classList.remove('active', 'completed');
                if (idx + 1 === currentStep) el.classList.add('active');
                if (idx + 1 < currentStep) el.classList.add('completed');
            });

            // Update header info
            const stepNum = document.getElementById('current-step-num');
            if (stepNum) stepNum.textContent = currentStep;
            
            const titles = [
                'Personal Information', 
                'Address Details', 
                'Educational Background', 
                'Family Information', 
                'School Preference',
                'Document Requirements'
            ];
            const sectionTitle = document.getElementById('form-section-title');
            if (sectionTitle && titles[currentStep - 1]) {
                sectionTitle.textContent = titles[currentStep - 1];
            }

            // Buttons
            document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'inline-flex';
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            if (nextBtn) nextBtn.style.display = currentStep === totalSteps ? 'none' : 'inline-flex';
            if (submitBtn) submitBtn.style.display = currentStep === totalSteps ? 'inline-flex' : 'none';
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (isApplicationLocked) {
            alert('You have already submitted an application. You cannot proceed.');
            return;
        }
        if (validateStep(currentStep)) {
            currentStep++;
            updateUI();
        }
    });

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (isApplicationLocked) {
            alert('You have already submitted an application. You cannot proceed.');
            return;
        }
        if (currentStep > 1) {
            currentStep--;
            updateUI();
        }
    });

    function validateStep(step) {
        // For renewal mode, only validate documents (step 6)
        if (window.isRenewal === true) {
            const stepEl = document.getElementById('step-6');
            if (!stepEl) return true;
            
            // Check if at least one renewal document is uploaded
            const renewalDocs = document.getElementById('renewal-documents');
            if (!renewalDocs) return true;
            
            const fileInputs = renewalDocs.querySelectorAll('.doc-file-input');
            let hasFile = false;
            fileInputs.forEach(input => {
                if (input.files && input.files.length > 0) {
                    hasFile = true;
                }
            });
            
            // Check for already uploaded documents
            const uploadedDocs = renewalDocs.querySelectorAll('a[href*="documents/view"]');
            if (uploadedDocs.length > 0) {
                hasFile = true;
            }
            
            if (!hasFile) {
                alert('Please upload at least one required renewal document.');
                return false;
            }
            
            return true;
        }
        
        // Regular validation for non-renewal applications
        const stepEl = document.getElementById('step-' + step);
        if (!stepEl) return true;
        
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
                        // Dispatch custom event when barangays are loaded
                        brgySelect.dispatchEvent(new CustomEvent('barangaysLoaded', { bubbles: true }));
                    });
            });
        }
    };
    ['mailing', 'permanent', 'origin'].forEach(setupLocation);

    // Copy mailing address functionality
    function copyMailingAddressTo(targetPrefix) {
        const mailingFields = {
            municipality: document.getElementById('mailing_municipality'),
            barangay: document.getElementById('mailing_barangay'),
            house_num: document.getElementById('mailing_house_num')
        };

        const targetFields = {
            municipality: document.getElementById(`${targetPrefix}_municipality`),
            barangay: document.getElementById(`${targetPrefix}_barangay`),
            house_num: document.getElementById(`${targetPrefix}_house_num`)
        };

        // Copy municipality
        if (mailingFields.municipality && targetFields.municipality) {
            const mailingMuniValue = mailingFields.municipality.value;
            const mailingBrgyValue = mailingFields.barangay ? mailingFields.barangay.value : '';
            
            // Set municipality value
            targetFields.municipality.value = mailingMuniValue;
            
            // Set up one-time listener for when barangays are loaded
            const onBarangaysLoaded = () => {
                if (mailingBrgyValue && targetFields.barangay) {
                    // Check if the barangay option exists
                    const optionExists = Array.from(targetFields.barangay.options).some(
                        opt => opt.value === mailingBrgyValue
                    );
                    if (optionExists) {
                        targetFields.barangay.value = mailingBrgyValue;
                        targetFields.barangay.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
                // Remove the listener after use
                targetFields.barangay.removeEventListener('barangaysLoaded', onBarangaysLoaded);
            };
            
            // Add listener before triggering change
            if (targetFields.barangay) {
                targetFields.barangay.addEventListener('barangaysLoaded', onBarangaysLoaded, { once: true });
            }
            
            // Trigger change event to load barangays
            targetFields.municipality.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Fallback: if barangays are already loaded (same municipality), set immediately
            if (targetFields.barangay && mailingBrgyValue) {
                const optionExists = Array.from(targetFields.barangay.options).some(
                    opt => opt.value === mailingBrgyValue
                );
                if (optionExists) {
                    targetFields.barangay.value = mailingBrgyValue;
                    targetFields.barangay.dispatchEvent(new Event('change', { bubbles: true }));
                    // Remove the listener since we already set it
                    targetFields.barangay.removeEventListener('barangaysLoaded', onBarangaysLoaded);
                }
            }
        }

        // Copy house number
        if (mailingFields.house_num && targetFields.house_num) {
            targetFields.house_num.value = mailingFields.house_num.value;
        }
    }

    // Setup "Same as Mailing Address" checkboxes
    document.querySelectorAll('.same-as-mailing-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const targetPrefix = this.dataset.targetPrefix;
            const addressFields = document.getElementById(`address-fields-${targetPrefix}`);
            
            if (this.checked) {
                // Copy mailing address values
                copyMailingAddressTo(targetPrefix);
                
                // Disable target fields
                if (addressFields) {
                    addressFields.querySelectorAll('select, input:not([readonly])').forEach(field => {
                        field.disabled = true;
                        field.classList.add('bg-slate-50', 'cursor-not-allowed');
                    });
                }
            } else {
                // Enable target fields
                if (addressFields) {
                    addressFields.querySelectorAll('select, input:not([readonly])').forEach(field => {
                        field.disabled = false;
                        field.classList.remove('bg-slate-50', 'cursor-not-allowed');
                    });
                }
            }
        });

        // Also copy when mailing address changes (if checkbox is checked)
        const targetPrefix = checkbox.dataset.targetPrefix;
        ['municipality', 'barangay', 'house_num'].forEach(fieldType => {
            const mailingField = document.getElementById(`mailing_${fieldType}`);
            if (mailingField) {
                mailingField.addEventListener('change', function() {
                    if (checkbox.checked) {
                        copyMailingAddressTo(targetPrefix);
                    }
                });
            }
        });
    });

    // Setup occupation "Other" field toggle
    document.querySelectorAll('.occupation-select').forEach(select => {
        const parentPrefix = select.id.replace('_occupation', '');
        const otherInput = document.getElementById(`${parentPrefix}_occupation_other`);
        
        if (otherInput) {
            // Initial check for existing value
            if (select.value === 'Other') {
                otherInput.classList.remove('hidden');
                otherInput.setAttribute('required', 'required');
            }
            
            select.addEventListener('change', function() {
                if (this.value === 'Other') {
                    otherInput.classList.remove('hidden');
                    otherInput.setAttribute('required', 'required');
                } else {
                    otherInput.classList.add('hidden');
                    otherInput.removeAttribute('required');
                    otherInput.value = '';
                }
            });
        }
    });

    // Draft persistence using database (AJAX)
    (function() {
        const formEl = document.getElementById('applicationForm');
        window.currentDraftId = null; // Track current draft ID

        if (!formEl) {
            return;
        }

        let saveTimeout;
        const scheduleDraftSave = () => {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(saveDraft, 1000); // Increased delay to reduce server load
        };

        function collectFormData() {
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
            return data;
        }

        function saveDraft() {
            const data = collectFormData();

            // Prepare request payload
            const payload = {
                draft_id: window.currentDraftId,
                current_step: currentStep || 1,
                form_data: data,
            };

            // Try to construct a name from form data
            const fname = data['first_name'] || '';
            const lname = data['last_name'] || '';
            if (fname || lname) {
                payload.name = `${fname} ${lname}`.trim() + ' - Scholarship Application';
            }

            // Save to server
            fetch('/student/drafts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value,
                },
                body: JSON.stringify(payload),
            })
            .then(response => response.json())
            .then(result => {
                if (result.success && result.draft) {
                    window.currentDraftId = result.draft.id;
                    updateDraftUI(result.draft.updated_at);
                }
            })
            .catch(error => {
                console.error('Error saving draft:', error);
                // Silently fail - don't interrupt user experience
            });
        }

        // Track if form is being submitted normally (to prevent duplicate save on unload)
        let isSubmitting = false;

        // Auto-save on page unload/refresh using fetch with keepalive for reliability
        function saveDraftOnUnload() {
            // Don't save if form is being submitted normally
            if (isSubmitting) {
                return;
            }

            // Check if form has any data worth saving
            const data = collectFormData();
            const hasData = Object.keys(data).some(key => {
                const value = data[key];
                if (Array.isArray(value)) {
                    return value.length > 0 && value.some(v => v && v.toString().trim() !== '');
                }
                return value && value.toString().trim() !== '';
            });

            if (!hasData) {
                return; // No data to save
            }

            // Prepare payload
            const payload = {
                draft_id: window.currentDraftId,
                current_step: currentStep || 1,
                form_data: data,
            };

            const fname = data['first_name'] || '';
            const lname = data['last_name'] || '';
            if (fname || lname) {
                payload.name = `${fname} ${lname}`.trim() + ' - Scholarship Application';
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value;

            // Use fetch with keepalive flag for reliable delivery during page unload
            // keepalive ensures the request continues even after page unloads
            fetch('/student/drafts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
                keepalive: true, // Critical: allows request to complete after page unload
            }).catch(error => {
                // Silently fail - we don't want to interrupt page navigation
                console.error('Error auto-saving draft on unload:', error);
            });
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

        window.restoreDraftFromData = function(draft) {
            if (!draft) {
                console.error('No draft data provided');
                formEl.reset();
                const siblingList = document.getElementById('siblings-list');
                if (siblingList) {
                    siblingList.innerHTML = '<p id="siblings-empty" class="p-6 text-sm text-slate-500 text-center border border-dashed border-slate-300 rounded-2xl">No siblings added yet.</p>';
                }
                refreshSiblingState();
                updateDraftUI(null);
                return;
            }

            window.currentDraftId = draft.id;
            if (draft.current_step) {
                currentStep = parseInt(draft.current_step) || 1;
            }
            updateDraftUI(draft.updated_at);

            const data = draft.form_data || {};
            
            // If form_data is empty or null, just reset the form
            if (!data || Object.keys(data).length === 0) {
                console.warn('Draft has no form data, resetting form');
                formEl.reset();
                const siblingList = document.getElementById('siblings-list');
                if (siblingList) {
                    siblingList.innerHTML = '<p id="siblings-empty" class="p-6 text-sm text-slate-500 text-center border border-dashed border-slate-300 rounded-2xl">No siblings added yet.</p>';
                }
                refreshSiblingState();
                return;
            }

            // Reset sibling list
            const siblingList = document.getElementById('siblings-list');
            if (siblingList) {
                siblingList.innerHTML = '';
                // Recreate the empty state element if it doesn't exist
                const emptyElement = document.getElementById('siblings-empty');
                if (!emptyElement) {
                    const emptyP = document.createElement('p');
                    emptyP.id = 'siblings-empty';
                    emptyP.className = 'p-6 text-sm text-slate-500 text-center border border-dashed border-slate-300 rounded-2xl';
                    emptyP.textContent = 'No siblings added yet.';
                    siblingList.appendChild(emptyP);
                }
            }

            // Ensure sibling entries exist before populating
            Object.entries(data).forEach(([name, value]) => {
                if (Array.isArray(value) && name.startsWith('sibling_') && name.endsWith('[]')) {
                    ensureSiblingEntries(value.length);
                }
            });

            // Populate form fields
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

        // Auto-save on page unload/refresh to prevent data loss
        let isUnloading = false;
        window.addEventListener('beforeunload', function(e) {
            // Only save if we're in the form view (not hub view)
            const formView = document.getElementById('application-form-view');
            if (!formView || formView.classList.contains('hidden')) {
                return;
            }

            // Check if application is locked (don't save if locked)
            if (isApplicationLocked) {
                return;
            }

            // Save draft before page unloads
            saveDraftOnUnload();
            
            // Optional: Show browser confirmation dialog
            // Uncomment the lines below if you want to warn users before leaving
            // const message = 'You have unsaved changes. Are you sure you want to leave?';
            // e.returnValue = message;
            // return message;
        });

        // Also save on visibility change (when user switches tabs/apps)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Page is now hidden, save draft
                const formView = document.getElementById('application-form-view');
                if (formView && !formView.classList.contains('hidden') && !isApplicationLocked) {
                    // Use regular saveDraft for visibility change (not unload)
                    saveDraft();
                }
            }
        });

        const clearDraftBtn = document.getElementById('clearDraftBtn');
        clearDraftBtn?.addEventListener('click', () => {
            if (!window.currentDraftId) {
                alert('No draft to delete.');
                return;
            }
            
            if (confirm('Delete this draft permanently?')) {
                fetch(`/student/drafts/${window.currentDraftId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value,
                    },
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        window.currentDraftId = null;
                        window.returnToHub();
                    } else {
                        alert('Failed to delete draft.');
                    }
                })
                .catch(error => {
                    console.error('Error deleting draft:', error);
                    alert('Error deleting draft. Please try again.');
                });
            }
        });

        // Toast notification function
        window.showToast = function(title, message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const iconSvg = type === 'success' 
                ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';

            toast.innerHTML = `
                <div class="toast-icon">
                    ${iconSvg}
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
            `;

            container.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        };

        formEl.addEventListener('submit', (e) => {
            // Mark as submitting to prevent auto-save on unload
            isSubmitting = true;

            // Prevent submission if application is locked
            if (isApplicationLocked) {
                e.preventDefault();
                isSubmitting = false; // Reset flag if submission prevented
                alert('You have already submitted an application. You cannot submit another one.');
                return false;
            }
            
            // Validate GPA if grades document is being uploaded
            const gradesFileInput = formEl.querySelector('input[name="documents[grades]"]');
            const gwaFileInput = formEl.querySelector('input[name="documents[gwa_previous_sem]"]');
            const gpaInputGrades = document.getElementById('gpa-input-grades');
            const gpaInputRenewal = document.getElementById('gpa-input-renewal');
            
            if (gradesFileInput && gradesFileInput.files && gradesFileInput.files.length > 0) {
                if (!gpaInputGrades || !gpaInputGrades.value || gpaInputGrades.value.trim() === '') {
                    e.preventDefault();
                    isSubmitting = false; // Reset flag if validation fails
                    alert('Please enter your GPA when uploading grades document.');
                    if (gpaInputGrades) {
                        gpaInputGrades.focus();
                        gpaInputGrades.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }
            }
            
            if (gwaFileInput && gwaFileInput.files && gwaFileInput.files.length > 0) {
                if (!gpaInputRenewal || !gpaInputRenewal.value || gpaInputRenewal.value.trim() === '') {
                    e.preventDefault();
                    isSubmitting = false; // Reset flag if validation fails
                    alert('Please enter your GPA when uploading GWA document.');
                    if (gpaInputRenewal) {
                        gpaInputRenewal.focus();
                        gpaInputRenewal.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }
            }
            
            // Remove required attribute from hidden GPA inputs to prevent validation errors
            const allGpaInputs = formEl.querySelectorAll('input[name="gpa"]');
            allGpaInputs.forEach(input => {
                // Check if input is in a hidden step or not visible
                const stepContainer = input.closest('.form-step');
                if (stepContainer && stepContainer.classList.contains('hidden')) {
                    input.removeAttribute('required');
                }
            });
            
            // Delete draft on successful submission
            if (window.currentDraftId) {
                fetch(`/student/drafts/${window.currentDraftId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value,
                    },
                })
                .then(() => {
                    window.currentDraftId = null;
                })
                .catch(error => {
                    console.error('Error deleting draft on submit:', error);
                });
            }
        });

        // Expose save function for the manual button
        window.saveDraftManual = function() {
            saveDraft();
            // Wait a bit for the save to complete
            setTimeout(() => {
                showSuccessModal('Draft Saved!', 'Application draft saved successfully!', () => {
                if (window.returnToHub) {
                    window.returnToHub();
                }
                });
            }, 500);
        };
    })();

    // Manual Save Draft Button
    function setupSaveDraftButton() {
        const saveDraftBtn = document.getElementById('saveDraftBtn');
        if (saveDraftBtn) {
            // Remove any existing listeners by cloning the button
            const newBtn = saveDraftBtn.cloneNode(true);
            saveDraftBtn.parentNode.replaceChild(newBtn, saveDraftBtn);
            
            newBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (window.saveDraftManual) {
                    window.saveDraftManual();
                } else {
                    console.error('saveDraftManual function not found');
                    alert('Error: Save draft function not available. Please refresh the page.');
                }
            });
        }
    }
    
    // Success Modal Functions
    window.showSuccessModal = function(title, message, onClose) {
        const modal = document.getElementById('successModal');
        const titleEl = document.getElementById('successModalTitle');
        const messageEl = document.getElementById('successModalMessage');
        
        if (modal && titleEl && messageEl) {
            titleEl.textContent = title;
            messageEl.textContent = message;
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            // Store callback
            if (onClose) {
                modal.dataset.onClose = 'true';
                window.successModalCallback = onClose;
            }
        }
    };

    window.closeSuccessModal = function() {
        const modal = document.getElementById('successModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            
            // Execute callback if exists
            if (modal.dataset.onClose === 'true' && window.successModalCallback) {
                window.successModalCallback();
                delete window.successModalCallback;
                delete modal.dataset.onClose;
            }
        }
    };

    // Close modal on backdrop click
    document.getElementById('successModal')?.addEventListener('click', function(e) {
        if (e.target === this || e.target.classList.contains('bg-black/60')) {
            closeSuccessModal();
        }
    });
    
    // Setup when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupSaveDraftButton);
    } else {
        setupSaveDraftButton();
    }
    
    // Also setup when form view becomes visible (in case it's hidden initially)
    const formView = document.getElementById('application-form-view');
    if (formView) {
        const observer = new MutationObserver(function(mutations) {
            if (!formView.classList.contains('hidden')) {
                setupSaveDraftButton();
            }
        });
        observer.observe(formView, { attributes: true, attributeFilter: ['class'] });
    }

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

    // Sibling modal logic - functions are now defined at the top of the script

    function updateSiblingLabels() {
        const items = document.querySelectorAll('#siblings-list .sibling-item .sibling-index');
        items.forEach((node, index) => {
            node.textContent = `${index + 1}.`;
        });
    }

    function refreshSiblingState() {
        const list = document.getElementById('siblings-list');
        if (!list) return;
        
        const hasItems = list.children.length > 0;
        let emptyElement = document.getElementById('siblings-empty');
        
        // Create the empty element if it doesn't exist
        if (!emptyElement) {
            emptyElement = document.createElement('p');
            emptyElement.id = 'siblings-empty';
            emptyElement.className = 'p-6 text-sm text-slate-500 text-center border border-dashed border-slate-300 rounded-2xl';
            emptyElement.textContent = 'No siblings added yet.';
            list.appendChild(emptyElement);
        }
        
        emptyElement.classList.toggle('hidden', hasItems);
        updateSiblingLabels();
    }

    // Document Upload Logic
    const uploadAllBtn = document.getElementById('upload-all-btn');
    
    // File selection feedback - use event delegation to handle dynamically loaded content
    function setupFileInputListeners() {
        document.querySelectorAll('.doc-file-input').forEach(input => {
            // Check if listener already attached
            if (input.dataset.listenerAttached === 'true') {
                return;
            }
            input.dataset.listenerAttached = 'true';
            
            input.addEventListener('change', function() {
                const container = this.closest('.doc-upload-container');
                if (!container) {
                    console.error('Container not found for file input');
                    return;
                }
                
                const label = container.querySelector('label');
                // file-name-display is a sibling of doc-upload-container within the same parent
                const parentContainer = container.parentElement;
                const fileNameDisplay = parentContainer ? parentContainer.querySelector('.file-name-display') : null;
                
                if (!label) {
                    console.error('Label not found');
                    return;
                }
                
                const clickText = label.querySelector('p');
                const icon = label.querySelector('svg');

                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    // Update visual state
                    label.classList.remove('bg-slate-50', 'border-slate-300');
                    label.classList.add('bg-orange-50', 'border-orange-400');
                    
                    // Update text and icon
                    if (icon) {
                        icon.classList.remove('text-slate-400');
                        icon.classList.add('text-orange-500');
                    }
                    
                    if (clickText) {
                        clickText.innerHTML = `<span class="font-semibold text-orange-700">Selected:</span> ${file.name}`;
                    }
                    
                    // Show file name and size
                    if (fileNameDisplay) {
                        const size = (file.size / 1024 / 1024).toFixed(2); // MB
                        fileNameDisplay.textContent = `${file.name} (${size} MB)`;
                        fileNameDisplay.classList.remove('hidden');
                    }
                    
                    // If this is a grades or gwa_previous_sem file input, make GPA required
                    const fileInputName = this.name;
                    if (fileInputName.includes('grades') || fileInputName.includes('gwa_previous_sem')) {
                        const gpaInput = document.getElementById('gpa-input-grades') || document.getElementById('gpa-input-renewal');
                        if (gpaInput) {
                            gpaInput.setAttribute('required', 'required');
                        }
                    }
                } else {
                    // Reset state
                    label.classList.add('bg-slate-50', 'border-slate-300');
                    label.classList.remove('bg-orange-50', 'border-orange-400');
                    if (icon) {
                        icon.classList.add('text-slate-400');
                        icon.classList.remove('text-orange-500');
                    }
                    if (clickText) {
                        clickText.innerHTML = `<span class="font-semibold">Click to upload</span> PDF or Image`;
                    }
                    if (fileNameDisplay) {
                        fileNameDisplay.classList.add('hidden');
                    }
                    
                    // If this is a grades or gwa_previous_sem file input, remove GPA required
                    const fileInputName = this.name;
                    if (fileInputName.includes('grades') || fileInputName.includes('gwa_previous_sem')) {
                        const gpaInput = document.getElementById('gpa-input-grades') || document.getElementById('gpa-input-renewal');
                        if (gpaInput) {
                            gpaInput.removeAttribute('required');
                        }
                    }
                }
            });
        });
    }
    
    // Setup listeners initially
    setupFileInputListeners();
    
    // Re-setup listeners when step 6 becomes visible (when updateUI is called)
    const originalUpdateUI = updateUI;
    updateUI = function() {
        originalUpdateUI();
        // If we're on step 6, setup file input listeners
        if (currentStep === 6) {
            setTimeout(setupFileInputListeners, 100);
        }
    };

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
    // Check if we should show the form or hub
    ( function initView() {
        const hubView = document.getElementById('application-hub');
        const formView = document.getElementById('application-form-view');
        const draftsContainer = document.getElementById('hub-recent-drafts');
        
        if (!hubView || !formView) return;

        function renderDraftsList() {
            fetch('/student/drafts', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(result => {
                if (result.success && result.drafts) {
                    const drafts = result.drafts;
                    
                    if (drafts.length > 0) {
                        draftsContainer.innerHTML = drafts.map(draft => {
                            const timestamp = new Date(draft.updated_at);
                            const timeStr = timestamp.toLocaleDateString() + ' ' + timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                            const title = draft.name || 'Scholarship Application';

                            return `
                            <button type="button" onclick="continueDraft('${draft.id}')" class="flex items-center p-4 bg-white border border-slate-200 rounded-xl shadow-sm hover:border-orange-300 hover:shadow-md transition-all group text-left w-full mb-3">
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
                } else {
                    draftsContainer.innerHTML = `
                        <div class="text-center py-12 bg-white rounded-xl border border-dashed border-slate-300 text-slate-400 text-sm">
                            No recent drafts found
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading drafts:', error);
                draftsContainer.innerHTML = `
                    <div class="text-center py-12 bg-white rounded-xl border border-dashed border-slate-300 text-slate-400 text-sm">
                        Error loading drafts
                    </div>
                `;
            });
        }
        
        // Expose renderDraftsList globally for returnToHub
        window.renderDraftsList = renderDraftsList;

        renderDraftsList();
    })();


</script>
@endpush 
@endsection
