@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50">
    <div class="mx-auto max-w-7xl px-4 py-10 text-slate-900 sm:px-6 lg:px-8 lg:py-14">
        <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
            <div class="space-y-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-orange-100 bg-orange-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                    Applicant Priority
                </span>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Applicant Priority Overview</h1>
                    <p class="mt-2 max-w-3xl text-sm text-slate-600">
                        Consolidated priority ladder for applicants under {{ $assignedBarangay }}. Weighted scoring: <strong class="text-slate-900">Priority IP groups 30%</strong>,
                        <strong class="text-slate-900">priority courses 25%</strong>, <strong class="text-slate-900">Tribal Certificate 20%</strong>, <strong class="text-slate-900">Income Tax Document 15%</strong>, <strong class="text-slate-900">Academic Performance 5%</strong>, <strong class="text-slate-900">Other Requirements 5%</strong>, with <strong class="text-slate-900">FCFS</strong> as the final tiebreaker (excluding BS IT, BS CS, BS Accountancy, BS Nursing, BS Education, BA Political Science). Course priority now follows the preferred program in each scholarship form and only uses the registration-time course when no preference exists.
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-600">
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold">
                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Updated {{ now()->format('M d, Y g:i A') }}
                </span>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 shadow-sm transition hover:bg-slate-50">Back to Dashboard</a>
            </div>
        </div>

        @php
            $totalApplicants = $applicantPriorityStatistics['total_applicants'] ?? 0;
            $priorityEthnoCount = $applicantPriorityStatistics['priority_ethno_count'] ?? 0;
            $priorityCourseCount = $applicantPriorityStatistics['priority_course_count'] ?? 0;
            $tribalCertCount = $applicantPriorityStatistics['tribal_cert_count'] ?? 0;
            $incomeTaxCount = $applicantPriorityStatistics['income_tax_count'] ?? 0;
            $academicCount = $applicantPriorityStatistics['academic_performance_count'] ?? 0;
            $otherReqCount = $applicantPriorityStatistics['other_requirements_count'] ?? 0;

            $priorityIpPercent = $totalApplicants ? min(100, round(($priorityEthnoCount / $totalApplicants) * 100)) : 0;
            $priorityCoursePercent = $totalApplicants ? min(100, round(($priorityCourseCount / $totalApplicants) * 100)) : 0;
            $otherReqPercent = $totalApplicants ? min(100, round(($otherReqCount / $totalApplicants) * 100)) : 0;

            $priorityCardConfig = [
                [
                    'label' => 'Priority IP Applicants',
                    'value' => $priorityEthnoCount,
                    'description' => 'High-impact ethnolinguistic groups',
                    'badge' => '30% weight',
                    'accent' => 'text-orange-600',
                    'chip' => 'border-orange-100 bg-orange-50 text-orange-700',
                ],
                [
                    'label' => 'Priority Courses',
                    'value' => $priorityCourseCount,
                    'description' => 'Strategic degree programs',
                    'badge' => '25% weight',
                    'accent' => 'text-fuchsia-600',
                    'chip' => 'border-fuchsia-100 bg-fuchsia-50 text-fuchsia-700',
                ],
                [
                    'label' => 'Tribal Certificate',
                    'value' => $tribalCertCount,
                    'description' => 'NCIP validation complete',
                    'badge' => '20% weight',
                    'accent' => 'text-rose-600',
                    'chip' => 'border-rose-100 bg-rose-50 text-rose-700',
                ],
                [
                    'label' => 'Income Tax Docs',
                    'value' => $incomeTaxCount,
                    'description' => 'ITO/ITR documents cleared',
                    'badge' => '15% weight',
                    'accent' => 'text-emerald-600',
                    'chip' => 'border-emerald-100 bg-emerald-50 text-emerald-700',
                ],
                [
                    'label' => 'Academic Performance',
                    'value' => $academicCount,
                    'description' => 'Grades verified for ranking',
                    'badge' => '5% weight',
                    'accent' => 'text-blue-600',
                    'chip' => 'border-blue-100 bg-blue-50 text-blue-700',
                ],
                [
                    'label' => 'Other Requirements',
                    'value' => $otherReqCount,
                    'description' => 'Final checklist approvals',
                    'badge' => '5% weight',
                    'accent' => 'text-slate-600',
                    'chip' => 'border-slate-200 bg-slate-50 text-slate-700',
                ],
            ];

            $documentProgress = [
                [
                    'label' => 'Tribal Certificate',
                    'value' => $tribalCertCount,
                    'gradient' => 'from-rose-200 via-orange-200 to-amber-300',
                ],
                [
                    'label' => 'Income Tax',
                    'value' => $incomeTaxCount,
                    'gradient' => 'from-emerald-200 via-emerald-300 to-teal-400',
                ],
                [
                    'label' => 'Academic Performance',
                    'value' => $academicCount,
                    'gradient' => 'from-sky-200 via-blue-300 to-indigo-400',
                ],
                [
                    'label' => 'Other Requirements',
                    'value' => $otherReqCount,
                    'gradient' => 'from-slate-200 via-slate-300 to-slate-400',
                ],
            ];

            $priorityWeights = [
                ['label' => 'Priority IP groups', 'value' => '30%', 'accent' => 'text-orange-500'],
                ['label' => 'Priority courses', 'value' => '25%', 'accent' => 'text-fuchsia-500'],
                ['label' => 'Tribal certificate', 'value' => '20%', 'accent' => 'text-rose-500'],
                ['label' => 'Income tax document', 'value' => '15%', 'accent' => 'text-green-500'],
                ['label' => 'Academic performance', 'value' => '5%', 'accent' => 'text-blue-500'],
                ['label' => 'Other requirements', 'value' => '5%', 'accent' => 'text-slate-500'],
            ];
        @endphp

        <div class="mt-10 grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,0.6fr)]">
            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Queue depth</p>
                            <p class="mt-1 text-4xl font-bold text-slate-900">{{ number_format($totalApplicants) }}</p>
                            <p class="text-sm text-slate-500">Active applicants under {{ $assignedBarangay }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-right text-xs text-slate-600">
                            <p class="text-[10px] uppercase tracking-[0.3em] text-slate-400">FCFS Tiebreaker</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">Earlier submissions outrank ties</p>
                        </div>
                    </div>
                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Priority IP share</p>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-2xl font-semibold text-slate-900">{{ $priorityIpPercent }}%</span>
                                <span class="text-xs text-slate-500">{{ $priorityEthnoCount }} applicants</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-orange-300 via-orange-400 to-amber-500" style="width: {{ $priorityIpPercent }}%;"></div>
                            </div>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Priority courses</p>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-2xl font-semibold text-slate-900">{{ $priorityCoursePercent }}%</span>
                                <span class="text-xs text-slate-500">{{ $priorityCourseCount }} applicants</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-fuchsia-300 via-violet-400 to-indigo-500" style="width: {{ $priorityCoursePercent }}%;"></div>
                            </div>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Final checklist</p>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-2xl font-semibold text-slate-900">{{ $otherReqPercent }}%</span>
                                <span class="text-xs text-slate-500">{{ $otherReqCount }} cleared</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-slate-300 to-slate-500" style="width: {{ $otherReqPercent }}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-2 text-[11px] text-slate-600">
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1">Holistic scoring</span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1">Live updates</span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1">Excludes BSIT, BSCS, BS Acctg, BS Nursing, BS Educ, BA PolSci</span>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($priorityCardConfig as $card)
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-slate-600">{{ $card['label'] }}</p>
                                <span class="rounded-full border px-2 py-0.5 text-[11px] font-semibold {{ $card['chip'] }}">{{ $card['badge'] }}</span>
                            </div>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($card['value']) }}</p>
                            <p class="text-sm text-slate-500">{{ $card['description'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Priority ladder weights</p>
                        <ul class="mt-4 space-y-3 text-sm">
                            @foreach($priorityWeights as $weight)
                                <li class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <span class="font-semibold text-slate-700">{{ $weight['label'] }}</span>
                                    <span class="text-sm font-bold {{ $weight['accent'] }}">{{ $weight['value'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Processing flow</p>
                        <ol class="mt-4 space-y-4 text-sm text-slate-600">
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-600">1</span>
                                <div>
                                    <p class="font-semibold text-slate-900">Queue + validate</p>
                                    <p class="text-xs text-slate-500">Applicants submit requirements per barangay assignment.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-600">2</span>
                                <div>
                                    <p class="font-semibold text-slate-900">Weighted scoring</p>
                                    <p class="text-xs text-slate-500">Weights apply per requirement to produce composite scores.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-600">3</span>
                                <div>
                                    <p class="font-semibold text-slate-900">FCFS tiebreak</p>
                                    <p class="text-xs text-slate-500">Submission timestamps finalize the ladder when scores tie.</p>
                                </div>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Queue health</p>
                        <span class="text-[11px] text-slate-500">{{ $totalApplicants ? 'Per document stage' : 'Awaiting queue' }}</span>
                    </div>
                    <div class="mt-5 space-y-5 text-sm text-slate-600">
                        @foreach($documentProgress as $metric)
                            @php
                                $percent = $totalApplicants ? min(100, round(($metric['value'] / $totalApplicants) * 100)) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>{{ $metric['label'] }}</span>
                                    <span class="font-semibold text-slate-700">{{ number_format($metric['value']) }} {{ Str::plural('applicant', $metric['value']) }}</span>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-gradient-to-r {{ $metric['gradient'] }}" style="width: {{ $percent }}%;"></div>
                                </div>
            </div>
                        @endforeach
            </div>
            </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Rank legend</p>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        <li class="flex items-center justify-between">
                            <span>Ranks 1 - 5</span>
                            <span class="h-2.5 w-20 rounded-full bg-fuchsia-400"></span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Ranks 6 - 10</span>
                            <span class="h-2.5 w-20 rounded-full bg-rose-400"></span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Ranks 11 - 20</span>
                            <span class="h-2.5 w-20 rounded-full bg-amber-400"></span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Ranks 21+</span>
                            <span class="h-2.5 w-20 rounded-full bg-sky-400"></span>
                        </li>
                    </ul>
                    <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                        <p class="font-semibold text-slate-800">Need focus?</p>
                        <p>Use the Review action per applicant to drill into submissions, or export from the dashboard for bulk work.</p>
            </div>
            </div>
            </div>
        </div>

        @if(!empty($prioritizedApplicants))
            <div class="mt-10 space-y-6">
                <div class="hidden overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm lg:block">
                <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-4 text-left">Priority Rank</th>
                                <th class="px-5 py-4 text-left">Score</th>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-left">Submission</th>
                                <th class="px-5 py-4 text-left">IP Group</th>
                                <th class="px-5 py-4 text-left">Tribal Cert</th>
                                <th class="px-5 py-4 text-left">Income Tax</th>
                                <th class="px-5 py-4 text-left">Academic Perf.</th>
                                <th class="px-5 py-4 text-left">Other Req.</th>
                                <th class="px-5 py-4 text-left">Course</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($prioritizedApplicants as $applicantData)
                                @php
                                    $priorityRank = $applicantData['priority_rank'] ?? 9999;
                                    $user = $applicantData['user'];
                                    $submittedAt = $applicantData['application_submitted_at'];
                                    $daysWaiting = $applicantData['days_since_submission'] ?? 0;
                                    $isPriorityEthno = $applicantData['is_priority_ethno'] ?? false;
                                    $ethnicity = $applicantData['ethnicity'] ?? 'N/A';
                                    $hasApprovedTribalCert = $applicantData['has_approved_tribal_cert'] ?? false;
                                    $hasApprovedIncomeTax = $applicantData['has_approved_income_tax'] ?? false;
                                    $hasApprovedGrades = $applicantData['has_approved_grades'] ?? false;
                                    $hasAllOtherRequirements = $applicantData['has_all_other_requirements'] ?? false;
                                    $course = $applicantData['course'] ?? 'N/A';
                                    $normalizedCourse = $applicantData['normalized_course'] ?? 'Other';
                                    $isPriorityCourse = $applicantData['is_priority_course'] ?? false;
                                    $priorityScore = $applicantData['priority_score'] ?? 0;
                                @endphp
                                    <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        @if($priorityRank && $priorityRank != 9999)
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white
                                                    @if($priorityRank <= 5) bg-fuchsia-500
                                                    @elseif($priorityRank <= 10) bg-rose-500
                                                    @elseif($priorityRank <= 20) bg-amber-400 text-slate-900
                                                    @else bg-sky-500
                                                @endif">#{{ $priorityRank }}</span>
                                        @else
                                                <span class="text-xs text-slate-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                            <span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700 border border-orange-200">
                                            {{ number_format($priorityScore, 0) }} pts
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 overflow-hidden rounded-full border border-slate-200 bg-slate-50">
                                                @if($user->profile_pic)
                                                    <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Profile" class="h-full w-full object-cover">
                                                @else
                                                        <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-slate-600">
                                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                    <p class="font-semibold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                                    <p class="text-[11px] text-slate-500">ID: {{ $user->id }}</p>
                                                </div>
                                        </div>
                                    </td>
                                        <td class="px-5 py-4 text-xs text-slate-500">
                                        <div>{{ $submittedAt->format('M d, Y') }}</div>
                                        <div class="text-[10px]">{{ $submittedAt->format('g:i A') }}</div>
                        ...
                                    </td>
                                    <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs
                                                @if($isPriorityEthno) border-orange-200 bg-orange-50 text-orange-700 font-semibold @else border-slate-200 bg-white text-slate-600 @endif">
                                            {{ $ethnicity }}
                                            @if($isPriorityEthno)
                                                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-orange-500"></span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($hasApprovedTribalCert)
                                                <span class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approved (3rd)
                                            </span>
                                            @else
                                                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs text-slate-500">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($hasApprovedIncomeTax)
                                                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approved (4th)
                                            </span>
                                            @else
                                                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs text-slate-500">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($hasApprovedGrades)
                                                <span class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approved (5th)
                                            </span>
                                            @else
                                                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs text-slate-500">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($hasAllOtherRequirements)
                                                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Complete (Final)
                                            </span>
                                            @else
                                                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs text-slate-500">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                        <td class="px-5 py-4 text-xs text-slate-500">
                                            <div class="font-semibold text-slate-900" title="{{ $course }}">{{ $course }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $normalizedCourse }}</div>
                                        @if($isPriorityCourse)
                                                <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-indigo-600">Priority</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                            <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700 transition hover:bg-slate-50">Review →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

                <div class="space-y-4 lg:hidden">
                    @foreach($prioritizedApplicants as $applicantData)
                        @php
                            $priorityRank = $applicantData['priority_rank'] ?? 9999;
                            $user = $applicantData['user'];
                            $submittedAt = $applicantData['application_submitted_at'];
                            $daysWaiting = $applicantData['days_since_submission'] ?? 0;
                            $isPriorityEthno = $applicantData['is_priority_ethno'] ?? false;
                            $ethnicity = $applicantData['ethnicity'] ?? 'N/A';
                            $hasApprovedTribalCert = $applicantData['has_approved_tribal_cert'] ?? false;
                            $hasApprovedIncomeTax = $applicantData['has_approved_income_tax'] ?? false;
                            $hasApprovedGrades = $applicantData['has_approved_grades'] ?? false;
                            $hasAllOtherRequirements = $applicantData['has_all_other_requirements'] ?? false;
                            $course = $applicantData['course'] ?? 'N/A';
                            $normalizedCourse = $applicantData['normalized_course'] ?? 'Other';
                            $isPriorityCourse = $applicantData['is_priority_course'] ?? false;
                            $priorityScore = $applicantData['priority_score'] ?? 0;
                        @endphp
                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Priority Rank</p>
                                    @if($priorityRank && $priorityRank != 9999)
                                        <p class="text-3xl font-extrabold text-slate-900">#{{ $priorityRank }}</p>
                                    @else
                                        <p class="text-xl font-semibold text-slate-400">N/A</p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700 border border-orange-200">
                                    {{ number_format($priorityScore, 0) }} pts
                                </span>
                            </div>

                            <div class="mt-4 flex items-center gap-3">
                                <div class="h-12 w-12 overflow-hidden rounded-full border border-slate-200 bg-slate-50">
                                    @if($user->profile_pic)
                                        <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Profile" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-sm font-semibold text-slate-600">
                                            {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                    <p class="text-[11px] text-slate-500">ID: {{ $user->id }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $course }}</p>
                                </div>
                            </div>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600">
                                    <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Submission</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ $submittedAt->format('M d, Y') }}</p>
                                    <p class="text-[10px]">{{ $submittedAt->format('g:i A') }}</p>
                                    <p class="text-[10px] text-orange-600">{{ $daysWaiting }} {{ Str::plural('day', $daysWaiting) }} waiting</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-xs">
                                    <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">IP Group</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ $ethnicity }}</p>
                                    @if($isPriorityEthno)
                                        <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-orange-50 px-2 py-0.5 text-[10px] font-semibold text-orange-700 border border-orange-200">Priority IP</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2 text-[11px]">
                                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1
                                    @if($hasApprovedTribalCert) border-rose-200 bg-rose-50 text-rose-700 @else border-slate-200 bg-white text-slate-500 @endif">
                                    Tribal Cert
                                    @if($hasApprovedTribalCert)
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @endif
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1
                                    @if($hasApprovedIncomeTax) border-emerald-200 bg-emerald-50 text-emerald-700 @else border-slate-200 bg-white text-slate-500 @endif">
                                    Income Tax
                                    @if($hasApprovedIncomeTax)
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @endif
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1
                                    @if($hasApprovedGrades) border-sky-200 bg-sky-50 text-sky-700 @else border-slate-200 bg-white text-slate-500 @endif">
                                    Academic
                                    @if($hasApprovedGrades)
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @endif
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1
                                    @if($hasAllOtherRequirements) border-slate-200 bg-slate-50 text-slate-700 @else border-slate-200 bg-white text-slate-500 @endif">
                                    Other Req.
                                    @if($hasAllOtherRequirements)
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @endif
                                </span>
                                @if($isPriorityCourse)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wide text-indigo-600">Priority Course</span>
                                @endif
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-[11px] text-slate-500">{{ $normalizedCourse }}</div>
                                <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700 transition hover:bg-slate-50">Review →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-500">
                <svg class="mx-auto mb-4 h-14 w-14 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Applicant prioritization data will appear once applications are queued.
            </div>
        @endif
    </div>
</div>
@endsection
