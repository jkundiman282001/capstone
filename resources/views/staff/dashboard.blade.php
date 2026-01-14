@extends('layouts.app')

@section('content')
<!-- Decorative Background Elements -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-amber-200/30 to-orange-200/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-tr from-orange-200/30 to-red-200/30 rounded-full blur-3xl"></div>
</div>

<!-- Notification Bar -->
<div class="bg-gradient-to-r from-orange-700 via-orange-600 to-orange-500 text-white shadow-2xl mb-8 relative">
    <!-- Decorative Pattern -->
    <div class="absolute inset-0 opacity-10 overflow-hidden">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
    </div>
    
    <div class="container mx-auto px-4 py-4 md:py-5 relative z-10">
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-end gap-4">
            <div class="flex items-center justify-between lg:justify-end space-x-3">
                <!-- Notification Bell -->
                <div class="relative z-[9999]">
                    <button class="text-white hover:text-white transition-all duration-200 relative p-2 md:p-2.5 rounded-xl hover:bg-white/15 backdrop-blur-sm border border-white/20 shadow-lg" onclick="toggleNotifDropdown()" aria-label="Notifications">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 15.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v4.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($notifications->count() > 0)
                            <span id="notif-badge" class="absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-bold rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center shadow-lg border-2 border-white">{{ $notifications->count() }}</span>
                        @endif
                    </button>
                    <!-- Notification Dropdown -->
                    <div id="notif-dropdown" class="absolute right-0 mt-3 w-[calc(100vw-2rem)] sm:w-96 bg-white rounded-3xl shadow-2xl border border-slate-100 hidden z-[10000] overflow-hidden transform origin-top-right transition-all duration-200">
                        <div class="p-4 md:p-5 border-b border-slate-100 bg-gradient-to-r from-orange-50 to-amber-50">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm md:text-base font-black text-slate-800 tracking-tight">Recent Notifications</h3>
                                @if($notifications && $notifications->count() > 0)
                                    <button onclick="markAllRead()" class="text-[10px] md:text-xs font-bold text-orange-600 hover:text-orange-700 transition-colors bg-orange-100/50 px-2 py-1 md:px-3 md:py-1.5 rounded-lg">Mark all as read</button>
                                @endif
                            </div>
                        </div>
                        <ul class="max-h-80 overflow-y-auto">
                            @forelse($notifications ?? [] as $notif)
                                @php
                                    $notifUrl = '#';
                                    if (isset($notif->data['student_id'])) {
                                        $notifUrl = route('staff.applications.view', $notif->data['student_id']);
                                    } elseif (isset($notif->data['type']) && $notif->data['type'] === 'announcement') {
                                        $notifUrl = route('staff.announcements.index');
                                    }
                                @endphp
                                <li onclick="window.location.href='{{ $notifUrl }}'" class="px-4 py-3 md:px-5 md:py-4 border-b border-slate-100 hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 cursor-pointer transition-all duration-200 group">
                                    <div class="flex items-start gap-3">
                                        <div class="w-2 h-2 bg-orange-500 rounded-full mt-1.5 group-hover:animate-pulse shadow-lg shadow-orange-500/50"></div>
                                        <div class="flex-1">
                                            <div class="text-xs md:text-sm text-slate-700 font-semibold leading-relaxed group-hover:text-orange-700 transition-colors">
                                                {{ $notif->data['message'] ?? ($notif->data['title'] ?? 'Notification') }}
                                            </div>
                                            <div class="text-[9px] md:text-[10px] text-slate-400 font-bold mt-1 uppercase tracking-wider">{{ $notif->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="px-5 py-10 text-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 15.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v4.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <p class="text-slate-600 font-bold">All caught up!</p>
                                    <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase tracking-widest">No new alerts</p>
                                </li>
                            @endforelse
                        </ul>
                        <div class="p-4 border-t border-slate-100 bg-slate-50">
                             <a href="{{ route('staff.notifications') }}" class="block text-center text-[10px] md:text-xs font-black text-slate-500 hover:text-orange-600 uppercase tracking-widest transition-colors">View All Notifications</a>
                         </div>
                    </div>
                </div>
                <!-- End Notification Bell -->
                <span id="real-time-clock" class="text-[10px] md:text-xs bg-white/15 backdrop-blur-md px-3 py-2 md:px-4 md:py-2 rounded-xl font-semibold border border-white/20 shadow-lg">{{ now()->format('M d, Y g:i A') }}</span>
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
            <div onclick="window.location.href='{{ route('staff.applicants.list') }}'" class="flex items-center space-x-3 text-sm bg-white p-4 rounded-2xl shadow-md hover:shadow-lg transition-all cursor-pointer group">
                <div class="w-3 h-3 bg-blue-500 rounded-full shadow-lg shadow-blue-500/50 animate-pulse"></div>
                <span class="text-slate-700 font-semibold group-hover:text-blue-600">{{ $newApplicants }} new scholarship applications require review</span>
            </div>
            @endif
            @if(collect($pendingRequirements)->where('is_overdue', true)->count() > 0)
            <div onclick="window.location.href='{{ route('staff.applicants.list') }}'" class="flex items-center space-x-3 text-sm bg-white p-4 rounded-2xl shadow-md hover:shadow-lg transition-all cursor-pointer group">
                <div class="w-3 h-3 bg-red-500 rounded-full shadow-lg shadow-red-500/50 animate-pulse"></div>
                <span class="text-slate-700 font-semibold group-hover:text-red-600">{{ collect($pendingRequirements)->where('is_overdue', true)->count() }} requirements are overdue</span>
            </div>
            @endif
            @if(collect($pendingRequirements)->where('priority', 1)->count() > 0)
            <div onclick="window.location.href='{{ route('staff.applicants.list') }}'" class="flex items-center space-x-3 text-sm bg-white p-4 rounded-2xl shadow-md hover:shadow-lg transition-all cursor-pointer group">
                <div class="w-3 h-3 bg-orange-500 rounded-full shadow-lg shadow-orange-500/50"></div>
                <span class="text-slate-700 font-semibold group-hover:text-orange-600">{{ collect($pendingRequirements)->where('priority', 1)->count() }} priority documents pending</span>
            </div>
            @endif
            <div class="flex items-center space-x-3 text-sm bg-white p-4 rounded-2xl shadow-md hover:shadow-lg transition-shadow">
                <div class="w-3 h-3 bg-green-500 rounded-full shadow-lg shadow-green-500/50"></div>
                <span class="text-slate-700 font-semibold">System maintenance scheduled for Saturday 2:00 AM</span>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-8">
    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-orange-700 to-orange-500 rounded-2xl md:rounded-3xl shadow-2xl p-6 md:p-8 mb-6 md:mb-8 text-white overflow-hidden">
        <!-- Decorative Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-black mb-2 md:mb-3 tracking-tight">NCIP Staff Dashboard</h1>
                <div class="flex flex-wrap items-center gap-2 md:gap-3 text-orange-100">
                    <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 py-1.5 md:px-4 md:py-2 rounded-xl border border-white/20">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="font-bold text-xs md:text-sm text-white">{{ $name }}</span>
                    </div>
                    <div class="bg-white/15 backdrop-blur-md px-3 py-1.5 md:px-4 md:py-2 rounded-xl border border-white/20">
                        <span class="font-semibold text-[10px] md:text-xs text-white">{{ $assignedBarangay }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Geographic Filters -->
    <div class="bg-white/70 backdrop-blur-xl rounded-2xl md:rounded-3xl shadow-xl border border-slate-200 p-6 md:p-8 mb-6 md:mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="font-black text-slate-900 text-lg md:text-xl">Geographic Filters</h3>
                <p class="text-[10px] md:text-sm text-slate-500 font-medium">Filter applicants by location and ethnicity</p>
            </div>
        </div>
        <form id="filter-form" method="GET" action="{{ route('staff.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
            <div>
                <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-2">Province</label>
                <select name="province" id="province-filter" class="form-select w-full border-slate-200 bg-slate-50 rounded-xl p-3 md:p-3.5 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-xs md:text-sm font-medium hover:bg-white">
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" {{ $selectedProvince == $province ? 'selected' : '' }}>{{ $province }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-2">Municipality</label>
                <select name="municipality" id="municipality-filter" class="form-select w-full border-slate-200 bg-slate-50 rounded-xl p-3 md:p-3.5 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-xs md:text-sm font-medium hover:bg-white">
                    <option value="">All Municipalities</option>
                    @foreach($municipalities as $municipality)
                        <option value="{{ $municipality }}" {{ $selectedMunicipality == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-2">Barangay</label>
                <select name="barangay" id="barangay-filter" class="form-select w-full border-slate-200 bg-slate-50 rounded-xl p-3 md:p-3.5 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-xs md:text-sm font-medium hover:bg-white">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-2">IP Group</label>
                <select name="ethno" id="ethno-filter" class="form-select w-full border-slate-200 bg-slate-50 rounded-xl p-3 md:p-3.5 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-xs md:text-sm font-medium hover:bg-white">
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
        <x-metric-card title="Total Applicants" :value="$totalScholars" icon="users" />
        <x-metric-card title="Total Grantees" :value="$totalGrantees" icon="user-plus" />
        <x-metric-card title="Total Graduates" :value="$activeScholars" icon="user-check" />
        <x-metric-card title="Slots left" :value="$inactiveScholars" icon="user-x" />
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
                    <h2 class="font-black text-slate-900 text-lg">{{ $barChartLabel ?? 'Scholars per Barangay' }}</h2>
                    <p class="text-xs text-slate-500 font-medium">{{ $barChartDescription ?? 'Top 10 barangays by applicant count' }}</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-900 text-lg">Application Status</h2>
                    <p class="text-xs text-slate-500 font-medium">Distribution by approval status</p>
                </div>
            </div>
            <div class="h-80">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- IP Group Analytics -->
    <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8 mb-8 hover:shadow-2xl transition-shadow duration-300">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
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

    <!-- Graduation Year Analytics -->
    <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8 mb-8 hover:shadow-2xl transition-shadow duration-300">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>
            <div>
                <h2 class="font-black text-slate-900 text-xl">Graduation Year Distribution</h2>
                <p class="text-sm text-slate-500 font-medium">Distribution of all graduated college applicants by year</p>
            </div>
        </div>
        <div class="h-80">
            <canvas id="gradYearChart"></canvas>
        </div>
    </div>

    <!-- Gender & Application Trends -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Gender Distribution -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8 hover:shadow-2xl transition-shadow duration-300">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-900 text-lg">Gender Distribution</h2>
                    <p class="text-xs text-slate-500 font-medium">Applicants by gender</p>
                </div>
            </div>
            <div class="h-80">
                <canvas id="genderChart"></canvas>
            </div>
        </div>

        <!-- Application Trends -->
        <div class="lg:col-span-2 bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8 hover:shadow-2xl transition-shadow duration-300">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-900 text-lg">Application Trends</h2>
                    <p class="text-xs text-slate-500 font-medium">New applications over the last 6 months</p>
                </div>
            </div>
            <div class="h-80">
                <canvas id="trendsChart"></canvas>
            </div>
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

    // Application Status Chart - Bar Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {!! json_encode($statusChartData) !!},
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
                    },
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.y || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `Applicants: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            weight: 'bold'
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
                            weight: 'bold'
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

    // Pie Chart - IP Group Distribution
    new Chart(document.getElementById('ipChart'), {
        type: 'pie',
        data: {!! json_encode($ipChartData) !!},
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'right',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12,
                            weight: '600'
                        }
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
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return ` ${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Application Trends Chart
    new Chart(document.getElementById('trendsChart'), {
        type: 'line',
        data: {!! json_encode($trendsChartData) !!},
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
                        color: '#64748b',
                        stepSize: 1
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

    // Graduation Year Chart
    new Chart(document.getElementById('gradYearChart'), {
        type: 'bar',
        data: {!! json_encode($gradYearChartData) !!},
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
                    borderRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            weight: 'bold'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Number of Applicants',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Graduation Year',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });

    // Gender Distribution Chart
    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {!! json_encode($genderChartData) !!},
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8
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

    // Real-time clock function
    function updateClock() {
        const now = new Date();
        const options = { 
            month: 'short', 
            day: '2-digit', 
            year: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: true 
        };
        
        // Use Intl.DateTimeFormat for consistent formatting
        const formatter = new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
        
        const parts = formatter.formatToParts(now);
        const map = new Map(parts.map(p => [p.type, p.value]));
        
        // Format: MMM DD, YYYY G:II:SS AM/PM
        const formatted = `${map.get('month')} ${map.get('day')}, ${map.get('year')} ${map.get('hour')}:${map.get('minute')}:${map.get('second')} ${map.get('dayPeriod')}`;
        
        document.getElementById('real-time-clock').textContent = formatted;
    }

    // Update clock every second
    setInterval(updateClock, 1000);
    // Initial call to prevent 1s delay
    updateClock();

    // Notification dropdown toggle
    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notif-dropdown');
        dropdown.classList.toggle('hidden');
    }
    
    // Mark all notifications as read
    function markAllRead() {
        fetch("{{ route('staff.notifications.markRead') }}", {
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
                // Reload page to update notification count
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error marking notifications as read:', error);
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
        // Geographic Filter Logic
        const setupGeographicFilters = () => {
            const provinceFilter = document.getElementById('province-filter');
            const municipalityFilter = document.getElementById('municipality-filter');
            const barangayFilter = document.getElementById('barangay-filter');
            const ethnoFilter = document.getElementById('ethno-filter');
            const filterForm = document.getElementById('filter-form');

            if (!filterForm) return;

            // Province Change
            provinceFilter?.addEventListener('change', function() {
                const province = this.value;
                if (province) {
                    fetch(`/address/municipalities?province=${encodeURIComponent(province)}`)
                        .then(response => response.json())
                        .then(municipalities => {
                            if (municipalityFilter) {
                                municipalityFilter.innerHTML = '<option value="">All Municipalities</option>';
                                municipalities.forEach(muni => {
                                    const option = document.createElement('option');
                                    option.value = muni;
                                    option.textContent = muni;
                                    municipalityFilter.appendChild(option);
                                });
                            }
                            if (barangayFilter) {
                                barangayFilter.innerHTML = '<option value="">All Barangays</option>';
                            }
                            filterForm.submit();
                        });
                } else {
                    filterForm.submit();
                }
            });

            // Municipality Change
            municipalityFilter?.addEventListener('change', function() {
                const municipality = this.value;
                if (municipality) {
                    fetch(`/address/barangays?municipality=${encodeURIComponent(municipality)}`)
                        .then(response => response.json())
                        .then(barangays => {
                            if (barangayFilter) {
                                barangayFilter.innerHTML = '<option value="">All Barangays</option>';
                                barangays.forEach(brgy => {
                                    const option = document.createElement('option');
                                    option.value = brgy;
                                    option.textContent = brgy;
                                    barangayFilter.appendChild(option);
                                });
                            }
                            filterForm.submit();
                        });
                } else {
                    filterForm.submit();
                }
            });

            // Other filters that trigger immediate submit
            [barangayFilter, ethnoFilter].forEach(filter => {
                filter?.addEventListener('change', () => filterForm.submit());
            });
        };

        setupGeographicFilters();
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