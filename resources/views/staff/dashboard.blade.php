@extends('layouts.app')

@section('content')
<!-- Notification Bar -->
<div class="bg-gradient-to-r from-orange-500 to-red-600 text-white py-3 px-4 mb-6">
    <div class="container mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-semibold">System Notifications</span>
                </div>
                <div class="flex items-center space-x-6 text-sm">
                    <span class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span>System Online</span>
                    </span>
                    <span class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                        <span>{{ $newApplicants }} New Applications</span>
                    </span>
                    <span class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-red-400 rounded-full animate-pulse"></div>
                        <span>{{ collect($pendingRequirements)->where('is_overdue', true)->count() }} Overdue Requirements</span>
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Notification Bell -->
                <div class="relative">
                    <button class="text-white hover:text-orange-200 transition-colors relative" onclick="toggleNotifDropdown()" aria-label="Notifications">
                        <!-- Bell Icon SVG -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 15.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v4.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($notifications->count() > 0)
                            <span id="notif-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">{{ $notifications->count() }}</span>
                        @endif
                    </button>
                    <!-- Dropdown -->
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white text-gray-800 rounded shadow-lg z-50 border border-orange-200">
                        <div class="p-3 border-b font-semibold text-orange-700">Notifications</div>
                        <ul class="max-h-72 overflow-y-auto">
                            @forelse($notifications as $notif)
                                <li class="px-4 py-2 border-b hover:bg-orange-50 cursor-pointer">
                                    <div class="text-sm">{{ $notif->data['message'] ?? '' }}</div>
                                    <div class="text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</div>
                                </li>
                            @empty
                                <li class="px-4 py-2 text-gray-400">No new notifications.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <!-- End Notification Bell -->
                <span class="text-xs opacity-75">Last updated: {{ now()->format('M d, Y g:i A') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Alerts Section -->
<div id="quick-alerts" class="container mx-auto mb-6 hidden">
    <div class="bg-white rounded-lg shadow-lg border-l-4 border-orange-500 p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-800">Recent Alerts</h3>
            <button class="text-gray-500 hover:text-gray-700" onclick="toggleNotifications()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="space-y-2">
            @if($newApplicants > 0)
            <div class="flex items-center space-x-2 text-sm">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                <span class="text-gray-700">{{ $newApplicants }} new scholarship applications require review</span>
            </div>
            @endif
            @if(collect($pendingRequirements)->where('is_overdue', true)->count() > 0)
            <div class="flex items-center space-x-2 text-sm">
                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                <span class="text-gray-700">{{ collect($pendingRequirements)->where('is_overdue', true)->count() }} requirements are overdue</span>
            </div>
            @endif
            @if(collect($pendingRequirements)->where('priority', 1)->count() > 0)
            <div class="flex items-center space-x-2 text-sm">
                <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                <span class="text-gray-700">{{ collect($pendingRequirements)->where('priority', 1)->count() }} priority documents pending</span>
            </div>
            @endif
            <div class="flex items-center space-x-2 text-sm">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <span class="text-gray-700">System maintenance scheduled for Saturday 2:00 AM</span>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">NCIP Staff Dashboard</h1>
            <p class="text-gray-600">Welcome, {{ $name }} ({{ $assignedBarangay }})</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('staff.applicants.list') }}" class="btn btn-primary">View Applicants</a>
            <a href="{{ route('staff.reports.download') }}" class="btn btn-primary">Download Report</a>
            <form method="POST" action="{{ route('staff.logout') }}" class="inline">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>

    <!-- Geographic Filters -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <h3 class="font-semibold mb-3 text-gray-800">Geographic Filters</h3>
        <form id="filter-form" method="GET" action="{{ route('staff.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                <select name="province" id="province-filter" class="form-select w-full border rounded p-2">
                    <option value="">All Provinces</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" {{ $selectedProvince == $province ? 'selected' : '' }}>{{ $province }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Municipality</label>
                <select name="municipality" id="municipality-filter" class="form-select w-full border rounded p-2">
                    <option value="">All Municipalities</option>
                    @foreach($municipalities as $municipality)
                        <option value="{{ $municipality }}" {{ $selectedMunicipality == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                <select name="barangay" id="barangay-filter" class="form-select w-full border rounded p-2">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IP Group</label>
                <select name="ethno" id="ethno-filter" class="form-select w-full border rounded p-2">
                    <option value="">All IP Groups</option>
                    @foreach($ethnicities as $ethno)
                        <option value="{{ $ethno->id }}" {{ $selectedEthno == $ethno->id ? 'selected' : '' }}>{{ $ethno->ethnicity }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-metric-card title="Total Scholars" :value="$totalScholars" icon="users" />
        <x-metric-card title="New Applicants" :value="$newApplicants" icon="user-plus" />
        <x-metric-card title="Active Scholars" :value="$activeScholars" icon="user-check" />
        <x-metric-card title="Inactive Scholars" :value="$inactiveScholars" icon="user-x" />
    </div>

    <!-- Data Visualizations -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold mb-2">Scholars per Barangay</h2>
            <canvas id="barChart"></canvas>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold mb-2">Application Status Breakdown</h2>
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow mb-6">
        <h2 class="font-semibold mb-2">Academic Performance Summary</h2>
        <canvas id="performanceChart"></canvas>
    </div>

    <!-- Pending Requirements Tracker (Prioritized) -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <h2 class="font-semibold mb-2">Pending Requirements (Prioritized)</h2>
        <ul>
            @foreach(collect($pendingRequirements)->sortBy('priority') as $item)
                <li class="flex justify-between items-center py-1">
                    <div>
                        <span class="font-semibold">{{ $item->scholar_name }}</span>
                        <span class="ml-2 text-xs px-2 py-1 rounded
                            {{ $item->priority == 1 ? 'bg-green-100 text-green-700 font-bold' : 'bg-gray-100 text-gray-700' }}">
                            {{ $item->priority == 1 ? 'PRIORITY: Certificate of Low Income' : 'Other Document' }}
                        </span>
                    </div>
                    <span class="text-xs px-2 py-1 rounded {{ $item->is_overdue ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $item->is_overdue ? 'Overdue' : 'Pending' }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Alerts/Deadlines Table -->
    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Recent Alerts & Deadlines</h2>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th>Scholar</th>
                    <th>Alert</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alerts as $alert)
                <tr>
                    <td>{{ $alert->scholar_name }}</td>
                    <td>{{ $alert->message }}</td>
                    <td>{{ $alert->due_date }}</td>
                    <td>{{ $alert->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Feedback/Support -->
    <div id="feedback-section" class="bg-white p-4 rounded shadow mt-6 md:w-96 mx-auto">
        <h2 class="font-semibold mb-2">Feedback & Support</h2>
        @if(session('success'))
            <div class="mb-2 p-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('staff.feedback') }}" class="mb-4">
            @csrf
            <textarea name="feedback_text" class="form-textarea w-full mb-2 border rounded p-2" rows="2" placeholder="Describe your issue or suggestion..." required></textarea>
            <button class="btn btn-secondary w-full bg-blue-600 text-white py-2 rounded">Submit</button>
        </form>
        <h3 class="font-semibold text-sm mb-1">Previous Feedback</h3>
        <ul class="max-h-32 overflow-y-auto text-xs text-gray-700">
            @forelse($feedbacks as $fb)
                <li class="mb-1 border-b pb-1">{{ $fb['text'] }} <span class="text-gray-400">({{ $fb['submitted_at'] }})</span></li>
            @empty
                <li class="text-gray-400">No feedback submitted yet.</li>
            @endforelse
        </ul>
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
</script>
@endpush 