@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-36 -left-24 h-96 w-96 rounded-full bg-orange-500/20 blur-3xl"></div>
        <div class="absolute top-80 right-[-160px] h-[420px] w-[420px] rounded-full bg-amber-500/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 text-slate-900 sm:px-6 lg:px-8 lg:py-14">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-orange-400/40 bg-orange-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-orange-700">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-orange-500"></span>
                    IP Group Priority
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">IP Group Priority Ranking</h1>
                <p class="max-w-2xl text-sm text-slate-600">
                    All applicants ranked by their IP Group rubric score (0-12 scale). Higher scores indicate stronger IP group documentation and verification. Priority IP groups (B'laan, Bagobo, Kalagan, Kaulo) receive a +2 point bonus.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-700">
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px]">
                    <svg class="mr-1 h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Updated {{ now()->format('M d, Y g:i A') }}
                </span>
                <a href="{{ route('staff.priorities.applicants') }}" class="inline-flex items-center gap-2 rounded-full border border-orange-300 bg-white hover:bg-orange-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">View All Priorities</a>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-orange-300 bg-white hover:bg-orange-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">Back to Dashboard</a>
            </div>
        </div>

        @if(isset($applicantsWithIpScores) && count($applicantsWithIpScores) > 0)
            <div class="mt-10 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-gradient-to-r from-orange-500 to-amber-600 text-xs uppercase tracking-[0.25em] text-white">
                            <tr>
                                <th class="px-5 py-4 text-left">Rank</th>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-left">IP Group</th>
                                <th class="px-5 py-4 text-center">IP Group Score</th>
                                <th class="px-5 py-4 text-center">Priority Status</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($applicantsWithIpScores as $index => $applicantData)
                                @php
                                    $user = $applicantData['user'];
                                    $ipScore = $applicantData['ip_rubric_score'];
                                    $ethnicity = $applicantData['ethnicity'] ?? 'Not Specified';
                                    $isPriorityEthno = $applicantData['is_priority_ethno'] ?? false;
                                    $rank = $index + 1;
                                @endphp
                                <tr class="transition hover:bg-orange-50">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                                @if($rank <= 3) bg-gradient-to-r from-orange-500 to-amber-600 text-white
                                                @elseif($rank <= 10) bg-orange-100 text-orange-700
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
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold
                                            @if($isPriorityEthno) border-orange-300 bg-orange-50 text-orange-700
                                            @else border-slate-200 bg-slate-50 text-slate-700
                                            @endif">
                                            {{ $ethnicity }}
                                            @if($isPriorityEthno)
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-orange-200 text-orange-800">Priority</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-lg font-bold
                                                @if($ipScore >= 10) text-orange-600
                                                @elseif($ipScore >= 8) text-amber-600
                                                @elseif($ipScore >= 6) text-yellow-600
                                                @elseif($ipScore >= 4) text-slate-600
                                                @else text-slate-400
                                                @endif">
                                                {{ number_format($ipScore, 1) }}
                                            </span>
                                            <span class="text-[10px] text-slate-500">/ 12</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($ipScore >= 10)
                                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-green-700">Excellent</span>
                                        @elseif($ipScore >= 8)
                                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700">Good</span>
                                        @elseif($ipScore >= 6)
                                            <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-yellow-700">Fair</span>
                                        @elseif($ipScore >= 4)
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-700">Basic</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-red-700">None</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-orange-300 bg-orange-600 hover:bg-orange-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white transition shadow-md">Review →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-500 shadow-sm">
                <svg class="mx-auto mb-4 h-14 w-14 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                No applicants found. Check back as new applications arrive.
            </div>
        @endif
    </div>
</div>
@endsection
