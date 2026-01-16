@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 p-6 md:p-8 font-sans">
    
    <div class="max-w-[1600px] mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 rounded-2xl bg-gradient-to-br from-slate-600 to-slate-800 shadow-lg shadow-slate-200/50">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Applicant Archives</h1>
                        <p class="text-slate-500 text-sm mt-0.5">View and manage archived applicant records</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8">
            <form method="GET" action="{{ route('staff.archives.index') }}" class="w-full">
                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    <div class="relative flex-grow w-full">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search archived applicants by name..." 
                               class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 pl-11 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all placeholder:text-slate-400">
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('staff.archives.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 hover:text-orange-600 transition-all text-center flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Date Archived</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Applicant Name</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Archived By</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Reason</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($archives as $archive)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="p-4">
                                <span class="text-sm font-medium text-slate-900">
                                    {{ $archive->archived_at->format('M d, Y h:i A') }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                        {{ substr($archive->user->first_name ?? '?', 0, 1) }}{{ substr($archive->user->last_name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">
                                            {{ $archive->user->first_name }} {{ $archive->user->middle_name }} {{ $archive->user->last_name }}
                                        </div>
                                        <div class="text-xs text-slate-500">ID: {{ $archive->user_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="text-sm text-slate-600">{{ $archive->archiver->name ?? 'System' }}</span>
                            </td>
                            <td class="p-4">
                                <div class="max-w-xs truncate text-sm text-slate-600" title="{{ $archive->replacement->replacement_reason ?? 'N/A' }}">
                                    {{ $archive->replacement->replacement_reason ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('staff.archives.show', $archive->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    View Data
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <h3 class="text-slate-900 font-bold text-lg mb-1">No archives found</h3>
                                    <p class="text-slate-500 text-sm">No applicant records have been archived yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($archives->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $archives->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
