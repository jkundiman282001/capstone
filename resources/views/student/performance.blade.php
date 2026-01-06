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
    
    // Check if student is a grantee (handle case variations)
    // Check both from priorityStatistics and directly from basicInfo
    $grantStatusFromBasicInfo = isset($basicInfo) && $basicInfo ? ($basicInfo->grant_status ?? null) : null;
    $isGrantee = ($studentGrantStatus && strtolower(trim((string)$studentGrantStatus)) === 'grantee') 
                 || ($grantStatusFromBasicInfo && strtolower(trim((string)$grantStatusFromBasicInfo)) === 'grantee');

    // Tracker configuration - move up so it can be used in the scholar bar
    $trackerStatus = strtolower($basicInfo->application_status ?? 'submitted');
    $trackerSteps = [
        'submitted' => ['label' => 'Submitted', 'color' => 'bg-emerald-500'],
        'review' => ['label' => 'Review', 'color' => 'bg-emerald-500'],
        'validation' => ['label' => 'Validation', 'color' => 'bg-orange-500'],
        'scholar' => ['label' => 'Scholar', 'color' => 'bg-blue-600']
    ];
    $currentStepIndex = 0;
    if ($trackerStatus === 'validated') $currentStepIndex = 2;
    if ($isGrantee) $currentStepIndex = 3;

    // ============================================
    // ACCEPTANCE CHANCE CALCULATION
    // ============================================
    
    // Initialize acceptance chance
    $acceptanceChance = null;
    
    // Calculate acceptance chance
    try {
        // CASE 1: Student is a grantee → set to null (will show "Grantee" instead)
        if ($isGrantee) {
            $acceptanceChance = null; 
        }
        // CASE 2: If validated but not yet grantee, calculate based on slots left and total applicants waiting
        elseif ($isStudentValidated && !$isGrantee) {
            if ($slotsLeft <= 0) {
                $acceptanceChance = 0.0;
            } elseif ($totalApplicants > 0) {
                $acceptanceChance = ($slotsLeft / $totalApplicants) * 100;
                $acceptanceChance = round($acceptanceChance, 2);
                $acceptanceChance = min(100.0, max(0.0, $acceptanceChance));
            } else {
                $acceptanceChance = 0.0;
            }
        }
        else {
            $acceptanceChance = null;
        }
        
        if ($acceptanceChance !== null) {
            if (!is_numeric($acceptanceChance) || $acceptanceChance < 0) {
                $acceptanceChance = 0.0;
            }
            if ($acceptanceChance > 100) {
                $acceptanceChance = 100.0;
            }
        }
        
    } catch (\Exception $e) {
        \Log::error('Error calculating acceptance chance in view', [
            'student_id' => auth()->id(),
            'error' => $e->getMessage()
        ]);
        $acceptanceChance = null;
    }
    
    $chancePercentage = $acceptanceChance;
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
            <div class="mt-4 pt-3 border-t border-slate-100">
              <p class="text-[10px] leading-relaxed text-slate-400 italic">
                <svg class="w-3 h-3 inline-block mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <strong>Note:</strong> Your priority rank is dynamic and may change as more applications are validated and ranked based on the system's scoring criteria.
              </p>
            </div>
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
            <h3 class="text-lg font-bold text-white">
              @if($isGranteeCheck)
                Scholarship Grant Status
              @else
                Scholarship Acceptance Chance
              @endif
            </h3>
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
            <p class="text-sm font-semibold text-slate-700 mb-6">
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
              <div class="mt-4 pt-3 border-t border-green-200">
                 <p class="text-[10px] leading-relaxed text-green-700/70 italic">
                   <svg class="w-3 h-3 inline-block mr-1 text-green-600/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                   </svg>
                   <strong>Disclaimer:</strong> This percentage is a statistical estimate based on available slots and the current applicant pool. It is for informational purposes only and does not guarantee scholarship approval.
                 </p>
               </div>
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

