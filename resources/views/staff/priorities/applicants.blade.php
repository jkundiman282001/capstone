@extends('layouts.app')

@section('content')
<div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-amber-50 via-orange-50 to-red-50">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="absolute -top-28 -left-28 h-96 w-96 rounded-full bg-orange-300/25 blur-3xl"></div>
        <div class="absolute -top-40 -right-24 h-[28rem] w-[28rem] rounded-full bg-rose-300/20 blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/3 h-[30rem] w-[30rem] rounded-full bg-amber-200/25 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 text-slate-900 sm:px-6 lg:px-8 lg:py-14">
        <div class="rounded-3xl border border-white/60 bg-white/70 p-6 shadow-sm backdrop-blur-xl sm:p-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-orange-600 to-rose-600 px-3 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-white shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-white/90"></span>
                        Applicant Priority
                    </span>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">Applicant Priority Overview</h1>
                        <p class="mt-3 max-w-3xl text-sm text-slate-600">
                            Consolidated priority ladder for applicants under <span class="font-semibold text-slate-900">{{ $assignedBarangay }}</span>. Weighted scoring:
                            <strong class="text-slate-900">IP group rubric 20%</strong>,
                            <strong class="text-slate-900">GWA (75–100) 30%</strong>,
                            <strong class="text-slate-900">Income Tax Return (ITR) 30%</strong>,
                            <strong class="text-slate-900">Citations/Awards 10%</strong>,
                            <strong class="text-slate-900">Social responsibility (essays) 10%</strong>,
                            with <strong class="text-slate-900">FCFS</strong> as the final tiebreaker.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center justify-start gap-3 text-sm text-slate-600 md:justify-end">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/60 bg-white/60 px-3 py-1 text-[11px] font-semibold shadow-sm backdrop-blur">
                        <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Updated {{ now()->format('M d, Y g:i A') }}
                    </span>
                    <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        @php
            $totalApplicants = $applicantPriorityStatistics['total_applicants'] ?? 0;
            $priorityEthnoCount = $applicantPriorityStatistics['priority_ethno_count'] ?? 0;
            $incomeTaxCount = $applicantPriorityStatistics['income_tax_count'] ?? 0;
            $academicCount = $applicantPriorityStatistics['academic_count'] ?? 0;
            $awardsCount = $applicantPriorityStatistics['awards_count'] ?? 0;
            $socialCount = $applicantPriorityStatistics['social_responsibility_count'] ?? 0;

            $priorityIpPercent = $totalApplicants ? min(100, round(($priorityEthnoCount / $totalApplicants) * 100)) : 0;
            $incomeTaxPercent = $totalApplicants ? min(100, round(($incomeTaxCount / $totalApplicants) * 100)) : 0;
            $academicPercent = $totalApplicants ? min(100, round(($academicCount / $totalApplicants) * 100)) : 0;
            $awardsPercent = $totalApplicants ? min(100, round(($awardsCount / $totalApplicants) * 100)) : 0;
            $socialPercent = $totalApplicants ? min(100, round(($socialCount / $totalApplicants) * 100)) : 0;

            $priorityCardConfig = [
                [
                    'label' => 'Priority IP Applicants',
                    'value' => $priorityEthnoCount,
                    'description' => 'High-impact ethnolinguistic groups',
                    'badge' => '20% weight',
                    'accent' => 'text-orange-600',
                    'chip' => 'border-orange-100 bg-orange-50 text-orange-700',
                ],
                [
                    'label' => 'Income Tax Return (ITR)',
                    'value' => $incomeTaxCount,
                    'description' => 'Approved income tax documents',
                    'badge' => '30% weight',
                    'accent' => 'text-emerald-600',
                    'chip' => 'border-emerald-100 bg-emerald-50 text-emerald-700',
                ],
                [
                    'label' => 'Academic (GWA 75–100)',
                    'value' => $academicCount,
                    'description' => 'Has GWA data to score',
                    'badge' => '30% weight',
                    'accent' => 'text-blue-600',
                    'chip' => 'border-blue-100 bg-blue-50 text-blue-700',
                ],
                [
                    'label' => 'Citations / Awards',
                    'value' => $awardsCount,
                    'description' => 'Applicants with recognitions',
                    'badge' => '10% weight',
                    'accent' => 'text-fuchsia-600',
                    'chip' => 'border-fuchsia-100 bg-fuchsia-50 text-fuchsia-700',
                ],
                [
                    'label' => 'Social Responsibility',
                    'value' => $socialCount,
                    'description' => 'Essays scored for community impact',
                    'badge' => '10% weight',
                    'accent' => 'text-slate-600',
                    'chip' => 'border-slate-200 bg-slate-50 text-slate-700',
                ],
            ];

            $documentProgress = [
                [
                    'label' => 'Income Tax Return (ITR)',
                    'value' => $incomeTaxCount,
                    'gradient' => 'from-emerald-200 via-emerald-300 to-teal-400',
                ],
                [
                    'label' => 'Academic (GWA 75–100)',
                    'value' => $academicCount,
                    'gradient' => 'from-sky-200 via-blue-300 to-indigo-400',
                ],
                [
                    'label' => 'Citations / Awards',
                    'value' => $awardsCount,
                    'gradient' => 'from-fuchsia-200 via-violet-300 to-indigo-300',
                ],
                [
                    'label' => 'Social responsibility',
                    'value' => $socialCount,
                    'gradient' => 'from-slate-200 via-slate-300 to-slate-400',
                ],
            ];

            $priorityWeights = [
                ['label' => 'IP group rubric', 'value' => '20%', 'accent' => 'text-orange-500'],
                ['label' => 'GWA (75–100)', 'value' => '30%', 'accent' => 'text-blue-500'],
                ['label' => 'Income tax return (ITR)', 'value' => '30%', 'accent' => 'text-green-500'],
                ['label' => 'Citations / awards', 'value' => '10%', 'accent' => 'text-fuchsia-500'],
                ['label' => 'Social responsibility (essays)', 'value' => '10%', 'accent' => 'text-slate-500'],
            ];
        @endphp

        <div class="mt-10 grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,0.6fr)]">
            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200/70 bg-white/80 p-6 shadow-sm backdrop-blur-xl">
                    <div class="flex flex-wrap items-start justify-between gap-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Queue depth</p>
                            <p class="mt-1 text-4xl font-bold text-slate-900">{{ number_format($totalApplicants) }}</p>
                            <p class="text-sm text-slate-500">Active applicants under {{ $assignedBarangay }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-3 text-right text-xs text-slate-600 backdrop-blur">
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
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">ITR approvals</p>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-2xl font-semibold text-slate-900">{{ $incomeTaxPercent }}%</span>
                                <span class="text-xs text-slate-500">{{ $incomeTaxCount }} applicants</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-200 via-emerald-300 to-teal-400" style="width: {{ $incomeTaxPercent }}%;"></div>
                            </div>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Academic data coverage</p>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-2xl font-semibold text-slate-900">{{ $academicPercent }}%</span>
                                <span class="text-xs text-slate-500">{{ $academicCount }} applicants</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-sky-200 via-blue-300 to-indigo-400" style="width: {{ $academicPercent }}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-2 text-[11px] text-slate-600">
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-white/60 px-3 py-1 font-semibold shadow-sm backdrop-blur">Holistic scoring</span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-white/60 px-3 py-1 font-semibold shadow-sm backdrop-blur">Live updates</span>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($priorityCardConfig as $card)
                        <div class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-slate-600">{{ $card['label'] }}</p>
                                <span class="rounded-full border px-2 py-0.5 text-[11px] font-semibold {{ $card['chip'] }}">{{ $card['badge'] }}</span>
                            </div>
                            <p class="mt-3 text-3xl font-semibold text-slate-900 tabular-nums">{{ number_format($card['value']) }}</p>
                            <p class="text-sm text-slate-500">{{ $card['description'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200/70 bg-white/80 p-6 shadow-sm backdrop-blur-xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Priority ladder weights</p>
                        <ul class="mt-4 space-y-3 text-sm">
                            @foreach($priorityWeights as $weight)
                                <li class="flex items-center justify-between rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-3 backdrop-blur">
                                    <span class="font-semibold text-slate-700">{{ $weight['label'] }}</span>
                                    <span class="text-sm font-bold {{ $weight['accent'] }}">{{ $weight['value'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="rounded-3xl border border-slate-200/70 bg-white/80 p-6 shadow-sm backdrop-blur-xl">
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
                @php
                    $topApplicantData = !empty($prioritizedApplicants) ? ($prioritizedApplicants[0] ?? null) : null;

                    $topUser = $topApplicantData['user'] ?? null;
                    $topRank = $topApplicantData['priority_rank'] ?? 1;
                    $topScore = (float) ($topApplicantData['priority_score'] ?? 0);
                    $topSubmittedAt = $topApplicantData['application_submitted_at'] ?? null;

                    $topIpRubric = (float) ($topApplicantData['ip_rubric_score'] ?? 0);
                    $topAcademicRubric = (float) ($topApplicantData['academic_rubric_score'] ?? 0);
                    $topHasItr = (bool) ($topApplicantData['has_approved_income_tax'] ?? false);
                    $topAwardsRubric = (float) ($topApplicantData['awards_rubric_score'] ?? 0);
                    $topSocialRubric = (float) ($topApplicantData['social_responsibility_rubric_score'] ?? 0);

                    $topIpContribution = max(0, min(20, ($topIpRubric / 12) * 20));
                    $topAcademicContribution = max(0, min(30, ($topAcademicRubric / 10) * 30));
                    $topItrContribution = $topHasItr ? 30.0 : 0.0;
                    $topAwardsContribution = max(0, min(10, ($topAwardsRubric / 10) * 10));
                    $topSocialContribution = max(0, min(10, ($topSocialRubric / 10) * 10));
                @endphp

                <div class="rounded-3xl border border-slate-200/70 bg-white/80 p-6 shadow-lg shadow-slate-900/5 backdrop-blur-xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.35em] text-slate-500">Top applicant</p>
                            <p class="mt-1 text-lg font-semibold text-slate-900">Highest priority right now</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-orange-600 to-rose-600 px-3 py-1 text-xs font-semibold text-white shadow-sm">
                            #{{ $topRank }}
                        </span>
                    </div>

                    @if($topApplicantData && $topUser)
                        <div class="mt-5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 overflow-hidden rounded-2xl border border-slate-200/70 bg-white/60 shadow-sm">
                                    @if($topUser->profile_pic_url)
                                        <img src="{{ $topUser->profile_pic_url }}" alt="Profile" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-orange-500 to-amber-600 text-white text-sm font-black">
                                            {{ $topUser->initials }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $topUser->first_name }} {{ $topUser->last_name }}</p>
                                    <p class="text-xs text-slate-500">ID: {{ $topUser->id }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total score</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900 tabular-nums">{{ number_format($topScore, 1) }}%</p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3">
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-3 text-xs text-slate-600 backdrop-blur">
                                <span class="font-semibold text-slate-700">IP (20%)</span>
                                <span class="font-bold text-orange-700 tabular-nums">{{ number_format($topIpContribution, 1) }}%</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-3 text-xs text-slate-600 backdrop-blur">
                                <span class="font-semibold text-slate-700">Academic (30%)</span>
                                <span class="font-bold text-sky-700 tabular-nums">{{ number_format($topAcademicContribution, 1) }}%</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-3 text-xs text-slate-600 backdrop-blur">
                                <span class="font-semibold text-slate-700">ITR (30%)</span>
                                <span class="font-bold tabular-nums @if($topHasItr) text-emerald-700 @else text-slate-500 @endif">{{ number_format($topItrContribution, 1) }}%</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-3 text-xs text-slate-600 backdrop-blur">
                                <span class="font-semibold text-slate-700">Awards (10%)</span>
                                <span class="font-bold text-fuchsia-700 tabular-nums">{{ number_format($topAwardsContribution, 1) }}%</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-3 text-xs text-slate-600 backdrop-blur">
                                <span class="font-semibold text-slate-700">Social (10%)</span>
                                <span class="font-bold text-slate-700 tabular-nums">{{ number_format($topSocialContribution, 1) }}%</span>
                            </div>
                        </div>

                        <div class="mt-5 flex items-center justify-between gap-3">
                            <div class="text-xs text-slate-500">
                                @if($topSubmittedAt)
                                    Submitted <span class="font-semibold text-slate-700">{{ $topSubmittedAt->format('M d, Y') }}</span>
                                @endif
                            </div>
                            <a href="{{ route('staff.applications.view', $topUser->id) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-white/60 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 shadow-sm transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10">
                                Review →
                            </a>
                        </div>
                    @else
                        <div class="mt-5 rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-6 text-center text-sm text-slate-500 backdrop-blur">
                            Top applicant will appear once applications are queued.
                        </div>
                    @endif
                </div>

                <div class="rounded-3xl border border-slate-200/70 bg-white/80 p-6 shadow-sm backdrop-blur-xl">
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

                <div class="rounded-3xl border border-slate-200/70 bg-white/80 p-6 shadow-sm backdrop-blur-xl">
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
                    <div class="mt-5 rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-3 text-xs text-slate-600 backdrop-blur">
                        <p class="font-semibold text-slate-800">Need focus?</p>
                        <p>Use the Review action per applicant to drill into submissions, or export from the dashboard for bulk work.</p>
            </div>
            </div>
            </div>
        </div>

        @if(!empty($prioritizedApplicants))
            <div class="mt-10 space-y-6">
                <div class="hidden overflow-hidden rounded-3xl border border-slate-200/70 bg-white/80 shadow-lg shadow-slate-900/5 backdrop-blur-xl lg:block">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-200/70 bg-white/50 px-6 py-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.35em] text-slate-500">Priority ladder</p>
                            <p class="mt-1 text-lg font-semibold text-slate-900">Score breakdown by category</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-600">
                            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-white/60 px-3 py-1 font-semibold shadow-sm backdrop-blur">
                                <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                                Total = 100%
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-white/60 px-3 py-1 font-semibold shadow-sm backdrop-blur">
                                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                Each column shows weighted contribution
                            </span>
                        </div>
                    </div>
                    <div class="max-h-[70vh] overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                            <thead class="sticky top-0 z-10 bg-white/80 text-xs font-semibold uppercase tracking-wide text-slate-500 backdrop-blur-xl">
                            <tr>
                                <th class="px-5 py-4 text-left">Rank</th>
                                <th class="px-5 py-4 text-left">Score</th>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-left">Submitted</th>
                                <th class="px-5 py-4 text-left">IP Group</th>
                                <th class="px-5 py-4 text-left">IP (20%)</th>
                                <th class="px-5 py-4 text-left">Academic (30%)</th>
                                <th class="px-5 py-4 text-left">ITR (30%)</th>
                                <th class="px-5 py-4 text-left">Awards (10%)</th>
                                <th class="px-5 py-4 text-left">Social (10%)</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                            <tbody class="divide-y divide-slate-200 bg-white/60">
                            @foreach($prioritizedApplicants as $applicantData)
                                @php
                                    $priorityRank = $applicantData['priority_rank'] ?? 9999;
                                    $user = $applicantData['user'];
                                    $submittedAt = $applicantData['application_submitted_at'];
                                    $daysWaiting = $applicantData['days_since_submission'] ?? 0;
                                    $isPriorityEthno = $applicantData['is_priority_ethno'] ?? false;
                                    $ethnicity = $applicantData['ethnicity'] ?? 'N/A';
                                    $hasApprovedIncomeTax = $applicantData['has_approved_income_tax'] ?? false;
                                    $ipRubricScore = $applicantData['ip_rubric_score'] ?? 0;
                                    $academicRubricScore = $applicantData['academic_rubric_score'] ?? 0;
                                    $awardsRubricScore = $applicantData['awards_rubric_score'] ?? 0;
                                    $socialRubricScore = $applicantData['social_responsibility_rubric_score'] ?? 0;
                                    $priorityScore = $applicantData['priority_score'] ?? 0;

                                    // Convert rubric/binary values into their weighted % contribution to total score (0–100)
                                    $ipContribution = max(0, min(20, ((float) $ipRubricScore / 12) * 20));
                                    $academicContribution = max(0, min(30, ((float) $academicRubricScore / 10) * 30));
                                    $itrContribution = $hasApprovedIncomeTax ? 30.0 : 0.0;
                                    $awardsContribution = max(0, min(10, ((float) $awardsRubricScore / 10) * 10));
                                    $socialContribution = max(0, min(10, ((float) $socialRubricScore / 10) * 10));

                                    $scoreBar = max(0, min(100, (float) $priorityScore));
                                    $ipBar = max(0, min(100, ($ipContribution / 20) * 100));
                                    $academicBar = max(0, min(100, ($academicContribution / 30) * 100));
                                    $itrBar = max(0, min(100, ($itrContribution / 30) * 100));
                                    $awardsBar = max(0, min(100, ($awardsContribution / 10) * 100));
                                    $socialBar = max(0, min(100, ($socialContribution / 10) * 100));
                                @endphp
                                    <tr class="group transition-colors hover:bg-slate-50/70">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($priorityRank && $priorityRank != 9999)
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white shadow-sm
                                                    @if($priorityRank <= 5) bg-fuchsia-500
                                                    @elseif($priorityRank <= 10) bg-rose-500
                                                    @elseif($priorityRank <= 20) bg-amber-400 text-slate-900
                                                    @else bg-sky-500
                                                @endif">#{{ $priorityRank }}</span>
                                        @else
                                                <span class="text-xs text-slate-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center rounded-full bg-gradient-to-r from-orange-600 to-rose-600 px-3 py-1 text-xs font-semibold text-white shadow-sm tabular-nums">
                                                {{ number_format((float) $priorityScore, 1) }}%
                                            </span>
                                            <div class="h-1.5 w-20 overflow-hidden rounded-full bg-slate-200/60">
                                                <div class="h-full rounded-full bg-gradient-to-r from-orange-500 to-rose-500" style="width: {{ $scoreBar }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 overflow-hidden rounded-full border border-slate-200/70 bg-white/60 shadow-sm">
                                                @if($user->profile_pic_url)
                                                    <img src="{{ $user->profile_pic_url }}" alt="Profile" class="h-full w-full object-cover">
                                                @else
                                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-orange-500 to-amber-600 text-white text-[10px] font-black">
                                                        {{ $user->initials }}
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
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-semibold text-orange-700 tabular-nums">{{ number_format($ipContribution, 1) }}%</span>
                                            <div class="h-1.5 w-16 overflow-hidden rounded-full bg-orange-100/70">
                                                <div class="h-full rounded-full bg-gradient-to-r from-orange-400 to-amber-500" style="width: {{ $ipBar }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-semibold text-sky-700 tabular-nums">{{ number_format($academicContribution, 1) }}%</span>
                                            <div class="h-1.5 w-16 overflow-hidden rounded-full bg-sky-100/70">
                                                <div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-indigo-500" style="width: {{ $academicBar }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-semibold tabular-nums @if($hasApprovedIncomeTax) text-emerald-700 @else text-slate-500 @endif">
                                                {{ number_format($itrContribution, 1) }}%
                                            </span>
                                            <div class="h-1.5 w-16 overflow-hidden rounded-full bg-emerald-100/60">
                                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-teal-500" style="width: {{ $itrBar }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-semibold text-fuchsia-700 tabular-nums">{{ number_format($awardsContribution, 1) }}%</span>
                                            <div class="h-1.5 w-16 overflow-hidden rounded-full bg-fuchsia-100/60">
                                                <div class="h-full rounded-full bg-gradient-to-r from-fuchsia-400 to-violet-500" style="width: {{ $awardsBar }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-semibold text-slate-700 tabular-nums">{{ number_format($socialContribution, 1) }}%</span>
                                            <div class="h-1.5 w-16 overflow-hidden rounded-full bg-slate-200/60">
                                                <div class="h-full rounded-full bg-gradient-to-r from-slate-400 to-slate-600" style="width: {{ $socialBar }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                            <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-white/60 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 shadow-sm transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10">Review →</a>
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
                            $hasApprovedIncomeTax = $applicantData['has_approved_income_tax'] ?? false;
                            $ipRubricScore = $applicantData['ip_rubric_score'] ?? 0;
                            $academicRubricScore = $applicantData['academic_rubric_score'] ?? 0;
                            $awardsRubricScore = $applicantData['awards_rubric_score'] ?? 0;
                            $socialRubricScore = $applicantData['social_responsibility_rubric_score'] ?? 0;
                            $priorityScore = $applicantData['priority_score'] ?? 0;

                            $ipContribution = max(0, min(20, ((float) $ipRubricScore / 12) * 20));
                            $academicContribution = max(0, min(30, ((float) $academicRubricScore / 10) * 30));
                            $itrContribution = $hasApprovedIncomeTax ? 30.0 : 0.0;
                            $awardsContribution = max(0, min(10, ((float) $awardsRubricScore / 10) * 10));
                            $socialContribution = max(0, min(10, ((float) $socialRubricScore / 10) * 10));
                        @endphp
                        <div class="rounded-3xl border border-slate-200/70 bg-white/80 p-5 shadow-lg shadow-slate-900/5 backdrop-blur-xl">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Priority Rank</p>
                                    @if($priorityRank && $priorityRank != 9999)
                                        <p class="text-3xl font-extrabold text-slate-900">#{{ $priorityRank }}</p>
                                    @else
                                        <p class="text-xl font-semibold text-slate-400">N/A</p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center rounded-full bg-gradient-to-r from-orange-600 to-rose-600 px-3 py-1 text-xs font-semibold text-white shadow-sm tabular-nums">
                                    {{ number_format((float) $priorityScore, 1) }}%
                                </span>
                            </div>

                            <div class="mt-4 flex items-center gap-3">
                                <div class="h-12 w-12 overflow-hidden rounded-full border border-slate-200 bg-slate-50">
                                    @if($user->profile_pic_url)
                                        <img src="{{ $user->profile_pic_url }}" alt="Profile" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-orange-500 to-amber-600 text-white text-sm font-black">
                                            {{ $user->initials }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                    <p class="text-[11px] text-slate-500">ID: {{ $user->id }}</p>
                                </div>
                            </div>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-slate-200/70 bg-white/60 p-3 text-xs text-slate-600 backdrop-blur">
                                    <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Submission</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ $submittedAt->format('M d, Y') }}</p>
                                    <p class="text-[10px]">{{ $submittedAt->format('g:i A') }}</p>
                                    <p class="text-[10px] text-orange-600">{{ $daysWaiting }} {{ Str::plural('day', $daysWaiting) }} waiting</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200/70 bg-white/60 p-3 text-xs backdrop-blur">
                                    <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">IP Group</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ $ethnicity }}</p>
                                    @if($isPriorityEthno)
                                        <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-orange-50 px-2 py-0.5 text-[10px] font-semibold text-orange-700 border border-orange-200">Priority IP</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 space-y-3">
                                <div class="grid gap-2 text-[11px] text-slate-600 sm:grid-cols-2">
                                    <div class="flex items-center justify-between rounded-2xl border border-slate-200/70 bg-white/60 px-3 py-2 backdrop-blur">
                                        <span class="font-semibold text-slate-700">IP (20%)</span>
                                        <span class="font-bold text-orange-700 tabular-nums">{{ number_format($ipContribution, 1) }}%</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-2xl border border-slate-200/70 bg-white/60 px-3 py-2 backdrop-blur">
                                        <span class="font-semibold text-slate-700">Academic (30%)</span>
                                        <span class="font-bold text-sky-700 tabular-nums">{{ number_format($academicContribution, 1) }}%</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-2xl border border-slate-200/70 bg-white/60 px-3 py-2 backdrop-blur">
                                        <span class="font-semibold text-slate-700">ITR (30%)</span>
                                        <span class="font-bold tabular-nums @if($hasApprovedIncomeTax) text-emerald-700 @else text-slate-500 @endif">{{ number_format($itrContribution, 1) }}%</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-2xl border border-slate-200/70 bg-white/60 px-3 py-2 backdrop-blur">
                                        <span class="font-semibold text-slate-700">Awards (10%)</span>
                                        <span class="font-bold text-fuchsia-700 tabular-nums">{{ number_format($awardsContribution, 1) }}%</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-2xl border border-slate-200/70 bg-white/60 px-3 py-2 backdrop-blur sm:col-span-2">
                                        <span class="font-semibold text-slate-700">Social (10%)</span>
                                        <span class="font-bold text-slate-700 tabular-nums">{{ number_format($socialContribution, 1) }}%</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end">
                                    <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-white/60 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 shadow-sm transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10">Review →</a>
                                </div>
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
