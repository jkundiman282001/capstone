@extends('layouts.student')

@section('title', 'Support & Help - IP Scholar Portal')

@push('head-scripts')
@endpush

@push('styles')
<style>
    details summary { list-style: none; cursor: pointer; user-select: none; }
    details summary::-webkit-details-marker { display: none; }
    details summary::marker { display: none; }
    details[open] summary .chevron { transform: rotate(180deg); }
    .priority-content { display: none; }
    .priority-content.active { display: block; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 pt-24 pb-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-orange-700">
                        <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                        Support Center
                    </div>
                    <h1 class="mt-4 text-3xl font-bold text-slate-900 sm:text-4xl">Support & Help</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-600">
                        Find answers, understand how the priority scoring works, and contact support if you’re stuck.
                    </p>
                </div>
                <a href="{{ route('student.dashboard') }}"
                   class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                    Back to Dashboard
                </a>
            </div>

            <!-- Quick links -->
            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                <a href="#faq" class="group rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm transition hover:border-slate-300 hover:shadow">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 0 0 10 10Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 2-2.5 2-2.5 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-900">FAQs</p>
                            <p class="text-xs text-slate-500">Common questions</p>
                        </div>
                        <svg class="ml-auto h-4 w-4 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-slate-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="m13 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </a>

                <a href="#rubrics" class="group rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm transition hover:border-slate-300 hover:shadow">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-orange-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 2 8.5 8l-6 .9 4.3 4.2-1 6 5.2-2.8 5.2 2.8-1-6 4.3-4.2-6-.9L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-900">Priority Rubrics</p>
                            <p class="text-xs text-slate-500">How scoring works</p>
                        </div>
                        <svg class="ml-auto h-4 w-4 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-slate-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="m13 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </a>

                <a href="#contact" class="group rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm transition hover:border-slate-300 hover:shadow">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-900">Contact</p>
                            <p class="text-xs text-slate-500">Message support</p>
                        </div>
                        <svg class="ml-auto h-4 w-4 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-slate-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="m13 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </a>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <!-- Left column -->
            <div class="space-y-6 lg:col-span-2">
                <!-- FAQ -->
                <section id="faq" class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-5">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 0 0 10 10Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 2-2.5 2-2.5 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Frequently Asked Questions</h2>
                                <p class="text-xs text-slate-500">Quick answers to common issues</p>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-200">
                        <details class="group">
                            <summary class="flex items-center justify-between gap-4 px-6 py-5">
                                <span class="font-semibold text-slate-900">How do I apply for a scholarship?</span>
                                <svg class="chevron h-5 w-5 text-slate-400 transition-transform" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </summary>
                            <div class="px-6 pb-5 text-sm text-slate-600">
                                Go to the Dashboard and click <strong>Apply for Scholarship</strong>. Fill out the form and submit.
                            </div>
                        </details>

                        <details class="group">
                            <summary class="flex items-center justify-between gap-4 px-6 py-5">
                                <span class="font-semibold text-slate-900">What documents are required?</span>
                                <svg class="chevron h-5 w-5 text-slate-400 transition-transform" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </summary>
                            <div class="px-6 pb-5 text-sm text-slate-600">
                                Requirements depend on the application type, but <strong>ITR</strong> is a key factor in the new priority scoring.
                                Check your application page for the latest checklist.
                            </div>
                        </details>

                        <details class="group">
                            <summary class="flex items-center justify-between gap-4 px-6 py-5">
                                <span class="font-semibold text-slate-900">How will I know if my application is approved?</span>
                                <svg class="chevron h-5 w-5 text-slate-400 transition-transform" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </summary>
                            <div class="px-6 pb-5 text-sm text-slate-600">
                                You’ll receive a portal notification and an email when your status changes.
                            </div>
                        </details>

                        <details class="group">
                            <summary class="flex items-center justify-between gap-4 px-6 py-5">
                                <span class="font-semibold text-slate-900">Who can I contact for urgent help?</span>
                                <svg class="chevron h-5 w-5 text-slate-400 transition-transform" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </summary>
                            <div class="px-6 pb-5 text-sm text-slate-600">
                                Use the contact form below or email <a class="font-semibold text-indigo-700 hover:underline" href="mailto:support@ipscholar.com">support@ipscholar.com</a>.
                                Put <strong>URGENT</strong> in the subject if needed.
                            </div>
                        </details>
                    </div>
                </section>

                <!-- Rubrics -->
                <section id="rubrics" class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-5">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-orange-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 2 8.5 8l-6 .9 4.3 4.2-1 6 5.2-2.8 5.2 2.8-1-6 4.3-4.2-6-.9L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Priority Scoring Rubrics</h2>
                                <p class="text-xs text-slate-500">Current scoring: IP 20%, GPA 30%, ITR 30%, Awards 10%, Social 10%</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="px-6 pt-5">
                        <div class="flex flex-wrap gap-2">
                            <button onclick="showPriorityTab('ip-group', this)"
                                    class="priority-tab active rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white shadow-sm">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M16 11a4 4 0 1 0-8 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M4 20c0-4 4-6 8-6s8 2 8 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    IP Group (20%)
                                </span>
                            </button>
                            <button onclick="showPriorityTab('gpa', this)"
                                    class="priority-tab rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 7h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                    </svg>
                                    GPA (30%)
                                </span>
                            </button>
                            <button onclick="showPriorityTab('itr', this)"
                                    class="priority-tab rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M7 2h10v20l-2-1-2 1-2-1-2 1-2-1-2 1V2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                        <path d="M9 6h6M9 10h6M9 14h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    ITR (30%)
                                </span>
                            </button>
                            <button onclick="showPriorityTab('awards', this)"
                                    class="priority-tab rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 2 8.5 8l-6 .9 4.3 4.2-1 6 5.2-2.8 5.2 2.8-1-6 4.3-4.2-6-.9L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>
                                    Awards (10%)
                                </span>
                            </button>
                            <button onclick="showPriorityTab('social', this)"
                                    class="priority-tab rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 21s-7-4.6-9.5-8.5C.2 9 .9 5.8 3.7 4.4 5.6 3.4 8 3.9 9.6 5.6L12 8l2.4-2.4c1.6-1.7 4-2.2 5.9-1.2 2.8 1.4 3.5 4.6 1.2 8.1C19 16.4 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>
                                    Social (10%)
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- IP -->
                        <div id="priority-ip-group" class="priority-content active space-y-4">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                <p class="font-semibold text-slate-900">IP Group (20%)</p>
                                <p class="mt-1">Rubric uses your IP verification document statuses. Priority IP groups receive a +2 bonus (max 12).</p>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Rubric</th>
                                            <th class="px-4 py-3 text-left">Meaning</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">10/10</td>
                                            <td class="px-4 py-3 text-slate-600">Validated docs (highest documentation quality)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">8/10</td>
                                            <td class="px-4 py-3 text-slate-600">Missing 1 supporting document (still strong)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">6/10</td>
                                            <td class="px-4 py-3 text-slate-600">Partial / pending / mixed statuses</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">4/10</td>
                                            <td class="px-4 py-3 text-slate-600">Self-declared only</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">0/10</td>
                                            <td class="px-4 py-3 text-slate-600">No IP affiliation</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- GPA -->
                        <div id="priority-gpa" class="priority-content space-y-4">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                <p class="font-semibold text-slate-900">GPA (30%)</p>
                                <p class="mt-1">GPA (1.0 best → 5.0 worst) is converted into a 0–10 rubric, then weighted at 30%.</p>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-700">
                                <p class="font-semibold text-slate-900">Formula</p>
                                <p class="mt-1">
                                    Rubric: <strong>((5 − GPA) / 4) × 10</strong><br>
                                    Contribution: <strong>(Rubric / 10) × 30</strong>
                                </p>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3 text-left">GPA</th>
                                            <th class="px-4 py-3 text-left">Rubric (0–10)</th>
                                            <th class="px-4 py-3 text-left">Contribution (0–30%)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        <tr><td class="px-4 py-3 font-semibold text-black">1.00</td><td class="px-4 py-3 text-black">10.0</td><td class="px-4 py-3 text-black">30%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-black">2.00</td><td class="px-4 py-3 text-black">7.5</td><td class="px-4 py-3 text-black">22.5%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-black">3.00</td><td class="px-4 py-3 text-black">5.0</td><td class="px-4 py-3 text-black">15%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-black">4.00</td><td class="px-4 py-3 text-black">2.5</td><td class="px-4 py-3 text-black">7.5%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-black">5.00</td><td class="px-4 py-3 text-black">0.0</td><td class="px-4 py-3 text-black">0%</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ITR -->
                        <div id="priority-itr" class="priority-content space-y-4">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                <p class="font-semibold text-slate-900">ITR (30%)</p>
                                <p class="mt-1">Binary rule: Approved ITR = 30%, otherwise 0%.</p>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Status</th>
                                            <th class="px-4 py-3 text-left">Contribution</th>
                                            <th class="px-4 py-3 text-left">Meaning</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">Approved</td>
                                            <td class="px-4 py-3 font-semibold text-emerald-700">30%</td>
                                            <td class="px-4 py-3 text-slate-600">Income document validated by staff</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">Not approved</td>
                                            <td class="px-4 py-3 font-semibold text-slate-700">0%</td>
                                            <td class="px-4 py-3 text-slate-600">Missing, pending, or rejected</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Awards -->
                        <div id="priority-awards" class="priority-content space-y-4">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                <p class="font-semibold text-slate-900">Citations / Awards (10%)</p>
                                <p class="mt-1">Uses Rank/Honors from your Education section. Best award is used.</p>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Rank/Honors</th>
                                            <th class="px-4 py-3 text-left">Rubric</th>
                                            <th class="px-4 py-3 text-left">Contribution</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        <tr><td class="px-4 py-3 font-semibold text-slate-900">Valedictorian</td><td class="px-4 py-3 text-black">10.0</td><td class="px-4 py-3 text-black">10%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-slate-900">Salutatorian</td><td class="px-4 py-3 text-black">9.5</td><td class="px-4 py-3 text-black">9.5%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-slate-900">With Highest Honors</td><td class="px-4 py-3 text-black">9.0</td><td class="px-4 py-3 text-black">9%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-slate-900">With High Honors</td><td class="px-4 py-3 text-black">8.0</td><td class="px-4 py-3 text-black">8%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-slate-900">With Honors</td><td class="px-4 py-3 text-black">7.0</td><td class="px-4 py-3 text-black">7%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-slate-900">Dean’s Lister</td><td class="px-4 py-3 text-black">6.5</td><td class="px-4 py-3 text-black">6.5%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-slate-900">Top 10</td><td class="px-4 py-3 text-black">6.0</td><td class="px-4 py-3 text-black">6%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-slate-900">Academic Awardee</td><td class="px-4 py-3 text-black">5.0</td><td class="px-4 py-3 text-black">5%</td></tr>
                                        <tr><td class="px-4 py-3 font-semibold text-slate-900">None</td><td class="px-4 py-3 text-black">0.0</td><td class="px-4 py-3 text-black">0%</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Social -->
                        <div id="priority-social" class="priority-content space-y-4">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                <p class="font-semibold text-slate-900">Social Responsibility (10%)</p>
                                <p class="mt-1">Based on your two essay answers. Scored with a consistent rubric (no AI) for stability.</p>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Rubric part</th>
                                            <th class="px-4 py-3 text-left">Max points</th>
                                            <th class="px-4 py-3 text-left">What to include</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">Completeness / length</td>
                                            <td class="px-4 py-3 text-black">4.0</td>
                                            <td class="px-4 py-3 text-slate-600">Clear answers with enough detail</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">Keyword coverage</td>
                                            <td class="px-4 py-3 text-black">5.0</td>
                                            <td class="px-4 py-3 text-slate-600">Community needs, actions, programs/projects</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">Concrete plan bonus</td>
                                            <td class="px-4 py-3 text-black">1.0</td>
                                            <td class="px-4 py-3 text-slate-600">Intent + community + specific initiative</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-700">
                                <p class="font-semibold text-slate-900">Tip</p>
                                <p class="mt-1">Mention your target barangay/community, a problem you want to solve, and the exact program/activity you will do.</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right column -->
            <div class="space-y-6">
                <!-- Contact -->
                <section id="contact" class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-5">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Contact Support</h2>
                                <p class="text-xs text-slate-500">We’ll respond as soon as possible</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="#" class="space-y-4 p-6">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Your Email</label>
                            <input type="email" name="email"
                                   value="{{ auth()->user()->email ?? '' }}"
                                   class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-500/10"
                                   readonly required>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Subject</label>
                            <input type="text" name="subject"
                                   class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10"
                                   placeholder="What do you need help with?" required>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Message</label>
                            <textarea name="message" rows="5"
                                      class="mt-2 w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-500/10"
                                      placeholder="Describe the issue or question..." required></textarea>
                        </div>
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M22 2 11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M22 2 15 22l-4-9-9-4L22 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                            Send Message
                        </button>
                        <p class="text-xs text-slate-500">
                            For urgent issues: include <strong>URGENT</strong> in the subject.
                        </p>
                    </form>
                </section>

                <!-- Quick links -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Quick links</p>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('student.dashboard') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-50">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 4h7v7H4V4Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M13 4h7v4h-7V4Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M13 10h7v10h-7V10Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M4 13h7v7H4v-7Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                Dashboard
                            </span>
                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="m13 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <a href="{{ route('student.notifications') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-50">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="M13.7 21a2 2 0 0 1-3.4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Notifications
                            </span>
                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="m13 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <a href="{{ route('student.profile') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-50">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M12 13a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                Profile
                            </span>
                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="m13 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showPriorityTab(tabName, buttonElement) {
        document.querySelectorAll('.priority-content').forEach(content => {
            content.classList.remove('active');
        });
        document.querySelectorAll('.priority-tab').forEach(tab => {
            tab.classList.remove('active');
            tab.classList.remove('bg-slate-900', 'text-white');
            tab.classList.add('border', 'border-slate-200', 'bg-white', 'text-slate-700');
        });

        const content = document.getElementById('priority-' + tabName);
        if (content) content.classList.add('active');

        if (buttonElement) {
            buttonElement.classList.add('active');
            buttonElement.classList.remove('border', 'border-slate-200', 'bg-white', 'text-slate-700');
            buttonElement.classList.add('bg-slate-900', 'text-white');
        }
    }
</script>
@endpush
@endsection


