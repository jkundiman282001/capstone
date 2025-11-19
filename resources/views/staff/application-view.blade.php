@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Enhanced Application Header -->
    <div class="bg-white/95 rounded-2xl shadow-2xl p-8 mb-8 relative overflow-hidden backdrop-blur-sm">
        <!-- Cultural Pattern Overlay -->
        <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
            <div class="w-full h-full bg-gradient-to-br from-gold to-sunrise-orange rounded-full"></div>
        </div>
        
        <div class="flex items-center justify-between mb-8 relative z-10">
            <div class="flex items-center space-x-6">
                <div class="relative">
                    @if($user->profile_pic)
                        <div class="w-20 h-20 rounded-2xl overflow-hidden shadow-xl floating">
                            <img src="{{ asset('storage/' . $user->profile_pic) }}" 
                                 alt="{{ $user->first_name }} {{ $user->last_name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-20 h-20 bg-gradient-to-br from-amber-700 via-green-600 to-yellow-400 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-xl floating">
                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-amber-700 text-sm">🌟</span>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-amber-700 mb-2">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</h2>
                    <p class="text-gray-600 text-lg">Application ID: #NCIP-{{ date('Y') }}-{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</p>
                    <div class="flex items-center space-x-3 mt-3">
                        <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-bold rounded-full border border-green-200">
                            ✅ Active
                        </span>
                        <span class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-bold rounded-full border border-blue-200">
                            💻 {{ $schoolPref->degree ?? 'N/A' }}
                        </span>
                        <span class="px-4 py-2 bg-yellow-100 text-amber-700 text-sm font-bold rounded-full border border-yellow-200">
                            🏔️ {{ $ethno->ethnicity ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right bg-gradient-to-br from-orange-100 to-yellow-100 p-6 rounded-2xl border border-orange-200">
                <p class="text-base text-gray-600 font-medium">Application Status</p>
                <p class="text-2xl font-bold text-orange-600 mt-1">Under Review</p>
                <p class="text-sm text-gray-500">Under Review</p>
            </div>
        </div>

        <!-- Enhanced Progress Bar -->
        <div class="mb-8 relative z-10">
            <div class="flex justify-between items-center mb-4">
                <span class="text-lg font-bold text-amber-700 flex items-center">
                    <span class="mr-2">📄</span> Document Completion
                </span>
                <span class="text-xl font-bold text-green-600">{{ $progressPercent }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-2xl h-6 shadow-inner">
                <div class="bg-gradient-to-r from-green-500 via-orange-500 to-yellow-400 h-6 rounded-2xl transition-all duration-1000 shadow-lg" style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="flex justify-between text-sm text-gray-600 mt-3">
                <span class="flex items-center"><span class="mr-1">✅</span> {{ $approvedCount }} of {{ $totalRequired }} documents submitted</span>
                <span class="flex items-center"><span class="mr-1">⏳</span> {{ $totalRequired - $approvedCount }} pending</span>
            </div>
        </div>
    </div>

    <!-- Enhanced Quick Stats with Cultural Icons -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/95 rounded-2xl shadow-xl p-6 relative overflow-hidden backdrop-blur-sm hover:scale-105 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-20 h-20 bg-green-100 rounded-full -mr-10 -mt-10"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-base font-bold text-gray-700 mb-1">Approved</p>
                    <p class="text-sm text-gray-500">Approved</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $documents->where('status', 'approved')->count() }}</p>
                </div>
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center">
                    <span class="text-2xl">✅</span>
                </div>
            </div>
        </div>

        <div class="bg-white/95 rounded-2xl shadow-xl p-6 relative overflow-hidden backdrop-blur-sm hover:scale-105 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-20 h-20 bg-red-100 rounded-full -mr-10 -mt-10"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-base font-bold text-gray-700 mb-1">Missing</p>
                    <p class="text-sm text-gray-500">Missing</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $totalRequired - $documents->whereIn('type', array_keys($requiredTypes))->count() }}</p>
                </div>
                <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center">
                    <span class="text-2xl">❌</span>
                </div>
            </div>
        </div>

        <div class="bg-white/95 rounded-2xl shadow-xl p-6 relative overflow-hidden backdrop-blur-sm hover:scale-105 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-20 h-20 bg-orange-100 rounded-full -mr-10 -mt-10"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-base font-bold text-gray-700 mb-1">In Review</p>
                    <p class="text-sm text-gray-500">In Review</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $documents->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center">
                    <span class="text-2xl">⏳</span>
                </div>
            </div>
        </div>

        <div class="bg-white/95 rounded-2xl shadow-xl p-6 relative overflow-hidden backdrop-blur-sm hover:scale-105 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100 rounded-full -mr-10 -mt-10"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-base font-bold text-gray-700 mb-1">Total</p>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalRequired }}</p>
                </div>
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <span class="text-2xl">📊</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Documents Section -->
    <div class="bg-white/95 rounded-2xl shadow-2xl p-8 mb-8 backdrop-blur-sm">
        <div class="mb-8 flex items-center justify-between">
            <div>
            <h3 class="text-2xl font-bold text-amber-700 flex items-center">
                <span class="mr-3">📋</span> Required Documents
            </h3>
                <p class="text-sm text-gray-600 mt-1">Priority: First Come, First Serve</p>
            </div>
            <button onclick="recalculateDocumentPriorities()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Recalculate Priorities
            </button>
        </div>

        <div class="space-y-4">
            @foreach($requiredTypes as $typeKey => $typeLabel)
                @php
                    $uploaded = $documents->firstWhere('type', $typeKey);
                    $status = $uploaded ? $uploaded->status : 'missing';
                    
                    // Get appropriate icon for each document type
                    $icon = match($typeKey) {
                        'birth_certificate' => '📋',
                        'income_document' => '💰',
                        'tribal_certificate' => '🏔️',
                        'endorsement' => '✅',
                        'good_moral' => '🤝',
                        'grades' => '📚',
                        default => '📄'
                    };
                @endphp
                
                @if($status === 'approved')
                    <!-- Approved Document -->
                    <div class="flex items-center justify-between p-6 bg-gradient-to-r from-green-100 to-green-50 border-2 border-green-300 rounded-2xl hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-green-600 rounded-2xl flex items-center justify-center">
                                <span class="text-white text-xl">✅</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-700 text-lg">{{ $typeLabel }}</h4>
                                <p class="text-green-600 font-medium">Approved • Uploaded {{ $uploaded->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-4 py-2 bg-green-600 text-white font-bold rounded-xl">✓ Accepted</span>
                            <button onclick="viewDocument('{{ asset('storage/' . $uploaded->filepath) }}', '{{ $uploaded->filename }}', '{{ $uploaded->filetype }}')" class="px-4 py-2 bg-blue-100 text-blue-700 font-semibold rounded-xl hover:bg-blue-200 transition-colors">View</button>
                        </div>
                    </div>
                @elseif($status === 'pending')
                    <!-- Pending Document with Priority -->
                    @php
                        $priorityRank = $uploaded->priority_rank ?? null;
                        $waitingHours = $uploaded->waiting_hours ?? 0;
                        $submittedAt = $uploaded->submitted_at ?? $uploaded->created_at;
                        
                        // Priority badge color
                        $priorityBadgeColor = 'bg-gray-500';
                        $priorityText = 'Not Ranked';
                        if ($priorityRank) {
                            if ($priorityRank <= 10) {
                                $priorityBadgeColor = 'bg-red-600';
                                $priorityText = 'High Priority';
                            } elseif ($priorityRank <= 50) {
                                $priorityBadgeColor = 'bg-orange-600';
                                $priorityText = 'Medium Priority';
                            } else {
                                $priorityBadgeColor = 'bg-yellow-600';
                                $priorityText = 'Low Priority';
                            }
                        }
                    @endphp
                    <div class="flex items-center justify-between p-6 bg-gradient-to-r from-orange-100 to-orange-50 border-2 border-orange-300 rounded-2xl hover:shadow-lg transition-all duration-300 relative">
                        @if($priorityRank)
                            <div class="absolute top-2 right-2 flex items-center space-x-2">
                                <span class="px-3 py-1 {{ $priorityBadgeColor }} text-white text-xs font-bold rounded-full">
                                    {{ $priorityText }}
                                </span>
                                <span class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-full">
                                    Rank #{{ $priorityRank }}
                                </span>
                            </div>
                        @endif
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center relative">
                                <span class="text-white text-xl">⏳</span>
                                @if($priorityRank && $priorityRank <= 10)
                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">!</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-700 text-lg">{{ $typeLabel }}</h4>
                                <p class="text-orange-600 font-medium">Pending Review • Uploaded {{ $submittedAt->diffForHumans() }}</p>
                                @if($waitingHours > 0)
                                    <p class="text-xs text-gray-600 mt-1">
                                        ⏱️ Waiting for {{ $waitingHours }} {{ Str::plural('hour', $waitingHours) }} 
                                        @if($waitingHours >= 72)
                                            <span class="text-red-600 font-bold">(Urgent!)</span>
                                        @elseif($waitingHours >= 48)
                                            <span class="text-orange-600 font-bold">(Priority)</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-4 py-2 bg-orange-500 text-white font-bold rounded-xl">Pending</span>
                            <button onclick="viewDocument('{{ asset('storage/' . $uploaded->filepath) }}', '{{ $uploaded->filename }}', '{{ $uploaded->filetype }}')" class="px-4 py-2 bg-blue-100 text-blue-700 font-semibold rounded-xl hover:bg-blue-200 transition-colors">View</button>
                            <button onclick="updateDocumentStatus({{ $uploaded->id }}, 'approved')" class="px-4 py-2 bg-green-100 text-green-700 font-semibold rounded-xl hover:bg-green-200 transition-colors">Accept</button>
                            <button onclick="updateDocumentStatus({{ $uploaded->id }}, 'rejected')" class="px-4 py-2 bg-red-100 text-red-700 font-semibold rounded-xl hover:bg-red-200 transition-colors">Reject</button>
                        </div>
                    </div>
                @elseif($status === 'rejected')
                    <!-- Rejected Document -->
                    <div class="flex items-center justify-between p-6 bg-gradient-to-r from-red-100 to-red-50 border-2 border-red-300 rounded-2xl hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-red-600 rounded-2xl flex items-center justify-center">
                                <span class="text-white text-xl">✗</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-700 text-lg">{{ $typeLabel }}</h4>
                                <p class="text-red-600 font-medium">Rejected • Uploaded {{ $uploaded->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-4 py-2 bg-red-600 text-white font-bold rounded-xl">Rejected</span>
                            <button onclick="viewDocument('{{ asset('storage/' . $uploaded->filepath) }}', '{{ $uploaded->filename }}', '{{ $uploaded->filetype }}')" class="px-4 py-2 bg-blue-100 text-blue-700 font-semibold rounded-xl hover:bg-blue-200 transition-colors">View</button>
                        </div>
                    </div>
                @else
                    <!-- Missing Document -->
                    <div class="flex items-center justify-between p-6 bg-gradient-to-r from-red-100 to-red-50 border-2 border-red-300 rounded-2xl hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-red-600 rounded-2xl flex items-center justify-center">
                                <span class="text-white text-xl">{{ $icon }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-700 text-lg">{{ $typeLabel }}</h4>
                                <p class="text-red-600 font-medium">Missing • {{ $typeLabel }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-4 py-2 bg-red-600 text-white font-bold rounded-xl">Missing</span>
                        </div>
                    </div>
                @endif
                @if($typeKey === 'grades' && $uploaded)
                    <button onclick="showGradesViewer(this)" data-user-id="{{ $user->id }}" class="px-4 py-2 bg-purple-100 text-purple-700 font-semibold rounded-xl hover:bg-purple-200 transition-colors">View Grades</button>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Priority Score Breakdown Section -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl shadow-xl p-8 mb-8 border border-blue-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-blue-800 flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Priority Score Analysis
                </h2>
                <p class="text-blue-600 mt-1">Weighted algorithm evaluation for scholarship prioritization</p>
    </div>
            <div class="text-right">
                @if($user->applicantScore)
                    <div class="text-3xl font-bold 
                        @if($user->applicantScore->total_score >= 80) text-red-600
                        @elseif($user->applicantScore->total_score >= 60) text-orange-600
                        @elseif($user->applicantScore->total_score >= 40) text-yellow-600
                        @else text-gray-600 @endif">
                        {{ number_format($user->applicantScore->total_score, 1) }}/100
    </div>
                    <div class="text-sm text-blue-600 font-medium">
                        @if($user->applicantScore->priority_rank)
                            Rank #{{ $user->applicantScore->priority_rank }}
                        @else
                            Not Ranked
                        @endif
            </div>
                    <div class="text-xs px-3 py-1 rounded-full mt-2
                        @if($user->applicantScore->total_score >= 80) bg-red-100 text-red-700
                        @elseif($user->applicantScore->total_score >= 60) bg-orange-100 text-orange-700
                        @elseif($user->applicantScore->total_score >= 40) bg-yellow-100 text-yellow-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ $user->applicantScore->priority_level }}
    </div>
                @else
                    <div class="text-center">
                        <div class="text-sm text-gray-500 mb-2">No Score Available</div>
                        <button onclick="calculateScore({{ $user->id }})" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                            Calculate Score
                        </button>
    </div>
                @endif
            </div>
        </div>

        @if($user->applicantScore)
            <!-- Score Breakdown Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <!-- Financial Need Score -->
                <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-green-500">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Financial Need
                        </h3>
                        <span class="text-2xl font-bold text-green-600">{{ number_format($user->applicantScore->financial_need_score, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                        <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" style="width: {{ $user->applicantScore->financial_need_score }}%"></div>
                    </div>
                    <div class="text-xs text-gray-600">
                        <strong>Weight:</strong> 25% (Highest Priority)<br>
                        <strong>Factors:</strong> Family income, unemployed parents, siblings in school
                    </div>
                </div>

                <!-- Academic Performance Score -->
                <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-blue-500">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Academic Performance
                        </h3>
                        <span class="text-2xl font-bold text-blue-600">{{ number_format($user->applicantScore->academic_performance_score, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" style="width: {{ $user->applicantScore->academic_performance_score }}%"></div>
                    </div>
                    <div class="text-xs text-gray-600">
                        <strong>Weight:</strong> 20%<br>
                        <strong>Factors:</strong> GPA, class ranking, academic honors
                    </div>
                </div>

                <!-- Document Completeness Score -->
                <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-purple-500">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Document Completeness
                        </h3>
                        <span class="text-2xl font-bold text-purple-600">{{ number_format($user->applicantScore->document_completeness_score, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                        <div class="bg-purple-500 h-3 rounded-full transition-all duration-1000" style="width: {{ $user->applicantScore->document_completeness_score }}%"></div>
                    </div>
                    <div class="text-xs text-gray-600">
                        <strong>Weight:</strong> 15%<br>
                        <strong>Factors:</strong> Required documents status, submission timing
                    </div>
                </div>

                <!-- Geographic Priority Score -->
                <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-orange-500">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Geographic Priority
                        </h3>
                        <span class="text-2xl font-bold text-orange-600">{{ number_format($user->applicantScore->geographic_priority_score, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                        <div class="bg-orange-500 h-3 rounded-full transition-all duration-1000" style="width: {{ $user->applicantScore->geographic_priority_score }}%"></div>
                    </div>
                    <div class="text-xs text-gray-600">
                        <strong>Weight:</strong> 15%<br>
                        <strong>Factors:</strong> Priority provinces/municipalities, rural areas
                    </div>
                </div>

                <!-- Indigenous Heritage Score -->
                <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-amber-500">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            Indigenous Heritage
                        </h3>
                        <span class="text-2xl font-bold text-amber-600">{{ number_format($user->applicantScore->indigenous_heritage_score, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                        <div class="bg-amber-500 h-3 rounded-full transition-all duration-1000" style="width: {{ $user->applicantScore->indigenous_heritage_score }}%"></div>
                    </div>
                    <div class="text-xs text-gray-600">
                        <strong>Weight:</strong> 15%<br>
                        <strong>Factors:</strong> Indigenous ethnicity, cultural heritage
                    </div>
                </div>

                <!-- Family Situation Score -->
                <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-pink-500">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Family Situation
                        </h3>
                        <span class="text-2xl font-bold text-pink-600">{{ number_format($user->applicantScore->family_situation_score, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                        <div class="bg-pink-500 h-3 rounded-full transition-all duration-1000" style="width: {{ $user->applicantScore->family_situation_score }}%"></div>
                    </div>
                    <div class="text-xs text-gray-600">
                        <strong>Weight:</strong> 10%<br>
                        <strong>Factors:</strong> Single parent, elderly/disabled members, family size
                    </div>
                </div>
            </div>

            <!-- Scoring Notes -->
            @if($user->applicantScore->scoring_notes)
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Scoring Analysis Notes
                    </h3>
                    <div class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg">
                        {{ $user->applicantScore->scoring_notes }}
                    </div>
                </div>
            @endif

            <!-- Last Calculated Info -->
            <div class="mt-4 text-xs text-gray-500 text-center">
                Score last calculated: {{ $user->applicantScore->last_calculated_at ? $user->applicantScore->last_calculated_at->format('M d, Y g:i A') : 'Never' }}
                <button onclick="recalculateScore({{ $user->id }})" class="ml-2 text-blue-600 hover:text-blue-800 underline">
                    Recalculate
                </button>
            </div>
        @else
            <!-- No Score Available -->
            <div class="bg-white rounded-xl p-8 text-center shadow-lg">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Priority Score Available</h3>
                <p class="text-gray-500 mb-4">This applicant hasn't been scored yet. Click the button below to calculate their priority score using our weighted algorithm.</p>
                <button onclick="calculateScore({{ $user->id }})" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    Calculate Priority Score
                </button>
            </div>
        @endif
    </div>

    <!-- Personal Information Section -->
    <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-xl p-8 mb-8 border border-blue-100">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Personal Information</h2>
        </div>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl p-5 shadow-md border-l-4 border-blue-500">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-500 uppercase">Type of Assistance</span>
                </div>
                <p class="text-lg font-bold text-gray-800">{{ $basicInfo->type_assist ?? 'N/A' }}</p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md border-l-4 border-green-500">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-500 uppercase">Email</span>
                </div>
                <p class="text-lg font-bold text-gray-800">{{ $user->email }}</p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md border-l-4 border-purple-500">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-500 uppercase">Contact Number</span>
                </div>
                <p class="text-lg font-bold text-gray-800">{{ $user->contact_num ?? 'Not provided' }}</p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md border-l-4 border-orange-500">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-500 uppercase">Date of Birth</span>
                </div>
                <p class="text-lg font-bold text-gray-800">{{ $basicInfo->birthdate ?? 'N/A' }}</p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md border-l-4 border-pink-500">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-pink-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-500 uppercase">Place of Birth</span>
                </div>
                <p class="text-lg font-bold text-gray-800">{{ $basicInfo->birthplace ?? 'N/A' }}</p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md border-l-4 border-indigo-500">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-500 uppercase">Gender</span>
                </div>
                <p class="text-lg font-bold text-gray-800">{{ $basicInfo->gender ?? 'N/A' }}</p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md border-l-4 border-teal-500">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-teal-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-500 uppercase">Civil Status</span>
                </div>
                <p class="text-lg font-bold text-gray-800">{{ $basicInfo->civil_status ?? 'N/A' }}</p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md border-l-4 border-amber-500">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-500 uppercase">Ethnolinguistic Group</span>
                </div>
                <p class="text-lg font-bold text-gray-800">{{ $ethno->ethnicity ?? 'Not specified' }}</p>
            </div>
        </div>

        <!-- Full Name Display -->
        <div class="mt-6 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 text-white">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-sm font-semibold uppercase opacity-90">Full Name</span>
            </div>
            <p class="text-2xl font-bold">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</p>
        </div>
    </div>

    <!-- Address Information Section -->
    <div class="bg-gradient-to-br from-white to-green-50 rounded-2xl shadow-xl p-8 mb-8 border border-green-100">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Address Information</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <!-- Mailing Address -->
            <div class="bg-white rounded-xl p-6 shadow-lg border-t-4 border-blue-500">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Mailing Address</h3>
                </div>
                @if($mailing && $mailing->address)
                    <div class="space-y-2 text-gray-700">
                        <p class="flex items-start">
                            <span class="font-semibold mr-2">📍</span>
                            <span>{{ $mailing->house_num ?? '' }}, {{ $mailing->address->barangay ?? '' }}, {{ $mailing->address->municipality ?? '' }}, {{ $mailing->address->province ?? '' }}</span>
                        </p>
                    </div>
                @else
                    <p class="text-gray-400 italic">Not provided</p>
                @endif
            </div>

            <!-- Permanent Address -->
            <div class="bg-white rounded-xl p-6 shadow-lg border-t-4 border-green-500">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Permanent Address</h3>
                </div>
                @if($permanent && $permanent->address)
                    <div class="space-y-2 text-gray-700">
                        <p class="flex items-start">
                            <span class="font-semibold mr-2">🏠</span>
                            <span>{{ $permanent->house_num ?? '' }}, {{ $permanent->address->barangay ?? '' }}, {{ $permanent->address->municipality ?? '' }}, {{ $permanent->address->province ?? '' }}</span>
                        </p>
                    </div>
                @else
                    <p class="text-gray-400 italic">Not provided</p>
                @endif
            </div>

            <!-- Place of Origin -->
            <div class="bg-white rounded-xl p-6 shadow-lg border-t-4 border-amber-500">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Place of Origin</h3>
                </div>
                @if($origin && $origin->address)
                    <div class="space-y-2 text-gray-700">
                        <p class="flex items-start">
                            <span class="font-semibold mr-2">🏔️</span>
                            <span>{{ $origin->house_num ?? '' }}, {{ $origin->address->barangay ?? '' }}, {{ $origin->address->municipality ?? '' }}, {{ $origin->address->province ?? '' }}</span>
                        </p>
                    </div>
                @else
                    <p class="text-gray-400 italic">Not provided</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Education Section -->
    <div class="bg-gradient-to-br from-white to-purple-50 rounded-2xl shadow-xl p-8 mb-8 border border-purple-100">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Educational Background</h2>
        </div>

        @forelse($education as $index => $edu)
            <div class="bg-white rounded-xl p-6 shadow-lg mb-4 border-l-4 border-purple-500">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                            <span class="text-2xl font-bold text-purple-600">{{ $index + 1 }}</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">{{ $edu->school_name ?? 'N/A' }}</h3>
                            <p class="text-sm text-gray-500">{{ $edu->category ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($edu->year_grad)
                        <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">
                            Graduated {{ $edu->year_grad }}
                        </span>
                    @endif
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase">School Type</span>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ $edu->school_type ?? 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Grade Average</span>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ $edu->grade_ave ?? 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Rank</span>
                        </div>
                        <p class="text-lg font-bold text-gray-800">
                            @if($edu->rank)
                                #{{ $edu->rank }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Year Graduate</span>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ $edu->year_grad ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl p-8 text-center shadow-lg">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <p class="text-gray-500 text-lg">No educational records available</p>
            </div>
        @endforelse
    </div>
    <!-- Family Information Section -->
    <div class="bg-gradient-to-br from-white to-rose-50 rounded-2xl shadow-xl p-8 mb-8 border border-rose-100">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-rose-600 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Family Information</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Father's Information -->
            <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-blue-500">
                <div class="flex items-center mb-5">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Father's Information</h3>
                </div>

                @if($familyFather)
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Name</span>
                            </div>
                            <p class="text-base font-bold text-gray-800">{{ $familyFather->name ?? 'N/A' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Status</span>
                                <p class="text-sm font-bold text-gray-800">{{ $familyFather->status ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Occupation</span>
                                <p class="text-sm font-bold text-gray-800">{{ $familyFather->occupation ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Address</span>
                            </div>
                            <p class="text-sm text-gray-700">{{ $familyFather->address ?? 'N/A' }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Office Address</span>
                            </div>
                            <p class="text-sm text-gray-700">{{ $familyFather->office_address ?? 'N/A' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Education</span>
                                <p class="text-sm font-bold text-gray-800">{{ $familyFather->educational_attainment ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Income</span>
                                <p class="text-sm font-bold text-gray-800">
                                    @if($familyFather->income)
                                        ₱{{ number_format((float) $familyFather->income, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($familyFather->ethno)
                            <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <span class="text-xs font-semibold text-amber-700 uppercase">Ethnolinguistic Group</span>
                                </div>
                                <p class="text-sm font-bold text-amber-800">{{ $familyFather->ethno->ethnicity ?? 'N/A' }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-400 italic text-center py-4">No father's information provided</p>
                @endif
            </div>

            <!-- Mother's Information -->
            <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-pink-500">
                <div class="flex items-center mb-5">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Mother's Information</h3>
                </div>

                @if($familyMother)
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Name</span>
                            </div>
                            <p class="text-base font-bold text-gray-800">{{ $familyMother->name ?? 'N/A' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Status</span>
                                <p class="text-sm font-bold text-gray-800">{{ $familyMother->status ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Occupation</span>
                                <p class="text-sm font-bold text-gray-800">{{ $familyMother->occupation ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Address</span>
                            </div>
                            <p class="text-sm text-gray-700">{{ $familyMother->address ?? 'N/A' }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Office Address</span>
                            </div>
                            <p class="text-sm text-gray-700">{{ $familyMother->office_address ?? 'N/A' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Education</span>
                                <p class="text-sm font-bold text-gray-800">{{ $familyMother->educational_attainment ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Income</span>
                                <p class="text-sm font-bold text-gray-800">
                                    @if($familyMother->income)
                                        ₱{{ number_format((float) $familyMother->income, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($familyMother->ethno)
                            <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <span class="text-xs font-semibold text-amber-700 uppercase">Ethnolinguistic Group</span>
                                </div>
                                <p class="text-sm font-bold text-amber-800">{{ $familyMother->ethno->ethnicity ?? 'N/A' }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-400 italic text-center py-4">No mother's information provided</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Siblings Section -->
    <div class="bg-gradient-to-br from-white to-indigo-50 rounded-2xl shadow-xl p-8 mb-8 border border-indigo-100">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Siblings Information</h2>
            <span class="ml-auto px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm font-semibold">
                {{ $siblings->count() }} {{ Str::plural('Sibling', $siblings->count()) }}
            </span>
        </div>

        @forelse($siblings as $index => $sibling)
            <div class="bg-white rounded-xl p-6 shadow-lg mb-4 border-l-4 border-indigo-500 hover:shadow-xl transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mr-4">
                            <span class="text-xl font-bold text-indigo-600">{{ $index + 1 }}</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">{{ $sibling->name ?? 'N/A' }}</h3>
                            <p class="text-sm text-gray-500">Sibling #{{ $index + 1 }}</p>
                        </div>
                    </div>
                    @if($sibling->present_status)
                        <span class="px-4 py-2 rounded-full text-sm font-semibold
                            @if($sibling->present_status === 'Studying') bg-green-100 text-green-700
                            @elseif($sibling->present_status === 'Working') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ $sibling->present_status }}
                        </span>
                    @endif
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Age</span>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ $sibling->age ?? 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Scholarship</span>
                        </div>
                        <p class="text-lg font-bold text-gray-800">
                            @if($sibling->scholarship)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">{{ $sibling->scholarship }}</span>
                            @else
                                <span class="text-gray-400">None</span>
                            @endif
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Course/Year</span>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ $sibling->course_year ?? 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Status</span>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ $sibling->present_status ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl p-8 text-center shadow-lg">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <p class="text-gray-500 text-lg">No siblings listed</p>
            </div>
        @endforelse
    </div>
    <!-- School Preference Section -->
    <div class="bg-gradient-to-br from-white to-cyan-50 rounded-2xl shadow-xl p-8 mb-8 border border-cyan-100">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-cyan-600 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v9M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">School Preference & Goals</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- First Preference -->
            <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-cyan-500">
                <div class="flex items-center mb-5">
                    <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center mr-4">
                        <span class="text-2xl font-bold text-cyan-600">1st</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">First Preference</h3>
                </div>

                @if($schoolPref)
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 uppercase">School Address</span>
                            </div>
                            <p class="text-sm font-bold text-gray-800">{{ $schoolPref->address ?? 'Not specified' }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Degree/Course</span>
                            </div>
                            <p class="text-base font-bold text-gray-800">{{ $schoolPref->degree ?? 'Not specified' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">School Type</span>
                                <p class="text-sm font-bold text-gray-800">{{ $schoolPref->school_type ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Years</span>
                                <p class="text-sm font-bold text-gray-800">
                                    @if($schoolPref->num_years)
                                        {{ $schoolPref->num_years }} {{ Str::plural('Year', $schoolPref->num_years) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-400 italic text-center py-4">No first preference provided</p>
                @endif
            </div>

            <!-- Second Preference -->
            <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-teal-500">
                <div class="flex items-center mb-5">
                    <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mr-4">
                        <span class="text-2xl font-bold text-teal-600">2nd</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Second Preference</h3>
                </div>

                @if($schoolPref)
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 uppercase">School Address</span>
                            </div>
                            <p class="text-sm font-bold text-gray-800">{{ $schoolPref->address2 ?? 'Not specified' }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Degree/Course</span>
                            </div>
                            <p class="text-base font-bold text-gray-800">{{ $schoolPref->degree2 ?? 'Not specified' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">School Type</span>
                                <p class="text-sm font-bold text-gray-800">{{ $schoolPref->school_type2 ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Years</span>
                                <p class="text-sm font-bold text-gray-800">
                                    @if($schoolPref->num_years2)
                                        {{ $schoolPref->num_years2 }} {{ Str::plural('Year', $schoolPref->num_years2) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-400 italic text-center py-4">No second preference provided</p>
                @endif
            </div>
        </div>

        <!-- Goals and Aspirations -->
        @if($schoolPref && ($schoolPref->ques_answer1 || $schoolPref->ques_answer2))
            <div class="bg-gradient-to-r from-cyan-600 to-teal-600 rounded-xl p-6 text-white shadow-lg">
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Goals & Aspirations
                </h3>

                @if($schoolPref->ques_answer1)
                    <div class="mb-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-sm font-semibold uppercase opacity-90">Contribution to Community</span>
                        </div>
                        <p class="text-base leading-relaxed bg-white/10 rounded-lg p-4 backdrop-blur-sm">{{ $schoolPref->ques_answer1 }}</p>
                    </div>
                @endif

                @if($schoolPref->ques_answer2)
                    <div>
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span class="text-sm font-semibold uppercase opacity-90">Plans After Graduation</span>
                        </div>
                        <p class="text-base leading-relaxed bg-white/10 rounded-lg p-4 backdrop-blur-sm">{{ $schoolPref->ques_answer2 }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Course Prioritization Section -->
    @if(isset($coursePrioritization) && $coursePrioritization['has_courses'])
        <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-2xl shadow-xl p-6 mb-6 border-2 border-purple-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-purple-800">Course Prioritization</h2>
                        <p class="text-purple-600 text-sm">Priority Analysis for {{ $user->first_name }} {{ $user->last_name }}</p>
                    </div>
                </div>
            </div>

            <!-- Applicant Score Summary -->
            <div class="bg-white rounded-xl p-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($coursePrioritization['applicant_score'], 1) }}</div>
                        <div class="text-sm text-gray-600">Applicant Score</div>
                        @if($coursePrioritization['applicant_rank'])
                            <div class="text-xs text-purple-500 mt-1">Rank #{{ $coursePrioritization['applicant_rank'] }}</div>
                        @endif
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $coursePrioritization['total_courses'] }}</div>
                        <div class="text-sm text-gray-600">Preferred Courses</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">
                            @php
                                $topCourse = !empty($coursePrioritization['courses']) ? $coursePrioritization['courses'][0] : null;
                                echo $topCourse ? number_format($topCourse['priority_score'], 1) : '0';
                            @endphp
                        </div>
                        <div class="text-sm text-gray-600">Top Course Priority</div>
                    </div>
                </div>
            </div>

            <!-- Course Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($coursePrioritization['courses'] as $course)
                    @php
                        $priorityLevel = $course['priority_level'] ?? 'Low Priority';
                        $priorityScore = $course['priority_score'] ?? 0;
                        $matchQuality = $course['match_quality'] ?? 'Fair Match';
                        $isHighPriority = $priorityScore >= 70;
                        $borderColor = $isHighPriority ? 'border-purple-500' : ($priorityScore >= 60 ? 'border-blue-500' : 'border-gray-300');
                        $bgGradient = $isHighPriority ? 'from-purple-50 to-pink-50' : 'from-blue-50 to-cyan-50';
                    @endphp
                    <div class="bg-gradient-to-br {{ $bgGradient }} rounded-xl shadow-lg p-6 border-l-4 {{ $borderColor }}">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-white font-bold">{{ $course['preference_rank'] }}</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">{{ $course['course_name'] }}</h3>
                                    <p class="text-xs text-gray-600 uppercase">{{ $course['preference'] }} Preference</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="px-3 py-1 rounded-full text-xs font-bold
                                    @if($priorityScore >= 80) bg-red-600 text-white
                                    @elseif($priorityScore >= 70) bg-purple-600 text-white
                                    @elseif($priorityScore >= 60) bg-blue-600 text-white
                                    @else bg-gray-600 text-white
                                    @endif">
                                    {{ number_format($priorityScore, 1) }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $priorityLevel }}</div>
                            </div>
                        </div>

                        <!-- School Information -->
                        <div class="bg-white/60 rounded-lg p-3 mb-3">
                            <div class="text-xs text-gray-600 mb-1">
                                <span class="font-semibold">School:</span> {{ $course['school_address'] }}
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600">
                                    <span class="font-semibold">Type:</span> {{ $course['school_type'] }}
                                </span>
                                @if($course['num_years'])
                                    <span class="text-gray-600">
                                        <span class="font-semibold">Duration:</span> {{ $course['num_years'] }} {{ Str::plural('Year', $course['num_years']) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Score Breakdown -->
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="bg-white/60 rounded p-2 text-center">
                                <div class="text-xs text-gray-600">Academic</div>
                                <div class="text-sm font-bold text-blue-600">{{ number_format($course['academic_score'], 1) }}</div>
                            </div>
                            <div class="bg-white/60 rounded p-2 text-center">
                                <div class="text-xs text-gray-600">Financial</div>
                                <div class="text-sm font-bold text-green-600">{{ number_format($course['financial_score'], 1) }}</div>
                            </div>
                            <div class="bg-white/60 rounded p-2 text-center">
                                <div class="text-xs text-gray-600">Geographic</div>
                                <div class="text-sm font-bold text-orange-600">{{ number_format($course['geographic_score'], 1) }}</div>
                            </div>
                            <div class="bg-white/60 rounded p-2 text-center">
                                <div class="text-xs text-gray-600">Heritage</div>
                                <div class="text-sm font-bold text-purple-600">{{ number_format($course['heritage_score'], 1) }}</div>
                            </div>
                        </div>

                        <!-- Match Quality -->
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold text-gray-700">Match Quality:</span>
                                <span class="px-2 py-1 rounded text-xs font-bold
                                    @if($matchQuality === 'Excellent Match') bg-green-600 text-white
                                    @elseif($matchQuality === 'Good Match') bg-blue-600 text-white
                                    @elseif($matchQuality === 'Fair Match') bg-yellow-600 text-white
                                    @else bg-gray-600 text-white
                                    @endif">
                                    {{ $matchQuality }}
                                </span>
                            </div>
                        </div>

                        <!-- Recommendations -->
                        @if(!empty($course['recommendations']))
                            <div class="border-t border-gray-200 pt-3">
                                <div class="text-xs font-semibold text-gray-700 mb-2">Recommendations:</div>
                                <ul class="space-y-1">
                                    @foreach($course['recommendations'] as $recommendation)
                                        <li class="text-xs text-gray-600 flex items-start">
                                            <span class="text-purple-600 mr-2">•</span>
                                            <span>{{ $recommendation }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(isset($coursePrioritization) && !$coursePrioritization['has_courses'])
        <div class="bg-white rounded-xl p-6 shadow-lg mb-6 border-2 border-gray-200">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <p class="text-gray-500 text-lg">{{ $coursePrioritization['message'] ?? 'No course information available for this applicant.' }}</p>
            </div>
        </div>
    @endif

    <!-- Back Button -->
    <div class="flex justify-center mb-6">
        <a href="{{ route('staff.dashboard') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>
</div>

<!-- Document Viewer Modal -->
<div id="documentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-2">
    <div class="bg-white rounded-lg shadow-xl w-[95vw] h-[95vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900"></h3>
            <button onclick="closeDocumentModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Content -->
        <div class="flex-1 p-2 overflow-hidden">
            <div id="documentViewer" class="w-full h-full"></div>
        </div>
        
        <!-- PDF Notice -->
        <div class="px-4 py-2 bg-blue-50 border-t border-gray-200">
            <div class="flex items-center gap-2 text-sm text-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Note: Only PDF files are accepted for document uploads</span>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-between p-4 border-t border-gray-200">
            <div class="flex items-center gap-2">
                <button onclick="downloadDocument()" class="px-4 py-2 bg-green-600 text-white rounded text-sm font-semibold hover:bg-green-700 transition">Download</button>
                <button onclick="printDocument()" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-semibold hover:bg-blue-700 transition">Print</button>
            </div>
            <button onclick="closeDocumentModal()" class="px-4 py-2 bg-gray-600 text-white rounded text-sm font-semibold hover:bg-gray-700 transition">Close</button>
        </div>
    </div>
</div>

<!-- Grades Viewer Modal -->
<div id="gradesModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-2">
    <div class="bg-white rounded-lg shadow-xl w-[90vw] max-w-2xl flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Extracted Grades Text</h3>
            <button onclick="closeGradesModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="flex-1 p-6 overflow-auto">
            <div class="mb-4 text-gray-600 text-sm">Below is the raw extracted text from the student's grades PDF.</div>
            <pre id="gradesText" class="bg-gray-100 p-4 rounded text-sm text-gray-800 whitespace-pre-wrap">Loading...</pre>
        </div>
        <div class="flex items-center justify-end p-4 border-t border-gray-200">
            <button onclick="closeGradesModal()" class="px-4 py-2 bg-gray-600 text-white rounded text-sm font-semibold hover:bg-gray-700 transition">Close</button>
        </div>
    </div>
</div>

<script>
let currentDocumentUrl = '';
let currentDocumentName = '';

function viewDocument(url, filename, filetype) {
    currentDocumentUrl = url;
    currentDocumentName = filename;
    
    const modal = document.getElementById('documentModal');
    const modalTitle = document.getElementById('modalTitle');
    const documentViewer = document.getElementById('documentViewer');
    
    modalTitle.textContent = filename;
    
    // Clear previous content
    documentViewer.innerHTML = '';
    
    // Handle different file types
    if (filetype.startsWith('image/')) {
        // Image files - show message that only PDFs are accepted
        const message = document.createElement('div');
        message.className = 'flex items-center justify-center h-full text-gray-500';
        message.innerHTML = `
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-lg font-semibold mb-2">Non-PDF File Detected</p>
                <p class="text-sm mb-4">This file is not in PDF format. Only PDF files are accepted for document uploads.</p>
                <button onclick="downloadDocument()" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Download to View</button>
            </div>
        `;
        documentViewer.appendChild(message);
    } else if (filetype === 'application/pdf') {
        // PDF files
        const iframe = document.createElement('iframe');
        iframe.src = url;
        iframe.className = 'w-full h-full border-0';
        iframe.title = filename;
        documentViewer.appendChild(iframe);
    } else {
        // Other file types (doc, docx, etc.) - show download message
        const message = document.createElement('div');
        message.className = 'flex items-center justify-center h-full text-gray-500';
        message.innerHTML = `
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="text-lg font-semibold mb-2">Non-PDF File Detected</p>
                <p class="text-sm mb-4">This file is not in PDF format. Only PDF files are accepted for document uploads.</p>
                <button onclick="downloadDocument()" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Download to View</button>
            </div>
        `;
        documentViewer.appendChild(message);
    }
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDocumentModal() {
    const modal = document.getElementById('documentModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function updateDocumentStatus(documentId, newStatus) {
    if (!confirm(`Are you sure you want to ${newStatus} this document?`)) {
        return;
    }

    fetch(`/staff/documents/${documentId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(`Document ${newStatus} successfully!`);
            // Reload the page to show updated status
            location.reload();
        } else {
            alert('Error updating document status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating document status');
    });
}

function downloadDocument() {
    if (currentDocumentUrl) {
        const link = document.createElement('a');
        link.href = currentDocumentUrl;
        link.download = currentDocumentName;
        link.click();
    }
}

function printDocument() {
    const documentViewer = document.getElementById('documentViewer');
    const iframe = documentViewer.querySelector('iframe');
    const img = documentViewer.querySelector('img');
    
    if (iframe) {
        iframe.contentWindow.print();
    } else if (img) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head><title>${currentDocumentName}</title></head>
                <body>
                    <img src="${img.src}" style="max-width: 100%; height: auto;">
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}

// Close modal when clicking outside
document.getElementById('documentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDocumentModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDocumentModal();
    }
});

function showGradesViewer(btn) {
    const userId = btn ? btn.getAttribute('data-user-id') : null;
    document.getElementById('gradesModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (!userId) return;
    const gradesText = document.getElementById('gradesText');
    gradesText.textContent = 'Loading...';
    fetch(`/staff/grades/${userId}`)
        .then(res => {
            if (!res.ok) {
                return res.json().then(data => {
                    throw new Error(data.error || 'Failed to contact AI service.');
                }).catch(() => {
                    throw new Error('Failed to contact AI service.');
                });
            }
            return res.json();
        })
        .then(data => {
            if (data.success && typeof data.text === 'string') {
                gradesText.textContent = data.text.trim() ? data.text : 'No text found in document.';
            } else {
                gradesText.textContent = data.error || 'Failed to extract text.';
            }
        })
        .catch((error) => {
            console.error('Error contacting AI service:', error);
            gradesText.textContent = (error.message || 'Error contacting AI service.') + '\nClick Retry below.';
            gradesText.insertAdjacentHTML('afterend', `<button onclick='retryGradesViewer(${userId})' class='mt-2 px-4 py-2 bg-purple-100 text-purple-700 rounded'>Retry</button>`);
        });
}

function retryGradesViewer(userId) {
    // Simulate a button object for showGradesViewer
    showGradesViewer({ getAttribute: () => userId });
}
// Optional: Close modal with Escape key
if (!window._gradesModalEscapeListener) {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeGradesModal();
        }
    });
    window._gradesModalEscapeListener = true;
}

// Scoring system functions
function calculateScore(userId) {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = 'Calculating...';
    button.disabled = true;

    fetch(`{{ route("staff.scores.calculate", ":id") }}`.replace(':id', userId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Score calculated successfully! Priority: ' + data.priority_level);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error calculating score. Please try again.');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function recalculateScore(userId) {
    if (!confirm('Recalculate the priority score for this applicant?')) {
        return;
    }
    calculateScore(userId);
}

// Document priority functions
function recalculateDocumentPriorities() {
    if (!confirm('Recalculate document priorities for all pending documents? This will update the First Come, First Serve ranking.')) {
        return;
    }

    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Recalculating...';
    button.disabled = true;

    fetch('{{ route("staff.documents.recalculate-priorities") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Document priorities recalculated successfully!\nTotal documents: ' + data.total_documents);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error recalculating document priorities. Please try again.');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>
@endsection
