@extends('layouts.app')

@section('content')
<!-- Decorative Background Elements -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-amber-200/30 to-orange-200/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-tr from-orange-200/30 to-red-200/30 rounded-full blur-3xl"></div>
</div>

<!-- Notification Bar -->
<div class="bg-gradient-to-r from-orange-700 via-orange-600 to-orange-500 text-white shadow-2xl mb-8 relative overflow-hidden">
    <!-- Decorative Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
    </div>
    
    <div class="container mx-auto px-4 py-5 relative z-10">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-6">
                <div class="flex items-center space-x-2 bg-white/15 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20 shadow-lg">
                    <svg class="w-5 h-5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-bold text-sm">System Status</span>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <span class="flex items-center space-x-2 bg-emerald-500/20 backdrop-blur-md px-4 py-2 rounded-xl border border-emerald-300/30 shadow-lg">
                        <div class="w-2.5 h-2.5 bg-emerald-300 rounded-full animate-pulse shadow-lg shadow-emerald-300/50"></div>
                        <span class="font-semibold">Online</span>
                    </span>
                    <span class="flex items-center space-x-2 bg-amber-500/20 backdrop-blur-md px-4 py-2 rounded-xl border border-amber-300/30 shadow-lg">
                        <div class="w-2.5 h-2.5 bg-amber-300 rounded-full shadow-lg shadow-amber-300/50"></div>
                        <span class="font-semibold">{{ $newApplicants }} New</span>
                    </span>
                    <span class="flex items-center space-x-2 bg-rose-500/20 backdrop-blur-md px-4 py-2 rounded-xl border border-rose-300/30 shadow-lg">
                        <div class="w-2.5 h-2.5 bg-rose-300 rounded-full animate-pulse shadow-lg shadow-rose-300/50"></div>
                        <span class="font-semibold">{{ collect($pendingRequirements)->where('is_overdue', true)->count() }} Overdue</span>
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Notification Bell -->
                <div class="relative">
                    <button class="text-white hover:text-white transition-all duration-200 relative p-2.5 rounded-xl hover:bg-white/15 backdrop-blur-sm border border-white/20 shadow-lg" onclick="toggleNotifDropdown()" aria-label="Notifications">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 15.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v4.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($notifications->count() > 0)
                            <span id="notif-badge" class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center shadow-lg border-2 border-white">{{ $notifications->count() }}</span>
                        @endif
                    </button>
                    <!-- Dropdown -->
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-3 w-80 bg-white text-gray-800 rounded-2xl shadow-2xl z-50 border border-slate-200 overflow-hidden">
                        <div class="p-4 border-b bg-gradient-to-r from-orange-50 to-amber-50 font-bold text-orange-700 flex items-center justify-between">
                            <span>Notifications</span>
                            <span class="text-xs font-semibold text-slate-500 bg-white px-2 py-1 rounded-lg">{{ $notifications->count() }} new</span>
                        </div>
                        <ul class="max-h-72 overflow-y-auto">
                            @forelse($notifications as $notif)
                                <li class="px-4 py-3 border-b border-slate-100 hover:bg-orange-50 cursor-pointer transition-all duration-150">
                                    <div class="text-sm text-slate-700 font-medium">{{ $notif->data['message'] ?? '' }}</div>
                                    <div class="text-xs text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                                </li>
                            @empty
                                <li class="px-4 py-6 text-center text-slate-400">No new notifications.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <!-- End Notification Bell -->
                <span class="text-xs bg-white/15 backdrop-blur-md px-4 py-2 rounded-xl font-semibold border border-white/20 shadow-lg">{{ now()->format('M d, Y g:i A') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Alerts Section -->
<div id="quick-alerts" class="container mx-auto mb-8 hidden">
    <div class="bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 rounded-3xl shadow-xl border-l-4 border-orange-600 p-6 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-500 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="font-black text-slate-900 text-lg">Recent Alerts</h3>
            </div>
            <button class="text-slate-400 hover:text-slate-700 transition-colors p-2 rounded-xl hover:bg-white shadow-sm" onclick="toggleNotifications()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="space-y-3">
            @if($newApplicants > 0)
            <div class="flex items-center space-x-3 text-sm bg-white p-4 rounded-2xl shadow-md hover:shadow-lg transition-shadow">
                <div class="w-3 h-3 bg-blue-500 rounded-full shadow-lg shadow-blue-500/50 animate-pulse"></div>
                <span class="text-slate-700 font-semibold">{{ $newApplicants }} new scholarship applications require review</span>
            </div>
            @endif
            @if(collect($pendingRequirements)->where('is_overdue', true)->count() > 0)
            <div class="flex items-center space-x-3 text-sm bg-white p-4 rounded-2xl shadow-md hover:shadow-lg transition-shadow">
                <div class="w-3 h-3 bg-red-500 rounded-full shadow-lg shadow-red-500/50 animate-pulse"></div>
                <span class="text-slate-700 font-semibold">{{ collect($pendingRequirements)->where('is_overdue', true)->count() }} requirements are overdue</span>
            </div>
            @endif
            @if(collect($pendingRequirements)->where('priority', 1)->count() > 0)
            <div class="flex items-center space-x-3 text-sm bg-white p-4 rounded-2xl shadow-md hover:shadow-lg transition-shadow">
                <div class="w-3 h-3 bg-orange-500 rounded-full shadow-lg shadow-orange-500/50"></div>
                <span class="text-slate-700 font-semibold">{{ collect($pendingRequirements)->where('priority', 1)->count() }} priority documents pending</span>
            </div>
            @endif
            <div class="flex items-center space-x-3 text-sm bg-white p-4 rounded-2xl shadow-md hover:shadow-lg transition-shadow">
                <div class="w-3 h-3 bg-green-500 rounded-full shadow-lg shadow-green-500/50"></div>
                <span class="text-slate-700 font-semibold">System maintenance scheduled for Saturday 2:00 AM</span>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-orange-700 to-orange-500 rounded-3xl shadow-2xl p-8 mb-8 text-white overflow-hidden">
        <!-- Decorative Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-4xl font-black mb-3 tracking-tight">NCIP Staff Dashboard</h1>
                <div class="flex items-center gap-3 text-orange-100">
                    <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="font-bold text-white">{{ $name }}</span>
                    </div>
                    <div class="bg-white/15 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20">
                        <span class="font-semibold text-white">{{ $assignedBarangay }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('staff.applicants.list') }}" class="group bg-white text-orange-600 hover:bg-orange-50 font-bold px-6 py-3 rounded-xl shadow-xl transition-all duration-200 hover:shadow-2xl hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    View Applicants
                </a>
                <a href="{{ route('staff.reports.download') }}" class="group bg-gradient-to-r from-amber-600 to-orange-700 text-white hover:from-amber-700 hover:to-orange-800 font-bold px-6 py-3 rounded-xl shadow-xl transition-all duration-200 hover:shadow-2xl hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download Report
                </a>
                <form method="POST" action="{{ route('staff.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="group bg-white/20 backdrop-blur-md border border-white/30 text-white hover:bg-white/30 font-bold px-6 py-3 rounded-xl shadow-xl transition-all duration-200 hover:shadow-2xl hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Geographic Filters -->
    <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="font-black text-slate-900 text-xl">Geographic Filters</h3>
                <p class="text-sm text-slate-500 font-medium">Filter applicants by location and ethnicity</p>
            </div>
        </div>
        <form id="filter-form" method="GET" action="{{ route('staff.dashboard') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">Province</label>
                <select name="province" id="province-filter" class="form-select w-full border-slate-200 bg-slate-50 rounded-xl p-3.5 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm font-medium hover:bg-white">
                    <option value="">All Provinces</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" {{ $selectedProvince == $province ? 'selected' : '' }}>{{ $province }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">Municipality</label>
                <select name="municipality" id="municipality-filter" class="form-select w-full border-slate-200 bg-slate-50 rounded-xl p-3.5 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm font-medium hover:bg-white">
                    <option value="">All Municipalities</option>
                    @foreach($municipalities as $municipality)
                        <option value="{{ $municipality }}" {{ $selectedMunicipality == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">Barangay</label>
                <select name="barangay" id="barangay-filter" class="form-select w-full border-slate-200 bg-slate-50 rounded-xl p-3.5 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm font-medium hover:bg-white">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">IP Group</label>
                <select name="ethno" id="ethno-filter" class="form-select w-full border-slate-200 bg-slate-50 rounded-xl p-3.5 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm font-medium hover:bg-white">
                    <option value="">All IP Groups</option>
                    @foreach($ethnicities as $ethno)
                        <option value="{{ $ethno->id }}" {{ $selectedEthno == $ethno->id ? 'selected' : '' }}>{{ $ethno->ethnicity }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-metric-card title="Total Scholars" :value="$totalScholars" icon="users" />
        <x-metric-card title="New Applicants" :value="$newApplicants" icon="user-plus" />
        <x-metric-card title="Active Scholars" :value="$activeScholars" icon="user-check" />
        <x-metric-card title="Inactive Scholars" :value="$inactiveScholars" icon="user-x" />
    </div>

    
    <!-- Data Visualizations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8 hover:shadow-2xl transition-shadow duration-300">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-900 text-lg">Scholars per Barangay</h2>
                    <p class="text-xs text-slate-500 font-medium">Top 10 barangays by applicant count</p>
                </div>
            </div>
            <div class="h-80">
                <canvas id="barChart"></canvas>
            </div>
        </div>
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8 hover:shadow-2xl transition-shadow duration-300">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-900 text-lg">Application Status</h2>
                    <p class="text-xs text-slate-500 font-medium">Distribution by approval status</p>
                </div>
            </div>
            <div class="h-80">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- IP Group Analytics -->
    <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8 mb-8 hover:shadow-2xl transition-shadow duration-300">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <h2 class="font-black text-slate-900 text-xl">IP Group Distribution</h2>
                <p class="text-sm text-slate-500 font-medium">Applicants by Indigenous Peoples group</p>
            </div>
        </div>
        <div class="h-96">
            <canvas id="ipChart"></canvas>
        </div>
    </div>


    <!-- Alerts/Deadlines Table -->
    <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8 mb-8 overflow-hidden">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h2 class="font-black text-slate-900 text-xl">Recent Alerts & Deadlines</h2>
                <p class="text-sm text-slate-500 font-medium">Track important dates and notifications</p>
            </div>
        </div>
        <div class="overflow-x-auto -mx-8 px-8">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-gradient-to-r from-orange-50 to-amber-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-wider">Scholar</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-wider">Alert</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @foreach($alerts as $alert)
                    <tr class="hover:bg-orange-50 transition-all duration-200 group">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900 group-hover:text-orange-600 transition-colors">{{ $alert->scholar_name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600 font-medium">{{ $alert->message }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 font-semibold">{{ $alert->due_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-4 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-orange-100 to-amber-100 text-orange-700 shadow-sm">{{ $alert->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Feedback/Support -->
    <div id="feedback-section" class="bg-gradient-to-br from-orange-50 via-amber-50 to-orange-50 rounded-3xl shadow-xl border border-orange-200 p-8 mt-8 max-w-3xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <div>
                <h2 class="font-black text-slate-900 text-xl">Feedback & Support</h2>
                <p class="text-sm text-slate-500 font-medium">Share your thoughts or report issues</p>
            </div>
        </div>
        @if(session('success'))
            <div class="mb-5 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border-l-4 border-emerald-500 text-emerald-700 rounded-2xl font-semibold shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        <form method="POST" action="{{ route('staff.feedback') }}" class="mb-6">
            @csrf
            <textarea name="feedback_text" class="form-textarea w-full mb-4 border-slate-200 bg-white rounded-2xl p-4 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm font-medium shadow-sm" rows="4" placeholder="Describe your issue or suggestion..." required></textarea>
            <button class="btn btn-secondary w-full bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Submit Feedback
            </button>
        </form>
        <div class="flex items-center gap-2 mb-3">
            <div class="h-px flex-1 bg-gradient-to-r from-transparent via-slate-300 to-transparent"></div>
            <h3 class="font-bold text-xs text-slate-500 uppercase tracking-wider">Previous Feedback</h3>
            <div class="h-px flex-1 bg-gradient-to-r from-transparent via-slate-300 to-transparent"></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 max-h-48 overflow-y-auto p-4 shadow-inner">
            <ul class="space-y-3 text-sm">
                @forelse($feedbacks as $fb)
                    <li class="pb-3 border-b border-slate-100 last:border-0">
                        <p class="mb-2 text-slate-700 font-medium leading-relaxed">{{ $fb['text'] }}</p>
                        <span class="text-xs text-slate-400 font-semibold">{{ $fb['submitted_at'] }}</span>
                    </li>
                @empty
                    <li class="text-center text-slate-400 py-6 font-medium">No feedback submitted yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Bar Chart - Scholars per Barangay
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {!! json_encode($barChartData) !!},
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        color: '#64748b'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 11,
                            weight: '600'
                        },
                        color: '#64748b'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Pie Chart - Application Status
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {!! json_encode($pieChartData) !!},
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#334155',
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });

    // Bar Chart - IP Group Distribution
    new Chart(document.getElementById('ipChart'), {
        type: 'bar',
        data: {!! json_encode($ipChartData) !!},
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.x || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `Applicants: ${value} (${percentage}% of total)`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        color: '#64748b'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                },
                y: {
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        color: '#334155'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Notification toggle functionality
    function toggleNotifications() {
        const quickAlerts = document.getElementById('quick-alerts');
        if (quickAlerts.classList.contains('hidden')) {
            quickAlerts.classList.remove('hidden');
            quickAlerts.classList.add('animate-fade-in');
        } else {
            quickAlerts.classList.add('hidden');
        }
    }

    // Auto-hide notifications after 10 seconds
    setTimeout(function() {
        const quickAlerts = document.getElementById('quick-alerts');
        if (!quickAlerts.classList.contains('hidden')) {
            quickAlerts.classList.add('hidden');
        }
    }, 10000);

    // Notification dropdown toggle
    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notif-dropdown');
        dropdown.classList.toggle('hidden');
        // Hide the notification badge when bell is clicked
        const badge = document.getElementById('notif-badge');
        if (badge) {
            badge.style.display = 'none';
        }
        // Mark notifications as read in backend
        fetch("{{ route('staff.notifications.markRead') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
    }
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('notif-dropdown');
        const bell = event.target.closest('button[onclick="toggleNotifDropdown()"]');
        if (!dropdown.contains(event.target) && !bell) {
            dropdown.classList.add('hidden');
        }
    });

    // Geographic filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const provinceFilter = document.getElementById('province-filter');
        const municipalityFilter = document.getElementById('municipality-filter');
        const barangayFilter = document.getElementById('barangay-filter');
        const ethnoFilter = document.getElementById('ethno-filter');
        const filterForm = document.getElementById('filter-form');

        // Auto-submit form when filters change
        [provinceFilter, municipalityFilter, barangayFilter, ethnoFilter].forEach(filter => {
            filter.addEventListener('change', function() {
                filterForm.submit();
            });
        });

        // Dynamic municipality loading based on province
        provinceFilter.addEventListener('change', function() {
            const province = this.value;
            if (province) {
                fetch(`/address/municipalities?province=${encodeURIComponent(province)}`)
                    .then(response => response.json())
                    .then(municipalities => {
                        municipalityFilter.innerHTML = '<option value="">All Municipalities</option>';
                        municipalities.forEach(municipality => {
                            const option = document.createElement('option');
                            option.value = municipality;
                            option.textContent = municipality;
                            municipalityFilter.appendChild(option);
                        });
                    });
            }
        });

        // Dynamic barangay loading based on municipality
        municipalityFilter.addEventListener('change', function() {
            const municipality = this.value;
            if (municipality) {
                fetch(`/address/barangays?municipality=${encodeURIComponent(municipality)}`)
                    .then(response => response.json())
                    .then(barangays => {
                        barangayFilter.innerHTML = '<option value="">All Barangays</option>';
                        barangays.forEach(barangay => {
                            const option = document.createElement('option');
                            option.value = barangay;
                            option.textContent = barangay;
                            barangayFilter.appendChild(option);
                        });
                    });
            }
        });
    });

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
@endpush 