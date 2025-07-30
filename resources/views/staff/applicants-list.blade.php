@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Applicants List</h1>
            <p class="text-gray-600">Manage and review scholarship applications</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            <form method="POST" action="{{ route('staff.logout') }}" class="inline">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h3 class="font-semibold mb-4 text-gray-800">Filters</h3>
        <form method="GET" action="{{ route('staff.applicants.list') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                <select name="province" id="province-filter" class="form-select w-full border rounded p-2">
                    <option value="">All Provinces</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" {{ $selectedProvince == $province ? 'selected' : '' }}>{{ $province }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Municipality</label>
                <select name="municipality" id="municipality-filter" class="form-select w-full border rounded p-2">
                    <option value="">All Municipalities</option>
                    @foreach($municipalities as $municipality)
                        <option value="{{ $municipality }}" {{ $selectedMunicipality == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                <select name="barangay" id="barangay-filter" class="form-select w-full border rounded p-2">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IP Group</label>
                <select name="ethno" id="ethno-filter" class="form-select w-full border rounded p-2">
                    <option value="">All IP Groups</option>
                    @foreach($ethnicities as $ethno)
                        <option value="{{ $ethno->id }}" {{ $selectedEthno == $ethno->id ? 'selected' : '' }}>{{ $ethno->ethnicity }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="form-select w-full border rounded p-2">
                    <option value="">All Status</option>
                    <option value="applied" {{ $selectedStatus == 'applied' ? 'selected' : '' }}>Applied</option>
                    <option value="not_applied" {{ $selectedStatus == 'not_applied' ? 'selected' : '' }}>Not Applied</option>
                </select>
            </div>
            <div class="md:col-span-5 flex gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('staff.applicants.list') }}" class="btn btn-secondary">Clear Filters</a>
            </div>
        </form>
    </div>

    <!-- Results Summary -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-blue-800 font-medium">Found {{ $applicants->total() }} applicant(s)</span>
            </div>
            <div class="text-sm text-blue-600">
                Showing {{ $applicants->firstItem() ?? 0 }} - {{ $applicants->lastItem() ?? 0 }} of {{ $applicants->total() }}
            </div>
        </div>
    </div>

    <!-- Applicants Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Group</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applicants as $applicant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($applicant->profile_pic)
                                            <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $applicant->profile_pic) }}" alt="Profile">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium">{{ substr($applicant->first_name, 0, 1) }}{{ substr($applicant->last_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $applicant->first_name }} {{ $applicant->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: {{ $applicant->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $applicant->email }}</div>
                                <div class="text-sm text-gray-500">{{ $applicant->contact_num ?? 'No contact' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($applicant->basicInfo && $applicant->basicInfo->fullAddress && $applicant->basicInfo->fullAddress->address)
                                    <div class="text-sm text-gray-900">{{ $applicant->basicInfo->fullAddress->address->barangay }}</div>
                                    <div class="text-sm text-gray-500">{{ $applicant->basicInfo->fullAddress->address->municipality }}, {{ $applicant->basicInfo->fullAddress->address->province }}</div>
                                @else
                                    <span class="text-gray-400">No address</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($applicant->ethno)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $applicant->ethno->ethnicity }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($applicant->basicInfo && $applicant->basicInfo->type_assist)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Applied
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Not Applied
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $documents = $applicant->documents ?? collect();
                                    $approvedDocs = $documents->where('status', 'approved')->count();
                                    $totalDocs = $documents->count();
                                @endphp
                                <div class="text-sm text-gray-900">{{ $approvedDocs }}/{{ $totalDocs }} approved</div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $totalDocs > 0 ? ($approvedDocs / $totalDocs) * 100 : 0 }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('staff.applications.view', $applicant->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 font-medium">
                                    View Details
                                </a>
                            </td>
                    </tr>
                @empty
                    <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 mb-2">No applicants found</p>
                                    <p class="text-gray-500">Try adjusting your filters or check back later.</p>
                                </div>
                            </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>

    <!-- Pagination -->
    @if($applicants->hasPages())
        <div class="mt-6">
            {{ $applicants->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Dynamic geographic filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const provinceFilter = document.getElementById('province-filter');
        const municipalityFilter = document.getElementById('municipality-filter');
        const barangayFilter = document.getElementById('barangay-filter');

        // Dynamic municipality loading based on province
        provinceFilter.addEventListener('change', function() {
            const province = this.value;
            if (province) {
                fetch(`/address/municipalities?province=${encodeURIComponent(province)}`)
                    .then(response => response.json())
                    .then(municipalities => {
                        municipalityFilter.innerHTML = '<option value="">All Municipalities</option>';
                        municipalities.forEach(municipality => {
                            const option = document.createElement('option');
                            option.value = municipality;
                            option.textContent = municipality;
                            municipalityFilter.appendChild(option);
                        });
                        // Clear barangay filter when province changes
                        barangayFilter.innerHTML = '<option value="">All Barangays</option>';
                    });
            }
        });

        // Dynamic barangay loading based on municipality
        municipalityFilter.addEventListener('change', function() {
            const municipality = this.value;
            if (municipality) {
                fetch(`/address/barangays?municipality=${encodeURIComponent(municipality)}`)
                    .then(response => response.json())
                    .then(barangays => {
                        barangayFilter.innerHTML = '<option value="">All Barangays</option>';
                        barangays.forEach(barangay => {
                            const option = document.createElement('option');
                            option.value = barangay;
                            option.textContent = barangay;
                            barangayFilter.appendChild(option);
                        });
                    });
            }
        });
    });
</script>
@endpush
@endsection 