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
    $acceptancePercent = $acceptancePercent ?? null;
    $acceptancePercentDisplay = max(0, min(100, $acceptancePercent ?? 0));
    $studentPriorityBreakdown = [
        ['label' => 'Priority IP group', 'weight' => '30%', 'met' => $priorityFactors['is_priority_ethno'] ?? false],
        ['label' => 'Priority course', 'weight' => '25%', 'met' => $priorityFactors['is_priority_course'] ?? false],
        ['label' => 'Tribal certificate', 'weight' => '20%', 'met' => $priorityFactors['has_approved_tribal_cert'] ?? false],
        ['label' => 'Income tax document', 'weight' => '15%', 'met' => $priorityFactors['has_approved_income_tax'] ?? false],
        ['label' => 'Academic performance', 'weight' => '5%', 'met' => $priorityFactors['has_approved_grades'] ?? false],
        ['label' => 'Other requirements', 'weight' => '5%', 'met' => $priorityFactors['has_all_other_requirements'] ?? false],
    ];
@endphp

<!-- Academic Performance Header -->
<section
  class="bg-gradient-to-r from-orange-700 to-orange-500 rounded-lg text-white px-8 py-8 flex flex-col md:flex-row md:items-center md:justify-between">
  <div>
    <h2 class="text-3xl font-bold leading-tight">Academic Performance</h2>
    <p class="mt-1 text-orange-200 text-sm">Track your progress and maintain scholarship eligibility</p>
  </div>
  <div class="mt-6 md:mt-0 text-right">
    <p class="font-bold text-lg">Current Semester</p>
    <p class="text-2xl font-extrabold tracking-tight">Fall 2024</p>
  </div>
</section>

<!-- Priority Rank Container -->
<section class="bg-gradient-to-br from-orange-100 to-amber-100 rounded-lg shadow-lg border-2 border-orange-300 p-6 space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-4">
      <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center shadow-lg">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
        </svg>
      </div>
      <div>
        <h3 class="text-xl font-bold text-orange-900">Application Priority Rank</h3>
        <p class="text-sm text-orange-700 mt-1">Your position in the scholarship applicant queue</p>
      </div>
    </div>
    <div class="text-center md:text-right">
      @if(isset($priorityRank) && $priorityRank)
        <div class="inline-block">
          <p class="text-sm font-semibold text-orange-700 mb-1">Current Rank</p>
          <div class="bg-white rounded-xl px-8 py-4 shadow-lg border-2 border-orange-400">
            <p class="text-5xl font-extrabold text-orange-600">#{{ $priorityRank }}</p>
          </div>
          <p class="text-xs text-orange-600 mt-2 font-medium">
            In applicant queue
            @if(($priorityStatistics['total_applicants'] ?? 0) > 0)
              <span class="text-orange-500/80">• {{ $priorityStatistics['total_applicants'] }} total applicants</span>
            @endif
          </p>
        </div>
      @else
        <div class="inline-block">
          <p class="text-sm font-semibold text-orange-700 mb-1">Current Rank</p>
          <div class="bg-white rounded-xl px-8 py-4 shadow-lg border-2 border-orange-300">
            <p class="text-2xl font-bold text-orange-500">Not yet ranked</p>
          </div>
          <p class="text-xs text-orange-600 mt-2 font-medium">Complete application to be ranked</p>
        </div>
      @endif
    </div>
  </div>

  <!-- Acceptance Likelihood -->
  <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Scholarship acceptance likelihood</p>
        <p class="mt-2 text-4xl font-bold text-slate-900">
          @if(!is_null($acceptancePercent))
            {{ $acceptancePercentDisplay }}%
          @else
            Pending
          @endif
        </p>
        <p class="text-sm text-slate-600 mt-2">
          Based on the same priority weights staff apply (Priority IP 30%, Courses 25%, Tribal Documents 20%, Income Tax 15%, Academics 5%, Other Requirements 5%).
        </p>
        @if(is_null($acceptancePercent))
          <p class="mt-3 text-sm font-semibold text-amber-600">Submit and have your documents approved to generate your percentage.</p>
        @endif
      </div>
      <div class="w-full max-w-md">
        <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
          <span>Completion progress</span>
          @if(isset($priorityRank) && $priorityRank)
            <span>#{{ $priorityRank }} in queue</span>
          @else
            <span>Rank pending</span>
          @endif
        </div>
        <div class="h-3 w-full rounded-full bg-slate-100 overflow-hidden">
          <div class="h-full rounded-full bg-gradient-to-r from-sky-400 via-indigo-500 to-blue-600 transition-all duration-500" style="width: {{ $acceptancePercentDisplay }}%;"></div>
        </div>
      </div>
    </div>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
      @foreach($studentPriorityBreakdown as $factor)
        @php
          $isBinaryStatus = in_array($factor['label'], ['Priority IP group', 'Priority course']);
          $statusLabel = $factor['met']
              ? 'Met'
              : ($isBinaryStatus ? 'Not met' : 'Pending');
        @endphp
        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
          <div>
            <p class="text-sm font-semibold text-slate-800">{{ $factor['label'] }}</p>
            <p class="text-xs text-slate-500">{{ $factor['weight'] }} weight</p>
          </div>
          @if($factor['met'])
            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
              {{ $statusLabel }}
            </span>
          @else
            @if($statusLabel === 'Not met')
              <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ $statusLabel }}
              </span>
            @else
              <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3" /></svg>
                {{ $statusLabel }}
              </span>
            @endif
          @endif
        </div>
      @endforeach
    </div>
  </section>