<!-- Unified Performance Content -->
<div class="max-w-7xl mx-auto px-6 space-y-8 pb-12">
    <!-- Top Bar: Type of Assistance -->
    @if(isset($basicInfo) && $basicInfo)
        <div class="bg-orange-100/80 backdrop-blur-sm border-l-4 border-orange-500 text-orange-700 p-4 rounded-2xl shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-200/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-bold uppercase tracking-wider opacity-70">Type of Assistance</span>
                    <p class="text-lg font-black tracking-tight">{{ $basicInfo->type_assist ? $basicInfo->type_assist : 'Not specified' }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8 items-start">
        <!-- Main Column: Compliance & Uploads -->
        <div class="w-full lg:w-2/3 space-y-8">
            <section class="backdrop-blur-lg bg-white/70 shadow-2xl rounded-[2.5rem] p-8 md:p-10 border border-white/40 flex flex-col space-y-8 select-none transition-all">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <h3 class="text-3xl font-black text-slate-900 flex items-center gap-4 tracking-tight">
                        <div class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-200">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M16 3v4a1 1 0 0 0 1 1h4"/>
                            </svg>
                        </div>
                        Compliance Checklist
                    </h3>

                    @php
                        $totalRequired = count($requiredTypes);
                        $approvedCount = $documents->whereIn('type', array_keys($requiredTypes))->where('status', 'approved')->count();
                        $progressPercent = $totalRequired > 0 ? round(($approvedCount / $totalRequired) * 100) : 0;
                    @endphp

                    <div class="flex items-center gap-4 bg-white/50 backdrop-blur-sm p-2 pr-6 rounded-2xl border border-white shadow-sm">
                        <div class="relative w-12 h-12">
                            <svg class="w-full h-full transform -rotate-90">
                                <circle class="text-slate-200" stroke-width="4" stroke="currentColor" fill="transparent" r="20" cx="24" cy="24" />
                                <circle class="text-orange-500 transition-all duration-1000" stroke-width="4" stroke-dasharray="125.6" stroke-dashoffset="{{ 125.6 * (1 - $progressPercent / 100) }}" stroke-linecap="round" stroke="currentColor" fill="transparent" r="20" cx="24" cy="24" />
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-[10px] font-black text-slate-700">{{ $progressPercent }}%</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-none">Overall Progress</p>
                            <p class="text-sm font-black text-slate-800">{{ $approvedCount }} of {{ $totalRequired }} Verified</p>
                        </div>
                    </div>
                </div>
                
                <!-- File Upload Notice -->
                <div class="bg-blue-50/50 backdrop-blur-sm border-l-4 border-blue-500 p-6 rounded-2xl shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-blue-900 mb-1">File Upload Guidelines</h4>
                            <div class="text-xs text-blue-700 space-y-1">
                                <p><strong>Accepted Formats:</strong> PDF, JPG, JPEG, PNG, GIF</p>
                                <p><strong>Maximum Size:</strong> 10MB per file</p>
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
                <a href="{{ route('documents.view', $uploaded->id) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600/90 text-white rounded-full text-xs font-bold shadow hover:bg-blue-700/90 transition">
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

        <!-- Side Column: Application Tracker -->
        <aside class="w-full lg:w-1/3 space-y-8 sticky top-24">
            <!-- Status Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] p-8 border border-white/40 shadow-2xl overflow-hidden relative group transition-all hover:shadow-orange-200/50">
                <div class="absolute top-0 right-0 p-6">
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-green-100 text-green-700 text-[10px] font-black uppercase tracking-widest border border-green-200 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        {{ $basicInfo->application_status ?? 'Pending' }}
                    </span>
                </div>

                <h4 class="text-xl font-black text-slate-900 mb-8 flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    Application Tracker
                </h4>

                <!-- Stepper -->
                <div class="relative flex justify-between mb-12 px-2">
                    <!-- Progress Line -->
                    <div class="absolute top-4 left-0 w-full h-0.5 bg-slate-100 -z-0"></div>
                    
                    @foreach($trackerSteps as $key => $step)
                        <div class="relative z-10 flex flex-col items-center gap-2">
                            <div class="w-8 h-8 rounded-full {{ $loop->index <= $currentStepIndex ? $step['color'] : 'bg-slate-200' }} border-4 border-white shadow-md flex items-center justify-center transition-all duration-500 {{ $loop->index === $currentStepIndex ? 'ring-4 ring-'.explode('-', $step['color'])[1].'-100 scale-110' : '' }}">
                                @if($loop->index < $currentStepIndex)
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-tighter {{ $loop->index <= $currentStepIndex ? 'text-slate-900' : 'text-slate-400' }}">{{ $step['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Recent Activity -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Recent Activity</h5>
                    </div>
                    
                    @if($isGrantee)
                    <div class="flex gap-4 group/item">
                        <div class="relative flex flex-col items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)] z-10"></div>
                            <div class="w-0.5 h-full bg-slate-100 my-1 absolute top-2"></div>
                        </div>
                        <div class="flex-1 pb-8">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-black text-slate-900 leading-none">Scholarship Granted</span>
                                <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">{{ $basicInfo->updated_at ? $basicInfo->updated_at->format('M d, h:i A') : 'Recently' }}</span>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 leading-relaxed mb-2 line-clamp-2 italic">"Congratulations! You are now an official scholarship grantee."</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-wider border border-blue-100 shadow-sm">Official</span>
                        </div>
                    </div>
                    @endif

                    @if($isStudentValidated)
                    <div class="flex gap-4 group/item">
                        <div class="relative flex flex-col items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] z-10"></div>
                            <div class="w-0.5 h-full bg-slate-100 my-1 absolute top-2"></div>
                        </div>
                        <div class="flex-1 pb-8">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-black text-slate-900 leading-none">Application Validated</span>
                                <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">{{ $basicInfo->updated_at ? $basicInfo->updated_at->format('M d, h:i A') : 'Recently' }}</span>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 leading-relaxed mb-2 line-clamp-2 italic">"Your application has been reviewed and validated."</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-wider border border-emerald-100 shadow-sm">Success</span>
                        </div>
                    </div>
                    @endif

                    @foreach($documents->where('status', 'approved')->sortByDesc('updated_at')->take(4) as $doc)
                    <div class="flex gap-4 group/item">
                        <div class="relative flex flex-col items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/50 group-hover/item:bg-emerald-500 transition-colors z-10"></div>
                            @if(!$loop->last) <div class="w-0.5 h-full bg-slate-100 my-1 absolute top-2"></div> @endif
                        </div>
                        <div class="flex-1 {{ !$loop->last ? 'pb-8' : '' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-black text-slate-900 leading-none">Document Approved</span>
                                <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">{{ $doc->updated_at->format('M d, h:i A') }}</span>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 leading-relaxed mb-2 line-clamp-1 italic">"{{ $requiredTypes[$doc->type] ?? $doc->type }} has been verified."</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-wider border border-emerald-100 shadow-sm">Success</span>
                        </div>
                    </div>
                    @endforeach

                    @if($documents->where('status', 'approved')->count() == 0 && !$isStudentValidated)
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-100">
                                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-slate-400">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </aside>
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
