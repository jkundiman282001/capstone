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
                    Priority IP Queue
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Top Priority Indigenous Applicants</h1>
                <p class="max-w-2xl text-sm text-slate-600">
                    Focused queue for B'laan, Bagobo, Kalagan, and Kaulo applicants who have submitted documents. Use this list to accelerate reviews for key indigenous partners.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-700">
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px]">
                    <svg class="mr-1 h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Updated {{ now()->format('M d, Y g:i A') }}
                </span>
                <a href="{{ route('staff.priorities.documents') }}" class="inline-flex items-center gap-2 rounded-full border border-orange-300 bg-white hover:bg-orange-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">View Document Queue</a>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-orange-300 bg-white hover:bg-orange-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition">Back to Dashboard</a>
            </div>
        </div>

        @php
            $priorityGroups = ["B'laan", 'Bagobo', 'Kalagan', 'Kaulo'];
        @endphp

        @if($priorityIpDocs && $priorityIpDocs->count() > 0)
            <div class="mt-10 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-gradient-to-r from-orange-500 to-amber-600 text-xs uppercase tracking-[0.25em] text-white">
                            <tr>
                                <th class="px-5 py-4 text-left">Applicant</th>
                                <th class="px-5 py-4 text-left">IP Group</th>
                                <th class="px-5 py-4 text-left">Document</th>
                                <th class="px-5 py-4 text-left">Submitted</th>
                                <th class="px-5 py-4 text-left">Waiting Time</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
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
                                <tr class="transition hover:bg-orange-50">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-900">{{ $document->user->first_name }} {{ $document->user->last_name }}</div>
                                        <div class="text-[11px] text-slate-500">ID: {{ $document->user_id }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-xs text-orange-700 font-semibold">{{ $ethLabel }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-600">{{ $docTypeLabel }}</td>
                                    <td class="px-5 py-4 text-xs text-slate-600">
                                        <div>{{ $submittedAt->format('M d, Y') }}</div>
                                        <div class="text-[10px]">{{ $submittedAt->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-600">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-slate-900">{{ $waitingHours }} {{ Str::plural('hour', $waitingHours) }}</span>
                                            @if($waitingHours >= 72)
                                                <span class="inline-flex rounded-full bg-rose-500 px-2 py-0.5 text-[10px] uppercase tracking-wide text-white">Urgent</span>
                                            @elseif($waitingHours >= 48)
                                                <span class="inline-flex rounded-full bg-orange-500 px-2 py-0.5 text-[10px] uppercase tracking-wide text-white">Priority</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <a href="{{ route('staff.applications.view', $document->user_id) }}" class="inline-flex items-center gap-2 rounded-full border border-orange-300 bg-orange-600 hover:bg-orange-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white transition shadow-md">Review →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-500 shadow-sm">
                <svg class="mx-auto mb-4 h-14 w-14 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 0v10l9 4 9-4V7" /></svg>
                No priority IP submissions in the queue. Check back as new documents arrive.
            </div>
        @endif
    </div>
</div>
@endsection
