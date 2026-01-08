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

        <!-- Account Management Section -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8">
            <div class="flex items-center gap-3 mb-8">
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

@push('scripts')
<script>
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
</script>
@endpush
@endsection

