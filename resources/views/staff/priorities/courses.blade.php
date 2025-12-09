@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-slate-950">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-36 right-[-160px] h-96 w-96 rounded-full bg-purple-500/20 blur-3xl"></div>
        <div class="absolute top-80 -left-32 h-[420px] w-[420px] rounded-full bg-pink-500/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 text-slate-100 sm:px-6 lg:px-8 lg:py-14">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-purple-400/40 bg-purple-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-purple-200">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-purple-300"></span>
                    Course Prioritization
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Priority Course Demand Overview</h1>
                <p class="max-w-2xl text-sm text-slate-300">
                    Aggregated demand and priority scoring for scholarship-aligned courses. Excluded programs: BS IT, BS CS, BS Accountancy, BS Nursing, BS Education, BA Political Science. Demand is driven by the preferred courses declared on scholarship applications, with the sign-up course acting only as a fallback when no preference has been submitted.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-100/80">
                <span class="inline-flex items-center rounded-full border border-slate-200/20 bg-white/10 px-3 py-1 text-[11px]">
                    <svg class="mr-1 h-3.5 w-3.5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Updated {{ now()->format('M d, Y g:i A') }}
                </span>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide transition hover:bg-white/20">Back to Dashboard</a>
            </div>
        </div>

        @if(!empty($courseStatistics) && ($courseStatistics['total_courses'] ?? 0) > 0)
            <div class="mt-10 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-purple-300/40">
                    <div class="absolute -right-8 -top-8 h-20 w-20 rounded-full bg-purple-400/20 blur-3xl transition group-hover:scale-110"></div>
                    <p class="text-xs uppercase tracking-[0.25em] text-purple-200">Total Courses</p>
                    <div class="mt-2 text-3xl font-bold text-white">{{ $courseStatistics['total_courses'] ?? 0 }}</div>
                    <p class="text-xs text-purple-100/70">Tracked in this analysis</p>
                </div>
                <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-blue-300/40">
                    <div class="absolute -right-8 -top-10 h-24 w-24 rotate-12 rounded-3xl bg-blue-400/20 blur-3xl transition group-hover:scale-110"></div>
                    <p class="text-xs uppercase tracking-[0.25em] text-blue-200">Applicants</p>
                    <div class="mt-2 text-3xl font-bold text-white">{{ $courseStatistics['total_applicants'] ?? 0 }}</div>
                    <p class="text-xs text-blue-100/70">Applicants mapped to courses</p>
                </div>
                <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-emerald-300/40">
                    <div class="absolute -right-8 -bottom-10 h-24 w-24 rounded-full bg-emerald-400/20 blur-3xl transition group-hover:scale-110"></div>
                    <p class="text-xs uppercase tracking-[0.25em] text-emerald-200">High Priority Courses</p>
                    <div class="mt-2 text-3xl font-bold text-white">{{ $courseStatistics['high_priority_courses'] ?? 0 }}</div>
                    <p class="text-xs text-emerald-100/70">Scoring 70 and above</p>
                </div>
                <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:border-amber-300/40">
                    <div class="absolute -left-8 top-6 h-24 w-24 rounded-full bg-amber-400/20 blur-3xl transition group-hover:scale-110"></div>
                    <p class="text-xs uppercase tracking-[0.25em] text-amber-200">High Priority Applicants</p>
                    <div class="mt-2 text-3xl font-bold text-white">{{ $courseStatistics['total_high_priority_applicants'] ?? 0 }}</div>
                    <p class="text-xs text-amber-100/70">Applicants aligned to key courses</p>
                </div>
            </div>

            @if($courseStatistics['most_popular_course'])
                <div class="mt-10 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-lg backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.25em] text-purple-200">Most Popular Course</p>
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                        <span class="text-xl font-semibold text-white">{{ $courseStatistics['most_popular_course']['name'] }}</span>
                        <span class="rounded-full border border-purple-300/40 bg-purple-300/15 px-3 py-1 text-xs text-purple-100">{{ $courseStatistics['most_popular_course']['applicants'] }} applicants</span>
                        <span class="rounded-full border border-purple-300/40 bg-purple-300/15 px-3 py-1 text-xs text-purple-100">Priority Score: {{ number_format($courseStatistics['most_popular_course']['priority_score'], 1) }}</span>
                    </div>
                </div>
            @endif

            @if(!empty($overallCoursePrioritization))
                <div class="mt-10 overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-xl backdrop-blur">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-white/10 text-sm text-slate-100">
                            <thead class="bg-purple-500/20 text-xs uppercase tracking-[0.25em] text-purple-100">
                                <tr>
                                    <th class="px-5 py-4 text-left">Priority Rank</th>
                                    <th class="px-5 py-4 text-left">Course</th>
                                    <th class="px-5 py-4 text-left">Applicants</th>
                                    <th class="px-5 py-4 text-left">Source Mix</th>
                                    <th class="px-5 py-4 text-left">Priority Mix</th>
                                    <th class="px-5 py-4 text-left">Average Score</th>
                                    <th class="px-5 py-4 text-left">Priority Score</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10 bg-white/5">
                                @foreach($overallCoursePrioritization as $course)
                                    @php
                                        $priorityRank = $course['priority_rank'] ?? 9999;
                                        $priorityLevel = $course['priority_level'] ?? 'Low Priority';
                                        $highPriority = $course['high_priority_applicants'] ?? 0;
                                        $mediumPriority = $course['medium_priority_applicants'] ?? 0;
                                        $lowPriority = $course['low_priority_applicants'] ?? 0;
                                    @endphp
                                    <tr class="transition hover:bg-purple-400/10">
                                        <td class="px-5 py-4">
                                            @if($priorityRank && $priorityRank != 9999)
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold text-white
                                                    @if($priorityRank <= 3) bg-rose-500/80
                                                    @elseif($priorityRank <= 5) bg-orange-500/80
                                                    @elseif($priorityRank <= 10) bg-yellow-500/70 text-slate-900
                                                    @else bg-purple-500/60
                                                    @endif">#{{ $priorityRank }}</span>
                                            @else
                                                <span class="text-xs text-slate-300/70">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="font-semibold text-white" title="{{ $course['course_name'] }}">{{ $course['course_name'] }}</div>
                                            @if(!empty($course['applicants']))
                                                <div class="mt-2 flex flex-wrap gap-2 text-xs text-purple-100/80">
                                                    @foreach(array_slice($course['applicants'], 0, 4) as $applicant)
                                                        <span class="inline-flex items-center gap-1 rounded-full border border-purple-300/40 bg-purple-300/15 px-2 py-0.5">
                                                            {{ $applicant['name'] }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($course['applicants']) > 4)
                                                        <span class="text-[10px] text-purple-200/70">+{{ count($course['applicants']) - 4 }} more</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-sm text-white">
                                            <div class="font-semibold">{{ $course['total_applicants'] }}</div>
                                            <div class="text-xs text-slate-200/70">total</div>
                                        </td>
                                        <td class="px-5 py-4 text-xs text-slate-200/80">
                                            <div class="space-y-1">
                                                @if($course['registered_count'] > 0)
                                                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-blue-300"></span>Registered: {{ $course['registered_count'] }}</div>
                                                @endif
                                                @if($course['preference_first_count'] > 0)
                                                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-300"></span>1st Pref: {{ $course['preference_first_count'] }}</div>
                                                @endif
                                                @if($course['preference_second_count'] > 0)
                                                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-amber-300"></span>2nd Pref: {{ $course['preference_second_count'] }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-xs text-slate-200/80">
                                            <div class="space-y-1">
                                                @if($highPriority > 0)
                                                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-rose-300"></span>High: {{ $highPriority }}</div>
                                                @endif
                                                @if($mediumPriority > 0)
                                                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-orange-300"></span>Medium: {{ $mediumPriority }}</div>
                                                @endif
                                                @if($lowPriority > 0)
                                                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-yellow-300"></span>Low: {{ $lowPriority }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm font-semibold text-white">{{ number_format($course['average_score'], 1) }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="inline-flex items-center gap-2 rounded-full border border-purple-300/40 bg-purple-300/15 px-3 py-1 text-xs text-purple-100">
                                                {{ number_format($course['priority_score'], 1) }}
                                                <span class="text-[10px] text-purple-200/80">{{ $priorityLevel }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="mt-10 rounded-2xl border border-white/10 bg-white/5 p-12 text-center text-sm text-slate-200/70">
                    <svg class="mx-auto mb-4 h-14 w-14 text-purple-200/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    No prioritized courses yet — encourage applicants to update their preferences.
                </div>
            @endif
        @else
            <div class="mt-10 rounded-2xl border border-white/10 bg-white/5 p-12 text-center text-sm text-slate-200/70">
                <svg class="mx-auto mb-4 h-14 w-14 text-purple-200/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                No course data available. Prioritization will populate as applicants submit program preferences.
            </div>
        @endif
    </div>
</div>
@endsection
