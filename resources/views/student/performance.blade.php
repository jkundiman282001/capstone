@extends('layouts.student')

@section('title', 'Performance - IP Scholar Portal')

@push('head-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
@endpush

@section('content')
<div class="bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 min-h-screen pt-20">
 <!-- Main Content Wrapper -->
  <main class="max-w-7xl mx-auto px-6 py-8 space-y-8">

@php
    $priorityFactors = $priorityFactors ?? [];
    
    // Get slots information from controller (priorityStatistics)
    $slotsLeft = $priorityStatistics['slots_left'] ?? null;
    // Total applicants waiting for approval (validated but not yet grantees)
    $totalApplicants = $priorityStatistics['total_applicants'] ?? 0;
    $granteesCount = $priorityStatistics['grantees_count'] ?? 0;
    $maxSlots = $priorityStatistics['max_slots'] ?? 120;
    $studentGrantStatus = $priorityStatistics['student_grant_status'] ?? null;
    
    // Check validation status: prioritize basicInfo directly, then fallback to priorityStatistics
    $isStudentValidated = false;
    if (isset($basicInfo) && $basicInfo && $basicInfo->application_status) {
        $isStudentValidated = (strtolower(trim((string)$basicInfo->application_status)) === 'validated');
    } elseif (isset($priorityStatistics['is_student_validated'])) {
        $isStudentValidated = (bool)$priorityStatistics['is_student_validated'];
    }
    
    // ============================================
    // ACCEPTANCE CHANCE CALCULATION (in view)
    // ============================================
    // Improved calculation that handles cases where slots > applicants
    // Uses applicants waiting for approval (validated but not yet grantees)
    // Rules:
    // 1. If student is a grantee → 100%
    // 2. If slots_left >= total_applicants_waiting:
    //    - If validated: 95% (high but not 100% due to remaining uncertainty)
    //    - If not validated: 85% (lower due to validation requirement)
    // 3. Otherwise → (slots_left / total_applicants_waiting) * 100
    // ============================================
    
    // Initialize acceptance chance
    $acceptanceChance = null;
    
    // Check if student is a grantee (handle case variations)
    // Check both from priorityStatistics and directly from basicInfo
    $grantStatusFromBasicInfo = isset($basicInfo) && $basicInfo ? ($basicInfo->grant_status ?? null) : null;
    $isGrantee = ($studentGrantStatus && strtolower(trim((string)$studentGrantStatus)) === 'grantee') 
                 || ($grantStatusFromBasicInfo && strtolower(trim((string)$grantStatusFromBasicInfo)) === 'grantee');
    
    // Calculate acceptance chance
    try {
        // CASE 1: Student is a grantee → set to null (will show "Grantee" instead)
        if ($isGrantee) {
            $acceptanceChance = null; // Don't show percentage, show "Grantee" instead
        }
        // CASE 2: If validated but not yet grantee, calculate based on slots left and total applicants waiting
        // Use $isStudentValidated (which now includes fallback check from basicInfo)
        elseif ($isStudentValidated && !$isGrantee) {
            if ($slotsLeft <= 0) {
                // No slots available
                $acceptanceChance = 0.0;
            } elseif ($totalApplicants > 0) {
                // Simple ratio calculation: (slots_left / total_applicants_waiting) * 100
                $acceptanceChance = ($slotsLeft / $totalApplicants) * 100;
                $acceptanceChance = round($acceptanceChance, 2);
                $acceptanceChance = min(100.0, max(0.0, $acceptanceChance));
            } else {
                // No applicants waiting for approval yet
                $acceptanceChance = 0.0;
            }
        }
        // CASE 3: Not validated yet
        else {
            $acceptanceChance = null; // Don't show percentage if not validated
        }
        
        // Final validation: ensure it's always a number between 0 and 100 or null
        if ($acceptanceChance !== null) {
            if (!is_numeric($acceptanceChance) || $acceptanceChance < 0) {
                $acceptanceChance = 0.0;
            }
            if ($acceptanceChance > 100) {
                $acceptanceChance = 100.0;
            }
        }
        
    } catch (\Exception $e) {
        // Log error and default to null (will show "Not available yet")
        \Log::error('Error calculating acceptance chance in view', [
            'student_id' => auth()->id(),
            'error' => $e->getMessage(),
            'grant_status' => $studentGrantStatus,
            'is_grantee' => $isGrantee,
            'slots_left' => $slotsLeft,
            'total_applicants_waiting' => $totalApplicants
        ]);
        $acceptanceChance = null;
    }
    
    // Legacy chance percentage calculation (kept for backward compatibility)
    $chancePercentage = $acceptanceChance;
    
    $studentPriorityBreakdown = [
        ['label' => 'IP group rubric', 'weight' => '20%', 'met' => ($priorityFactors['ip_rubric_score'] ?? 0) > 0],
        ['label' => 'Academic (GPA 1.0–5.0)', 'weight' => '30%', 'met' => ($priorityFactors['academic_rubric_score'] ?? 0) > 0],
        ['label' => 'Income Tax Return (ITR)', 'weight' => '30%', 'met' => $priorityFactors['has_approved_income_tax'] ?? false],
        ['label' => 'Citations / Awards', 'weight' => '10%', 'met' => ($priorityFactors['awards_rubric_score'] ?? 0) > 0],
        ['label' => 'Social responsibility (essays)', 'weight' => '10%', 'met' => ($priorityFactors['social_responsibility_rubric_score'] ?? 0) > 0],
    ];
@endphp

<!-- Performance Dashboard -->
<div class="mb-8">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-6xl mx-auto">
    <!-- Priority Rank Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
      <div class="bg-gradient-to-br from-orange-500 via-amber-500 to-orange-600 px-6 py-5">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-white">Priority Rank</h3>
            <p class="text-xs text-orange-100">Your position in queue</p>
          </div>
        </div>
      </div>
      <div class="p-6">
        @if(isset($priorityRank) && $priorityRank)
          <div class="text-center">
            <div class="inline-flex items-center justify-center w-28 h-28 rounded-full bg-gradient-to-br from-orange-100 to-amber-100 border-4 border-orange-300 mb-4">
              <span class="text-5xl font-black text-orange-600">#{{ $priorityRank }}</span>
            </div>
            <p class="text-sm font-semibold text-slate-700 mb-1">Out of {{ $priorityStatistics['total_applicants'] ?? 0 }} applicants</p>
            @if(isset($slotsLeft) && $slotsLeft !== null)
              <p class="text-xs text-slate-500">{{ number_format($slotsLeft) }} slots available</p>
            @endif
          </div>
        @else
          <div class="text-center py-6">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 mb-4">
              <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <p class="text-lg font-semibold text-slate-600 mb-1">Not yet ranked</p>
            <p class="text-xs text-slate-500">Complete application to be ranked</p>
          </div>
        @endif
      </div>
    </div>

    <!-- Chance Percentage Card -->
    @php
        // Check if student is a grantee - check multiple sources
        $isGranteeCheck = false;
        
        // Check 1: From basicInfo directly (most reliable)
        if (isset($basicInfo) && $basicInfo && $basicInfo->grant_status) {
            $grantStatusValue = strtolower(trim((string)$basicInfo->grant_status));
            if ($grantStatusValue === 'grantee') {
                $isGranteeCheck = true;
            }
        }
        
        // Check 2: From priorityStatistics
        if (!$isGranteeCheck && $studentGrantStatus && strtolower(trim((string)$studentGrantStatus)) === 'grantee') {
            $isGranteeCheck = true;
        }
        
        // Check 3: From the calculated $isGrantee variable
        if (!$isGranteeCheck && isset($isGrantee) && $isGrantee) {
            $isGranteeCheck = true;
        }
        
        // Use the calculated acceptance chance from the calculation above
        // But if student is a grantee, set to null to show "Grantee" instead
        $displayChance = $isGranteeCheck ? null : $acceptanceChance;
    @endphp
    @if($displayChance !== null || $isGranteeCheck)
    <div class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 rounded-2xl shadow-xl border-2 border-green-200 overflow-hidden">
      <div class="bg-gradient-to-br from-green-500 via-emerald-500 to-teal-600 px-6 py-5">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-white">Scholarship Acceptance Chance</h3>
            <p class="text-xs text-green-100">
              @if($isGranteeCheck)
                Your scholarship grant status
              @else
                Your probability of receiving the scholarship
              @endif
            </p>
          </div>
        </div>
      </div>
      <div class="p-6">
        <div class="text-center">
          @if($isGranteeCheck)
            <!-- Show "Grantee" instead of percentage -->
            <div class="inline-flex items-center justify-center w-28 h-28 rounded-full bg-gradient-to-br from-green-100 to-emerald-100 border-4 border-green-300 mb-4 shadow-lg">
              <div class="text-center">
                <span class="text-2xl font-black text-green-600 block leading-tight">Grantee</span>
              </div>
            </div>
            <p class="text-sm font-semibold text-slate-700 mb-3">
              Congratulations! You are a scholarship grantee.
            </p>
          @else
            <!-- Show percentage for non-grantees -->
            <div class="inline-flex items-center justify-center w-28 h-28 rounded-full bg-gradient-to-br from-green-100 to-emerald-100 border-4 border-green-300 mb-4 shadow-lg">
              <div class="text-center">
                <span class="text-5xl font-black text-green-600 block leading-none">{{ number_format($displayChance, 1) }}</span>
                <span class="text-xl font-bold text-green-600">%</span>
              </div>
            </div>
            <p class="text-sm font-semibold text-slate-700 mb-3">
              @if($displayChance >= 80)
                Excellent chance! You're in a strong position.
              @elseif($displayChance >= 50)
                Good chance! Keep up the good work.
              @elseif($displayChance >= 30)
                Moderate chance. Continue improving your application.
              @elseif($displayChance > 0)
                Lower chance. Focus on completing all requirements.
              @else
                No slots available at the moment.
              @endif
            </p>
          @endif
          @if(isset($priorityStatistics) && !$isGranteeCheck)
            <div class="mt-4 pt-4 border-t border-green-200 space-y-2">
              <div class="flex justify-between text-xs">
                <span class="text-slate-600">Available Slots:</span>
                <span class="font-bold text-green-700">{{ number_format($priorityStatistics['slots_left'] ?? 0) }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-slate-600">Total Applicants Waiting for Approval:</span>
                <span class="font-bold text-slate-700">{{ number_format($priorityStatistics['total_applicants'] ?? 0) }}</span>
              </div>
              @if(isset($priorityStatistics['grantees_count']))
                <div class="flex justify-between text-xs">
                  <span class="text-slate-600">Current Grantees:</span>
                  <span class="font-bold text-slate-700">{{ number_format($priorityStatistics['grantees_count']) }}</span>
                </div>
              @endif
            </div>
          @endif
          
          <!-- Debug Section: Show applicants data -->
          @if(isset($priorityStatistics['applicants_waiting_debug']) && config('app.debug'))
            <div class="mt-4 pt-4 border-t border-green-200">
              <details class="text-xs">
                <summary class="cursor-pointer text-slate-500 hover:text-slate-700 font-semibold mb-2">Debug: Applicants Waiting for Approval</summary>
                <div class="mt-2 space-y-1 bg-slate-50 p-3 rounded-lg">
                  <div class="font-bold text-slate-700 mb-2">Total Count: {{ count($priorityStatistics['applicants_waiting_debug']) }}</div>
                  @foreach($priorityStatistics['applicants_waiting_debug'] as $app)
                    <div class="text-slate-600">
                      • {{ $app['name'] }} (ID: {{ $app['user_id'] }}) - Grant Status: <span class="font-semibold">{{ $app['grant_status'] }}</span>
                    </div>
                  @endforeach
                </div>
              </details>
              <details class="text-xs mt-2">
                <summary class="cursor-pointer text-slate-500 hover:text-slate-700 font-semibold mb-2">Debug: All Validated Applicants</summary>
                <div class="mt-2 space-y-1 bg-slate-50 p-3 rounded-lg">
                  <div class="font-bold text-slate-700 mb-2">Total Count: {{ count($priorityStatistics['all_validated_debug']) }}</div>
                  @foreach($priorityStatistics['all_validated_debug'] as $app)
                    <div class="text-slate-600">
                      • {{ $app['name'] }} (ID: {{ $app['user_id'] }}) - Grant Status: <span class="font-semibold">{{ $app['grant_status'] }}</span>
                    </div>
                  @endforeach
                </div>
              </details>
            </div>
          @endif
        </div>
      </div>
    </div>
    @else
    <!-- Placeholder if no chance data -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
      <div class="bg-gradient-to-br from-slate-400 to-slate-500 px-6 py-5">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-white">Scholarship Acceptance Chance</h3>
            <p class="text-xs text-slate-100">Your probability of receiving the scholarship</p>
          </div>
        </div>
      </div>
      <div class="p-6">
        <div class="text-center py-6">
          <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 mb-4">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <p class="text-sm font-semibold text-slate-600 mb-1">Not available yet</p>
          <p class="text-xs text-slate-500">Complete your application to see your chance</p>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>

<!-- Priority Factors Section -->
<div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6">
  <div class="mb-6">
    <h3 class="text-xl font-bold text-slate-800 mb-2">Priority Factors</h3>
    <p class="text-sm text-slate-600">Factors that influence your scholarship priority ranking</p>
  </div>
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($studentPriorityBreakdown as $factor)
      @php
        $statusLabel = $factor['met'] ? 'Met' : 'Not met';
      @endphp
      <div class="group relative bg-gradient-to-br from-slate-50 to-white rounded-xl border-2 
        @if($factor['met']) border-green-200 hover:border-green-300
        @else border-red-200 hover:border-red-300
        @endif p-5 transition-all hover:shadow-lg">
        <div class="flex items-start justify-between mb-3">
          <div class="flex-1">
            <p class="font-bold text-slate-800 mb-1">{{ $factor['label'] }}</p>
            <p class="text-xs text-slate-500 font-medium">{{ $factor['weight'] }} weight</p>
          </div>
          @if($factor['met'])
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
          @else
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </div>
          @endif
        </div>
        <div class="mt-3 pt-3 border-t border-slate-200">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
            @if($factor['met']) bg-green-100 text-green-700
            @else bg-red-100 text-red-700
            @endif">
            {{ $statusLabel }}
          </span>
        </div>
      </div>
    @endforeach
  </div>
</div>
<!-- Show Type of Assistance if application is complete -->
@if(isset($basicInfo) && $basicInfo)
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 rounded mb-4">
                <strong>Type of Assistance:</strong>
                <span class="font-semibold">
                    {{ $basicInfo->type_assist ? $basicInfo->type_assist : 'Not specified' }}
                </span>
            </div>
        </div>
    @endif



<!-- Event Participation & Attendance -->
<!-- Compliance Checklist & Upload Documents (Glassmorphism Centered) -->
<div class="flex justify-center items-start w-full py-12">
  <section class="backdrop-blur-lg bg-white/60 shadow-2xl rounded-3xl p-10 border border-white/30 max-w-5xl w-full flex flex-col space-y-10 select-none transition-all">
    <h3 class="text-3xl font-black text-gray-900 mb-4 flex items-center gap-3 tracking-tight drop-shadow">
      <svg class="w-9 h-9 text-orange-500" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M16 3v4a1 1 0 0 0 1 1h4"/></svg>
      Compliance Checklist & Uploads
    </h3>
    
    <!-- File Upload Notice -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg mb-6">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div class="ml-3">
          <h4 class="text-sm font-medium text-blue-800">File Upload Guidelines</h4>
          <div class="mt-1 text-sm text-blue-700">
            <p><strong>Accepted Formats:</strong> You can upload documents as <strong>PDF files</strong> or <strong>image files</strong> (JPG, JPEG, PNG, GIF).</p>
            <p class="mt-1"><strong>Maximum Size:</strong> 10MB per file</p>
            <p class="mt-1 text-xs text-blue-600"><em>Tip: For best results, ensure images are clear and readable. PDF format is recommended for multi-page documents.</em></p>
          </div>
        </div>
      </div>
    </div>
    @if(session('success'))
      <div class="bg-green-200/80 text-green-900 rounded-xl p-3 mb-2 text-center font-bold shadow">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="bg-red-200/80 text-red-900 rounded-xl p-3 mb-2 shadow">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @php
      $totalRequired = count($requiredTypes);
      $approvedCount = $documents->whereIn('type', array_keys($requiredTypes))->where('status', 'approved')->count();
      $progressPercent = $totalRequired > 0 ? round(($approvedCount / $totalRequired) * 100) : 0;
    @endphp
    <div class="mb-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-1">
        <span class="text-sm font-semibold text-gray-700">Document Approval Progress</span>
        <div class="flex items-center gap-3">
          <span class="text-sm font-bold text-orange-600">{{ $progressPercent }}%</span>
          <button id="upload-all-btn" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-orange-600 text-white text-xs font-semibold shadow hover:bg-orange-700 transition focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 focus:ring-offset-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Upload All
          </button>
        </div>
      </div>
      <div class="w-full bg-orange-100 rounded-full h-3 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-400 to-orange-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
      </div>
    </div>
    <ul class="space-y-6">
      @foreach($requiredTypes as $typeKey => $typeLabel)
        @php
          $uploaded = $documents->firstWhere('type', $typeKey);
          $status = $uploaded ? $uploaded->status : 'missing';
        @endphp
        <li class="bg-gradient-to-br from-orange-100/80 via-amber-100/60 to-white/60 border border-orange-200/60 rounded-2xl p-6 md:p-7 flex flex-col gap-5 shadow-lg hover:shadow-2xl transition-all">
          <div class="flex items-start gap-4 md:gap-5 min-w-0">
            @if($status === 'approved')
              <span class="w-12 h-12 rounded-full bg-green-500/90 flex items-center justify-center text-white text-2xl font-black shadow-lg border-4 border-white/40 flex-shrink-0">✓</span>
            @elseif($status === 'pending')
              <span class="w-12 h-12 rounded-full bg-yellow-400/90 flex items-center justify-center text-white text-2xl font-black shadow-lg border-4 border-white/40 flex-shrink-0">!</span>
            @elseif($status === 'rejected')
              <span class="w-12 h-12 rounded-full bg-red-500/90 flex items-center justify-center text-white text-2xl font-black shadow-lg border-4 border-white/40 flex-shrink-0">×</span>
            @else
              <span class="w-12 h-12 rounded-full bg-red-500/90 flex items-center justify-center text-white text-2xl font-black shadow-lg border-4 border-white/40 flex-shrink-0">×</span>
            @endif
            <div class="flex flex-col min-w-0 flex-1">
              <span class="font-extrabold text-base md:text-lg text-gray-900 leading-tight tracking-tight break-words">{{ $typeLabel }}</span>
              <div class="flex items-center gap-2 mt-2 flex-wrap">
                @if($status === 'approved')
                  <span class="bg-green-200/80 text-green-900 px-3 py-0.5 rounded-full text-xs font-bold shadow">Approved</span>
                @elseif($status === 'pending')
                  <span class="bg-yellow-200/80 text-yellow-900 px-3 py-0.5 rounded-full text-xs font-bold shadow">Pending</span>
                @elseif($status === 'rejected')
                  <span class="bg-red-200/80 text-red-900 px-3 py-0.5 rounded-full text-xs font-bold shadow">Rejected</span>
                @else
                  <span class="bg-red-200/80 text-red-900 px-3 py-0.5 rounded-full text-xs font-bold shadow">Missing</span>
                @endif
                @if($uploaded)
                  <span class="text-gray-400 text-xs">• Uploaded {{ $uploaded->created_at->diffForHumans() }}</span>
                @endif
              </div>
            </div>
          </div>
          
          <!-- Rejection Feedback Display -->
          @if($status === 'rejected' && $uploaded)
            @if($uploaded->rejection_reason)
              <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm mb-4">
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-red-900 mb-1">Rejection Feedback from Staff</h4>
                    <div class="bg-white border border-red-200 rounded-lg p-3 mb-2">
                      <p class="text-xs font-semibold text-red-700 uppercase tracking-wide mb-1">Staff Feedback:</p>
                      <p class="text-sm text-red-900 leading-relaxed whitespace-pre-wrap">{{ $uploaded->rejection_reason }}</p>
                    </div>
                    <div class="bg-orange-100 border border-orange-300 rounded-lg p-3 mt-3">
                      <p class="text-xs font-bold text-orange-900 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Action Required: Please upload a corrected document below
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            @else
              <div class="bg-amber-50 border-l-4 border-amber-400 rounded-lg p-4 shadow-sm mb-4">
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-amber-900 mb-1">Document Rejected</h4>
                    <p class="text-sm text-amber-800 mb-2">This document has been rejected. Please upload a corrected version.</p>
                    <div class="bg-orange-100 border border-orange-300 rounded-lg p-3">
                      <p class="text-xs font-bold text-orange-900 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        You can resubmit this document below
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @endif
          
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full pt-2 border-t border-orange-200/40">
            <div class="flex flex-col md:items-start gap-2">
              @if($uploaded)
                <a href="{{ asset('storage/' . $uploaded->filepath) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600/90 text-white rounded-full text-xs font-bold shadow hover:bg-blue-700/90 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  View Document
                </a>
              @endif
            </div>
            @if(!$uploaded || ($uploaded && in_array($uploaded->status, ['rejected', 'pending'])))
              <form method="POST" action="{{ route('documents.upload') }}" enctype="multipart/form-data" class="doc-upload-form flex items-center gap-2 w-full md:w-auto" data-type="{{ $typeKey }}" data-label="{{ $typeLabel }}">
                @csrf
                <input type="hidden" name="type" value="{{ $typeKey }}">
                <div class="flex flex-col gap-1.5 w-full md:w-auto">
                  @if($status === 'rejected')
                    <div class="mb-2">
                      <p class="text-xs font-bold text-orange-700 mb-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Resubmit Document:
                      </p>
                    </div>
                  @elseif($status === 'pending')
                    <div class="mb-2">
                      <p class="text-xs font-bold text-amber-700 mb-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Replace Document:
                      </p>
                    </div>
                  @endif
                  <input type="file" name="upload-file" required class="doc-file-input block text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs {{ $status === 'rejected' ? 'file:bg-red-50/80 file:text-red-700 hover:file:bg-red-100/80' : ($status === 'pending' ? 'file:bg-amber-50/80 file:text-amber-700 hover:file:bg-amber-100/80' : 'file:bg-orange-50/80 file:text-orange-700 hover:file:bg-orange-100/80') }} focus:outline-none focus:ring-2 focus:ring-orange-400/60" accept=".pdf,.jpg,.jpeg,.png,.gif">
                  <span class="text-xs {{ $status === 'rejected' ? 'text-red-600 font-semibold' : ($status === 'pending' ? 'text-amber-600 font-semibold' : 'text-gray-500') }}">
                    @if($status === 'rejected')
                      Select corrected file then use "Upload All"
                    @elseif($status === 'pending')
                      Select new file to replace current submission
                    @else
                      Select file then use "Upload All"
                    @endif
                  </span>
                </div>
              </form>
            @endif
          </div>
        </li>
      @endforeach
    </ul>
  </section>
</div>
</main>
</div>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const uploadAllBtn = document.getElementById('upload-all-btn');
  if (!uploadAllBtn) {
    return;
  }

  uploadAllBtn.addEventListener('click', async function() {
    const forms = Array.from(document.querySelectorAll('.doc-upload-form'));
    const formsToUpload = forms.filter(form => {
      const fileInput = form.querySelector('.doc-file-input');
      return fileInput && fileInput.files.length > 0;
    });

    if (formsToUpload.length === 0) {
      alert('Please select at least one PDF document before uploading.');
      return;
    }

    uploadAllBtn.disabled = true;
    const originalText = uploadAllBtn.innerHTML;
    uploadAllBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Uploading...';

    let hasErrors = false;

    const errorMessages = [];
    
    for (const form of formsToUpload) {
      const formData = new FormData(form);
      
      // Get CSRF token from meta tag or form
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                       form.querySelector('input[name="_token"]')?.value;
      
      if (csrfToken) {
        formData.append('_token', csrfToken);
      }
      
      try {
        const response = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken || '',
            'Accept': 'application/json',
          },
          body: formData,
        });

        let responseData;
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
          responseData = await response.json();
        } else {
          // If not JSON, try to get text response
          const text = await response.text();
          responseData = { success: response.ok, message: text || response.statusText };
        }
        
        if (!response.ok || (responseData.success === false)) {
          hasErrors = true;
          const errorMsg = responseData.message || 
                          (responseData.errors ? Object.values(responseData.errors).flat().join(', ') : '') ||
                          response.statusText;
          errorMessages.push(errorMsg);
          console.error('Upload error:', responseData);
        }
      } catch (error) {
        hasErrors = true;
        errorMessages.push('Network error: ' + error.message);
        console.error('Upload exception:', error);
      }
    }

    if (hasErrors) {
      const errorMsg = errorMessages.length > 0 
        ? 'Upload errors:\n' + errorMessages.join('\n')
        : 'Some documents failed to upload. Please check:\n- File size is under 10MB\n- File is PDF, JPG, JPEG, PNG, or GIF\n- File is not corrupted\n\nPlease review and try again.';
      alert(errorMsg);
    } else {
      alert('All selected documents were uploaded successfully!');
    }

    window.location.reload();
    uploadAllBtn.innerHTML = originalText;
  });
});
</script>
@endpush