</section>
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

<!-- Current Academic Performance and Quick Stats -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <!-- Left: Current Academic Performance -->
  <section
    class="bg-white shadow rounded-lg p-6 col-span-2 space-y-6 border border-gray-200">

    <div class="flex justify-between items-center">
      <h3 class="font-semibold text-lg text-gray-900">Current Academic Performance</h3>
      <span
        class="text-green-600 bg-green-100 px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap select-none">Eligible</span>
    </div>

    <!-- GPA, Credits Enrolled, Total Credits -->
    <div
      class="grid grid-cols-1 sm:grid-cols-3 border border-gray-100 rounded-lg overflow-hidden text-center divide-x divide-gray-100">
      <div class="p-6">
        <p class="text-4xl font-extrabold text-blue-600">3.85</p>
        <p class="font-semibold text-sm text-gray-700 mt-1">Current GPA</p>
        <p class="mt-2 text-green-600 text-xs">+0.15 from last semester</p>
      </div>
      <div class="p-6">
        <p class="text-4xl font-extrabold text-blue-700">18</p>
        <p class="font-semibold text-sm text-gray-700 mt-1">Credits Enrolled</p>
        <a href="#" class="text-blue-600 text-xs mt-2 inline-block hover:underline">Full-time status</a>
      </div>
      <div class="p-6">
        <p class="text-4xl font-extrabold text-purple-700">75</p>
        <p class="font-semibold text-sm text-gray-700 mt-1">Total Credits</p>
        <p class="mt-2 text-purple-600 text-xs">62.5% complete</p>
      </div>
    </div>

    <!-- GPA Progress Bar -->
    <div class="pt-2 w-full">
      <div class="flex justify-between text-xs text-gray-500 mb-1 px-1">
        <span>GPA Progress</span>
        <span>Target: 3.5</span>
      </div>
      <progress class="w-full h-3 rounded bg-blue-100" value="3.85" max="4.0"></progress>
    </div>

    <!-- Academic Standing -->
    <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-md text-sm">
      <div class="flex items-center space-x-2 font-semibold">
        <svg class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path d="M9 12l2 2 4-4"></path>
        </svg>
        <span>Excellent Academic Standing</span>
      </div>
      <p class="mt-1 text-green-800 text-sm">Your GPA of 3.85 exceeds the minimum requirement of 3.5. You are currently
        eligible for all scholarship opportunities and maintaining good academic progress.</p>
    </div>
  </section>

  <!-- Right: Quick Stats -->
  <section
    class="bg-white shadow rounded-lg p-6 border border-gray-200 max-w-md mx-auto md:mx-0 flex flex-col space-y-4">
    <h3 class="font-semibold text-lg text-gray-900">Quick Stats</h3>
    <dl class="space-y-3">
      <div class="flex justify-between items-center">
        <dt>Scholarship Status</dt>
        <dd><span
            class="bg-green-100 text-green-700 rounded-full px-3 py-1 text-xs font-semibold select-none">Active</span></dd>
      </div>
      <div class="flex justify-between items-center">
        <dt>Academic Warning</dt>
        <dd class="flex items-center space-x-1 text-green-700 font-semibold text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
            stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path d="M20 6L9 17l-5-5"></path>
          </svg>
          <span>None</span>
        </dd>
      </div>
      <div class="flex justify-between items-center">
        <dt>Probation Status</dt>
        <dd class="flex items-center space-x-1 text-green-700 font-semibold text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
            stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path d="M20 6L9 17l-5-5"></path>
          </svg>
          <span>Clear</span>
        </dd>
      </div>
      <div class="flex justify-between items-center">
        <dt>Graduation Track</dt>
        <dd><a href="#" class="text-blue-600 font-semibold hover:underline select-none">On Track</a></dd>
      </div>
    </dl>
  </section>
</section>

