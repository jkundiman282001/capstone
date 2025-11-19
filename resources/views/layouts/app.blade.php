<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NCIP Staff Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-indigo-50 min-h-screen font-sans">
    <!-- Header -->
    <header class="bg-indigo-600 shadow-xl border-b border-indigo-400/20">
        <div class="container mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <img src="{{ asset('National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" alt="NCIP Logo" class="h-12 w-12 rounded-xl bg-white p-1.5 shadow-lg ring-2 ring-white/50">
                </div>
                <div class="flex flex-col">
                    <span class="text-white text-xl sm:text-2xl font-bold tracking-tight">NCIP Scholarship System</span>
                    <span class="text-indigo-100 text-xs sm:text-sm font-medium">Staff Portal</span>
                </div>
            </div>
            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden text-white hover:text-indigo-100 p-2 rounded-lg hover:bg-white/10 transition-colors" onclick="toggleSidebar()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </header>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-72 bg-white shadow-2xl border-r border-indigo-200/50 hidden md:block fixed md:static h-screen md:h-auto z-40 overflow-y-auto">
            <nav class="flex flex-col h-full py-6 px-4 space-y-6">
                <!-- Main Navigation -->
                <section>
                    <div class="flex items-center px-4 mb-4">
                        <div class="h-px flex-1 bg-indigo-200"></div>
                        <h2 class="px-3 text-xs font-bold uppercase tracking-widest text-indigo-600 bg-indigo-50 rounded-lg py-1">Main</h2>
                        <div class="h-px flex-1 bg-indigo-200"></div>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('staff.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl text-indigo-700 bg-indigo-100 font-semibold shadow-md hover:shadow-lg transition-all duration-200 border-l-4 border-indigo-600 group">
                            <svg class="w-5 h-5 mr-3 text-indigo-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0H7m6 0v6m0 0H7m6 0h6"></path>
                            </svg>
                            Dashboard Overview
                        </a>
                        <a href="{{ route('staff.applicants.list') }}" class="flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 transition-all duration-200 group border-l-4 border-transparent hover:border-indigo-400">
                            <svg class="w-5 h-5 mr-3 text-gray-500 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            View Applicants
                        </a>
                        <a href="{{ route('staff.reports.download') }}" class="flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 transition-all duration-200 group border-l-4 border-transparent hover:border-indigo-400">
                            <svg class="w-5 h-5 mr-3 text-gray-500 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Reports
                        </a>
                        <a href="#feedback-section" class="flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 transition-all duration-200 group border-l-4 border-transparent hover:border-indigo-400">
                            <svg class="w-5 h-5 mr-3 text-gray-500 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Feedback & Support
                        </a>
                        <form method="POST" action="{{ route('staff.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-rose-50 transition-all duration-200 group border-l-4 border-transparent hover:border-rose-400">
                                <svg class="w-5 h-5 mr-3 text-gray-500 group-hover:text-rose-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </section>

                <!-- Priorities Section -->
                <section>
                    <div class="flex items-center px-4 mb-4">
                        <div class="h-px flex-1 bg-purple-200"></div>
                        <h2 class="px-3 text-xs font-bold uppercase tracking-widest text-purple-600 bg-purple-50 rounded-lg py-1">Priorities</h2>
                        <div class="h-px flex-1 bg-purple-200"></div>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('staff.priorities.applicants') }}" class="flex items-center px-4 py-3 rounded-xl text-emerald-700 hover:bg-emerald-50 transition-all duration-200 group border-l-4 border-transparent hover:border-emerald-400">
                            <svg class="w-5 h-5 mr-3 text-emerald-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Applicant Priority
                        </a>
                        <a href="{{ route('staff.priorities.documents') }}" class="flex items-center px-4 py-3 rounded-xl text-cyan-700 hover:bg-cyan-50 transition-all duration-200 group border-l-4 border-transparent hover:border-cyan-400">
                            <svg class="w-5 h-5 mr-3 text-cyan-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Document Queue
                        </a>
                        <a href="{{ route('staff.priorities.ip') }}" class="flex items-center px-4 py-3 rounded-xl text-amber-700 hover:bg-amber-50 transition-all duration-200 group border-l-4 border-transparent hover:border-amber-400">
                            <svg class="w-5 h-5 mr-3 text-amber-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Indigenous Priority
                        </a>
                        <a href="{{ route('staff.priorities.tribal-certificate') }}" class="flex items-center px-4 py-3 rounded-xl text-orange-700 hover:bg-orange-50 transition-all duration-200 group border-l-4 border-transparent hover:border-orange-400">
                            <svg class="w-5 h-5 mr-3 text-orange-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Tribal Certificate
                        </a>
                        <a href="{{ route('staff.priorities.courses') }}" class="flex items-center px-4 py-3 rounded-xl text-purple-700 hover:bg-purple-50 transition-all duration-200 group border-l-4 border-transparent hover:border-purple-400">
                            <svg class="w-5 h-5 mr-3 text-purple-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Course Demand
                        </a>
                    </div>
                </section>
            </nav>
        </aside>
        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-30 md:hidden" onclick="toggleSidebar()"></div>
        <!-- Main Content -->
        <main class="flex-1 min-h-screen">
            <div class="p-0">
                @yield('content')
            </div>
        </main>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('hidden');
            overlay.classList.toggle('hidden');
        }
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const btn = document.getElementById('mobile-menu-btn');
            const overlay = document.getElementById('sidebar-overlay');
            if (window.innerWidth < 768 && !sidebar.contains(event.target) && !btn.contains(event.target) && !overlay.classList.contains('hidden')) {
                toggleSidebar();
            }
        });
    </script>
    @stack('scripts')
</body>
</html> 