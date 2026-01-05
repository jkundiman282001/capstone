@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-36 -left-24 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute top-80 right-[-160px] h-[420px] w-[420px] rounded-full bg-indigo-500/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 text-slate-900 sm:px-6 lg:px-8 lg:py-14">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-400/40 bg-blue-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-blue-700">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-blue-500"></span>
                    GWA Priority
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">GWA (Grade Weighted Average) Priority Ranking</h1>
                <p class="max-w-2xl text-sm text-slate-600">
                    All applicants ranked by their GWA/Academic rubric score (0-10 scale). Higher scores indicate better academic performance. GWA is measured on a 75-100 scale (where 100 is best).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-700">
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px]">
                    <svg class="mr-1 h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Updated {{ now()->format('M d, Y g:i A') }}
                </span>
                <a href="{{ route('staff.priorities.applicants') }}" class="inline-flex items-center gap-2 rounded-full border border-blue-300 bg-white hover:bg-blue-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">View All Priorities</a>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-blue-300 bg-white hover:bg-blue-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">Back to Dashboard</a>
            </div>
        </div>

        @if(isset($applicantsWithGwaScores) && count($applicantsWithGwaScores) > 0)
            <div class="mt-10 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-gradient-to-r from-blue-500 to-indigo-600 text-xs uppercase tracking-[0.25em] text-white">
                            <tr>
                                <th class="px-5 py-4 text-left">Rank</th>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-center">GWA</th>
                                <th class="px-5 py-4 text-center">GWA Score</th>
                                <th class="px-5 py-4 text-center">Performance Level</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($applicantsWithGwaScores as $index => $applicantData)
                                @php
                                    $user = $applicantData['user'];
                                    $gwaScore = $applicantData['academic_rubric_score'];
                                    $gpa = $applicantData['gpa'];
                                    $rank = $index + 1;
                                @endphp
                                <tr class="transition hover:bg-blue-50">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                                @if($rank <= 3) bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                                @elseif($rank <= 10) bg-blue-100 text-blue-700
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
                                        @if($gpa !== null)
                                            <span class="text-lg font-bold
                                                @if($gpa >= 95) text-green-600
                                                @elseif($gpa >= 90) text-blue-600
                                                @elseif($gpa >= 85) text-amber-600
                                                @elseif($gpa >= 80) text-orange-600
                                                @else text-slate-600
                                                @endif">
                                                {{ number_format($gpa, 2) }}
                                            </span>
                                        @else
                                            <span class="text-sm text-slate-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-lg font-bold
                                                @if($gwaScore >= 8) text-blue-600
                                                @elseif($gwaScore >= 6) text-indigo-600
                                                @elseif($gwaScore >= 4) text-amber-600
                                                @elseif($gwaScore >= 2) text-orange-600
                                                @else text-slate-400
                                                @endif">
                                                {{ number_format($gwaScore, 1) }}
                                            </span>
                                            <span class="text-[10px] text-slate-500">/ 10</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($gwaScore >= 8)
                                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-green-700">Excellent</span>
                                        @elseif($gwaScore >= 6)
                                            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-blue-700">Very Good</span>
                                        @elseif($gwaScore >= 4)
                                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700">Good</span>
                                        @elseif($gwaScore >= 2)
                                            <span class="inline-flex rounded-full bg-orange-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-orange-700">Fair</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-700">No Data</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-blue-300 bg-blue-600 hover:bg-blue-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white transition shadow-md">Review →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-500 shadow-sm">
                <svg class="mx-auto mb-4 h-14 w-14 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                No applicants found. Check back as new applications arrive.
            </div>
        @endif
    </div>
</div>
@endsection
