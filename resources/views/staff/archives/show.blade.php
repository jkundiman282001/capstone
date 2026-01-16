@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 p-6 md:p-8 font-sans">
    
    <div class="max-w-[1600px] mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('staff.archives.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-orange-600 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Archives
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                        Archived Data: {{ $archive->user->first_name }} {{ $archive->user->last_name }}
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">
                        Archived on {{ $archive->archived_at->format('F d, Y \a\t h:i A') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Basic Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- JSON Data Dump (Formatted) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="font-bold text-slate-900 text-lg mb-4">Complete Archived Data Snapshot</h3>
                    <div class="bg-slate-900 rounded-xl p-4 overflow-x-auto">
                        <pre class="text-xs text-green-400 font-mono">{{ json_encode($archive->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            </div>

            <!-- Right Column: Meta Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="font-bold text-slate-900 text-base mb-4">Archive Metadata</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Archived By</label>
                            <div class="text-sm font-medium text-slate-900">{{ $archive->archiver->name ?? 'System' }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Replacement Reason</label>
                            <div class="text-sm font-medium text-slate-900">{{ $archive->replacement->replacement_reason ?? 'N/A' }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Replacement ID</label>
                            <div class="text-sm font-medium text-slate-900">#{{ $archive->replacement_id ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
