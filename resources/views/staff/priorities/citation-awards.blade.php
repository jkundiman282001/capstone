@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-36 -left-24 h-96 w-96 rounded-full bg-fuchsia-500/20 blur-3xl"></div>
        <div class="absolute top-80 right-[-160px] h-[420px] w-[420px] rounded-full bg-pink-500/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 text-slate-900 sm:px-6 lg:px-8 lg:py-14">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-fuchsia-400/40 bg-fuchsia-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-fuchsia-700">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-fuchsia-500"></span>
                    Citation/Awards Priority
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Citation/Awards Priority Ranking</h1>
                <p class="max-w-2xl text-sm text-slate-600">
                    All applicants ranked by their Citation/Awards rubric score (0-10 scale). Higher scores indicate better academic honors and achievements. The best award/rank from the applicant's education records is used for scoring.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-700">
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px]">
                    <svg class="mr-1 h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Updated {{ now()->format('M d, Y g:i A') }}
                </span>
                <a href="{{ route('staff.priorities.applicants') }}" class="inline-flex items-center gap-2 rounded-full border border-fuchsia-300 bg-white hover:bg-fuchsia-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">View All Priorities</a>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-fuchsia-300 bg-white hover:bg-fuchsia-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">Back to Dashboard</a>
            </div>
        </div>

        @if(isset($applicantsWithAwardsScores) && count($applicantsWithAwardsScores) > 0)
            <div class="mt-10 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-gradient-to-r from-fuchsia-500 to-pink-600 text-xs uppercase tracking-[0.25em] text-white">
                            <tr>
                                <th class="px-5 py-4 text-left">Rank</th>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-center">Best Award</th>
                                <th class="px-5 py-4 text-center">Awards Score</th>
                                <th class="px-5 py-4 text-center">Achievement Level</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($applicantsWithAwardsScores as $index => $applicantData)
                                @php
                                    $user = $applicantData['user'];
                                    $awardsScore = $applicantData['awards_rubric_score'];
                                    $bestAward = $applicantData['best_award'];
                                    $allAwards = $applicantData['awards'];
                                    $rank = $index + 1;
                                @endphp
                                <tr class="transition hover:bg-fuchsia-50">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                                @if($rank <= 3) bg-gradient-to-r from-fuchsia-500 to-pink-600 text-white
                                                @elseif($rank <= 10) bg-fuchsia-100 text-fuchsia-700
                                                @else bg-slate-100 text-slate-600
                                                @endif">
                                                {{ $rank }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="text-[11px] text-slate-500">ID: {{ $user->id }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($bestAward)
                                            <span class="inline-flex rounded-full border border-fuchsia-200 bg-fuchsia-50 px-3 py-1 text-xs font-semibold text-fuchsia-700">
                                                {{ $bestAward }}
                                            </span>
                                            @if(count($allAwards) > 1)
                                                <div class="text-[10px] text-slate-500 mt-1">+{{ count($allAwards) - 1 }} more</div>
                                            @endif
                                        @else
                                            <span class="text-sm text-slate-400">No Awards</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-lg font-bold
                                                @if($awardsScore >= 8) text-fuchsia-600
                                                @elseif($awardsScore >= 6) text-pink-600
                                                @elseif($awardsScore >= 4) text-rose-600
                                                @elseif($awardsScore >= 2) text-orange-600
                                                @else text-slate-400
                                                @endif">
                                                {{ number_format($awardsScore, 1) }}
                                            </span>
                                            <span class="text-[10px] text-slate-500">/ 10</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($awardsScore >= 8)
                                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-green-700">Excellent</span>
                                        @elseif($awardsScore >= 6)
                                            <span class="inline-flex rounded-full bg-fuchsia-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-fuchsia-700">Very Good</span>
                                        @elseif($awardsScore >= 4)
                                            <span class="inline-flex rounded-full bg-pink-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-pink-700">Good</span>
                                        @elseif($awardsScore >= 2)
                                            <span class="inline-flex rounded-full bg-orange-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-orange-700">Fair</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-700">No Awards</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-fuchsia-300 bg-fuchsia-600 hover:bg-fuchsia-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white transition shadow-md">Review →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-500 shadow-sm">
                <svg class="mx-auto mb-4 h-14 w-14 text-fuchsia-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                No applicants found. Check back as new applications arrive.
            </div>
        @endif
    </div>
</div>
@endsection

