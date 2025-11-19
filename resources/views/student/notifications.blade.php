@extends('layouts.student')

@section('title', 'Notifications - IP Scholar Portal')

@push('head-scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'ip-cyan': '#06B6D4',
                    'ip-dark': '#0F172A',
                    'ip-card': '#1E293B'
                }
            }
        }
    }
</script>
@endpush

@push('styles')
<style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #767575 0%, #C0C0C0 50%, #FFFFFF 100%);
        }
        
        .notification-card {
            transition: all 0.3s ease;
        }
        
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .priority-urgent {
            border-left-color: #ef4444;
        }
        
        .priority-high {
            border-left-color: #f97316;
        }
        
        .priority-normal {
            border-left-color: #06b6d4;
        }
        
        .unread {
            background-color: #fef3c7;
        }
        
        .read {
            background-color: #f8fafc;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 pt-20">

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white py-12">
        <div class="max-w-4xl mx-auto px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Notifications</h1>
                    <p class="text-orange-100">Stay updated with your scholarship application status and important announcements</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="markAllAsRead()" class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg hover:bg-white/30 transition-all text-sm">
                        Mark All as Read
                    </button>
                    <button onclick="refreshNotifications()" class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg hover:bg-white/30 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Section -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-sm border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Notifications</p>
                        <p class="text-2xl font-bold text-gray-800">{{ count($notifications) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-sm border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Unread</p>
                        <p class="text-2xl font-bold text-orange-600">{{ collect($notifications)->where('is_read', false)->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-sm border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Urgent</p>
                        <p class="text-2xl font-bold text-red-600">{{ collect($notifications)->where('priority', 'urgent')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-xl p-4 shadow-sm border mb-6">
            <div class="flex items-center space-x-6">
                <button onclick="filterNotifications('all')" class="filter-tab active px-4 py-2 rounded-lg font-medium transition-all" data-filter="all">
                    All ({{ count($notifications) }})
                </button>
                <button onclick="filterNotifications('unread')" class="filter-tab px-4 py-2 rounded-lg font-medium transition-all" data-filter="unread">
                    Unread ({{ collect($notifications)->where('is_read', false)->count() }})
                </button>
                <button onclick="filterNotifications('urgent')" class="filter-tab px-4 py-2 rounded-lg font-medium transition-all" data-filter="urgent">
                    Urgent ({{ collect($notifications)->where('priority', 'urgent')->count() }})
                </button>
                <button onclick="filterNotifications('application')" class="filter-tab px-4 py-2 rounded-lg font-medium transition-all" data-filter="application">
                    Application ({{ collect($notifications)->where('type', 'application_status')->count() }})
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="notification-card bg-white rounded-xl p-6 shadow-sm border-l-4 {{ $notification['is_read'] ? 'read' : 'unread' }} priority-{{ $notification['priority'] }}" data-type="{{ $notification['type'] }}" data-priority="{{ $notification['priority'] }}" data-read="{{ $notification['is_read'] ? 'true' : 'false' }}" data-id="{{ $notification['id'] }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="font-semibold text-gray-800">{{ $notification['title'] }}</h3>
                                @if(!$notification['is_read'])
                                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                @endif
                                @if($notification['priority'] === 'urgent')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Urgent</span>
                                @elseif($notification['priority'] === 'high')
                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-medium">High</span>
                                @endif
                            </div>
                            <p class="text-gray-600 mb-3">{{ $notification['message'] }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $notification['created_at']->diffForHumans() }}</span>
                                    </span>
                                    <span class="capitalize">{{ $notification['type'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if(!$notification['is_read'])
                                        <button onclick="markAsRead({{ $notification['id'] }})" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                                            Mark as Read
                                        </button>
                                    @endif
                                    <button onclick="deleteNotification({{ $notification['id'] }})" class="text-sm text-gray-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl p-12 text-center shadow-sm border">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No notifications yet</h3>
                    <p class="text-gray-600">You're all caught up! Check back later for updates on your application.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        // Filter functionality
        function filterNotifications(filter) {
            const notifications = document.querySelectorAll('.notification-card');
            const tabs = document.querySelectorAll('.filter-tab');
            
            // Update active tab
            tabs.forEach(tab => {
                tab.classList.remove('active', 'bg-orange-100', 'text-orange-700');
                tab.classList.add('text-gray-600');
            });
            event.target.classList.add('active', 'bg-orange-100', 'text-orange-700');
            
            notifications.forEach(notification => {
                const type = notification.dataset.type;
                const priority = notification.dataset.priority;
                const isRead = notification.dataset.read === 'true';
                
                let show = false;
                
                switch(filter) {
                    case 'all':
                        show = true;
                        break;
                    case 'unread':
                        show = !isRead;
                        break;
                    case 'urgent':
                        show = priority === 'urgent';
                        break;
                    case 'application':
                        show = type === 'application_status';
                        break;
                }
                
                notification.style.display = show ? 'block' : 'none';
            });
        }
        
        // Mark as read functionality
        function markAsRead(id) {
            // In a real app, make an AJAX call to mark as read
            const notification = document.querySelector(`[data-id="${id}"]`);
            if (notification) {
                notification.classList.remove('unread');
                notification.classList.add('read');
                notification.dataset.read = 'true';
                
                // Remove unread indicator
                const indicator = notification.querySelector('.bg-orange-500');
                if (indicator) indicator.remove();
                
                // Update stats
                updateStats();
            }
        }
        
        // Mark all as read
        function markAllAsRead() {
            const unreadNotifications = document.querySelectorAll('.notification-card.unread');
            unreadNotifications.forEach(notification => {
                notification.classList.remove('unread');
                notification.classList.add('read');
                notification.dataset.read = 'true';
                
                const indicator = notification.querySelector('.bg-orange-500');
                if (indicator) indicator.remove();
            });
            
            updateStats();
        }
        
        // Delete notification
        function deleteNotification(id) {
            if (confirm('Are you sure you want to delete this notification?')) {
                const notification = document.querySelector(`[data-id="${id}"]`);
                if (notification) {
                    notification.remove();
                    updateStats();
                }
            }
        }
        
        // Refresh notifications
        function refreshNotifications() {
            location.reload();
        }
        
        // Update stats
        function updateStats() {
            const total = document.querySelectorAll('.notification-card').length;
            const unread = document.querySelectorAll('.notification-card.unread').length;
            const urgent = document.querySelectorAll('.notification-card[data-priority="urgent"]').length;
            
            // Update stats display (you would update the actual elements here)
            console.log(`Total: ${total}, Unread: ${unread}, Urgent: ${urgent}`);
        }
</div>
@endsection

@push('scripts')
<script>
    // Notification functions
    function markAllAsRead() {
        // Implementation here
    }
    
    function refreshNotifications() {
        location.reload();
    }
    
    // Update stats
    function updateStats() {
        const total = document.querySelectorAll('.notification-card').length;
        const unread = document.querySelectorAll('.notification-card.unread').length;
        const urgent = document.querySelectorAll('.notification-card[data-priority="urgent"]').length;
        
        // Update stats display (you would update the actual elements here)
        console.log(`Total: ${total}, Unread: ${unread}, Urgent: ${urgent}`);
    }
</script>
@endpush 
