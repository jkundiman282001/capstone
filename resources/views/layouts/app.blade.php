<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NCIP Staff Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}">
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
                    <img src="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" alt="NCIP Logo" class="relative h-14 w-14 rounded-2xl bg-white p-2 shadow-2xl ring-4 ring-white/30">
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
            <nav class="flex flex-col h-full py-8 px-5 space-y-6">
                <!-- Dashboard Section -->
                <section>
                    <div class="mb-4">
                        <h2 class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3">Overview</h2>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('staff.dashboard') }}" class="group block px-5 py-4 rounded-2xl {{ request()->routeIs('staff.dashboard') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-xl shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <span class="text-sm tracking-wide">Dashboard</span>
                                <div class="w-2 h-2 rounded-full {{ request()->routeIs('staff.dashboard') ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                            </div>
                        </a>
                        <a href="{{ route('staff.notifications') }}" class="group block px-5 py-4 rounded-2xl {{ request()->routeIs('staff.notifications') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-xl shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm tracking-wide">Notifications</span>
                                    @php
                                        try {
                                            $unreadCount = auth()->guard('staff')->user()->unreadNotifications->count();
                                        } catch (\Throwable $e) {
                                            $unreadCount = 0;
                                        }
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="px-2 py-0.5 text-xs font-black rounded-full {{ request()->routeIs('staff.notifications') ? 'bg-white/20 text-white' : 'bg-orange-100 text-orange-700' }}">{{ $unreadCount }}</span>
                                    @endif
                                </div>
                                <div class="w-2 h-2 rounded-full {{ request()->routeIs('staff.notifications') ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                            </div>
                        </a>
                    </div>
                </section>

                <!-- Applications & Masterlist Section -->
                <section>
                    <div class="mb-4">
                        <h2 class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3">Applications</h2>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('staff.applicants.list') }}" class="group block px-5 py-4 rounded-2xl {{ (request()->routeIs('staff.applicants.*') || request()->routeIs('staff.applications.*')) && request()->get('status') !== 'rejected' ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-xl shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <span class="text-sm tracking-wide">View Applicants</span>
                                <div class="w-2 h-2 rounded-full {{ (request()->routeIs('staff.applicants.*') || request()->routeIs('staff.applications.*')) && request()->get('status') !== 'rejected' ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                            </div>
                        </a>
                        <a href="{{ route('staff.applicants.list', ['status' => 'rejected']) }}" class="group block px-5 py-4 rounded-2xl {{ request()->get('status') === 'rejected' ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-xl shadow-red-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-red-50 hover:to-rose-50 border-2 border-slate-200 hover:border-red-300 font-semibold text-slate-700 hover:text-red-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300 relative">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm tracking-wide">Rejected Applicants</span>
                                    @php
                                        try {
                                            $rejectedCount = \App\Models\BasicInfo::where('application_status', 'rejected')
                                                ->where(function($query) {
                                                    $query->whereRaw("LOWER(TRIM(grant_status)) != 'grantee'")
                                                          ->orWhereNull('grant_status');
                                                })->count();
                                        } catch (\Throwable $e) {
                                            $rejectedCount = 0;
                                        }
                                    @endphp
                                    @if($rejectedCount > 0)
                                        <span class="px-2 py-0.5 text-xs font-black rounded-full {{ request()->get('status') === 'rejected' ? 'bg-white/20 text-white' : 'bg-red-100 text-red-700' }}">{{ $rejectedCount }}</span>
                                    @endif
                                </div>
                                <div class="w-2 h-2 rounded-full {{ request()->get('status') === 'rejected' ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-red-500' }} transition-colors"></div>
                            </div>
                        </a>
                        <a href="{{ route('staff.applicants.list', ['status' => 'terminated']) }}" class="group block px-5 py-4 rounded-2xl {{ request()->get('status') === 'terminated' ? 'bg-gradient-to-r from-orange-600 to-red-600 text-white font-bold shadow-xl shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300 relative">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm tracking-wide">Terminated Applicants</span>
                                    @php
                                        try {
                                            $terminatedCount = \App\Models\BasicInfo::where(function($query) {
                                                $query->where('application_status', 'terminated')
                                                      ->orWhere(function($q) {
                                                          $q->where('application_status', 'rejected')
                                                            ->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'");
                                                      });
                                            })->count();
                                        } catch (\Throwable $e) {
                                            $terminatedCount = 0;
                                        }
                                    @endphp
                                    @if($terminatedCount > 0)
                                        <span class="px-2 py-0.5 text-xs font-black rounded-full {{ request()->get('status') === 'terminated' ? 'bg-white/20 text-white' : 'bg-orange-100 text-orange-700' }}">{{ $terminatedCount }}</span>
                                    @endif
                                </div>
                                <div class="w-2 h-2 rounded-full {{ request()->get('status') === 'terminated' ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                            </div>
                        </a>
                        <!-- Masterlist Dropdown -->
                        <div>
                            <button onclick="toggleMasterlistDropdown()" class="group w-full px-5 py-4 rounded-2xl {{ request()->routeIs('staff.masterlist.*') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-xl shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-between">
                                <span class="text-sm tracking-wide">Masterlist</span>
                                <svg id="masterlist-chevron" class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('staff.masterlist.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="masterlist-dropdown" class="pl-2 pt-1.5 space-y-1.5 {{ request()->routeIs('staff.masterlist.*') ? '' : 'hidden' }} overflow-hidden transition-all duration-300">
                                <!-- Regular Dropdown -->
                                <div>
                                    <button onclick="toggleRegularDropdown()" class="group w-full px-4 py-2.5 rounded-xl {{ request()->routeIs('staff.masterlist.regular.*') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-lg shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-between">
                                        <span class="text-xs tracking-wide">Regular</span>
                                        <svg id="regular-chevron" class="w-3 h-3 transition-transform duration-200 {{ request()->routeIs('staff.masterlist.regular.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div id="regular-dropdown" class="pl-2 pt-1.5 space-y-1 {{ request()->routeIs('staff.masterlist.regular.*') ? '' : 'hidden' }} overflow-hidden transition-all duration-300">
                                        <a href="{{ route('staff.masterlist.regular.grantees') }}" class="group block px-4 py-2 rounded-lg {{ request()->routeIs('staff.masterlist.regular.grantees') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-md' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-medium text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs tracking-wide">Grantees</span>
                                                <div class="w-1 h-1 rounded-full {{ request()->routeIs('staff.masterlist.regular.grantees') ? 'bg-white/50' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                                            </div>
                                        </a>
                                        <a href="{{ route('staff.masterlist.regular.all') }}" class="group block px-4 py-2 rounded-lg {{ request()->routeIs('staff.masterlist.regular.all') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-md' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-medium text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-xs tracking-wide">Waiting</span>
                                                    @php
                                                        try {
                                                            $waitingCount = \App\Models\BasicInfo::where('grant_status', 'waiting')
                                                                ->orWhere(function($query) {
                                                                    $query->where('application_status', 'validated')
                                                                          ->whereNull('grant_status');
                                                                })->count();
                                                        } catch (\Throwable $e) {
                                                            $waitingCount = 0;
                                                        }
                                                    @endphp
                                                    @if($waitingCount > 0)
                                                        <span class="px-2 py-0.5 text-xs font-black rounded-full {{ request()->routeIs('staff.masterlist.regular.all') ? 'bg-white/20 text-white' : 'bg-orange-100 text-orange-700' }}">{{ $waitingCount }}</span>
                                                    @endif
                                                </div>
                                                <div class="w-1 h-1 rounded-full {{ request()->routeIs('staff.masterlist.regular.all') ? 'bg-white/50' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ route('staff.masterlist.pamana') }}" class="group block px-4 py-2.5 rounded-xl {{ request()->routeIs('staff.masterlist.pamana') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-lg shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">Pamana</span>
                                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('staff.masterlist.pamana') ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Priority Management Section -->
                <section>
                    <div class="mb-4">
                        <h2 class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3">Priority Management</h2>
                    </div>
                    <div class="space-y-1.5">
                        <!-- Applicant Priority Dropdown -->
                        <div>
                            <button onclick="togglePriorityDropdown()" class="group w-full px-4 py-3 rounded-xl {{ request()->routeIs('staff.priorities.applicants') || request()->routeIs('staff.priorities.ip') || request()->routeIs('staff.priorities.academic-performance') || request()->routeIs('staff.priorities.income-tax') || request()->routeIs('staff.priorities.other-requirements') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-lg shadow-orange-600/20' : 'bg-white border-slate-200 hover:border-orange-300 hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 font-medium text-slate-700 hover:text-orange-700 shadow-sm' }} border hover:shadow-md transition-all duration-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs tracking-wide">Applicant Priority</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ request()->routeIs('staff.priorities.applicants') || request()->routeIs('staff.priorities.ip') || request()->routeIs('staff.priorities.academic-performance') || request()->routeIs('staff.priorities.income-tax') || request()->routeIs('staff.priorities.other-requirements') ? 'bg-white/20 text-white' : 'bg-orange-100 text-orange-700 group-hover:bg-orange-200' }}">TOP</span>
                                </div>
                                <svg id="priority-chevron" class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('staff.priorities.ip') || request()->routeIs('staff.priorities.academic-performance') || request()->routeIs('staff.priorities.income-tax') || request()->routeIs('staff.priorities.other-requirements') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="priority-dropdown" class="pl-2 pt-1.5 space-y-1 {{ request()->routeIs('staff.priorities.ip') || request()->routeIs('staff.priorities.academic-performance') || request()->routeIs('staff.priorities.income-tax') || request()->routeIs('staff.priorities.other-requirements') ? '' : 'hidden' }} overflow-hidden transition-all duration-300">
                                <a href="{{ route('staff.priorities.applicants') }}" class="group block px-4 py-2.5 rounded-lg {{ request()->routeIs('staff.priorities.applicants') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-md' : 'bg-white border-slate-200 hover:border-orange-300 hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 font-medium text-slate-700 hover:text-orange-700 shadow-sm' }} border hover:shadow-sm transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">View All</span>
                                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('staff.priorities.applicants') ? 'bg-white/50' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                                    </div>
                                </a>
                                <a href="{{ route('staff.priorities.ip') }}" class="group block px-4 py-2.5 rounded-lg {{ request()->routeIs('staff.priorities.ip') ? 'bg-amber-500 border-amber-600 text-white font-bold shadow-md' : 'bg-white border-slate-200 hover:border-amber-400 hover:bg-amber-50 font-medium text-slate-700 hover:text-amber-700 shadow-sm' }} border hover:shadow-sm transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">IP Group</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ request()->routeIs('staff.priorities.ip') ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700 group-hover:bg-amber-200' }}">20%</span>
                                    </div>
                                </a>
                                <a href="{{ route('staff.priorities.academic-performance') }}" class="group block px-4 py-2.5 rounded-lg {{ request()->routeIs('staff.priorities.academic-performance') ? 'bg-blue-500 border-blue-600 text-white font-bold shadow-md' : 'bg-white border-slate-200 hover:border-blue-400 hover:bg-blue-50 font-medium text-slate-700 hover:text-blue-700 shadow-sm' }} border hover:shadow-sm transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">GWA</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ request()->routeIs('staff.priorities.academic-performance') ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-700 group-hover:bg-blue-200' }}">30%</span>
                                    </div>
                                </a>
                                <a href="{{ route('staff.priorities.income-tax') }}" class="group block px-4 py-2.5 rounded-lg {{ request()->routeIs('staff.priorities.income-tax') ? 'bg-green-500 border-green-600 text-white font-bold shadow-md' : 'bg-white border-slate-200 hover:border-green-400 hover:bg-green-50 font-medium text-slate-700 hover:text-green-700 shadow-sm' }} border hover:shadow-sm transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">ITR</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ request()->routeIs('staff.priorities.income-tax') ? 'bg-white/20 text-white' : 'bg-green-100 text-green-700 group-hover:bg-green-200' }}">30%</span>
                                    </div>
                                </a>
                                <a href="{{ route('staff.priorities.citation-awards') }}" class="group block px-4 py-2.5 rounded-lg {{ request()->routeIs('staff.priorities.citation-awards') ? 'bg-fuchsia-500 border-fuchsia-600 text-white font-bold shadow-md' : 'bg-white border-slate-200 hover:border-fuchsia-400 hover:bg-fuchsia-50 font-medium text-slate-700 hover:text-fuchsia-700 shadow-sm' }} border hover:shadow-sm transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">Citation/Awards</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ request()->routeIs('staff.priorities.citation-awards') ? 'bg-white/20 text-white' : 'bg-fuchsia-100 text-fuchsia-700 group-hover:bg-fuchsia-200' }}">10%</span>
                                    </div>
                                </a>
                                <a href="{{ route('staff.priorities.social-responsibility') }}" class="group block px-4 py-2.5 rounded-lg {{ request()->routeIs('staff.priorities.social-responsibility') ? 'bg-slate-500 border-slate-600 text-white font-bold shadow-md' : 'bg-white border-slate-200 hover:border-slate-400 hover:bg-slate-50 font-medium text-slate-700 hover:text-slate-700 shadow-sm' }} border hover:shadow-sm transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">Social Responsibility</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ request()->routeIs('staff.priorities.social-responsibility') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 group-hover:bg-slate-200' }}">10%</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Reports & Tools Section -->
                <section>
                    <div class="mb-4">
                        <h2 class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3">Reports & Tools</h2>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('staff.settings') }}" class="group block px-5 py-4 rounded-2xl {{ request()->routeIs('staff.settings') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-xl shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <span class="text-sm tracking-wide">Settings</span>
                                <div class="w-2 h-2 rounded-full {{ request()->routeIs('staff.settings') ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                            </div>
                        </a>
                        <!-- Reports Dropdown -->
                        <div>
                            <button type="button" onclick="toggleReportsDropdown()"
                                class="group w-full px-5 py-4 rounded-2xl {{ request()->routeIs('staff.reports.index') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold shadow-xl shadow-orange-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 border-2 border-slate-200 hover:border-orange-300 font-semibold text-slate-700 hover:text-orange-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-between">
                                <span class="text-sm tracking-wide">Reports</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full {{ request()->routeIs('staff.reports.index') ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-orange-500' }} transition-colors"></div>
                                    <svg id="reports-chevron" class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('staff.reports.index') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>
                            <div id="reports-dropdown" class="pl-2 pt-1.5 space-y-1.5 {{ request()->routeIs('staff.reports.index') ? '' : 'hidden' }} overflow-hidden transition-all duration-300">
                                <a href="{{ route('staff.reports.index', ['tab' => 'grantees']) }}" class="group block px-4 py-2.5 rounded-xl {{ request()->routeIs('staff.reports.index') && request()->get('tab', 'grantees') === 'grantees' ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 border-2 border-slate-200 hover:border-blue-300 font-semibold text-slate-700 hover:text-blue-700 shadow-sm' }} hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">Grantees</span>
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-md {{ request()->routeIs('staff.reports.index') && request()->get('tab', 'grantees') === 'grantees' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700' }}"></span>
                                    </div>
                                </a>
                                <a href="{{ route('staff.reports.index', ['tab' => 'pamana']) }}" class="group block px-4 py-2.5 rounded-xl {{ request()->routeIs('staff.reports.index') && request()->get('tab') === 'pamana' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg shadow-emerald-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 border-2 border-slate-200 hover:border-emerald-300 font-semibold text-slate-700 hover:text-emerald-700 shadow-sm' }} hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">Pamana</span>
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-md {{ request()->routeIs('staff.reports.index') && request()->get('tab') === 'pamana' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700' }}"></span>
                                    </div>
                                </a>
                                <a href="{{ route('staff.reports.index', ['tab' => 'waiting']) }}" class="group block px-4 py-2.5 rounded-xl {{ request()->routeIs('staff.reports.index') && request()->get('tab') === 'waiting' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold shadow-lg shadow-purple-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-2 border-slate-200 hover:border-purple-300 font-semibold text-slate-700 hover:text-purple-700 shadow-sm' }} hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">Waiting List</span>
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-md {{ request()->routeIs('staff.reports.index') && request()->get('tab') === 'waiting' ? 'bg-white/20 text-white' : 'bg-purple-50 text-purple-700' }}"></span>
                                    </div>
                                </a>
                                <a href="{{ route('staff.reports.index', ['tab' => 'disqualified']) }}" class="group block px-4 py-2.5 rounded-xl {{ request()->routeIs('staff.reports.index') && request()->get('tab') === 'disqualified' ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-lg shadow-red-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-red-50 hover:to-rose-50 border-2 border-slate-200 hover:border-red-300 font-semibold text-slate-700 hover:text-red-700 shadow-sm' }} hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">Disqualified</span>
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-md {{ request()->routeIs('staff.reports.index') && request()->get('tab') === 'disqualified' ? 'bg-white/20 text-white' : 'bg-red-50 text-red-700' }}"></span>
                                    </div>
                                </a>
                                <a href="{{ route('staff.reports.index', ['tab' => 'replacements']) }}" class="group block px-4 py-2.5 rounded-xl {{ request()->routeIs('staff.reports.index') && request()->get('tab') === 'replacements' ? 'bg-gradient-to-r from-yellow-500 to-amber-500 text-white font-bold shadow-lg shadow-yellow-500/20' : 'bg-white hover:bg-gradient-to-r hover:from-yellow-50 hover:to-amber-50 border-2 border-slate-200 hover:border-yellow-300 font-semibold text-slate-700 hover:text-yellow-700 shadow-sm' }} hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs tracking-wide">Replacements</span>
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-md {{ request()->routeIs('staff.reports.index') && request()->get('tab') === 'replacements' ? 'bg-white/20 text-white' : 'bg-yellow-50 text-yellow-800' }}"></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('staff.announcements.index') }}" class="group block px-5 py-4 rounded-2xl {{ request()->routeIs('staff.announcements.*') ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-xl shadow-blue-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 border-2 border-slate-200 hover:border-blue-300 font-semibold text-slate-700 hover:text-blue-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <span class="text-sm tracking-wide">Announcements</span>
                                <div class="w-2 h-2 rounded-full {{ request()->routeIs('staff.announcements.*') ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-blue-500' }} transition-colors"></div>
                            </div>
                        </a>
                        <a href="{{ route('staff.archives.index') }}" class="group block px-5 py-4 rounded-2xl {{ request()->routeIs('staff.archives.*') ? 'bg-gradient-to-r from-slate-700 to-slate-800 text-white font-bold shadow-xl shadow-slate-600/20' : 'bg-white hover:bg-gradient-to-r hover:from-slate-50 hover:to-gray-50 border-2 border-slate-200 hover:border-slate-300 font-semibold text-slate-700 hover:text-slate-700 shadow-sm' }} hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <span class="text-sm tracking-wide">Archives</span>
                                <div class="w-2 h-2 rounded-full {{ request()->routeIs('staff.archives.*') ? 'bg-white/50 group-hover:bg-white' : 'bg-slate-300 group-hover:bg-slate-500' }} transition-colors"></div>
                            </div>
                        </a>
                    </div>
                </section>

                <!-- Account & Support Section -->
                <section>
                    <div class="mb-4">
                        <h2 class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3">Account</h2>
                    </div>
                    <div class="space-y-2">
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
            </nav>
        </aside>
        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-30 md:hidden" onclick="toggleSidebar()"></div>
        <!-- Main Content -->
        <main class="flex-1 min-h-screen min-w-0">
            <div class="p-0 min-w-0">
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

        // Toggle Priority Dropdown
        function togglePriorityDropdown() {
            const dropdown = document.getElementById('priority-dropdown');
            const chevron = document.getElementById('priority-chevron');
            
            dropdown.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        // Toggle Masterlist Dropdown
        function toggleMasterlistDropdown() {
            const dropdown = document.getElementById('masterlist-dropdown');
            const chevron = document.getElementById('masterlist-chevron');
            
            dropdown.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        // Toggle Regular Dropdown
        function toggleRegularDropdown() {
            const dropdown = document.getElementById('regular-dropdown');
            const chevron = document.getElementById('regular-chevron');
            
            dropdown.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        // Toggle Reports Dropdown
        function toggleReportsDropdown() {
            const dropdown = document.getElementById('reports-dropdown');
            const chevron = document.getElementById('reports-chevron');
            if (!dropdown || !chevron) return;
            dropdown.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        // Auto-expand dropdowns if a sub-item is active on page load
        document.addEventListener('DOMContentLoaded', function() {
            const priorityDropdown = document.getElementById('priority-dropdown');
            const priorityChevron = document.getElementById('priority-chevron');
            
            if (priorityDropdown && !priorityDropdown.classList.contains('hidden')) {
                priorityChevron.classList.add('rotate-180');
            }

            const masterlistDropdown = document.getElementById('masterlist-dropdown');
            const masterlistChevron = document.getElementById('masterlist-chevron');
            
            if (!masterlistDropdown.classList.contains('hidden')) {
                masterlistChevron.classList.add('rotate-180');
            }

            const regularDropdown = document.getElementById('regular-dropdown');
            const regularChevron = document.getElementById('regular-chevron');
            
            if (!regularDropdown.classList.contains('hidden')) {
                regularChevron.classList.add('rotate-180');
            }

            const reportsDropdown = document.getElementById('reports-dropdown');
            const reportsChevron = document.getElementById('reports-chevron');
            if (reportsDropdown && reportsChevron && !reportsDropdown.classList.contains('hidden')) {
                reportsChevron.classList.add('rotate-180');
            }
        });
    </script>
    @stack('scripts')
</body>
</html> 
