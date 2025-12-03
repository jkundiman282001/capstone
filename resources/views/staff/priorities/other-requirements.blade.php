@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-slate-950">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-20 h-96 w-96 rounded-full bg-gray-500/20 blur-3xl"></div>
        <div class="absolute top-72 -left-32 h-[420px] w-[420px] rounded-full bg-slate-500/10 blur-3xl"></div>
        <div class="absolute bottom-[-200px] right-16 h-[480px] w-[480px] rounded-full bg-zinc-500/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 text-slate-100 sm:px-6 lg:px-8 lg:py-14">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-gray-400/40 bg-gray-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-gray-200">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-gray-300"></span>
                    Final Priority
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Other Requirements</h1>
                <p class="max-w-2xl text-sm text-slate-300">
                    Students who have successfully uploaded and been approved for all remaining required documents: <strong class="text-gray-200">Birth Certificate</strong>, <strong class="text-gray-200">Endorsement Letter</strong>, and <strong class="text-gray-200">Good Moral Certificate</strong>. This is the final priority category for document completion.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-100/80">
                <span class="inline-flex items-center rounded-full border border-slate-200/20 bg-white/10 px-3 py-1 text-[11px]">
                    <svg class="mr-1 h-3.5 w-3.5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Updated {{ now()->format('M d, Y g:i A') }}
                </span>
                <a href="{{ route('staff.priorities.documents') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition hover:bg-white/20">View Document Queue</a>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition hover:bg-white/20">Back to Dashboard</a>
            </div>
        </div>

        <div class="mt-10 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-gray-300/40">
                <div class="absolute -right-8 -top-8 h-20 w-20 rounded-full bg-gray-400/20 blur-3xl transition group-hover:scale-110"></div>
                <p class="text-xs uppercase tracking-[0.25em] text-gray-200">Total Completed</p>
                <div class="mt-2 text-3xl font-bold text-white">{{ $totalApproved ?? 0 }}</div>
                <p class="text-xs text-gray-100/70">Students with all documents</p>
            </div>
            <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-slate-300/40">
                <div class="absolute -right-8 -top-10 h-24 w-24 rotate-12 rounded-3xl bg-slate-400/20 blur-3xl transition group-hover:scale-110"></div>
                <p class="text-xs uppercase tracking-[0.25em] text-slate-200">Recently Completed</p>
                <div class="mt-2 text-3xl font-bold text-white">{{ $recentlyApproved ?? 0 }}</div>
                <p class="text-xs text-slate-100/70">Completed in last 7 days</p>
            </div>
            <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-zinc-300/40">
                <div class="absolute -right-8 -bottom-10 h-24 w-24 rounded-full bg-zinc-400/20 blur-3xl transition group-hover:scale-110"></div>
                <p class="text-xs uppercase tracking-[0.25em] text-zinc-200">Required Documents</p>
                <div class="mt-2 text-3xl font-bold text-white">3</div>
                <p class="text-xs text-zinc-100/70">Birth Cert, Endorsement, Good Moral</p>
            </div>
            <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-slate-300/40">
                <div class="absolute -left-8 top-6 h-24 w-24 rounded-full bg-slate-400/20 blur-3xl transition group-hover:scale-110"></div>
                <p class="text-xs uppercase tracking-[0.25em] text-slate-200">Total Students</p>
                <div class="mt-2 text-3xl font-bold text-white">{{ $prioritizedUsers->count() }}</div>
                <p class="text-xs text-slate-100/70">In this priority list</p>
            </div>
        </div>

        @if($prioritizedUsers && $prioritizedUsers->count() > 0)
            <div class="mt-10 overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-xl backdrop-blur">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-sm text-slate-100">
                        <thead class="bg-gray-500/20 text-xs uppercase tracking-[0.25em] text-gray-100">
                            <tr>
                                <th class="px-5 py-4 text-left">#</th>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-left">Indigenous Group</th>
                                <th class="px-5 py-4 text-left">Birth Certificate</th>
                                <th class="px-5 py-4 text-left">Endorsement</th>
                                <th class="px-5 py-4 text-left">Good Moral</th>
                                <th class="px-5 py-4 text-left">Last Document Approved</th>
                                <th class="px-5 py-4 text-left">Days Since Completion</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 bg-white/5">
                            @foreach($prioritizedUsers as $index => $user)
                                @php
                                    $birthCert = $user->documents->where('type', 'birth_certificate')->where('status', 'approved')->first();
                                    $endorsement = $user->documents->where('type', 'endorsement')->where('status', 'approved')->first();
                                    $goodMoral = $user->documents->where('type', 'good_moral')->where('status', 'approved')->first();
                                    
                                    // Get the most recent approval date
                                    $allApprovedDocs = collect([$birthCert, $endorsement, $goodMoral])->filter();
                                    $lastApprovedAt = $allApprovedDocs->max('updated_at');
                                    $daysSinceCompletion = $lastApprovedAt ? now()->diffInDays($lastApprovedAt) : null;
                                    
                                    $ethnoLabel = $user->ethno->ethnicity ?? 'Not specified';
                                @endphp
                                <tr class="transition hover:bg-gray-400/10">
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold text-white
                                            @if($index < 5) bg-gray-500/80
                                            @elseif($index < 10) bg-slate-500/70 text-slate-900
                                            @else bg-zinc-500/70
                                            @endif">#{{ $index + 1 }}</span>
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
                                                @if($user->basicInfo && $user->basicInfo->type_assist)
                                                    <p class="text-[10px] text-gray-300/70 mt-1">{{ $user->basicInfo->type_assist }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-white">{{ $ethnoLabel }}</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($birthCert)
                                            <span class="inline-flex items-center gap-2 rounded-full border border-gray-400/40 bg-gray-400/30 px-3 py-1 text-xs font-semibold text-white">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-slate-300">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Missing
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($endorsement)
                                            <span class="inline-flex items-center gap-2 rounded-full border border-gray-400/40 bg-gray-400/30 px-3 py-1 text-xs font-semibold text-white">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-slate-300">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Missing
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($goodMoral)
                                            <span class="inline-flex items-center gap-2 rounded-full border border-gray-400/40 bg-gray-400/30 px-3 py-1 text-xs font-semibold text-white">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-slate-300">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Missing
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-200/80">
                                        @if($lastApprovedAt)
                                            <div>{{ $lastApprovedAt->format('M d, Y') }}</div>
                                            <div class="text-[10px]">{{ $lastApprovedAt->format('g:i A') }}</div>
                                        @else
                                            <span class="text-slate-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-200/80">
                                        @if($daysSinceCompletion !== null)
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-white">{{ $daysSinceCompletion }} {{ Str::plural('day', $daysSinceCompletion) }}</span>
                                                @if($daysSinceCompletion <= 7)
                                                    <span class="inline-flex rounded-full bg-slate-500/80 px-2 py-0.5 text-[10px] uppercase tracking-wide text-white">Recent</span>
                                                @elseif($daysSinceCompletion <= 30)
                                                    <span class="inline-flex rounded-full bg-amber-500/80 px-2 py-0.5 text-[10px] uppercase tracking-wide text-white">This Month</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <a href="{{ route('staff.applications.view', $user->id) }}" class="inline-flex items-center gap-2 rounded-full border border-gray-300/40 bg-gray-300/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-100 transition hover:bg-gray-300/25">View →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-white/10 bg-white/5 p-12 text-center text-sm text-slate-200/70">
                <svg class="mx-auto mb-4 h-14 w-14 text-gray-200/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                No students with all other required documents approved found. Students will appear here once they upload and have all three documents (Birth Certificate, Endorsement, and Good Moral Certificate) approved by staff.
            </div>
        @endif
    </div>
</div>
@endsection

