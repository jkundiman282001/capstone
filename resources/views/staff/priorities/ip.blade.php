@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-slate-950">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-36 -left-24 h-96 w-96 rounded-full bg-amber-500/20 blur-3xl"></div>
        <div class="absolute top-80 right-[-160px] h-[420px] w-[420px] rounded-full bg-yellow-500/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 text-slate-100 sm:px-6 lg:px-8 lg:py-14">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/40 bg-amber-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-amber-200">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-amber-300"></span>
                    Priority IP Queue
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Top Priority Indigenous Applicants</h1>
                <p class="max-w-2xl text-sm text-slate-300">
                    Focused queue for B'laan, Bagobo, Kalagan, and Kaulo applicants who have submitted documents. Use this list to accelerate reviews for key indigenous partners.
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

        @php
            $priorityGroups = ["B'laan", 'Bagobo', 'Kalagan', 'Kaulo'];
        @endphp

        @if($priorityIpDocs && $priorityIpDocs->count() > 0)
            <div class="mt-10 overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-xl backdrop-blur">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-sm text-slate-100">
                        <thead class="bg-amber-500/20 text-xs uppercase tracking-[0.25em] text-amber-100">
                            <tr>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-left">IP Group</th>
                                <th class="px-5 py-4 text-left">Document</th>
                                <th class="px-5 py-4 text-left">Submitted</th>
                                <th class="px-5 py-4 text-left">Waiting Time</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 bg-white/5">
                            @foreach($priorityIpDocs as $document)
                                @php
                                    $ethLabel = optional(optional($document->user)->ethno)->ethnicity;
                                    $submittedAt = $document->submitted_at ?? $document->created_at;
                                    $waitingHours = $document->waiting_hours ?? 0;
                                    $docTypes = [
                                        'birth_certificate' => 'Birth Certificate',
                                        'income_document' => 'Income Document',
                                        'tribal_certificate' => 'Tribal Certificate',
                                        'endorsement' => 'Endorsement',
                                        'good_moral' => 'Good Moral',
                                        'grades' => 'Grades'
                                    ];
                                    $docTypeLabel = $docTypes[$document->type] ?? ucwords(str_replace('_', ' ', $document->type));
                                @endphp
                                <tr class="transition hover:bg-amber-400/10">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-white">{{ $document->user->first_name }} {{ $document->user->last_name }}</div>
                                        <div class="text-[11px] text-slate-200/70">ID: {{ $document->user_id }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-white">{{ $ethLabel }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-200/80">{{ $docTypeLabel }}</td>
                                    <td class="px-5 py-4 text-xs text-slate-200/80">
                                        <div>{{ $submittedAt->format('M d, Y') }}</div>
                                        <div class="text-[10px]">{{ $submittedAt->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-200/80">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-white">{{ $waitingHours }} {{ Str::plural('hour', $waitingHours) }}</span>
                                            @if($waitingHours >= 72)
                                                <span class="inline-flex rounded-full bg-rose-500/80 px-2 py-0.5 text-[10px] uppercase tracking-wide text-white">Urgent</span>
                                            @elseif($waitingHours >= 48)
                                                <span class="inline-flex rounded-full bg-orange-500/80 px-2 py-0.5 text-[10px] uppercase tracking-wide text-white">Priority</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <a href="{{ route('staff.applications.view', $document->user_id) }}" class="inline-flex items-center gap-2 rounded-full border border-amber-300/40 bg-amber-300/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-100 transition hover:bg-amber-300/25">Review →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-white/10 bg-white/5 p-12 text-center text-sm text-slate-200/70">
                <svg class="mx-auto mb-4 h-14 w-14 text-amber-200/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 0v10l9 4 9-4V7" /></svg>
                No priority IP submissions in the queue. Check back as new documents arrive.
            </div>
        @endif
    </div>
</div>
@endsection
