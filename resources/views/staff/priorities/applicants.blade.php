@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-slate-950">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-20 h-96 w-96 rounded-full bg-emerald-500/20 blur-3xl"></div>
        <div class="absolute top-80 -left-32 h-[420px] w-[420px] rounded-full bg-teal-500/10 blur-3xl"></div>
        <div class="absolute bottom-[-180px] right-20 h-[480px] w-[480px] rounded-full bg-purple-500/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 text-slate-100 sm:px-6 lg:px-8 lg:py-14">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-200">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-300"></span>
                    Applicant Priority
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Applicant Priority Overview</h1>
                <p class="max-w-2xl text-sm text-slate-300">
                    Consolidated priority ladder for applicants under {{ $assignedBarangay }}. Sequencing is based on <strong class="text-amber-200">Priority IP groups (1st)</strong>,
                    <strong class="text-purple-200">priority courses (2nd)</strong>, <strong class="text-orange-200">Certificate of Tribal Membership (3rd)</strong>, and <strong class="text-emerald-200">FCFS</strong> as tiebreaker (excluding BS IT, BS CS, BS Accountancy, BS Nursing, BS Education, BA Political Science).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-100/80">
                <span class="inline-flex items-center rounded-full border border-slate-200/20 bg-white/10 px-3 py-1 text-[11px]">
                    <svg class="mr-1 h-3.5 w-3.5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Updated {{ now()->format('M d, Y g:i A') }}
                </span>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition hover:bg-white/20">Back to Dashboard</a>
            </div>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-emerald-300/40">
                <div class="absolute -right-8 -top-8 h-20 w-20 rounded-full bg-emerald-400/20 blur-3xl transition group-hover:scale-110"></div>
                <p class="text-xs uppercase tracking-[0.25em] text-emerald-200">Total Applicants</p>
                <div class="mt-2 text-3xl font-bold text-white">{{ $applicantPriorityStatistics['total_applicants'] ?? 0 }}</div>
                <p class="text-xs text-emerald-100/70">Queued in priority ladder</p>
            </div>
            <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-amber-300/40">
                <div class="absolute -right-8 -top-10 h-24 w-24 rotate-12 rounded-3xl bg-amber-400/20 blur-3xl transition group-hover:scale-110"></div>
                <p class="text-xs uppercase tracking-[0.25em] text-amber-200">Priority IP Applicants</p>
                <div class="mt-2 text-3xl font-bold text-white">{{ $applicantPriorityStatistics['priority_ethno_count'] ?? 0 }}</div>
                <p class="text-xs text-amber-100/70">From prioritized IP groups</p>
            </div>
            <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-orange-300/40">
                <div class="absolute -right-8 -top-10 h-24 w-24 rotate-12 rounded-3xl bg-orange-400/20 blur-3xl transition group-hover:scale-110"></div>
                <p class="text-xs uppercase tracking-[0.25em] text-orange-200">Tribal Certificate</p>
                <div class="mt-2 text-3xl font-bold text-white">{{ $applicantPriorityStatistics['tribal_cert_count'] ?? 0 }}</div>
                <p class="text-xs text-orange-100/70">With approved certificates (Rank 3)</p>
            </div>
            <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-purple-300/40">
                <div class="absolute -right-8 -bottom-10 h-24 w-24 rounded-full bg-purple-400/20 blur-3xl transition group-hover:scale-110"></div>
                <p class="text-xs uppercase tracking-[0.25em] text-purple-200">Priority Courses</p>
                <div class="mt-2 text-3xl font-bold text-white">{{ $applicantPriorityStatistics['priority_course_count'] ?? 0 }}</div>
                <p class="text-xs text-purple-100/70">Aligned with priority offerings</p>
            </div>
            <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-cyan-300/40">
                <div class="absolute -left-8 top-6 h-24 w-24 rounded-full bg-cyan-400/20 blur-3xl transition group-hover:scale-110"></div>
                <p class="text-xs uppercase tracking-[0.25em] text-cyan-200">Queue Depth</p>
                <div class="mt-2 text-3xl font-bold text-white">{{ $applicantPriorityStatistics['oldest_application']['days_waiting'] ?? 0 }}</div>
                <p class="text-xs text-cyan-100/70">Longest wait in days</p>
            </div>
        </div>

        @if(!empty($prioritizedApplicants))
            <div class="mt-10 overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-xl backdrop-blur">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-sm text-slate-100">
                        <thead class="bg-emerald-500/20 text-xs uppercase tracking-[0.25em] text-emerald-100">
                            <tr>
                                <th class="px-5 py-4 text-left">Priority Rank</th>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-left">Submission</th>
                                <th class="px-5 py-4 text-left">IP Group</th>
                                <th class="px-5 py-4 text-left">Tribal Cert</th>
                                <th class="px-5 py-4 text-left">Course</th>
                                <th class="px-5 py-4 text-left">Priority Signals</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 bg-white/5">
                            @foreach($prioritizedApplicants as $applicantData)
                                @php
                                    $priorityRank = $applicantData['priority_rank'] ?? 9999;
                                    $user = $applicantData['user'];
                                    $submittedAt = $applicantData['application_submitted_at'];
                                    $daysWaiting = $applicantData['days_since_submission'] ?? 0;
                                    $isPriorityEthno = $applicantData['is_priority_ethno'] ?? false;
                                    $ethnicity = $applicantData['ethnicity'] ?? 'N/A';
                                    $hasApprovedTribalCert = $applicantData['has_approved_tribal_cert'] ?? false;
                                    $course = $applicantData['course'] ?? 'N/A';
                                    $normalizedCourse = $applicantData['normalized_course'] ?? 'Other';
                                    $isPriorityCourse = $applicantData['is_priority_course'] ?? false;
                                @endphp
                                <tr class="transition hover:bg-emerald-400/10">
                                    <td class="px-5 py-4">
                                        @if($priorityRank && $priorityRank != 9999)
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold text-white
                                                @if($priorityRank <= 5) bg-rose-500/80
                                                @elseif($priorityRank <= 10) bg-orange-500/80
                                                @elseif($priorityRank <= 20) bg-yellow-500/70 text-slate-900
                                                @else bg-emerald-500/70
                                                @endif">#{{ $priorityRank }}</span>
                                        @else
                                            <span class="text-xs text-slate-300/70">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 overflow-hidden rounded-full border border-white/20 bg-white/10">
                                                @if($user->profile_pic)
                                                    <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Profile" class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-slate-100">
                                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-white">{{ $user->first_name }} {{ $user->last_name }}</p>
                                                <p class="text-[11px] text-slate-200/70">ID: {{ $user->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-200/80">
                                        <div>{{ $submittedAt->format('M d, Y') }}</div>
                                        <div class="text-[10px]">{{ $submittedAt->format('g:i A') }}</div>
                                        <div class="mt-1 text-[10px] text-emerald-200/80">{{ $daysWaiting }} {{ Str::plural('day', $daysWaiting) }} waiting</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-white
                                            @if($isPriorityEthno) bg-amber-400/30 text-white @else bg-white/5 text-slate-100 @endif">
                                            {{ $ethnicity }}
                                            @if($isPriorityEthno)
                                                <span class="inline-flex h-3 w-3 rounded-full bg-amber-300"></span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($hasApprovedTribalCert)
                                            <span class="inline-flex items-center gap-2 rounded-full border border-orange-400/40 bg-orange-400/30 px-3 py-1 text-xs font-semibold text-white">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approved (3rd)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-slate-300">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-200/80">
                                        <div class="font-semibold text-white" title="{{ $course }}">{{ $course }}</div>
                                        <div class="text-[10px] text-slate-300/60">{{ $normalizedCourse }}</div>
                                        @if($isPriorityCourse)
                                            <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-purple-500/70 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white">Priority</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-200/80">
                                        <div class="space-y-1">
                                            <span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-amber-300"></span>{{ $isPriorityEthno ? 'Priority IP (1st)' : 'General IP' }}</span>
                                            <span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-purple-300"></span>{{ $isPriorityCourse ? 'Priority Course (2nd)' : 'Standard Course' }}</span>
                                            <span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-orange-300"></span>{{ $hasApprovedTribalCert ? 'Tribal Cert ✓ (3rd)' : 'Tribal Cert Pending' }}</span>
                                            <span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>FCFS (Tiebreaker)</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-emerald-300/40 bg-emerald-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-100 transition hover:bg-emerald-300/20">Review →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-white/10 bg-white/5 p-12 text-center text-sm text-slate-200/70">
                <svg class="mx-auto mb-4 h-14 w-14 text-emerald-200/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Applicant prioritization data will appear once applications are queued.
            </div>
        @endif
    </div>
</div>
@endsection
