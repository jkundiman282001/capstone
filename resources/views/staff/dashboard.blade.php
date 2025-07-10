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
                <button class="text-white hover:text-orange-200 transition-colors" onclick="toggleNotifications()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                    </svg>
                </button>
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
            <select class="form-select" name="barangay">
                <option value="">All Barangays</option>
                @foreach($barangays as $barangay)
                    <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                @endforeach
            </select>
            <select class="form-select" name="ip_group">
                <option value="">All IP Groups</option>
                @foreach($ipGroups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
            <select class="form-select" name="semester">
                <option value="">All Semesters</option>
                @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                @endforeach
            </select>
            <a href="{{ route('staff.reports.download') }}" class="btn btn-primary">Download Report</a>
        </div>
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
</script>
@endpush 