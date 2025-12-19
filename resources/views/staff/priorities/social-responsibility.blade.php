@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-36 -left-24 h-96 w-96 rounded-full bg-slate-500/20 blur-3xl"></div>
        <div class="absolute top-80 right-[-160px] h-[420px] w-[420px] rounded-full bg-gray-500/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 text-slate-900 sm:px-6 lg:px-8 lg:py-14">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-slate-400/40 bg-slate-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-slate-700">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-slate-500"></span>
                    Social Responsibility Priority
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Social Responsibility Priority Ranking</h1>
                <p class="max-w-2xl text-sm text-slate-600">
                    All applicants ranked by their Social Responsibility rubric score (0-10 scale). Higher scores indicate stronger commitment to community service and IP community contribution. Scores are based on essay quality, length, and keyword coverage related to community service, IP culture, and development plans.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-700">
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px]">
                    <svg class="mr-1 h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Updated {{ now()->format('M d, Y g:i A') }}
                </span>
                <a href="{{ route('staff.priorities.applicants') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white hover:bg-slate-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">View All Priorities</a>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white hover:bg-slate-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">Back to Dashboard</a>
            </div>
        </div>

        @if(isset($applicantsWithSocialScores) && count($applicantsWithSocialScores) > 0)
            <div class="mt-10 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-gradient-to-r from-slate-500 to-gray-600 text-xs uppercase tracking-[0.25em] text-white">
                            <tr>
                                <th class="px-5 py-4 text-left">Rank</th>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-center">Essay Length</th>
                                <th class="px-5 py-4 text-center">Social Responsibility Score</th>
                                <th class="px-5 py-4 text-center">Commitment Level</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($applicantsWithSocialScores as $index => $applicantData)
                                @php
                                    $user = $applicantData['user'];
                                    $socialScore = $applicantData['social_responsibility_rubric_score'];
                                    $textLength = $applicantData['text_length'];
                                    $rank = $index + 1;
                                @endphp
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                                @if($rank <= 3) bg-gradient-to-r from-slate-500 to-gray-600 text-white
                                                @elseif($rank <= 10) bg-slate-100 text-slate-700
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
                                        <span class="text-sm font-semibold
                                            @if($textLength >= 800) text-green-600
                                            @elseif($textLength >= 500) text-blue-600
                                            @elseif($textLength >= 250) text-amber-600
                                            @elseif($textLength >= 120) text-orange-600
                                            @else text-slate-400
                                            @endif">
                                            {{ number_format($textLength) }} chars
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-lg font-bold
                                                @if($socialScore >= 8) text-slate-600
                                                @elseif($socialScore >= 6) text-gray-600
                                                @elseif($socialScore >= 4) text-slate-500
                                                @elseif($socialScore >= 2) text-slate-400
                                                @else text-slate-300
                                                @endif">
                                                {{ number_format($socialScore, 1) }}
                                            </span>
                                            <span class="text-[10px] text-slate-500">/ 10</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($socialScore >= 8)
                                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-green-700">Excellent</span>
                                        @elseif($socialScore >= 6)
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-700">Very Good</span>
                                        @elseif($socialScore >= 4)
                                            <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-gray-700">Good</span>
                                        @elseif($socialScore >= 2)
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-600">Fair</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-500">No Essays</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-slate-600 hover:bg-slate-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white transition shadow-md">Review →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-500 shadow-sm">
                <svg class="mx-auto mb-4 h-14 w-14 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                No applicants found. Check back as new applications arrive.
            </div>
        @endif
    </div>
</div>
@endsection

