@extends('layouts.student')

@section('title', 'Scholarship Renewal - IP Scholar Portal')

@section('content')
<div class="min-h-screen bg-[#f8fafc] pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-black text-slate-900 mb-2">Scholarship Renewal</h1>
            <p class="text-slate-500">Submit your documents to renew your scholarship grant.</p>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-red-700 font-semibold">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-red-700 font-bold">Please correct the following errors:</p>
                </div>
                <ul class="list-disc list-inside text-sm text-red-600 font-medium ml-8">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('student.renew.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Academic Progression -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 overflow-hidden mb-8 border border-slate-100">
                <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800">Academic Progression</h2>
                        <p class="text-sm text-slate-500 font-medium">Update your year level for the upcoming semester</p>
                    </div>
                </div>
                <div class="p-8">
                    @php
                        $currentYear = $existingApplication->current_year_level ?? 1;
                        // Default to next year, capped at 5
                        $defaultNextYear = min((int)$currentYear + 1, 5);
                    @endphp
                    <label class="block text-sm font-bold text-slate-700 mb-2">Incoming Year Level</label>
                    <select name="target_year_level" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-semibold text-slate-700">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ (old('target_year_level') == $i || (!old('target_year_level') && $i == $defaultNextYear)) ? 'selected' : '' }}>
                                {{ $i == 1 ? '1st' : ($i == 2 ? '2nd' : ($i == 3 ? '3rd' : ($i . 'th'))) }} Year
                            </option>
                        @endfor
                    </select>
                    <div class="mt-3 flex items-start gap-3 p-3 bg-blue-50 rounded-xl border border-blue-100">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            Current Level: <span class="font-bold">{{ $currentYear == 1 ? '1st' : ($currentYear == 2 ? '2nd' : ($currentYear == 3 ? '3rd' : ($currentYear . 'th'))) }} Year</span>.
                            Please select the year level you are <strong>enrolling in</strong>. If you are repeating a year, please select your current year level.
                        </p>
                    </div>
                </div>
            </div>

            <!-- GWA Update -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 overflow-hidden mb-8 border border-slate-100">
                <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800">Academic Performance</h2>
                        <p class="text-sm text-slate-500 font-medium">Update your GWA for the previous semester</p>
                    </div>
                </div>
                <div class="p-8">
                    <label class="block text-sm font-bold text-slate-700 mb-2">General Weighted Average (GWA)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="gpa" 
                        value="{{ old('gpa', $existingApplication->gpa) }}"
                        class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-semibold text-slate-700"
                        placeholder="e.g. 85.50"
                    >
                    <p class="mt-2 text-xs text-slate-500">Enter your GWA from the previous semester as reflected in your uploaded grades.</p>
                </div>
            </div>

            <!-- Required Documents -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 overflow-hidden mb-8 border border-slate-100">
                <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-orange-100 flex items-center justify-center text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-800">Required Documents</h2>
                            <p class="text-sm text-slate-500 font-medium">Upload clear copies of the following documents</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-8 space-y-6">
                    @foreach($renewalRequiredTypes as $typeKey => $typeLabel)
                        @php
                            $uploaded = $documents->firstWhere('type', $typeKey);
                            $status = $uploaded ? $uploaded->status : 'missing';
                            $statusColor = match($status) {
                                'approved' => 'text-green-600 bg-green-50 border-green-200',
                                'pending' => 'text-amber-600 bg-amber-50 border-amber-200',
                                'rejected' => 'text-red-600 bg-red-50 border-red-200',
                                default => 'text-slate-500 bg-slate-50 border-slate-200',
                            };
                            $statusLabel = ucfirst($status);
                        @endphp

                        <div class="p-6 rounded-2xl border-2 {{ $uploaded ? 'border-slate-200' : 'border-dashed border-slate-300' }} hover:border-orange-200 transition-colors bg-white group">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg">{{ $typeLabel }}</h3>
                                    @if($uploaded)
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusColor }}">
                                                {{ $statusLabel }}
                                            </span>
                                            <span class="text-xs text-slate-400">
                                                Submitted {{ $uploaded->submitted_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    @else
                                        <p class="text-sm text-slate-500 mt-1">Please upload this document</p>
                                    @endif
                                </div>
                                
                                @if($uploaded)
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('documents.view', $uploaded->id) }}" target="_blank" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition-colors">
                                            View File
                                        </a>
                                    </div>
                                @endif
                            </div>

                            @if($status !== 'approved')
                                <div class="relative">
                                    <input 
                                        type="file" 
                                        name="documents[{{ $typeKey }}]" 
                                        id="doc_{{ $typeKey }}" 
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        class="block w-full text-sm text-slate-500
                                            file:mr-4 file:py-2.5 file:px-4
                                            file:rounded-xl file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-orange-50 file:text-orange-700
                                            hover:file:bg-orange-100
                                            cursor-pointer"
                                    >
                                    <p class="mt-2 text-xs text-slate-400">
                                        Allowed formats: PDF, JPG, PNG. Max size: 10MB.
                                        @if($uploaded)
                                            <span class="text-amber-600 font-medium">Uploading a new file will replace the current one.</span>
                                        @endif
                                    </p>
                                </div>
                            @elseif($status === 'approved')
                                <div class="bg-green-50 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 border border-green-100">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm font-medium">This document has been approved. No further action needed.</span>
                                </div>
                            @endif

                            @if($uploaded && $uploaded->status === 'rejected' && $uploaded->rejection_reason)
                                <div class="mt-4 bg-red-50 text-red-700 px-4 py-3 rounded-xl border border-red-100">
                                    <p class="text-xs font-bold uppercase mb-1">Rejection Reason:</p>
                                    <p class="text-sm">{{ $uploaded->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <button type="submit" class="px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold rounded-2xl shadow-xl shadow-green-600/20 hover:shadow-green-600/40 hover:-translate-y-1 transition-all flex items-center gap-3 text-lg">
                    <span>Submit Renewal Application</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('student.dashboard') }}" class="text-slate-500 font-medium hover:text-slate-800 transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
