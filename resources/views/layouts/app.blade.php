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
<body class="bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 min-h-screen font-sans">
    <!-- Header -->
    <header class="bg-gradient-to-r from-orange-700 via-orange-600 to-orange-500 shadow-2xl border-b border-white/10 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
        </div>
        
        <div class="container mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8 py-5 relative z-10">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-white/20 rounded-2xl blur-md"></div>
                    <img src="{{ asset('National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" alt="NCIP Logo" class="relative h-14 w-14 rounded-2xl bg-white p-2 shadow-2xl ring-4 ring-white/30">
                </div>
                <div class="flex flex-col">
                    <span class="text-white text-xl sm:text-2xl font-black tracking-tight">NCIP-EAP</span>
                    <span class="text-orange-100 text-xs sm:text-sm font-bold tracking-wide">Admin</span>
                </div>
            </div>
            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden text-white hover:text-white p-3 rounded-xl hover:bg-white/15 transition-all backdrop-blur-sm border border-white/20 shadow-lg" onclick="toggleSidebar()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </header>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-80 bg-gradient-to-b from-slate-50 to-white shadow-2xl border-r border-slate-200 hidden md:block fixed md:static h-screen md:h-auto z-40 overflow-y-auto">
            <nav class="flex flex-col h-full py-8 px-5 space-y-8">
                <!-- Main Navigation -->
                <section>
                    <div class="mb-5">
                        <h2 class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Main Menu</h2>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('staff.dashboard') }}" class="group block px-5 py-4 rounded-2xl {{ request()->routeIs('staff.dashboard') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-xl shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <span class="text-sm tracking-wide">Dashboard Overview</span>
                                <div class="w-2 h-2 rounded-full {{ request()->routeIs('staff.dashboard') ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                            </div>
                        </a>
                        <a href="{{ route('staff.applicants.list') }}" class="group block px-5 py-4 rounded-2xl {{ request()->routeIs('staff.applicants.*') || request()->routeIs('staff.applications.*') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-xl shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <span class="text-sm tracking-wide">View Applicants</span>
                                <div class="w-2 h-2 rounded-full {{ request()->routeIs('staff.applicants.*') || request()->routeIs('staff.applications.*') ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                            </div>
                        </a>
                        <a href="{{ route('staff.reports.download') }}" class="group block px-5 py-4 rounded-2xl {{ request()->routeIs('staff.reports.*') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-xl shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <span class="text-sm tracking-wide">Download Reports</span>
                                <div class="w-2 h-2 rounded-full {{ request()->routeIs('staff.reports.*') ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                            </div>
                        </a>
                        <a href="#feedback-section" class="group block px-5 py-4 rounded-2xl bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <span class="text-sm tracking-wide">Feedback & Support</span>
                                <div class="w-2 h-2 rounded-full bg-slate-300 group-hover:bg-orange-500 transition-colors"></div>
                            </div>
                        </a>
                        <form method="POST" action="{{ route('staff.logout') }}" class="inline w-full">
                            @csrf
                            <button type="submit" class="group w-full px-5 py-4 rounded-2xl bg-white hover:bg-gradient-to-r hover:from-rose-50 hover:to-red-50 border-2 border-slate-200 hover:border-rose-300 font-semibold text-slate-700 hover:text-rose-700 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm tracking-wide">Logout</span>
                                    <div class="w-2 h-2 rounded-full bg-slate-300 group-hover:bg-rose-500 transition-colors"></div>
                                </div>
                            </button>
                        </form>
                    </div>
                </section>

                <!-- Priorities Section -->
                <section>
                    <div class="mb-5">
                        <h2 class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Priority Management</h2>
                    </div>
                    <div class="space-y-1.5">
                        <a href="{{ route('staff.priorities.applicants') }}" class="group block px-4 py-3 rounded-xl {{ request()->routeIs('staff.priorities.applicants') ? 'bg-emerald-500 border-emerald-600 text-white font-bold shadow-lg' : 'bg-white border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 font-medium text-slate-700 hover:text-emerald-700 shadow-sm' }} border hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <span class="text-xs tracking-wide">Applicant Priority</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ request()->routeIs('staff.priorities.applicants') ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700 group-hover:bg-emerald-200' }}">TOP</span>
                            </div>
                        </a>
                        <a href="{{ route('staff.priorities.documents') }}" class="group block px-4 py-3 rounded-xl {{ request()->routeIs('staff.priorities.documents') ? 'bg-cyan-500 border-cyan-600 text-white font-bold shadow-lg' : 'bg-white border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 font-medium text-slate-700 hover:text-cyan-700 shadow-sm' }} border hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <span class="text-xs tracking-wide">Document Queue</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ request()->routeIs('staff.priorities.documents') ? 'bg-white/20 text-white' : 'bg-cyan-100 text-cyan-700 group-hover:bg-cyan-200' }}">FCFS</span>
                            </div>
                        </a>
                        <a href="{{ route('staff.priorities.ip') }}" class="group block px-4 py-3 rounded-xl {{ request()->routeIs('staff.priorities.ip') ? 'bg-amber-500 border-amber-600 text-white font-bold shadow-lg' : 'bg-white border-slate-200 hover:border-amber-400 hover:bg-amber-50 font-medium text-slate-700 hover:text-amber-700 shadow-sm' }} border hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <span class="text-xs tracking-wide">Indigenous Priority</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ request()->routeIs('staff.priorities.ip') ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700 group-hover:bg-amber-200' }}">30%</span>
                            </div>
                        </a>
                        <a href="{{ route('staff.priorities.tribal-certificate') }}" class="group block px-4 py-3 rounded-xl {{ request()->routeIs('staff.priorities.tribal-certificate') ? 'bg-orange-500 border-orange-600 text-white font-bold shadow-lg' : 'bg-white border-slate-200 hover:border-orange-400 hover:bg-orange-50 font-medium text-slate-700 hover:text-orange-700 shadow-sm' }} border hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <span class="text-xs tracking-wide">Tribal Certificate</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ request()->routeIs('staff.priorities.tribal-certificate') ? 'bg-white/20 text-white' : 'bg-orange-100 text-orange-700 group-hover:bg-orange-200' }}">20%</span>
                            </div>
                        </a>
                        <a href="{{ route('staff.priorities.income-tax') }}" class="group block px-4 py-3 rounded-xl {{ request()->routeIs('staff.priorities.income-tax') ? 'bg-green-500 border-green-600 text-white font-bold shadow-lg' : 'bg-white border-slate-200 hover:border-green-400 hover:bg-green-50 font-medium text-slate-700 hover:text-green-700 shadow-sm' }} border hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <span class="text-xs tracking-wide">Income Tax</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ request()->routeIs('staff.priorities.income-tax') ? 'bg-white/20 text-white' : 'bg-green-100 text-green-700 group-hover:bg-green-200' }}">15%</span>
                            </div>
                        </a>
                        <a href="{{ route('staff.priorities.academic-performance') }}" class="group block px-4 py-3 rounded-xl {{ request()->routeIs('staff.priorities.academic-performance') ? 'bg-blue-500 border-blue-600 text-white font-bold shadow-lg' : 'bg-white border-slate-200 hover:border-blue-400 hover:bg-blue-50 font-medium text-slate-700 hover:text-blue-700 shadow-sm' }} border hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <span class="text-xs tracking-wide">Academic Performance</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ request()->routeIs('staff.priorities.academic-performance') ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-700 group-hover:bg-blue-200' }}">5%</span>
                            </div>
                        </a>
                        <a href="{{ route('staff.priorities.other-requirements') }}" class="group block px-4 py-3 rounded-xl {{ request()->routeIs('staff.priorities.other-requirements') ? 'bg-slate-500 border-slate-600 text-white font-bold shadow-lg' : 'bg-white border-slate-200 hover:border-slate-400 hover:bg-slate-50 font-medium text-slate-700 hover:text-slate-700 shadow-sm' }} border hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <span class="text-xs tracking-wide">Other Requirements</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ request()->routeIs('staff.priorities.other-requirements') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 group-hover:bg-slate-200' }}">5%</span>
                            </div>
                        </a>
                        <a href="{{ route('staff.priorities.courses') }}" class="group block px-4 py-3 rounded-xl {{ request()->routeIs('staff.priorities.courses') ? 'bg-purple-500 border-purple-600 text-white font-bold shadow-lg' : 'bg-white border-slate-200 hover:border-purple-400 hover:bg-purple-50 font-medium text-slate-700 hover:text-purple-700 shadow-sm' }} border hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <span class="text-xs tracking-wide">Course Demand</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ request()->routeIs('staff.priorities.courses') ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-700 group-hover:bg-purple-200' }}">25%</span>
                            </div>
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