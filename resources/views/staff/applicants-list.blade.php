@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 p-6 md:p-8 font-sans">
    
    <div class="max-w-[1600px] mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 rounded-2xl bg-gradient-to-br from-orange-600 to-amber-600 shadow-lg shadow-orange-200/50">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Applicants</h1>
                        <p class="text-slate-500 text-sm mt-0.5">Manage scholarship applications</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <button onclick="calculateAllScores()" class="group bg-white border-2 border-slate-200 text-slate-700 hover:border-orange-500 hover:bg-orange-50 shadow-sm hover:shadow-md rounded-xl px-5 py-3 text-sm font-bold transition-all flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>Recalculate</span>
                </button>
                <button onclick="showTopPriority()" class="group bg-gradient-to-r from-orange-600 to-amber-600 text-white hover:from-orange-700 hover:to-amber-700 shadow-lg shadow-orange-200/50 hover:shadow-xl hover:shadow-orange-300/50 rounded-xl px-5 py-3 text-sm font-bold transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    <span>Top Priority</span>
                </button>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filters
                </h3>
                <a href="{{ route('staff.applicants.list') }}" class="text-xs font-bold text-orange-600 hover:text-orange-800 px-3 py-1.5 rounded-lg hover:bg-orange-50 transition-all">Clear All</a>
            </div>
            
            <form method="GET" action="{{ route('staff.applicants.list') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Province</label>
                        <select name="province" id="province-filter" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
                            <option value="">All Provinces</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province }}" {{ $selectedProvince == $province ? 'selected' : '' }}>{{ $province }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Municipality</label>
                        <select name="municipality" id="municipality-filter" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
                            <option value="">All Municipalities</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality }}" {{ $selectedMunicipality == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Barangay</label>
                        <select name="barangay" id="barangay-filter" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $barangay)
                                <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">IP Group</label>
                        <select name="ethno" id="ethno-filter" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
                            <option value="">All IP Groups</option>
                            @foreach($ethnicities as $ethno)
                                <option value="{{ $ethno->id }}" {{ $selectedEthno == $ethno->id ? 'selected' : '' }}>{{ $ethno->ethnicity }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Priority</label>
                        <select name="priority" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
                            <option value="">All Levels</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High Priority (80+)</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium Priority (60-79)</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low Priority (40-59)</option>
                            <option value="very_low" {{ request('priority') == 'very_low' ? 'selected' : '' }}>Very Low (<40)</option>
                        </select>
                    </div>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white rounded-xl px-6 py-2.5 text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <span>Apply Filters</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Bar -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <span class="text-3xl font-black text-slate-900">{{ $applicants->total() }}</span>
                <span class="text-sm font-medium text-slate-500">applicants found</span>
            </div>
            <div class="text-xs font-medium text-slate-500">
                Showing {{ $applicants->firstItem() ?? 0 }}-{{ $applicants->lastItem() ?? 0 }}
            </div>
        </div>

        <!-- Applicants Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-8">
            @forelse($applicants as $applicant)
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-slate-200 hover:border-orange-200 overflow-hidden transition-all duration-300 hover:-translate-y-1">
                    <!-- Card Header with Gradient -->
                    <div class="relative h-24 bg-gradient-to-br from-orange-500 via-amber-500 to-red-500 p-4">
                        <div class="absolute top-3 right-3">
                            @if($applicant->applicantScore)
                                <div class="bg-white/95 backdrop-blur-sm rounded-xl px-3 py-1.5 shadow-lg">
                                    <div class="text-center">
                                        <div class="text-xl font-black
                                            @if($applicant->applicantScore->total_score >= 80) text-emerald-600
                                            @elseif($applicant->applicantScore->total_score >= 60) text-amber-500
                                            @else text-slate-400 @endif">
                                            {{ number_format($applicant->applicantScore->total_score, 0) }}
                                        </div>
                                        <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wide">Score</div>
                                    </div>
                                </div>
                            @else
                                <button onclick="calculateScore({{ $applicant->id }})" class="bg-white/95 backdrop-blur-sm text-orange-600 hover:bg-white rounded-lg px-2.5 py-1 text-[10px] font-bold transition-all shadow-lg">
                                    Calculate
                                </button>
                            @endif
                        </div>
                        
                        <!-- Status Badge -->
                        @if($applicant->basicInfo && $applicant->basicInfo->type_assist)
                            @php
                                $appStatus = $applicant->basicInfo->application_status ?? 'pending';
                                $isValidated = $appStatus === 'validated';
                            @endphp
                            <div class="absolute top-3 left-3">
                                <span class="{{ $isValidated ? 'bg-emerald-500' : 'bg-amber-500' }} text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg flex items-center gap-1">
                                    @if($isValidated)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Validated
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Pending
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Profile Picture -->
                    <div class="relative -mt-12 flex justify-center px-4">
                        @if($applicant->profile_pic)
                            <img class="h-24 w-24 rounded-2xl object-cover border-4 border-white shadow-xl group-hover:scale-105 transition-transform" src="{{ asset('storage/' . $applicant->profile_pic) }}" alt="Profile">
                        @else
                            <div class="h-24 w-24 rounded-2xl bg-gradient-to-br from-slate-200 to-slate-300 text-slate-600 flex items-center justify-center font-black text-3xl border-4 border-white shadow-xl group-hover:scale-105 transition-transform">
                                {{ substr($applicant->first_name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Card Content -->
                    <div class="p-4 pt-3">
                        <!-- Name -->
                        <div class="text-center mb-3">
                            <h3 class="font-bold text-slate-900 text-base leading-tight group-hover:text-orange-600 transition-colors">
                                {{ $applicant->first_name }} {{ $applicant->last_name }}
                            </h3>
                            <p class="text-[11px] font-medium text-slate-400 mt-0.5">ID: {{ $applicant->id }}</p>
                        </div>

                        <!-- Rank Badge -->
                        @if($applicant->applicantScore)
                            <div class="flex justify-center mb-3">
                                <span class="text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full
                                    @if($applicant->applicantScore->total_score >= 80) bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($applicant->applicantScore->total_score >= 60) bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-slate-100 text-slate-600 border border-slate-200 @endif">
                                    Rank #{{ $applicant->applicantScore->priority_rank ?? '-' }}
                                </span>
                            </div>
                        @endif

                        <!-- Contact Info -->
                        <div class="space-y-2 mb-3">
                            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-2.5 py-2 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="font-medium truncate">{{ Str::limit($applicant->email, 20) }}</span>
                            </div>
                            @if($applicant->contact_num)
                            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-2.5 py-2 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 12.284 3 6V5z"></path></svg>
                                <span class="font-medium">{{ $applicant->contact_num }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Tags -->
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                {{ Str::limit($applicant->basicInfo->fullAddress->address->municipality ?? 'N/A', 12) }}
                            </span>
                            @if($applicant->ethno)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-purple-50 text-purple-700">
                                    <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    {{ Str::limit($applicant->ethno->ethnicity, 10) }}
                                </span>
                            @endif
                        </div>

                        <!-- Documents Progress -->
                        @php
                            $documents = $applicant->documents ?? collect();
                            $approvedDocs = $documents->where('status', 'approved')->count();
                            $totalDocs = $documents->count();
                            $percentage = $totalDocs > 0 ? ($approvedDocs / $totalDocs) * 100 : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Documents</span>
                                <span class="text-xs font-bold text-slate-700">{{ $approvedDocs }}/{{ $totalDocs }}</span>
                            </div>
                            <div class="relative w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="absolute inset-0 
                                    @if($percentage >= 100) bg-gradient-to-r from-emerald-400 to-green-500
                                    @elseif($percentage >= 50) bg-gradient-to-r from-orange-400 to-amber-500
                                    @else bg-gradient-to-r from-slate-300 to-slate-400 @endif
                                    h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route('staff.applications.view', $applicant->id) }}" class="w-full flex items-center justify-center gap-2 bg-slate-900 hover:bg-orange-600 text-white rounded-xl px-4 py-2.5 font-bold text-sm shadow-md hover:shadow-lg transition-all group/btn">
                            <span>Review</span>
                            <svg class="w-4 h-4 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-16 text-center">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h3 class="text-slate-900 font-bold text-lg mb-2">No applicants found</h3>
                        <p class="text-slate-500 text-sm max-w-md mx-auto mb-4">Try adjusting your filters to see more results.</p>
                        <a href="{{ route('staff.applicants.list') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl transition-all text-sm">
                            Clear all filters
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($applicants->hasPages())
            <div class="flex justify-center">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 px-6 py-4">
                    {{ $applicants->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinceFilter = document.getElementById('province-filter');
        const municipalityFilter = document.getElementById('municipality-filter');
        const barangayFilter = document.getElementById('barangay-filter');

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
                        barangayFilter.innerHTML = '<option value="">All Barangays</option>';
                    });
            }
        });

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

    function calculateAllScores() {
        if (!confirm('Recalculate all applicant scores? This may take a moment.')) return;
        
        const button = event.currentTarget;
        const originalContent = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
        button.disabled = true;

        fetch('{{ route("staff.scores.calculate-all") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(d => {
            if(d.success) location.reload();
            else alert(d.message);
        })
        .finally(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    function calculateScore(userId) {
        const button = event.currentTarget;
        button.innerText = '...';
        button.disabled = true;

        fetch(`{{ route("staff.scores.calculate", ":id") }}`.replace(':id', userId), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            if(d.success) location.reload();
        });
    }

    function showTopPriority() {
        fetch('{{ route("staff.scores.top-priority") }}?limit=10')
        .then(r => r.json())
        .then(d => {
            if(d.success) {
                let msg = 'Top Priority Applicants:\n\n';
                d.applicants.forEach((a, i) => {
                    msg += `${i+1}. ${a.user.first_name} ${a.user.last_name} - Score: ${a.total_score}\n`;
                });
                alert(msg);
            }
        });
    }
</script>
@endpush
@endsection
