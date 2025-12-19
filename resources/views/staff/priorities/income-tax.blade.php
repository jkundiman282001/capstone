@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-36 -left-24 h-96 w-96 rounded-full bg-green-500/20 blur-3xl"></div>
        <div class="absolute top-80 right-[-160px] h-[420px] w-[420px] rounded-full bg-emerald-500/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 text-slate-900 sm:px-6 lg:px-8 lg:py-14">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-green-400/40 bg-green-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-green-700">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-green-500"></span>
                    ITR Priority
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">ITR (Income Tax Return) Priority Ranking</h1>
                <p class="max-w-2xl text-sm text-slate-600">
                    All applicants ranked by their ITR document status. Applicants with approved Income Tax Return/Tax Exemption/Indigency documents are ranked first, followed by those without approved ITR documents. This is a binary priority (approved = 1.0, not approved = 0.0).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-700">
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px]">
                    <svg class="mr-1 h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Updated {{ now()->format('M d, Y g:i A') }}
                </span>
                <a href="{{ route('staff.priorities.applicants') }}" class="inline-flex items-center gap-2 rounded-full border border-green-300 bg-white hover:bg-green-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">View All Priorities</a>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-green-300 bg-white hover:bg-green-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">Back to Dashboard</a>
            </div>
        </div>

        @if(isset($applicantsWithItrStatus) && count($applicantsWithItrStatus) > 0)
            <div class="mt-10 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-gradient-to-r from-green-500 to-emerald-600 text-xs uppercase tracking-[0.25em] text-white">
                            <tr>
                                <th class="px-5 py-4 text-left">Rank</th>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-center">ITR Status</th>
                                <th class="px-5 py-4 text-center">ITR Score</th>
                                <th class="px-5 py-4 text-center">Approved Date</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($applicantsWithItrStatus as $index => $applicantData)
                                @php
                                    $user = $applicantData['user'];
                                    $hasApprovedItr = $applicantData['has_approved_income_tax'];
                                    $itrStatus = $applicantData['itr_status'];
                                    $itrApprovedAt = $applicantData['itr_approved_at'];
                                    $itrScore = $applicantData['itr_score'];
                                    $rank = $index + 1;
                                @endphp
                                <tr class="transition hover:bg-green-50">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                                @if($rank <= 3 && $hasApprovedItr) bg-gradient-to-r from-green-500 to-emerald-600 text-white
                                                @elseif($hasApprovedItr) bg-green-100 text-green-700
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
                                        @if($hasApprovedItr)
                                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-green-700 border border-green-200">Approved</span>
                                        @elseif($itrStatus === 'pending')
                                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700 border border-amber-200">Pending</span>
                                        @elseif($itrStatus === 'rejected')
                                            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-red-700 border border-red-200">Rejected</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-700 border border-slate-200">Not Submitted</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-lg font-bold
                                                @if($itrScore == 1.0) text-green-600
                                                @else text-slate-400
                                                @endif">
                                                {{ number_format($itrScore, 1) }}
                                            </span>
                                            <span class="text-[10px] text-slate-500">/ 1.0</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center text-xs text-slate-600">
                                        @if($itrApprovedAt)
                                            <div>{{ $itrApprovedAt->format('M d, Y') }}</div>
                                            <div class="text-[10px]">{{ $itrApprovedAt->format('g:i A') }}</div>
                                        @else
                                            <span class="text-slate-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-green-300 bg-green-600 hover:bg-green-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white transition shadow-md">Review →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-500 shadow-sm">
                <svg class="mx-auto mb-4 h-14 w-14 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                No applicants found. Check back as new applications arrive.
            </div>
        @endif
    </div>
</div>
@endsection