<!-- Semester-by-Semester Breakdown Table -->
<section
  class="bg-white shadow rounded-lg p-6 border border-gray-200 max-w-4xl mx-auto overflow-x-auto scroll-px-6 scroll-py-2">
  <h3 class="font-semibold text-lg text-gray-900 mb-6">Semester-by-Semester Breakdown</h3>
  <table class="w-full min-w-[600px] whitespace-nowrap text-left text-gray-700">
    <thead class="border-b border-gray-300">
      <tr>
        <th class="pb-2">Semester</th>
        <th class="pb-2">GPA</th>
        <th class="pb-2">Credits</th>
        <th class="pb-2">Status</th>
        <th class="pb-2">Remarks</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      <tr>
        <td class="py-3">
          <p class="font-semibold">Fall 2024</p>
          <p class="text-xs text-gray-500">Current</p>
        </td>
        <td class="py-3 text-blue-700 font-semibold">3.85</td>
        <td class="py-3">18</td>
        <td class="py-3">
          <span
            class="bg-green-100 text-green-700 text-xs font-semibold rounded-full px-3 py-1 select-none whitespace-nowrap">In Progress</span>
        </td>
        <td class="py-3">Excellent performance</td>
      </tr>
      <tr>
        <td class="py-3">
          <p class="font-semibold">Spring 2024</p>
          <p class="text-xs text-gray-500">Completed</p>
        </td>
        <td class="py-3 text-green-700 font-semibold">3.70</td>
        <td class="py-3">15</td>
        <td class="py-3">
          <span
            class="bg-green-100 text-green-700 text-xs font-semibold rounded-full px-3 py-1 select-none whitespace-nowrap">Completed</span>
        </td>
        <td class="py-3">Good academic standing</td>
      </tr>
      <tr>
        <td class="py-3">
          <p class="font-semibold">Fall 2023</p>
          <p class="text-xs text-gray-500">Completed</p>
        </td>
        <td class="py-3 text-blue-700 font-semibold">3.65</td>
        <td class="py-3">16</td>
        <td class="py-3">
          <span
            class="bg-green-100 text-green-700 text-xs font-semibold rounded-full px-3 py-1 select-none whitespace-nowrap">Completed</span>
        </td>
        <td class="py-3">Satisfactory progress</td>
      </tr>
      <tr>
        <td class="py-3">
          <p class="font-semibold">Spring 2023</p>
          <p class="text-xs text-gray-500">Completed</p>
        </td>
        <td class="py-3 text-yellow-700 font-semibold">3.45</td>
        <td class="py-3">14</td>
        <td class="py-3">
          <span
            class="bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full px-3 py-1 select-none whitespace-nowrap">Completed</span>
        </td>
        <td class="py-3">Below target, improvement needed</td>
      </tr>
    </tbody>
  </table>
</section>

