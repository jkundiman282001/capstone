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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
            <div class="flex items-center gap-4">
            <div class="text-xs font-medium text-slate-500">
                Showing {{ $applicants->firstItem() ?? 0 }}-{{ $applicants->lastItem() ?? 0 }}
                </div>
                @if(isset($masterlistType) && $masterlistType === 'Regular Grantees')
                    <button onclick="openGranteesReport()" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        View Report
                    </button>
                @endif
            </div>
        </div>

        <!-- Applicants Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-8">
            @forelse($applicants as $applicant)
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-slate-200 hover:border-orange-200 overflow-hidden transition-all duration-300 hover:-translate-y-1">
                    <!-- Card Header with Gradient -->
                    <div class="relative h-24 bg-gradient-to-br from-orange-500 via-amber-500 to-red-500 p-4">
                        <!-- Status Badge -->
                        @if($applicant->basicInfo && $applicant->basicInfo->type_assist)
                            @php
                                $appStatus = $applicant->basicInfo->application_status ?? 'pending';
                                $isValidated = $appStatus === 'validated';
                                $isRejected = $appStatus === 'rejected';
                            @endphp
                            <div class="absolute top-3 left-3">
                                <span class="{{ $isValidated ? 'bg-emerald-500' : ($isRejected ? 'bg-red-500' : 'bg-amber-500') }} text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg flex items-center gap-1">
                                    @if($isValidated)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Validated
                                    @elseif($isRejected)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Rejected
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

