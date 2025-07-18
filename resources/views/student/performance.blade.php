<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance - IP Scholar Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 min-h-screen">
    <!-- Enhanced Navigation Header -->
    <nav x-data="{ open: false }" class="shadow-2xl sticky top-0 z-50 bg-black/20 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo and Brand -->
                <div class="flex items-center space-x-4">
                    <div class="text-2xl font-bold">
                        <span class="text-orange-400">IndiGenSys</span>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('student.dashboard') }}" class="text-gray-600 hover:text-orange-600 transition">Home</a>
                    <a href="{{ route('student.profile') }}" class="text-gray-600 hover:text-orange-600 transition">Profile</a>
                    <a href="{{ route('student.performance') }}" class="text-gray-600 hover:text-orange-600 transition">Performance</a>
                    <a href="{{ route('student.notifications') }}" class="text-gray-600 hover:text-orange-600 transition">Notification</a>
                    <a href="{{ route('student.support') }}" class="text-gray-600 hover:text-orange-600 transition">Support/Help</a>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                        @guest
                            <a href="{{ url('/auth') }}" class="px-6 py-2 border border-orange-500 text-orange-400 rounded-lg hover:bg-orange-700 hover:text-white transition-all">Login</a>
                            <a href="{{ url('/auth') }}" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-700 transition-all glow-effect">Sign Up</a>
                        @endguest
                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="px-6 py-2 border border-orange-500 text-orange-400 rounded-lg hover:bg-orange-700 hover:text-white transition-all">Log Out</button>
                            </form>
                        @endauth
                </div>
                <!-- Hamburger Button (Mobile) -->
                <button @click="open = !open" class="md:hidden text-orange-400 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="open" @click.away="open = false" class="md:hidden bg-black/90 backdrop-blur-md border-t border-white/10 px-6 py-4">
            <div class="flex flex-col space-y-4">
                <a href="{{ route('student.dashboard') }}" class="text-white hover:text-orange-400 transition-colors">Home</a>
                <a href="{{ route('student.profile') }}" class="text-white hover:text-orange-400 transition-colors">Profile</a>
                <a href="{{ route('student.performance') }}" class="text-white hover:text-orange-400 transition-colors">Performance</a>
                <a href="{{ route('student.notifications') }}" class="text-white hover:text-orange-400 transition-colors">Notification</a>
                <a href="{{ route('student.support') }}" class="text-white hover:text-orange-400 transition-colors">Support/Help</a>
                @guest
                    <a href="{{ url('/auth') }}" class="px-6 py-2 border border-orange-500 text-orange-400 rounded-lg hover:bg-orange-700 hover:text-white transition-all">Login</a>
                    <a href="{{ url('/auth') }}" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-700 transition-all glow-effect">Sign Up</a>
                @endguest
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-0 text-orange-400 hover:text-white transition-all">Log Out</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>
     <!-- Main Content Wrapper -->
  <main class="max-w-7xl mx-auto px-6 py-8 space-y-8">

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
<section
  class="bg-white shadow rounded-lg p-6 border border-gray-200 max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
  <!-- Left grid col spanning 2 for events -->
  <div class="col-span-1 md:col-span-2 space-y-5">
    <h3 class="font-semibold text-lg text-gray-900">Event Participation &amp; Attendance</h3>
    <div
      class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm text-gray-700 font-normal select-none max-w-full">
      <!-- Cards -->
      <div
        class="rounded-lg border border-gray-200 p-4 flex flex-col justify-between cursor-default hover:shadow-md transition duration-150">
        <div>
          <p class="font-semibold text-gray-900 mb-1">Indigenous Cultural Workshop</p>
          <p class="mb-2 text-xs text-gray-600">Cultural heritage preservation workshop</p>
        </div>
        <div class="flex justify-between items-center text-xs text-gray-600">
          <time datetime="2024-10-15">October 15, 2024</time>
          <span>2 hours</span>
        </div>
        <span
          class="mt-4 inline-block bg-green-100 text-green-700 rounded-md text-xs font-semibold px-2 py-1 select-none w-max">Attended</span>
      </div>

      <div
        class="rounded-lg border border-gray-200 p-4 flex flex-col justify-between cursor-default hover:shadow-md transition duration-150">
        <div>
          <p class="font-semibold text-gray-900 mb-1">Academic Success Seminar</p>
          <p class="mb-2 text-xs text-gray-600">Study strategies and time management</p>
        </div>
        <div class="flex justify-between items-center text-xs text-gray-600">
          <time datetime="2024-09-28">September 28, 2024</time>
          <span>3 hours</span>
        </div>
        <span
          class="mt-4 inline-block bg-green-100 text-green-700 rounded-md text-xs font-semibold px-2 py-1 select-none w-max">Attended</span>
      </div>

      <div
        class="rounded-lg border border-gray-200 p-4 flex flex-col justify-between cursor-default hover:shadow-md transition duration-150">
        <div>
          <p class="font-semibold text-gray-900 mb-1">Community Service Day</p>
          <p class="mb-2 text-xs text-gray-600">Local community outreach program</p>
        </div>
        <div class="flex justify-between items-center text-xs text-gray-600">
          <time datetime="2024-12-10">December 10, 2024</time>
          <span>4 hours</span>
        </div>
        <span
          class="mt-4 inline-block bg-yellow-100 text-yellow-700 rounded-md text-xs font-semibold px-2 py-1 select-none w-max">Scheduled</span>
      </div>

      <div
        class="rounded-lg border border-gray-200 p-4 flex flex-col justify-between cursor-default hover:shadow-md transition duration-150">
        <div>
          <p class="font-semibold text-gray-900 mb-1">Leadership Development</p>
          <p class="mb-2 text-xs text-gray-600">Student leadership training program</p>
        </div>
        <div class="flex justify-between items-center text-xs text-gray-600">
          <time datetime="2025-01-15">January 15, 2025</time>
          <span>6 hours</span>
        </div>
        <span
          class="mt-4 inline-block bg-blue-100 text-blue-700 rounded-md text-xs font-semibold px-2 py-1 select-none w-max">Registered</span>
      </div>

      <div
        class="rounded-lg border border-gray-200 p-4 flex flex-col justify-between cursor-default hover:shadow-md transition duration-150">
        <div>
          <p class="font-semibold text-gray-900 mb-1">Mentorship Program</p>
          <p class="mb-2 text-xs text-gray-600">Ongoing mentorship with industry professional</p>
        </div>
        <div class="flex justify-between items-center text-xs text-gray-600">
          <time datetime="2024-01-01">Ongoing</time>
          <span>Monthly</span>
        </div>
        <span
          class="mt-4 inline-block bg-green-100 text-green-700 rounded-md text-xs font-semibold px-2 py-1 select-none w-max">Active</span>
      </div>

      <div
        class="rounded-lg border border-gray-200 p-4 flex flex-col justify-between cursor-default hover:shadow-md transition duration-150">
        <div>
          <p class="font-semibold text-gray-900 mb-1">Career Fair</p>
          <p class="mb-2 text-xs text-gray-600">Annual career and internship fair</p>
        </div>
        <div class="flex justify-between items-center text-xs text-gray-600">
          <time datetime="2024-11-05">November 5, 2024</time>
          <span>4 hours</span>
        </div>
        <span
          class="mt-4 inline-block bg-gray-100 text-gray-700 rounded-md text-xs font-semibold px-2 py-1 select-none w-max">Missed</span>
      </div>
    </div>

    <!-- Summary stats -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-4 gap-4 text-center text-sm font-semibold rounded-lg">
      <div class="bg-green-100 text-green-700 p-4 rounded">
        <p class="text-3xl">4</p>
        <p>Events Attended</p>
      </div>
      <div class="bg-blue-100 text-blue-700 p-4 rounded">
        <p class="text-3xl">2</p>
        <p>Events Scheduled</p>
      </div>
      <div class="bg-yellow-100 text-yellow-800 p-4 rounded">
        <p class="text-3xl">1</p>
        <p>Events Missed</p>
      </div>
      <div class="bg-purple-100 text-purple-700 p-4 rounded">
        <p class="text-3xl">12</p>
        <p>Total Hours</p>
      </div>
    </div>
  </div>

  <!-- Right: Compliance Checklist & Upload Documents -->
  <aside class="space-y-6">
    <!-- Compliance Checklist -->
    <section
      class="bg-white shadow rounded-lg p-6 border border-gray-200 max-w-md w-full flex flex-col space-y-6 select-none">
      <h3 class="font-semibold text-lg text-gray-900">Compliance Checklist</h3>
      <ul class="space-y-4 text-sm text-gray-700">
        <li
          class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3 flex justify-between items-center">
          <div class="flex items-start space-x-3">
            <span
              class="flex-shrink-0 w-6 h-6 rounded-md bg-green-600 text-white flex items-center justify-center text-xs font-bold select-none">✓</span>
            <div>
              <p class="font-semibold leading-tight">Grade Report</p>
              <p class="text-xs text-green-900">Fall 2024 grades submitted</p>
            </div>
          </div>
          <span class="text-green-600 font-semibold text-xs whitespace-nowrap">Complete</span>
        </li>
        <li
          class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3 flex justify-between items-center">
          <div class="flex items-start space-x-3">
            <span
              class="flex-shrink-0 w-6 h-6 rounded-md bg-green-600 text-white flex items-center justify-center text-xs font-bold select-none">✓</span>
            <div>
              <p class="font-semibold leading-tight">Certificate of Enrollment</p>
              <p class="text-xs text-green-900">Current semester enrollment verified</p>
            </div>
          </div>
          <span class="text-green-600 font-semibold text-xs whitespace-nowrap">Complete</span>
        </li>
        <li
          class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg p-3 flex justify-between items-center">
          <div class="flex items-start space-x-3">
            <span
              class="flex-shrink-0 w-6 h-6 rounded-md bg-yellow-400 text-yellow-900 flex items-center justify-center text-xs font-bold select-none">!</span>
            <div>
              <p class="font-semibold leading-tight">Community Service Log</p>
              <p class="text-xs text-yellow-900">Due by December 15, 2024</p>
            </div>
          </div>
          <span class="text-yellow-700 font-semibold text-xs whitespace-nowrap">Pending</span>
        </li>
        <li
          class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 flex justify-between items-center">
          <div class="flex items-start space-x-3">
            <span
              class="flex-shrink-0 w-6 h-6 rounded-md bg-red-600 text-white flex items-center justify-center text-xs font-bold select-none">×</span>
            <div>
              <p class="font-semibold leading-tight">Financial Aid Form</p>
              <p class="text-xs text-red-900">Required for next semester</p>
            </div>
          </div>
          <span class="text-red-600 font-semibold text-xs whitespace-nowrap">Missing</span>
        </li>
        <li
          class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3 flex justify-between items-center">
          <div class="flex items-start space-x-3">
            <span
              class="flex-shrink-0 w-6 h-6 rounded-md bg-green-600 text-white flex items-center justify-center text-xs font-bold select-none">✓</span>
            <div>
              <p class="font-semibold leading-tight">Academic Plan</p>
              <p class="text-xs text-green-900">Updated for current semester</p>
            </div>
          </div>
          <span class="text-green-600 font-semibold text-xs whitespace-nowrap">Complete</span>
        </li>
      </ul>

      <!-- Overall Compliance -->
      <div class="bg-blue-50 border border-blue-200 p-4 rounded-md">
        <div class="flex justify-between font-semibold text-blue-700 mb-1 text-sm select-none">
          <span>Overall Compliance</span>
          <span>80%</span>
        </div>
        <div class="w-full bg-blue-100 rounded-full h-2 overflow-hidden">
          <div class="bg-blue-600 h-2 rounded-full w-[80%]"></div>
        </div>
      </div>
    </section>

    <!-- Upload Documents -->
    <section
      class="bg-white shadow rounded-lg p-6 border border-gray-200 max-w-md w-full flex flex-col space-y-4 select-none">
      <h3 class="font-semibold text-lg text-gray-900">Upload Documents</h3>

      <label for="upload-file"
        class="flex flex-col items-center justify-center border-2 border-dashed border-gray-400 rounded-lg cursor-pointer p-8 text-gray-500 hover:border-gray-600 hover:text-gray-700 transition">
        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
          <polyline points="17 8 12 3 7 8"></polyline>
          <line x1="12" y1="3" x2="12" y2="15"></line>
        </svg>
        <span class="text-sm font-medium">Upload a file</span>
        <span class="text-xs mt-0.5">PDF, DOC, or image files up to 10MB</span>
        <input id="upload-file" type="file" class="hidden" />
      </label>

      <div class="text-sm text-gray-600">
        <p class="font-semibold mb-2">Recent Uploads</p>
        <ul class="space-y-2">
          <li
            class="flex items-center justify-between bg-gray-100 rounded-md px-3 py-2 text-gray-800 hover:bg-gray-200 transition cursor-default">
            <div class="flex items-center space-x-3">
              <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 24 24"
                aria-hidden="true" focusable="false">
                <path
                  d="M14.7 2.1H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.3L14.7 2.1zM12 19a1.5 1.5 0 1 1 1.501-1.5A1.5 1.5 0 0 1 12 19zm2.5-7.5h-5v-1h5z" />
              </svg>
              <div>
                <p class="font-semibold">Fall_2024_Grades.pdf</p>
                <p class="text-xs text-gray-500">Uploaded 2 days ago</p>
              </div>
            </div>
            <a href="#" class="text-blue-600 hover:underline hover:text-blue-800">View</a>
          </li>
          <li
            class="flex items-center justify-between bg-gray-100 rounded-md px-3 py-2 text-gray-800 hover:bg-gray-200 transition cursor-default">
            <div class="flex items-center space-x-3">
              <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="currentColor" viewBox="0 0 24 24"
                aria-hidden="true" focusable="false">
                <path
                  d="M19 2H9a2 2 0 0 0-2 2v2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-3h2a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM9 6h7v7h-2v-1a2 2 0 0 0-2-2H9z" />
              </svg>
              <div>
                <p class="font-semibold">Enrollment_Certificate.pdf</p>
                <p class="text-xs text-gray-500">Uploaded 1 week ago</p>
              </div>
            </div>
            <a href="#" class="text-blue-600 hover:underline hover:text-blue-800">View</a>
          </li>
        </ul>
      </div>
    </section>
  </aside>
</section>

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
</body>

</html> 