<!-- Event Participation & Attendance -->
<!-- Compliance Checklist & Upload Documents (Glassmorphism Centered) -->
<div class="flex justify-center items-start w-full py-12">
  <section class="backdrop-blur-lg bg-white/60 shadow-2xl rounded-3xl p-10 border border-white/30 max-w-5xl w-full flex flex-col space-y-10 select-none transition-all">
    <h3 class="text-3xl font-black text-gray-900 mb-4 flex items-center gap-3 tracking-tight drop-shadow">
      <svg class="w-9 h-9 text-orange-500" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M16 3v4a1 1 0 0 0 1 1h4"/></svg>
      Compliance Checklist & Uploads
    </h3>
    
    <!-- PDF Only Notice -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg mb-6">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div class="ml-3">
          <h4 class="text-sm font-medium text-blue-800">Important Notice</h4>
          <div class="mt-1 text-sm text-blue-700">
            <p><strong>PDF Files Only:</strong> All documents must be uploaded in PDF format. Please convert your documents to PDF before uploading.</p>
            <p class="mt-1"><strong>Maximum Size:</strong> 10MB per file</p>
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
    <ul class="space-y-7">
      @foreach($requiredTypes as $typeKey => $typeLabel)
        @php
          $uploaded = $documents->firstWhere('type', $typeKey);
          $status = $uploaded ? $uploaded->status : 'missing';
        @endphp
        <li class="bg-gradient-to-br from-orange-100/80 via-amber-100/60 to-white/60 border border-orange-200/60 rounded-2xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-5 shadow-lg hover:shadow-2xl transition-all">
          <div class="flex items-center gap-5 min-w-0">
            @if($status === 'approved')
              <span class="w-12 h-12 rounded-full bg-green-500/90 flex items-center justify-center text-white text-2xl font-black shadow-lg border-4 border-white/40">✓</span>
            @elseif($status === 'pending')
              <span class="w-12 h-12 rounded-full bg-yellow-400/90 flex items-center justify-center text-white text-2xl font-black shadow-lg border-4 border-white/40">!</span>
            @else
              <span class="w-12 h-12 rounded-full bg-red-500/90 flex items-center justify-center text-white text-2xl font-black shadow-lg border-4 border-white/40">×</span>
            @endif
            <div class="flex flex-col min-w-0">
              <span class="font-extrabold text-lg text-gray-900 truncate tracking-tight">{{ $typeLabel }}</span>
              <div class="flex items-center gap-2 mt-1">
                @if($status === 'approved')
                  <span class="bg-green-200/80 text-green-900 px-3 py-0.5 rounded-full text-xs font-bold shadow">Approved</span>
                @elseif($status === 'pending')
                  <span class="bg-yellow-200/80 text-yellow-900 px-3 py-0.5 rounded-full text-xs font-bold shadow">Pending</span>
                @else
                  <span class="bg-red-200/80 text-red-900 px-3 py-0.5 rounded-full text-xs font-bold shadow">Missing</span>
                @endif
                @if($uploaded)
                  <span class="text-gray-400 text-xs">• Uploaded {{ $uploaded->created_at->diffForHumans() }}</span>
                @endif
              </div>
            </div>
          </div>
          <div class="flex flex-col md:items-end gap-2 w-full md:w-auto">
            @if($uploaded)
              <a href="{{ asset('storage/' . $uploaded->filepath) }}" target="_blank" class="inline-block px-5 py-2 bg-blue-600/90 text-white rounded-full text-xs font-bold shadow hover:bg-blue-700/90 transition">View</a>
            @endif
            @if(!$uploaded || ($uploaded && $uploaded->status === 'rejected'))
              <form method="POST" action="{{ route('documents.upload') }}" enctype="multipart/form-data" class="doc-upload-form flex items-center gap-2 w-full md:w-auto" data-type="{{ $typeKey }}" data-label="{{ $typeLabel }}">
                @csrf
                <input type="hidden" name="type" value="{{ $typeKey }}">
                <div class="flex flex-col gap-1">
                  <input type="file" name="upload-file" required class="doc-file-input block text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:bg-orange-50/80 file:text-orange-700 hover:file:bg-orange-100/80 focus:outline-none focus:ring-2 focus:ring-orange-400/60" accept=".pdf">
                  <span class="text-xs text-gray-500">Select file then use “Upload All”</span>
                </div>
              </form>
            @endif
          </div>
        </li>
      @endforeach
    </ul>
  </section>
</div>


<!-- System-Generated Feedback -->
<section class="bg-white shadow rounded-lg p-6 border border-gray-200 max-w-4xl mx-auto select-none">
  <h3 class="font-semibold text-lg text-gray-900 mb-4">System-Generated Feedback</h3>

  <div class="space-y-4">
    <article class="border border-green-200 bg-green-50 rounded-md p-4 text-green-700">
      <div class="flex items-center space-x-2 font-semibold">
        <svg class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path d="M9 12l2 2 4-4"></path>
        </svg>
        <h4>Excellent Academic Performance</h4>
      </div>
      <p class="text-sm mt-1">Your GPA of 3.85 demonstrates strong academic commitment. You're exceeding the minimum
        requirement and maintaining excellent standing for scholarship renewal.</p>
    </article>

    <article class="border border-blue-300 bg-blue-100 rounded-md p-4 text-blue-700">
      <div class="flex items-center space-x-2 font-semibold">
        <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <circle cx="12" cy="12" r="10" />
          <line x1="12" y1="16" x2="12" y2="12" />
          <line x1="12" y1="8" x2="12" y2="8" />
        </svg>
        <h4>Action Required: Community Service Log</h4>
      </div>
      <p class="text-sm mt-1 ml-7">Please submit your community service log by December 15, 2024. This is required to
        maintain full compliance with scholarship requirements.</p>
    </article>

    <article class="border border-yellow-300 bg-yellow-50 rounded-md p-4 text-yellow-800">
      <div class="flex items-center space-x-2 font-semibold">
        <svg class="w-5 h-5 flex-shrink-0 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
          <line x1="12" y1="9" x2="12" y2="13" />
          <line x1="12" y1="17" x2="12" y2="17" />
        </svg>
        <h4>Recommendation: Financial Aid Form</h4>
      </div>
      <p class="text-sm mt-1 ml-7">Consider submitting your financial aid form early for the next semester to ensure
        timely processing and avoid delays in scholarship disbursement.</p>
    </article>
  </div>
</section>
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

    for (const form of formsToUpload) {
      const formData = new FormData(form);
      try {
        const response = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: formData,
        });

        if (!response.ok) {
          hasErrors = true;
        }
      } catch (error) {
        hasErrors = true;
      }
    }

    if (hasErrors) {
      alert('Some documents failed to upload. Please review the selections and try again.');
    } else {
      alert('All selected documents were uploaded successfully!');
    }

    window.location.reload();
    uploadAllBtn.innerHTML = originalText;
  });
});
</script>
@endpush