<!-- Grantees Report Modal -->
<div id="granteesReportModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-200 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white">Grantees Report</h2>
                    <p class="text-sm text-white/90">Excel-style grid view of all grantee applicants</p>
                </div>
            </div>
            <button onclick="closeGranteesReport()" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Modal Body - Excel-like Grid -->
        <div class="flex-1 overflow-auto p-6 bg-slate-50">
            <div id="reportLoading" class="flex items-center justify-center py-20">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mb-4"></div>
                    <p class="text-slate-600 font-medium">Loading grantees data...</p>
                </div>
            </div>
            <div id="reportContent" class="hidden">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse" style="min-width: 2400px;">
                            <thead class="bg-gradient-to-r from-emerald-700 to-green-800 sticky top-0 z-10">
                                <!-- First row with main headers -->
                                <tr>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Province, Municipality, Barangay, AD Reference No.</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Contact Number/Email</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">BATCH</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">NO</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">NAME</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">AGE</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">GENDER</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">IP GROUP</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">SCHOOL</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">COURSE</th>
                                    <th colspan="5" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">YEAR</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">GRANTS</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">REMARKS/STATUS</th>
                                </tr>
                                <!-- Second row with sub-headers -->
                                <tr>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">F</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">M</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">Private</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">Public</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">1st</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">2nd</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">3rd</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">4th</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">5th</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">1st Sem</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">2nd Sem</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody" class="bg-white">
                                <!-- Data will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm text-slate-600">
                    <span id="reportCount" class="font-medium"></span>
                    <button onclick="exportToCSV()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export to CSV
                    </button>
                </div>
            </div>
        </div>
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

    let granteesData = [];

    function openGranteesReport() {
        const modal = document.getElementById('granteesReportModal');
        const loading = document.getElementById('reportLoading');
        const content = document.getElementById('reportContent');
        const tableBody = document.getElementById('reportTableBody');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        tableBody.innerHTML = '';

        // Get current filter values
        const params = new URLSearchParams();
        const province = document.getElementById('province-filter')?.value;
        const municipality = document.getElementById('municipality-filter')?.value;
        const barangay = document.getElementById('barangay-filter')?.value;
        const ethno = document.getElementById('ethno-filter')?.value;

        if (province) params.append('province', province);
        if (municipality) params.append('municipality', municipality);
        if (barangay) params.append('barangay', barangay);
        if (ethno) params.append('ethno', ethno);

        fetch(`{{ route('staff.grantees.report') }}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    granteesData = data.grantees;
                    renderReportTable(data.grantees);
                    loading.classList.add('hidden');
                    content.classList.remove('hidden');
                } else {
                    alert('Error loading report data');
                    closeGranteesReport();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading report data');
                closeGranteesReport();
            });
    }

    function closeGranteesReport() {
        const modal = document.getElementById('granteesReportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function renderReportTable(grantees) {
        const tableBody = document.getElementById('reportTableBody');
        const reportCount = document.getElementById('reportCount');
        
        reportCount.textContent = `Total Grantees: ${grantees.length}`;

        if (grantees.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-slate-500">
                        No grantees found
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = grantees.map((grantee, index) => {
            const rowClass = index % 2 === 0 ? 'bg-white' : 'bg-slate-50';
            
            // Format address and AD Reference
            const addressLine = [
                grantee.province || '',
                grantee.municipality || '',
                grantee.barangay || '',
                grantee.ad_reference || ''
            ].filter(Boolean).join(', ');
            
            // Gender checkboxes (using exact values from form: "Male" or "Female")
            const isFemale = grantee.is_female || false;
            const isMale = grantee.is_male || false;
            
            // School type checkboxes
            const isPrivate = grantee.is_private || false;
            const isPublic = grantee.is_public || false;
            
            // Year level checkboxes (using boolean flags from controller)
            const is1st = grantee.is_1st || false;
            const is2nd = grantee.is_2nd || false;
            const is3rd = grantee.is_3rd || false;
            const is4th = grantee.is_4th || false;
            const is5th = grantee.is_5th || false;
            
            return `
                <tr class="${rowClass} hover:bg-blue-50 transition-colors">
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${addressLine}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${grantee.contact_email || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${grantee.batch || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center font-medium">${grantee.no || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${grantee.name || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${grantee.age || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isFemale ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isMale ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${grantee.ethnicity || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isPrivate ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isPublic ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${grantee.course || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is1st ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is2nd ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is3rd ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is4th ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is5th ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${grantee.grant_1st_sem || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${grantee.grant_2nd_sem || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${grantee.remarks || ''}</td>
                </tr>
            `;
        }).join('');
    }

    function exportToCSV() {
        if (granteesData.length === 0) {
            alert('No data to export');
            return;
        }

        // CSV Headers matching the grid table view structure
        const headers = [
            'Province, Municipality, Barangay, AD Reference No.',
            'Contact Number/Email',
            'BATCH',
            'NO',
            'NAME',
            'AGE',
            'GENDER',
            'IP GROUP',
            'SCHOOL (Private)',
            'SCHOOL (Public)',
            'COURSE',
            'YEAR',
            'GRANTS (1st Sem)',
            'GRANTS (2nd Sem)',
            'REMARKS/STATUS'
        ];

        // CSV Rows - matching the grid table view format
        const rows = granteesData.map(grantee => {
            // Format address and AD Reference (combined like in table)
            const addressLine = [
                grantee.province || '',
                grantee.municipality || '',
                grantee.barangay || '',
                grantee.ad_reference || ''
            ].filter(Boolean).join(', ');
            
            // Gender - combine F and M into single value
            const isFemale = grantee.is_female || false;
            const isMale = grantee.is_male || false;
            let genderValue = '';
            if (isFemale && isMale) {
                genderValue = 'F, M';
            } else if (isFemale) {
                genderValue = 'F';
            } else if (isMale) {
                genderValue = 'M';
            }
            
            // Year - combine all year levels into single value
            const is1st = grantee.is_1st || false;
            const is2nd = grantee.is_2nd || false;
            const is3rd = grantee.is_3rd || false;
            const is4th = grantee.is_4th || false;
            const is5th = grantee.is_5th || false;
            const yearLevels = [];
            if (is1st) yearLevels.push('1st');
            if (is2nd) yearLevels.push('2nd');
            if (is3rd) yearLevels.push('3rd');
            if (is4th) yearLevels.push('4th');
            if (is5th) yearLevels.push('5th');
            const yearValue = yearLevels.join(', ');
            
            return [
                addressLine,
                grantee.contact_email || '',
                grantee.batch || '',
                grantee.no || '',
                grantee.name || '',
                grantee.age || '',
                genderValue,
                grantee.ethnicity || '',
                grantee.is_private ? '✓' : '',
                grantee.is_public ? '✓' : '',
                grantee.course || '',
                yearValue,
                grantee.grant_1st_sem || '',
                grantee.grant_2nd_sem || '',
                grantee.remarks || ''
            ];
        });

        // Escape CSV values
        function escapeCSV(value) {
            if (value === null || value === undefined) return '';
            const stringValue = String(value);
            // Always wrap in quotes for consistency and to handle special characters
            return `"${stringValue.replace(/"/g, '""')}"`;
        }

        // Create CSV content with UTF-8 BOM for Excel compatibility
        const csvContent = [
            headers.map(escapeCSV).join(','),
            ...rows.map(row => row.map(escapeCSV).join(','))
        ].join('\r\n'); // Use \r\n for better Windows/Excel compatibility

        // Add UTF-8 BOM for Excel to recognize special characters correctly
        const BOM = '\uFEFF';
        const csvWithBOM = BOM + csvContent;

        // Create blob and download
        const blob = new Blob([csvWithBOM], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        const dateStr = new Date().toISOString().split('T')[0];
        link.setAttribute('download', `Grantees_Report_${dateStr}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Close modal on outside click
    document.getElementById('granteesReportModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeGranteesReport();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeGranteesReport();
        }
    });

</script>
@endpush
@endsection
