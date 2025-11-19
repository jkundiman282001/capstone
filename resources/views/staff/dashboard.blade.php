@extends('layouts.app')

@section('content')
<!-- Notification Bar -->
<div class="bg-indigo-600 text-white shadow-lg mb-6">
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-6">
                <div class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg">
                    <svg class="w-5 h-5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-semibold text-sm">System Status</span>
                </div>
                <div class="flex flex-wrap items-center gap-4 md:gap-6 text-sm">
                    <span class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg">
                        <div class="w-2.5 h-2.5 bg-emerald-300 rounded-full animate-pulse shadow-lg shadow-emerald-300/50"></div>
                        <span class="font-medium">System Online</span>
                    </span>
                    <span class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg">
                        <div class="w-2.5 h-2.5 bg-amber-300 rounded-full shadow-lg shadow-amber-300/50"></div>
                        <span class="font-medium">{{ $newApplicants }} New Applications</span>
                    </span>
                    <span class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg">
                        <div class="w-2.5 h-2.5 bg-rose-300 rounded-full animate-pulse shadow-lg shadow-rose-300/50"></div>
                        <span class="font-medium">{{ collect($pendingRequirements)->where('is_overdue', true)->count() }} Overdue</span>
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Notification Bell -->
                <div class="relative">
                    <button class="text-white hover:text-indigo-100 transition-all duration-200 relative p-2 rounded-lg hover:bg-white/10 backdrop-blur-sm" onclick="toggleNotifDropdown()" aria-label="Notifications">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 15.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v4.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($notifications->count() > 0)
                            <span id="notif-badge" class="absolute -top-0.5 -right-0.5 bg-rose-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center shadow-lg">{{ $notifications->count() }}</span>
                        @endif
                    </button>
                    <!-- Dropdown -->
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white text-gray-800 rounded-xl shadow-2xl z-50 border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b bg-indigo-50 font-semibold text-indigo-700 flex items-center justify-between">
                            <span>Notifications</span>
                            <span class="text-xs font-normal text-gray-500">{{ $notifications->count() }} new</span>
                        </div>
                        <ul class="max-h-72 overflow-y-auto">
                            @forelse($notifications as $notif)
                                <li class="px-4 py-3 border-b border-gray-100 hover:bg-indigo-50 cursor-pointer transition-all duration-150">
                                    <div class="text-sm text-gray-700 font-medium">{{ $notif->data['message'] ?? '' }}</div>
                                    <div class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                                </li>
                            @empty
                                <li class="px-4 py-6 text-center text-gray-400">No new notifications.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <!-- End Notification Bell -->
                <span class="text-xs opacity-90 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg font-medium">Updated: {{ now()->format('M d, Y g:i A') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Alerts Section -->
<div id="quick-alerts" class="container mx-auto mb-6 hidden">
    <div class="bg-indigo-50 rounded-xl shadow-lg border-l-4 border-indigo-500 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Recent Alerts
            </h3>
            <button class="text-gray-500 hover:text-gray-700 transition-colors p-1 rounded-lg hover:bg-white" onclick="toggleNotifications()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="space-y-3">
            @if($newApplicants > 0)
            <div class="flex items-center space-x-3 text-sm bg-white p-3 rounded-lg shadow-sm">
                <div class="w-3 h-3 bg-blue-500 rounded-full shadow-lg shadow-blue-500/50"></div>
                <span class="text-gray-700 font-medium">{{ $newApplicants }} new scholarship applications require review</span>
            </div>
            @endif
            @if(collect($pendingRequirements)->where('is_overdue', true)->count() > 0)
            <div class="flex items-center space-x-3 text-sm bg-white p-3 rounded-lg shadow-sm">
                <div class="w-3 h-3 bg-red-500 rounded-full shadow-lg shadow-red-500/50"></div>
                <span class="text-gray-700 font-medium">{{ collect($pendingRequirements)->where('is_overdue', true)->count() }} requirements are overdue</span>
            </div>
            @endif
            @if(collect($pendingRequirements)->where('priority', 1)->count() > 0)
            <div class="flex items-center space-x-3 text-sm bg-white p-3 rounded-lg shadow-sm">
                <div class="w-3 h-3 bg-orange-500 rounded-full shadow-lg shadow-orange-500/50"></div>
                <span class="text-gray-700 font-medium">{{ collect($pendingRequirements)->where('priority', 1)->count() }} priority documents pending</span>
            </div>
            @endif
            <div class="flex items-center space-x-3 text-sm bg-white p-3 rounded-lg shadow-sm">
                <div class="w-3 h-3 bg-green-500 rounded-full shadow-lg shadow-green-500/50"></div>
                <span class="text-gray-700 font-medium">System maintenance scheduled for Saturday 2:00 AM</span>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="bg-indigo-600 rounded-2xl shadow-xl p-6 mb-6 text-white">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">NCIP Staff Dashboard</h1>
                <p class="text-indigo-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Welcome, <span class="font-semibold">{{ $name }}</span> ({{ $assignedBarangay }})
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('staff.applicants.list') }}" class="bg-white text-indigo-600 hover:bg-indigo-50 font-semibold px-4 py-2 rounded-lg shadow-md transition-all duration-200 hover:shadow-lg">
                    View Applicants
                </a>
                <a href="{{ route('staff.reports.download') }}" class="bg-indigo-700 text-white hover:bg-indigo-800 font-semibold px-4 py-2 rounded-lg shadow-md transition-all duration-200 hover:shadow-lg">
                    Download Report
                </a>
                <form method="POST" action="{{ route('staff.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 font-semibold px-4 py-2 rounded-lg shadow-md transition-all duration-200">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Geographic Filters -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-4 text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Geographic Filters
        </h3>
        <form id="filter-form" method="GET" action="{{ route('staff.dashboard') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Province</label>
                <select name="province" id="province-filter" class="form-select w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                    <option value="">All Provinces</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" {{ $selectedProvince == $province ? 'selected' : '' }}>{{ $province }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Municipality</label>
                <select name="municipality" id="municipality-filter" class="form-select w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                    <option value="">All Municipalities</option>
                    @foreach($municipalities as $municipality)
                        <option value="{{ $municipality }}" {{ $selectedMunicipality == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Barangay</label>
                <select name="barangay" id="barangay-filter" class="form-select w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">IP Group</label>
                <select name="ethno" id="ethno-filter" class="form-select w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                    <option value="">All IP Groups</option>
                    @foreach($ethnicities as $ethno)
                        <option value="{{ $ethno->id }}" {{ $selectedEthno == $ethno->id ? 'selected' : '' }}>{{ $ethno->ethnicity }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-metric-card title="Total Scholars" :value="$totalScholars" icon="users" />
        <x-metric-card title="New Applicants" :value="$newApplicants" icon="user-plus" />
        <x-metric-card title="Active Scholars" :value="$activeScholars" icon="user-check" />
        <x-metric-card title="Inactive Scholars" :value="$inactiveScholars" icon="user-x" />
    </div>

    
    <!-- Data Visualizations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <h2 class="font-bold text-gray-800 mb-4 text-lg">Scholars per Barangay</h2>
            <div class="h-64">
                <canvas id="barChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <h2 class="font-bold text-gray-800 mb-4 text-lg">Application Status Breakdown</h2>
            <div class="h-64">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
        <h2 class="font-bold text-gray-800 mb-4 text-lg">Academic Performance Summary</h2>
        <div class="h-80">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <!-- Pending Requirements Tracker (Prioritized) -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
        <h2 class="font-bold text-gray-800 mb-4 text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Pending Requirements (Prioritized)
        </h2>
        <div class="space-y-3">
            @foreach(collect($pendingRequirements)->sortBy('priority') as $item)
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="flex flex-col md:flex-row md:items-center gap-2">
                        <span class="font-semibold text-gray-800">{{ $item->scholar_name }}</span>
                        <span class="text-xs px-3 py-1.5 rounded-full font-semibold
                            {{ $item->priority == 1 ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-gray-100 text-gray-700 border border-gray-300' }}">
                            {{ $item->priority == 1 ? 'PRIORITY: Certificate of Low Income' : 'Other Document' }}
                        </span>
                    </div>
                    <span class="text-xs px-3 py-1.5 rounded-full font-semibold {{ $item->is_overdue ? 'bg-rose-100 text-rose-700 border border-rose-300' : 'bg-amber-100 text-amber-700 border border-amber-300' }}">
                        {{ $item->is_overdue ? 'Overdue' : 'Pending' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Alerts/Deadlines Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6 overflow-hidden">
        <h2 class="font-bold text-gray-800 mb-4 text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Recent Alerts & Deadlines
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Scholar</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Alert</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($alerts as $alert)
                    <tr class="hover:bg-indigo-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $alert->scholar_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $alert->message }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $alert->due_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">{{ $alert->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Feedback/Support -->
    <div id="feedback-section" class="bg-indigo-50 rounded-xl shadow-lg border border-indigo-200 p-6 mt-6 max-w-2xl mx-auto">
        <h2 class="font-bold text-gray-800 mb-4 text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Feedback & Support
        </h2>
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-700 rounded-lg font-medium">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('staff.feedback') }}" class="mb-4">
            @csrf
            <textarea name="feedback_text" class="form-textarea w-full mb-3 border-2 border-gray-300 rounded-lg p-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" rows="3" placeholder="Describe your issue or suggestion..." required></textarea>
            <button class="btn btn-secondary w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg shadow-md transition-all duration-200 hover:shadow-lg">
                Submit Feedback
            </button>
        </form>
        <h3 class="font-semibold text-sm mb-2 text-gray-700">Previous Feedback</h3>
        <div class="bg-white rounded-lg border border-gray-200 max-h-40 overflow-y-auto p-3">
            <ul class="space-y-2 text-sm text-gray-700">
                @forelse($feedbacks as $fb)
                    <li class="pb-2 border-b border-gray-200 last:border-0">
                        <p class="mb-1">{{ $fb['text'] }}</p>
                        <span class="text-xs text-gray-400">{{ $fb['submitted_at'] }}</span>
                    </li>
                @empty
                    <li class="text-center text-gray-400 py-4">No feedback submitted yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {!! json_encode($barChartData) !!},
        options: { responsive: true }
    });
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {!! json_encode($pieChartData) !!},
        options: { responsive: true }
    });
    new Chart(document.getElementById('performanceChart'), {
        type: 'line',
        data: {!! json_encode($performanceChartData) !!},
        options: { responsive: true }
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