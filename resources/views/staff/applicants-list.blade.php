@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Applicants List</h1>
            <p class="text-gray-600">Manage and review scholarship applications</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="calculateAllScores()" class="btn btn-primary bg-green-600 hover:bg-green-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Calculate Scores
            </button>
            <button onclick="showTopPriority()" class="btn btn-primary bg-blue-600 hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                Top Priority
            </button>
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority Level</label>
                <select name="priority" class="form-select w-full border rounded p-2">
                    <option value="">All Priorities</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High Priority (80+)</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium Priority (60-79)</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low Priority (40-59)</option>
                    <option value="very_low" {{ request('priority') == 'very_low' ? 'selected' : '' }}>Very Low Priority (<40)</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
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
                                @if($applicant->applicantScore)
                                    <div class="text-center">
                                        <div class="text-lg font-bold 
                                            @if($applicant->applicantScore->total_score >= 80) text-red-600
                                            @elseif($applicant->applicantScore->total_score >= 60) text-orange-600
                                            @elseif($applicant->applicantScore->total_score >= 40) text-yellow-600
                                            @else text-gray-600 @endif">
                                            {{ number_format($applicant->applicantScore->total_score, 1) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($applicant->applicantScore->priority_rank)
                                                Rank #{{ $applicant->applicantScore->priority_rank }}
                                            @else
                                                Not Ranked
                                            @endif
                                        </div>
                                        <div class="text-xs px-2 py-1 rounded-full mt-1
                                            @if($applicant->applicantScore->total_score >= 80) bg-red-100 text-red-700
                                            @elseif($applicant->applicantScore->total_score >= 60) bg-orange-100 text-orange-700
                                            @elseif($applicant->applicantScore->total_score >= 40) bg-yellow-100 text-yellow-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ $applicant->applicantScore->priority_level }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <div class="text-sm text-gray-400">No Score</div>
                                        <button onclick="calculateScore({{ $applicant->id }})" class="text-xs text-blue-600 hover:text-blue-800 mt-1">
                                            Calculate
                                        </button>
                                    </div>
                                @endif
                            </td>
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
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
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

    // Scoring system functions
    function calculateAllScores() {
        if (!confirm('This will calculate scores for all applicants. This may take a few minutes. Continue?')) {
            return;
        }

        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Calculating...';
        button.disabled = true;

        fetch('{{ route("staff.scores.calculate-all") }}', {
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
                alert('Scores calculated successfully for ' + data.results.length + ' applicants!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error calculating scores. Please try again.');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }

    function calculateScore(userId) {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = 'Calculating...';
        button.disabled = true;

        fetch(`{{ route("staff.scores.calculate", ":id") }}`.replace(':id', userId), {
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
                alert('Score calculated successfully! Priority: ' + data.priority_level);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error calculating score. Please try again.');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }

    function showTopPriority() {
        fetch('{{ route("staff.scores.top-priority") }}?limit=10', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let message = 'Top Priority Applicants:\n\n';
                data.applicants.forEach((applicant, index) => {
                    message += `${index + 1}. ${applicant.user.first_name} ${applicant.user.last_name} - Score: ${applicant.total_score} (Rank #${applicant.priority_rank})\n`;
                });
                alert(message);
            } else {
                alert('No priority data available. Please calculate scores first.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching top priority applicants.');
        });
    }
</script>
@endpush
@endsection 