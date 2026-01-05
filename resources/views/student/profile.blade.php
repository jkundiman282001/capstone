@extends('layouts.student')

@section('title', 'My Profile - IP Scholar Portal')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }
    
    @keyframes bounce-slow {
        0%, 100% {
            transform: translateY(-50%) translateX(0);
        }
        50% {
            transform: translateY(-50%) translateX(-8px);
        }
    }
    
    .animate-bounce-slow {
        animation: bounce-slow 2s ease-in-out infinite;
    }
    
    @media (max-width: 768px) {
        #year-level-guide {
            position: fixed !important;
            right: 1rem !important;
            left: 1rem !important;
            top: auto !important;
            bottom: 2rem !important;
            transform: none !important;
            max-width: calc(100% - 2rem) !important;
        }
        
        #year-level-guide .absolute.left-0 {
            display: none;
        }
    }
</style>
@endpush

@push('head-scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
@endpush

@section('content')
@php
    $student = $student ?? auth()->user();
    $fullName = trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name);
    $studentNumber = $student->student_number ?? ('IP-' . str_pad($student->id, 5, '0', STR_PAD_LEFT));
    $courseName = $student->course ?? 'Course not set';
    $ethnicity = optional($student->ethno)->ethnicity ?? 'Not declared';
    $applicationStatus = $applicationStatus ?? 'pending';
    $isRejected = $applicationStatus === 'rejected';
    $isValidated = $applicationStatus === 'validated';
    $grantStatus = $grantStatus ?? optional($student->basicInfo)->grant_status;
    $isGrantee = $grantStatus === 'grantee';
    $isWaitingList = $grantStatus === 'waiting_list';
    
    if ($isGrantee) {
        $statusLabel = 'Grantee';
        $statusClasses = 'text-green-700 bg-green-50 border-green-200';
    } elseif ($isRejected) {
        $statusLabel = 'Rejected';
        $statusClasses = 'text-red-700 bg-red-50 border-red-200';
    } elseif ($isValidated) {
        $statusLabel = 'Validated';
        $statusClasses = 'text-green-700 bg-green-50 border-green-200';
    } else {
        $statusLabel = 'Pending';
        $statusClasses = 'text-amber-700 bg-amber-50 border-amber-200';
    }
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-12 pt-20 relative overflow-hidden selection:bg-orange-100 selection:text-orange-900">
    
    <!-- Decorative Background Elements -->
    <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-orange-50/80 via-white to-transparent pointer-events-none"></div>
    <div class="absolute -top-[20%] -right-[10%] w-[800px] h-[800px] bg-gradient-to-br from-blue-50/40 to-purple-50/40 rounded-full blur-3xl pointer-events-none"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Sidebar (Profile Card) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Main Profile Card -->
                <div class="glass-card rounded-3xl p-8 text-center relative group hover:shadow-xl hover:shadow-orange-100/50 transition-all duration-500">
                    
                    <!-- Avatar Section -->
                    <div class="relative inline-block mx-auto mb-6">
                        <div class="w-36 h-36 rounded-full p-1.5 bg-white shadow-2xl shadow-orange-100/50 cursor-pointer group/avatar relative z-10 transition-transform duration-300 hover:scale-105" onclick="document.getElementById('profile-pic-input').click()">
                            <div class="w-full h-full rounded-full overflow-hidden relative bg-slate-50">
                                <img
                                    id="profile-pic-image"
                                    src="{{ $student->profile_pic_url ?? '' }}"
                                    alt="Profile"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover/avatar:scale-110 {{ $student->profile_pic_url ? '' : 'hidden' }}"
                                    onerror="this.style.display='none'; document.getElementById('profile-pic-placeholder').classList.remove('hidden'); document.getElementById('profile-pic-placeholder').classList.add('flex');"
                                >
                                <div id="profile-pic-placeholder" class="w-full h-full flex items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100 text-orange-300 {{ $student->profile_pic ? 'hidden' : 'flex' }}">
                                    <i data-lucide="user" class="w-16 h-16"></i>
                                </div>
                                
                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 group-hover/avatar:opacity-100 transition-opacity duration-300 backdrop-blur-[2px]">
                                    <i data-lucide="camera" class="w-8 h-8 text-white drop-shadow-lg"></i>
                                </div>
                            </div>
                            
                            <button class="absolute bottom-1 right-1 p-2.5 rounded-2xl bg-white text-slate-600 hover:text-orange-600 shadow-lg border border-slate-100 transition-all hover:scale-110 hover:-rotate-12 group-hover/avatar:translate-x-1 group-hover/avatar:translate-y-1">
                                <i data-lucide="pen-line" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <input type="file" id="profile-pic-input" accept="image/*" class="hidden" onchange="uploadProfilePic(this)">
                    </div>

                    <h2 class="text-2xl font-bold text-slate-800 mb-1 tracking-tight">{{ $fullName }}</h2>
                    <p class="text-sm text-slate-500 mb-6 font-medium">{{ $student->email }}</p>
                    <div class="flex justify-center mb-4">
                        <span class="px-4 py-1.5 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="flex flex-wrap justify-center gap-2 mb-8">
                        @if($isGrantee)
                            <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100 shadow-sm flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                    <circle cx="12" cy="8" r="6"/>
                                    <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                                </svg>
                                Official Grantee
                            </span>
                        @elseif($isWaitingList)
                            <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-100 shadow-sm flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                Waiting List
                            </span>
                        @endif
                        
                        <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-600 border border-orange-100 shadow-sm">IP Scholar</span>
                        @if($isGrantee)
                            @if($courseName !== 'Course not set')
                                <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-100 shadow-sm">{{ $courseName }}</span>
                            @endif
                            @if(optional($student->basicInfo)->current_year_level)
                                <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">{{ $student->basicInfo->current_year_level }} Year</span>
                            @endif
                        @endif
                    </div>
                    
                    <!-- Apply Button in Sidebar -->
                    <a href="{{ route('student.apply') }}" class="w-full btn bg-slate-900 text-white hover:bg-slate-800 rounded-xl py-3.5 font-bold shadow-lg shadow-slate-900/20 hover:shadow-slate-900/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 group">
                        <span>Apply for Scholarship</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Right Content -->
            <div class="lg:col-span-8 space-y-8">
                
                @if($isGrantee)
                <!-- Grantee Success Banner -->
                <div class="glass-card rounded-[2rem] shadow-xl shadow-green-200/40 overflow-hidden border-2 border-green-200 mb-8">
                    <div class="px-8 py-6 bg-gradient-to-r from-green-50 to-emerald-50">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                    <circle cx="12" cy="8" r="6"/>
                                    <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-black text-green-900 mb-1">Official Scholarship Grantee</h3>
                                <p class="text-sm text-green-800 mb-3 leading-relaxed">Congratulations! You are officially recognized as a scholarship grantee. Your hard work and dedication have been rewarded.</p>
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/60 rounded-lg border border-green-200">
                                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                    <span class="text-xs font-bold text-green-700 uppercase tracking-wider">Active Grantee Status</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($isWaitingList)
                <!-- Waiting List Information Banner -->
                <div class="glass-card rounded-[2rem] shadow-xl shadow-blue-200/40 overflow-hidden border-2 border-blue-200 mb-8">
                    <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-black text-blue-900 mb-1">Scholarship Waiting List</h3>
                                <p class="text-sm text-blue-800 mb-3 leading-relaxed">Your application has been validated and placed on the official waiting list. You will be notified immediately if a slot becomes available.</p>
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/60 rounded-lg border border-blue-200">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Waiting List Priority Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($isRejected && isset($rejectionReason) && $rejectionReason)
                    <!-- Rejection Alert Banner -->
                    <div class="glass-card rounded-[2rem] shadow-xl shadow-red-200/40 overflow-hidden border-2 border-red-200">
                        <div class="px-8 py-6 bg-gradient-to-r from-red-50 to-rose-50">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-black text-red-900 mb-2">Application Rejected</h3>
                                    <p class="text-sm text-red-800 mb-4 leading-relaxed">{{ $rejectionReason }}</p>
                                    <div class="bg-white/60 rounded-lg p-3 border border-red-200">
                                        <p class="text-xs font-bold text-red-700 mb-1">What's Next?</p>
                                        <p class="text-xs text-red-800">Please review the reason above and address the concerns mentioned. You may reapply or contact support if you have questions.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Profile Edit Form -->
                <div class="glass-card rounded-[2rem] shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-white/50">
                        <h3 class="font-bold text-xl text-slate-800 flex items-center gap-2">
                            Personal Information
                        </h3>
                        <p class="text-slate-500 text-sm mt-1">Update your basic profile details here.</p>
                    </div>

                    <form action="{{ route('student.update-profile') }}" method="POST" class="p-8 space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" placeholder="Enter first name" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 placeholder:text-orange-500 text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name', $student->middle_name) }}" placeholder="Enter middle name" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 placeholder:text-orange-500 text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" placeholder="Enter last name" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 placeholder:text-orange-500 text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Contact Number</label>
                                <input type="text" name="contact_num" value="{{ old('contact_num', $student->contact_num) }}" placeholder="Enter contact number" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 placeholder:text-orange-500 text-slate-800">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $student->email) }}" placeholder="Enter email address" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 placeholder:text-orange-500 text-slate-800">
                            </div>
                            <div class="space-y-2 relative" id="year-level-field-container">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Current Year Level</label>
                                <select name="current_year_level" id="current-year-level-select" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 text-slate-800">
                                    <option value="">Select Year Level</option>
                                    <option value="1st" {{ old('current_year_level', optional($student->basicInfo)->current_year_level) == '1st' ? 'selected' : '' }}>1st Year</option>
                                    <option value="2nd" {{ old('current_year_level', optional($student->basicInfo)->current_year_level) == '2nd' ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3rd" {{ old('current_year_level', optional($student->basicInfo)->current_year_level) == '3rd' ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4th" {{ old('current_year_level', optional($student->basicInfo)->current_year_level) == '4th' ? 'selected' : '' }}>4th Year</option>
                                    <option value="5th" {{ old('current_year_level', optional($student->basicInfo)->current_year_level) == '5th' ? 'selected' : '' }}>5th Year</option>
                                </select>
                                
                                @if(!optional($student->basicInfo)->current_year_level)
                                <!-- Arrow Guide Tooltip -->
                                <div id="year-level-guide" class="absolute -right-64 top-1/2 -translate-y-1/2 z-50 animate-bounce-slow hidden md:block">
                                    <div class="relative bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-xl p-4 shadow-2xl max-w-xs">
                                        <div class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-full">
                                            <svg class="w-8 h-8 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"/>
                                            </svg>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 mt-0.5">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-bold text-sm mb-1">Complete Your Profile</p>
                                                <p class="text-xs text-white/90">Please select your current year level to help us better assist you with scholarship opportunities.</p>
                                            </div>
                                            <button onclick="dismissYearLevelGuide()" class="flex-shrink-0 text-white/80 hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="btn bg-orange-600 text-white hover:bg-orange-700 rounded-xl px-8 py-3 text-sm font-bold shadow-lg shadow-orange-600/20 hover:-translate-y-0.5 transition-all">
                                Save Changes
                            </button>
                        </div>
                    </form>
                    </div>

                <!-- Current Academic Performance - GWA Only -->
                <div class="glass-card rounded-[2rem] shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-white/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl text-slate-800">Current Academic Performance</h3>
                                <p class="text-slate-500 text-sm mt-0.5">General Weighted Average (GWA)</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        @if($currentGWA !== null)
                            <!-- GWA Display Card -->
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 border-4 
                                    @if($currentGWA >= 90) border-green-300
                                    @elseif($currentGWA >= 85) border-amber-300
                                    @else border-red-300
                                    @endif shadow-lg mb-4">
                                    <div class="text-center">
                                        <p class="text-5xl font-black 
                                            @if($currentGWA >= 90) text-green-600
                                            @elseif($currentGWA >= 85) text-amber-600
                                            @else text-red-600
                                            @endif leading-none" id="gwa-display-value">{{ number_format($currentGWA, 2) }}</p>
                                        <p class="text-lg font-bold text-slate-600 mt-1">GWA</p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-slate-700 mb-1">Current General Weighted Average</p>
                                <p class="text-xs text-slate-500">Scale: 75 - 100 (Philippine Grading System)</p>
                            </div>

                            <!-- GWA Progress Bar -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-semibold text-slate-600">GWA Progress</span>
                                    <span class="text-xs font-bold 
                                        @if($currentGWA >= 90) text-green-600
                                        @elseif($currentGWA >= 85) text-amber-600
                                        @else text-red-600
                                        @endif">
                                        Target: 90
                                    </span>
                                </div>
                                <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                    <div class="h-full rounded-full transition-all duration-500
                                        @if($currentGWA >= 90) bg-gradient-to-r from-green-400 to-green-600
                                        @elseif($currentGWA >= 85) bg-gradient-to-r from-amber-400 to-amber-600
                                        @else bg-gradient-to-r from-red-400 to-red-600
                                        @endif" 
                                        style="width: {{ (($currentGWA - 75) / 25) * 100 }}%">
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs text-slate-500 mt-1">
                                    <span>75</span>
                                    <span>90 (Target)</span>
                                    <span>100</span>
                                </div>
                            </div>

                            <!-- Academic Standing -->
                            @php
                                $gwaStatus = 'good';
                                $gwaStatusClasses = 'bg-green-50 border-green-200 text-green-700';
                                $gwaStatusTextColor = 'text-green-800';
                                $gwaStatusIconColor = 'text-green-600';
                                if ($currentGWA < 85) {
                                    $gwaStatus = 'poor';
                                    $gwaStatusClasses = 'bg-red-50 border-red-200 text-red-700';
                                    $gwaStatusTextColor = 'text-red-800';
                                    $gwaStatusIconColor = 'text-red-600';
                                } elseif ($currentGWA < 90) {
                                    $gwaStatus = 'fair';
                                    $gwaStatusClasses = 'bg-amber-50 border-amber-200 text-amber-700';
                                    $gwaStatusTextColor = 'text-amber-800';
                                    $gwaStatusIconColor = 'text-amber-600';
                                }
                            @endphp
                            <div class="{{ $gwaStatusClasses }} rounded-xl p-5 border-2">
                                <div class="flex items-center gap-3 mb-3">
                                    @if($currentGWA >= 90)
                                        <svg class="w-6 h-6 flex-shrink-0 {{ $gwaStatusIconColor }}" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 flex-shrink-0 {{ $gwaStatusIconColor }}" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    @endif
                                    <h4 class="font-bold text-lg {{ $gwaStatusTextColor }}">
                                        @if($currentGWA >= 90)
                                            Excellent Academic Standing
                                        @elseif($currentGWA >= 85)
                                            Good Academic Standing
                                        @else
                                            Needs Improvement
                                        @endif
                                    </h4>
                                </div>
                                <p class="{{ $gwaStatusTextColor }} text-sm leading-relaxed">
                                    @if($currentGWA >= 90)
                                        Your GWA of <strong>{{ number_format($currentGWA, 2) }}</strong> exceeds the target of 90. You are currently eligible for all scholarship opportunities and maintaining excellent academic progress.
                                    @elseif($currentGWA >= 85)
                                        Your GWA of <strong>{{ number_format($currentGWA, 2) }}</strong> is in good standing. Maintaining or improving this will maximize your scholarship opportunities.
                                    @else
                                        Your GWA of <strong>{{ number_format($currentGWA, 2) }}</strong> is below the recommended 85. Please focus on improving your academic performance to maintain scholarship eligibility.
                                    @endif
                                </p>
                            </div>
                        @else
                            <!-- No GWA Recorded -->
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-100 mb-4">
                                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-slate-700 mb-2">GWA Not Yet Recorded</h4>
                                <p class="text-sm text-slate-600 max-w-md mx-auto">Your GWA will be recorded when you upload your grades document in the application form.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Crop Modal -->
    <div id="cropper-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/80 backdrop-blur-md p-4 transition-all">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full overflow-hidden flex flex-col max-h-[85vh] ring-1 ring-white/20">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-lg text-slate-800">Adjust Photo</h3>
                <button onclick="closeCropper()" class="p-2 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="relative flex-1 bg-slate-900 overflow-hidden flex items-center justify-center min-h-[300px]">
                <img id="cropper-image" class="max-w-full max-h-[60vh]">
            </div>
            <div class="p-5 border-t border-slate-100 bg-white flex justify-end gap-3">
                <button onclick="closeCropper()" class="btn bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl px-6 py-2.5 font-semibold">Cancel</button>
                <button onclick="cropAndUpload()" class="btn bg-orange-600 text-white hover:bg-orange-700 rounded-xl px-6 py-2.5 font-semibold shadow-lg shadow-orange-600/20">Save Photo</button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="upload-progress" class="fixed inset-0 z-[70] hidden items-center justify-center bg-white/80 backdrop-blur-md">
        <div class="flex flex-col items-center bg-white p-8 rounded-3xl shadow-2xl border border-slate-100">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-orange-500 rounded-full border-t-transparent animate-spin"></div>
            </div>
            <p class="text-slate-800 font-bold text-lg">Uploading...</p>
            <p class="text-slate-400 text-sm mt-1">Please wait a moment</p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // --- Cropper Logic ---
    let cropper;
    let selectedFile;

    function uploadProfilePic(input) {
        const file = input.files[0];
        if (!file) return;
        selectedFile = file;

        const modal = document.getElementById('cropper-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        const img = document.getElementById('cropper-image');
        img.src = URL.createObjectURL(file);

            if (cropper) cropper.destroy();
        setTimeout(() => {
            if (typeof Cropper === 'undefined') {
                console.error('CropperJS library failed to load. Please check your network connection.');
                alert('Unable to load the photo editor. Please check your internet connection and try again.');
                closeCropper();
                return;
            }
            cropper = new Cropper(img, {
                aspectRatio: 1,
                viewMode: 1,
                background: false,
                autoCropArea: 1,
            });
        }, 100);
    }

    function closeCropper() {
        const modal = document.getElementById('cropper-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (cropper) cropper.destroy();
        cropper = null;
        selectedFile = null;
        document.getElementById('profile-pic-input').value = '';
    }

    function cropAndUpload() {
        if (!cropper) return;
        cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingQuality: 'high'
        }).toBlob(function(blob) {
            document.getElementById('upload-progress').classList.remove('hidden');
            document.getElementById('upload-progress').classList.add('flex');

            const formData = new FormData();
            formData.append('profile_pic', blob, selectedFile.name);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("student.update-profile-pic") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const image = document.getElementById('profile-pic-image');
                    const placeholder = document.getElementById('profile-pic-placeholder');
                    if (image) {
                        image.src = data.profile_pic_url + '?t=' + new Date().getTime();
                        image.classList.remove('hidden');
                    }
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                    closeCropper();
                } else {
                    alert('Failed to update.');
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                document.getElementById('upload-progress').classList.add('hidden');
                document.getElementById('upload-progress').classList.remove('flex');
            });
        }, 'image/jpeg', 0.95);
    }

    // Year Level Guide Functions
    function dismissYearLevelGuide() {
        const guide = document.getElementById('year-level-guide');
        if (guide) {
            guide.style.display = 'none';
            // Store dismissal in localStorage
            localStorage.setItem('year_level_guide_dismissed', 'true');
        }
    }

    // Show guide on page load if not dismissed and year level is empty
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }

        const guide = document.getElementById('year-level-guide');
        const yearLevelSelect = document.getElementById('current-year-level-select');
        
        if (guide && yearLevelSelect) {
            // Check if guide was previously dismissed
            const wasDismissed = localStorage.getItem('year_level_guide_dismissed') === 'true';
            
            if (!wasDismissed && !yearLevelSelect.value) {
                // Show guide after a short delay for better UX
                setTimeout(() => {
                    guide.style.display = 'block';
                }, 500);
            }
            
            // Hide guide when year level is selected
            yearLevelSelect.addEventListener('change', function() {
                if (this.value) {
                    dismissYearLevelGuide();
                }
            });
        }
        
        // Ensure profile picture displays correctly on page load
        const profilePicImage = document.getElementById('profile-pic-image');
        const profilePicPlaceholder = document.getElementById('profile-pic-placeholder');
        
        if (profilePicImage && profilePicPlaceholder) {
            // Check if image has a src
            if (profilePicImage.src && profilePicImage.src !== window.location.href) {
                // Image has a source, ensure it's visible
                profilePicImage.classList.remove('hidden');
                profilePicPlaceholder.classList.add('hidden');
                profilePicPlaceholder.classList.remove('flex');
                
                // Handle image load error
                profilePicImage.onerror = function() {
                    this.style.display = 'none';
                    profilePicPlaceholder.classList.remove('hidden');
                    profilePicPlaceholder.classList.add('flex');
                };
                
                // Handle successful image load
                profilePicImage.onload = function() {
                    this.style.display = '';
                    this.classList.remove('hidden');
                    profilePicPlaceholder.classList.add('hidden');
                    profilePicPlaceholder.classList.remove('flex');
                };
            }
        }
    });

</script>
@endpush 
