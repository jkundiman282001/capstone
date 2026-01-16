@extends('layouts.app')

@section('content')
<!-- Decorative Background Elements -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-amber-200/30 to-orange-200/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-tr from-orange-200/30 to-red-200/30 rounded-full blur-3xl"></div>
</div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-orange-700 to-orange-500 rounded-3xl shadow-2xl p-8 mb-8 text-white overflow-hidden">
        <!-- Decorative Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
        </div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-black mb-3 tracking-tight">Settings</h1>
            <p class="text-orange-100 text-lg">Manage system configuration</p>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-green-700 font-semibold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-lg">
        <div class="flex items-center mb-2">
            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-red-700 font-bold">Please correct the following errors:</p>
        </div>
        <ul class="list-disc list-inside text-sm text-red-600 font-medium">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Settings Form -->
    <div class="space-y-8">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8">
            <form method="POST" action="{{ route('staff.settings.update') }}" class="space-y-6">
                @csrf
                
                <!-- Max Slots Setting -->
                <div class="border-b border-slate-200 pb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-black text-slate-900 text-xl">Maximum Scholarship Slots</h2>
                            <p class="text-sm text-slate-500 font-medium">Set the maximum number of scholarship slots available</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="max_slots" class="block text-sm font-bold text-slate-700 mb-2">Maximum Slots</label>
                        <input 
                            type="number" 
                            id="max_slots" 
                            name="max_slots" 
                            value="{{ old('max_slots', $maxSlots) }}" 
                            min="1" 
                            required
                            class="w-full md:w-64 border-slate-200 bg-slate-50 rounded-xl p-3.5 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm font-medium hover:bg-white @error('max_slots') border-red-500 @enderror"
                        >
                        @error('max_slots')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-slate-500">This value determines the total number of scholarship slots available in the system.</p>
                    </div>
                </div>

                <!-- Renew Button Setting -->
                <!--
                    Renew Button Setting
                    Allows admins to enable/disable the "Renew Scholarship" button for grantees.
                    - Checkbox: Toggles the 'enable_renew_button' setting.
                    - Visual Feedback: Shows a warning message when disabled.
                -->
                <div class="border-b border-slate-200 pb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <!-- Refresh Icon -->
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-black text-slate-900 text-xl">Scholarship Renewal</h2>
                            <p class="text-sm text-slate-500 font-medium">Control the availability of the scholarship renewal button</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="enable_renew_button" class="sr-only peer" {{ $enableRenewButton ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                            <span class="ml-3 text-sm font-medium text-slate-700">Enable Renewal Button</span>
                        </label>
                        <p class="mt-2 text-xs text-slate-500">When disabled, the "Renew Scholarship" button will be hidden for grantees, and they won't be able to access the renewal application.</p>
                        
                        <!-- Visual Feedback -->
                        @if(!$enableRenewButton)
                            <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-sm text-amber-700">The renewal feature is currently <strong>disabled</strong>. Students will not see the renewal option.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <a href="{{ route('staff.dashboard') }}" class="px-6 py-3 rounded-xl border-2 border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Manual Applicant Encoding Section -->
        @if(false)
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-black text-slate-900 text-xl">Manual Applicant Encoding</h2>
                        <p class="text-sm text-slate-500 font-medium">Temporarily encode an applicant application form manually</p>
                    </div>
                </div>
                <button 
                    type="button"
                    onclick="window.openEncodeModal()"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Encode Applicant
                </button>
            </div>
        </div>
        @endif

        <!-- Account Management Section -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8">
            @if(false)
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-900 text-xl">Manually Encoded Applicants</h2>
                    <p class="text-sm text-slate-500 font-medium">List of applicants encoded manually by staff</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 mb-8">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Applicant Name</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Email Address</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Encoded Date</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($manualApplicants as $applicant)
                                @php
                                    $fullName = trim(($applicant->first_name ?? '') . ' ' . ($applicant->middle_name ?? '') . ' ' . ($applicant->last_name ?? ''));
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors applicant-row" data-search="{{ strtolower($fullName . ' ' . $applicant->email) }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                                {{ $applicant->initials }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900">{{ $fullName }}</div>
                                                <div class="text-xs text-slate-500">ID: #{{ $applicant->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-slate-600 font-medium">{{ $applicant->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-slate-500">{{ $applicant->created_at->format('M d, Y h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button 
                                            type="button"
                                            onclick="window.confirmDeleteApplicant({{ $applicant->id }}, '{{ addslashes($fullName) }}')"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-red-50 text-red-600 border border-red-200 rounded-xl font-bold text-xs transition-all"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500 italic font-medium">
                                        No manually encoded applicants yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @endif
            <div class="flex items-center gap-3 mb-8 pt-8 border-t border-slate-100">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-900 text-xl">Applicant Account Management</h2>
                    <p class="text-sm text-slate-500 font-medium">Manage and delete applicant accounts permanently</p>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <input 
                        type="text" 
                        id="applicantSearch" 
                        placeholder="Search by name or email..." 
                        class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-200 rounded-2xl text-sm font-medium focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all"
                    >
                    <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Applicants Table -->
            <div class="overflow-hidden rounded-2xl border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Applicant Name</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Email Address</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="applicantsTableBody" class="divide-y divide-slate-100 bg-white">
                            @forelse($applicants as $applicant)
                                @php
                                    $fullName = trim(($applicant->first_name ?? '') . ' ' . ($applicant->middle_name ?? '') . ' ' . ($applicant->last_name ?? ''));
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors applicant-row" data-search="{{ strtolower($fullName . ' ' . $applicant->email) }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($applicant->profile_pic_url)
                                        <img src="{{ $applicant->profile_pic_url }}" class="w-10 h-10 rounded-xl object-cover shadow-sm">
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                            {{ $applicant->initials }}
                                        </div>
                                    @endif
                                            <div>
                                                <div class="font-bold text-slate-900">{{ $fullName }}</div>
                                                <div class="text-xs text-slate-500">ID: #{{ $applicant->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-slate-600 font-medium">{{ $applicant->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button 
                                            type="button"
                                            onclick="window.confirmDeleteApplicant({{ $applicant->id }}, '{{ addslashes($fullName) }}')"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-red-50 text-red-600 border border-red-200 rounded-xl font-bold text-xs transition-all"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Account
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-500 italic font-medium">
                                        No applicants found in the system.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Applicant Confirmation Modal -->
<div id="deleteApplicantModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[70] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-6 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-xl font-black text-slate-900 mb-2">Delete Applicant Account?</h3>
            <p class="text-slate-500 text-sm mb-6">
                Are you sure you want to delete <span id="deleteApplicantName" class="font-bold text-slate-900"></span>? 
                This action is <span class="text-red-600 font-bold uppercase">permanent</span> and will remove all associated documents, basic information, and history.
            </p>
            
            <div class="flex flex-col gap-3">
                <button id="confirmDeleteBtn" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-200 transition-all flex items-center justify-center gap-2">
                    <span id="deleteBtnText">Yes, Delete Account</span>
                    <div id="deleteBtnSpinner" class="hidden animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                </button>
                <button onclick="window.closeDeleteModal()" class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Manual Encoding Modal -->
@if(false)
<div id="encodeApplicantModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[70] hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto animate-in fade-in zoom-in duration-200">
        <div class="p-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900">Manual Applicant Encoding</h3>
                </div>
                <button onclick="window.closeEncodeModal()" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('staff.settings.encode-applicant') }}" method="POST" class="space-y-8">
                @csrf
                
                <div class="space-y-12">
                    <!-- 1. Personal Information -->
                    <section>
                        <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">1</span>
                            Personal Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">First Name <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Enter first name">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Middle Name</label>
                                <input type="text" name="middle_name" class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Enter middle name">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Enter last name">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="email@example.com">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Contact Number <span class="text-red-500">*</span></label>
                                <input type="text" name="contact_num" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="09xxxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Gender <span class="text-red-500">*</span></label>
                                <select name="gender" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Birthdate <span class="text-red-500">*</span></label>
                                <input type="date" name="birthdate" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Place of Birth <span class="text-red-500">*</span></label>
                                <input type="text" name="birthplace" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="City/Municipality">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Civil Status <span class="text-red-500">*</span></label>
                                <select name="civil_status" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">Select Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">IP Group (Ethnolinguistic) <span class="text-red-500">*</span></label>
                                <select name="ethno_id" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">Select IP Group</option>
                                    @foreach($ethnicities as $ethno)
                                        <option value="{{ $ethno->id }}">{{ $ethno->ethnicity }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- 2. Address Information -->
                    @foreach(['mailing' => 'Mailing Address', 'permanent' => 'Permanent Address', 'origin' => 'Place of Origin/Birth'] as $prefix => $title)
                    <section>
                        <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">2.{{ $loop->iteration }}</span>
                            {{ $title }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Province <span class="text-red-500">*</span></label>
                                <select name="{{ $prefix }}_province" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">Select Province</option>
                                    @foreach($provinces as $prov)
                                        <option value="{{ $prov }}" {{ $prov === 'Davao del Sur' ? 'selected' : '' }}>{{ $prov }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Municipality <span class="text-red-500">*</span></label>
                                <select name="{{ $prefix }}_municipality" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">Select Municipality</option>
                                    @foreach($municipalities as $muni)
                                        <option value="{{ $muni }}">{{ $muni }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Barangay <span class="text-red-500">*</span></label>
                                <select name="{{ $prefix }}_barangay" required class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">Select Barangay</option>
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">House No. / Street</label>
                                <input type="text" name="{{ $prefix }}_house_num" class="w-full border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="House #, Street name">
                            </div>
                        </div>
                    </section>
                    @endforeach

                    <!-- 3. Academic Information -->
                    <section>
                        <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">3</span>
                            Academic Information
                        </h4>
                        <div class="space-y-8">
                            @foreach([
                                ['key' => 'elem', 'label' => 'Elementary', 'required' => true],
                                ['key' => 'hs', 'label' => 'High School', 'required' => true],
                                ['key' => 'voc', 'label' => 'Vocational', 'required' => false],
                                ['key' => 'college', 'label' => 'College', 'required' => false]
                            ] as $level)
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                                <h5 class="text-sm font-bold text-slate-600 mb-4">{{ $level['label'] }} @if($level['required']) <span class="text-red-500">*</span> @endif</h5>
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 mb-1">School Name</label>
                                        <input type="text" name="{{ $level['key'] }}_school" {{ $level['required'] ? 'required' : '' }} class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Type</label>
                                        <select name="{{ $level['key'] }}_type" {{ $level['required'] ? 'required' : '' }} class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                            <option value="">Select</option>
                                            <option value="Public">Public</option>
                                            <option value="Private">Private</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Year Graduated</label>
                                        <input type="text" name="{{ $level['key'] }}_year" {{ $level['required'] ? 'required' : '' }} class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="YYYY">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">GWA</label>
                                        <input type="text" name="{{ $level['key'] }}_avg" {{ $level['required'] ? 'required' : '' }} class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="00.00">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Rank/Honors</label>
                                        <input type="text" name="{{ $level['key'] }}_rank" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </section>

                    <!-- 4. Parents Information -->
                    <section>
                        <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">4</span>
                            Parents Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach(['father' => 'Father', 'mother' => 'Mother'] as $parent => $label)
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                                <h5 class="text-sm font-bold text-slate-600 mb-4">{{ $label }}'s Details</h5>
                                <div class="space-y-4">
                                    <div class="flex gap-4 mb-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="{{ $parent }}_status" value="Living" checked class="text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm text-slate-700">Living</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="{{ $parent }}_status" value="Deceased" class="text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm text-slate-700">Deceased</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Full Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="{{ $parent }}_name" required class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Address</label>
                                        <input type="text" name="{{ $parent }}_address" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Occupation</label>
                                            <input type="text" name="{{ $parent }}_occupation" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Annual Income</label>
                                            <input type="text" name="{{ $parent }}_income" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Educational Attainment</label>
                                        <input type="text" name="{{ $parent }}_education" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Office Address</label>
                                        <input type="text" name="{{ $parent }}_office_address" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">IP Group</label>
                                        <select name="{{ $parent }}_ethno" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                            <option value="">Select</option>
                                            @foreach($ethnicities as $ethno)
                                                <option value="{{ $ethno->id }}">{{ $ethno->ethnicity }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </section>

                    <!-- 5. Siblings Information -->
                    <section>
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">5</span>
                                Siblings Information
                            </h4>
                            <button type="button" onclick="addSiblingRow()" class="px-4 py-2 bg-orange-50 text-orange-600 text-xs font-bold rounded-lg hover:bg-orange-100 transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Sibling
                            </button>
                        </div>
                        <div id="siblingsContainer" class="space-y-4">
                            <!-- Sibling rows will be added here -->
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 relative group">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Full Name</label>
                                        <input type="text" name="sibling_name[]" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Sibling's full name">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Age</label>
                                        <input type="text" name="sibling_age[]" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Age">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Status</label>
                                        <input type="text" name="sibling_status[]" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="e.g. Student">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Course/Year</label>
                                        <input type="text" name="sibling_course[]" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="e.g. BSIT-1">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Scholarship (if any)</label>
                                        <input type="text" name="sibling_scholarship[]" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Scholarship name">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 6. School Preference -->
                    <section>
                        <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center">6</span>
                            School Preference
                        </h4>
                        @php
                            $courseOptions = [
                                'Aerospace Engineering', 'Agribusiness', 'Agricultural Economics', 'Agricultural Engineering', 'Agricultural Technology', 'Agriculture', 'Animal Science', 'Anthropology', 'Aqua-Culture and Fisheries', 'Aquaculture', 'Archaeology', 'Architecture', 'Automotive Engineering', 'Biochemistry', 'Biology', 'Biotechnology', 'Business Administration', 'Business Management', 'Chemical Engineering', 'Chemistry', 'Civil Engineering', 'Communication Arts', 'Community Development', 'Community Services', 'Computer Engineering', 'Computer Science', 'Conservation', 'Construction Engineering', 'Constitutional Law', 'Counseling', 'Criminal Justice', 'Criminology', 'Crop Science', 'Cultural Studies', 'Curriculum Development', 'Dance', 'Data Science', 'Development Studies', 'Diplomatic Studies', 'Earth Science', 'Ecology', 'Economics', 'Education', 'Educational Administration', 'Electrical Engineering', 'Electronics Engineering', 'Elementary Education', 'Entrepreneurship', 'Environmental Engineering', 'Environmental Management', 'Environmental Science', 'Ethnic Studies', 'Finance', 'Fine Arts', 'Fisheries', 'Food Technology', 'Foreign Service', 'Forensic Science', 'Forestry and Environment Studies', 'Geodetic Engineering', 'Geographic Information Systems', 'Geological Engineering', 'Geology', 'Geomatics', 'Geophysics', 'Health Sciences', 'History', 'Hospitality Management', 'Hotel and Restaurant Management', 'Human Resource Management', 'Human Services', 'Industrial Engineering', 'Information Systems', 'Information Technology', 'International Relations', 'International Studies', 'Journalism', 'Jurisprudence', 'Land Surveying', 'Law', 'Law Enforcement', 'Legal Studies', 'Literature', 'Manufacturing Engineering', 'Marine Biology', 'Marine Science', 'Marketing', 'Mathematics', 'Mechanical Engineering', 'Medical Laboratory Science', 'Medicine and Allied Health Sciences', 'Mineral Processing', 'Mining Engineering', 'Mining Technology', 'Music', 'Natural Resource Management', 'Nutrition', 'Occupational Therapy', 'Oceanography', 'Operations Management', 'Pharmacy', 'Philosophy', 'Physical Education', 'Physical Therapy', 'Physics', 'Political Science', 'Psychology', 'Public Administration', 'Public Health', 'Radiologic Technology', 'Rural Development', 'Secondary Education', 'Security Management', 'Social Sciences', 'Social Welfare', 'Social Work', 'Sociology', 'Special Education', 'Sports Science', 'Statistics', 'Structural Engineering', 'Surveying', 'Theater Arts', 'Tourism', 'Transportation Engineering', 'Urban Planning', 'Other'
                            ];
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach(['school1' => 'First Choice', 'school2' => 'Second Choice'] as $key => $label)
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                                <h5 class="text-sm font-bold text-slate-600 mb-4">{{ $label }} <span class="text-red-500">*</span></h5>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">School Name</label>
                                        <input type="text" name="{{ $key }}_name" required class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">School Address</label>
                                        <input type="text" name="{{ $key }}_address" required class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Primary Course</label>
                                            <select name="{{ $key }}_course1" required onchange="toggleOtherCourse(this, '{{ $key }}_course1_other_container')" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                                <option value="">Select Course</option>
                                                @foreach($courseOptions as $course)
                                                    <option value="{{ $course }}">{{ $course }}</option>
                                                @endforeach
                                            </select>
                                            <div id="{{ $key }}_course1_other_container" class="mt-2 hidden">
                                                <input type="text" name="{{ $key }}_course1_other" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Specify course">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Alternate Course</label>
                                            <select name="{{ $key }}_course_alt" onchange="toggleOtherCourse(this, '{{ $key }}_course_alt_other_container')" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                                <option value="">Select Course</option>
                                                @foreach($courseOptions as $course)
                                                    <option value="{{ $course }}">{{ $course }}</option>
                                                @endforeach
                                            </select>
                                            <div id="{{ $key }}_course_alt_other_container" class="mt-2 hidden">
                                                <input type="text" name="{{ $key }}_course_alt_other" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Specify course">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Type</label>
                                            <select name="{{ $key }}_type" required class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                                <option value="Public">Public</option>
                                                <option value="Private">Private</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Duration (Years)</label>
                                            <input type="text" name="{{ $key }}_years" required class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="e.g. 4">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </section>

                    <!-- 7. Essay -->
                    <section>
                        <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-pink-100 text-pink-600 flex items-center justify-center">7</span>
                            Essay
                        </h4>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">How will you contribute to your IP community? <span class="text-red-500">*</span></label>
                                <textarea name="contribution" required rows="4" class="w-full border-slate-200 rounded-xl p-4 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Write your response here..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">What are your plans after graduation? <span class="text-red-500">*</span></label>
                                <textarea name="plans_after_grad" required rows="4" class="w-full border-slate-200 rounded-xl p-4 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Write your response here..."></textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="flex items-center justify-end gap-4 pt-8 border-t border-slate-100">
                    <button type="button" onclick="window.closeEncodeModal()" class="px-6 py-3 rounded-xl border-2 border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                        Encode Applicant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    @if(false)
    window.openEncodeModal = function() {
        const modal = document.getElementById('encodeApplicantModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    };

    window.closeEncodeModal = function() {
        const modal = document.getElementById('encodeApplicantModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    };

    window.toggleOtherCourse = function(select, containerId) {
        const container = document.getElementById(containerId);
        if (select.value === 'Other') {
            container.classList.remove('hidden');
            container.querySelector('input').setAttribute('required', 'required');
        } else {
            container.classList.add('hidden');
            container.querySelector('input').removeAttribute('required');
            container.querySelector('input').value = '';
        }
    };

    window.addSiblingRow = function() {
        const container = document.getElementById('siblingsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'p-4 bg-slate-50 rounded-xl border border-slate-100 relative group animate-fadeIn';
        newRow.innerHTML = `
            <button type="button" onclick="this.parentElement.remove()" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-all hover:bg-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Full Name</label>
                    <input type="text" name="sibling_name[]" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Sibling's full name">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Age</label>
                    <input type="text" name="sibling_age[]" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Age">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Status</label>
                    <input type="text" name="sibling_status[]" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="e.g. Student">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Course/Year</label>
                    <input type="text" name="sibling_course[]" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="e.g. BSIT-1">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Scholarship (if any)</label>
                    <input type="text" name="sibling_scholarship[]" class="w-full border-slate-200 rounded-xl p-2.5 text-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="Scholarship name">
                </div>
            </div>
        `;
        container.appendChild(newRow);
    };
    @endif

    // Search Functionality
    document.getElementById('applicantSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.applicant-row');
        
        rows.forEach(row => {
            const searchText = row.getAttribute('data-search');
            if (searchText.includes(searchTerm)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });

    let applicantIdToDelete = null;

    window.confirmDeleteApplicant = function(id, name) {
        applicantIdToDelete = id;
        document.getElementById('deleteApplicantName').textContent = name;
        const modal = document.getElementById('deleteApplicantModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeDeleteModal = function() {
        const modal = document.getElementById('deleteApplicantModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        applicantIdToDelete = null;
    };

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!applicantIdToDelete) return;

        const btn = this;
        const text = document.getElementById('deleteBtnText');
        const spinner = document.getElementById('deleteBtnSpinner');

        // Disable button and show spinner
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        text.textContent = 'Deleting...';
        spinner.classList.remove('hidden');

        fetch(`/staff/applications/${applicantIdToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and reload
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
                // Reset button
                btn.disabled = false;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
                text.textContent = 'Yes, Delete Account';
                spinner.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please try again.');
            // Reset button
            btn.disabled = false;
            btn.classList.remove('opacity-70', 'cursor-not-allowed');
            text.textContent = 'Yes, Delete Account';
            spinner.classList.add('hidden');
        });
    });

    // Auto-open modal if there are errors related to encoding
    @if(false && $errors->any() && (old('first_name') || old('email') || old('contact_num')))
        window.addEventListener('load', function() {
            window.openEncodeModal();
        });
    @endif
</script>
@endpush
@endsection

