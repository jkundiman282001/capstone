@extends('layouts.student')

@section('title', 'Notifications - IP Scholar Portal')

@push('head-scripts')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    
    body {
        font-family: 'Inter', sans-serif;
    }
    
    .notification-list-item {
        background: white;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .notification-list-item::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #f97316 0%, #ea580c 50%, #dc2626 100%);
        transform: scaleY(0);
        transform-origin: top;
        transition: transform 0.3s ease;
    }
    
    .notification-list-item.unread::after {
        transform: scaleY(1);
    }
    
    .notification-list-item:hover {
        transform: translateX(4px);
        box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.1);
    }
    
    .notification-list-item.unread {
        background: linear-gradient(90deg, #fff7ed 0%, #ffffff 100%);
    }
    
    .notification-list-item.read {
        background: white;
        opacity: 0.95;
    }
    
    .icon-badge {
        width: 40px;
        height: 40px;
        min-width: 40px;
        min-height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        position: relative;
    }
    
    .icon-badge svg {
        display: block;
        width: 20px;
        height: 20px;
        margin: 0;
        padding: 0;
        flex-shrink: 0;
    }
    
    .icon-badge.urgent {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .icon-badge.high {
        background: #fed7aa;
        color: #ea580c;
    }
    
    .icon-badge.normal {
        background: #dbeafe;
        color: #2563eb;
    }
    
    .unread-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        flex-shrink: 0;
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(1.1);
        }
    }
    
    .stat-box {
        background: white;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .stat-box:hover {
        border-color: #f97316;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
    }
    
    .filter-button {
        transition: all 0.2s ease;
    }
    
    .filter-button.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
    }
    
    .filter-button:not(.active):hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
    
    .time-badge {
        background: #f1f5f9;
        color: #64748b;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 pt-20 pb-12">
    
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-6">
                <div>
                    <h1 class="text-4xl font-black text-slate-900 mb-2">Notifications</h1>
                    <p class="text-slate-600">Manage your application updates</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="markAllAsRead()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg transition-all text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mark All Read
                    </button>
                    <button onclick="refreshNotifications()" class="p-2 bg-white hover:bg-slate-50 rounded-lg transition-all border border-slate-200">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-3 mb-6">
                <div class="stat-box rounded-lg p-4 text-center">
                    <div class="text-2xl font-black text-slate-900 mb-1">{{ count($notifications) }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase">Total</div>
                </div>
                <div class="stat-box rounded-lg p-4 text-center">
                    <div class="text-2xl font-black text-orange-600 mb-1">{{ collect($notifications)->where('is_read', false)->count() }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase">Unread</div>
                </div>
                <div class="stat-box rounded-lg p-4 text-center">
                    <div class="text-2xl font-black text-red-600 mb-1">{{ collect($notifications)->where('priority', 'urgent')->count() }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase">Urgent</div>
                </div>
            </div>
            
            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div class="relative flex-1 max-w-sm w-full">
                    <input 
                        type="text" 
                        id="searchNotifications" 
                        placeholder="Search..." 
                        class="w-full px-4 py-2.5 pl-10 bg-white border-2 border-slate-200 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all text-slate-700"
                        onkeyup="searchNotifications(this.value)"
                    >
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="filterNotifications('all')" class="filter-button active px-4 py-2 rounded-lg font-bold text-sm bg-slate-900 text-white border-2 border-slate-900" data-filter="all">
                        All (<span id="count-all">{{ count($notifications) }}</span>)
                    </button>
                    <button onclick="filterNotifications('unread')" class="filter-button px-4 py-2 rounded-lg font-bold text-sm bg-white text-slate-700 border-2 border-slate-200" data-filter="unread">
                        Unread (<span id="count-unread">{{ collect($notifications)->where('is_read', false)->count() }}</span>)
                    </button>
                    <button onclick="filterNotifications('urgent')" class="filter-button px-4 py-2 rounded-lg font-bold text-sm bg-white text-slate-700 border-2 border-slate-200" data-filter="urgent">
                        Urgent (<span id="count-urgent">{{ collect($notifications)->where('priority', 'urgent')->count() }}</span>)
                    </button>
                    <button onclick="filterNotifications('application')" class="filter-button px-4 py-2 rounded-lg font-bold text-sm bg-white text-slate-700 border-2 border-slate-200" data-filter="application">
                        Application (<span id="count-application">{{ collect($notifications)->where('type', 'application_status')->count() }}</span>)
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-3">
            @forelse($notifications as $index => $notification)
                <div class="notification-list-item {{ $notification['is_read'] ? 'read' : 'unread' }} animate-fade-in" 
                     style="animation-delay: {{ $index * 0.03 }}s; opacity: 0;"
                     data-type="{{ $notification['type'] }}" 
                     data-priority="{{ $notification['priority'] }}" 
                     data-read="{{ $notification['is_read'] ? 'true' : 'false' }}" 
                     data-id="{{ $notification['id'] }}"
                     data-title="{{ strtolower($notification['title']) }}"
                     data-message="{{ strtolower($notification['message']) }}">
                    
                    <div class="p-5">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="icon-badge 
                                @if($notification['priority'] === 'urgent') urgent
                                @elseif($notification['priority'] === 'high') high
                                @else normal
                                @endif">
                                @if($notification['type'] === 'application_status')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($notification['type'] === 'requirement_reminder')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <h3 class="text-base font-black text-slate-900">{{ $notification['title'] }}</h3>
                                        @if(!$notification['is_read'])
                                            <span class="unread-dot"></span>
                                        @endif
                                        @if($notification['priority'] === 'urgent')
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs font-bold">Urgent</span>
                                        @elseif($notification['priority'] === 'high')
                                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-xs font-bold">High</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        @if(!$notification['is_read'])
                                            <button onclick="markAsRead('{{ $notification['id'] }}')" class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded transition-all" title="Mark as read">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        @endif
                                        <button onclick="deleteNotification('{{ $notification['id'] }}')" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-all" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <p class="text-sm text-slate-600 mb-3 leading-relaxed">{{ $notification['message'] }}</p>
                                
                                <div class="flex items-center gap-3 text-xs">
                                    <span class="time-badge">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $notification['created_at']->diffForHumans() }}
                                    </span>
                                    <span class="text-slate-400">•</span>
                                    <span class="text-slate-500 font-medium">{{ str_replace('_', ' ', ucwords($notification['type'], '_')) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-lg border-2 border-dashed border-slate-200">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-2">No Notifications</h3>
                    <p class="text-slate-600 text-sm">You're all caught up!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let currentFilter = 'all';
    let searchQuery = '';
    
    // Search functionality
    function searchNotifications(query) {
        searchQuery = query.toLowerCase();
        applyFilters();
    }
    
    // Filter functionality
    function filterNotifications(filter) {
        currentFilter = filter;
        applyFilters();
        
        // Update active button
        const buttons = document.querySelectorAll('.filter-button');
        buttons.forEach(btn => {
            btn.classList.remove('active', 'bg-slate-900', 'text-white', 'border-slate-900');
            btn.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
        });
        event.target.classList.add('active', 'bg-slate-900', 'text-white', 'border-slate-900');
        event.target.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
    }
    
    // Apply both search and filter
    function applyFilters() {
        const notifications = document.querySelectorAll('.notification-list-item');
        let visibleCount = 0;
        
        notifications.forEach(notification => {
            const type = notification.dataset.type;
            const priority = notification.dataset.priority;
            const isRead = notification.dataset.read === 'true';
            const title = notification.dataset.title || '';
            const message = notification.dataset.message || '';
            
            // Filter logic
            let show = false;
            switch(currentFilter) {
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
            
            // Search logic
            if (show && searchQuery) {
                show = title.includes(searchQuery) || message.includes(searchQuery);
            }
            
            notification.style.display = show ? 'flex' : 'none';
            if (show) visibleCount++;
        });
        
        // Show empty state if no results
        const emptyState = document.querySelector('.empty-state');
        if (visibleCount === 0 && notifications.length > 0) {
            if (!emptyState) {
                const container = document.querySelector('.space-y-3');
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'empty-state text-center py-16 bg-white rounded-lg border-2 border-dashed border-slate-200';
                emptyDiv.innerHTML = `
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-2">No Results Found</h3>
                    <p class="text-slate-600 text-sm">Try adjusting your search or filter.</p>
                `;
                container.appendChild(emptyDiv);
            }
        } else if (emptyState) {
            emptyState.remove();
        }
    }
    
    // Mark as read functionality with AJAX
    function markAsRead(id) {
        const notification = document.querySelector(`[data-id="${id}"]`);
        if (!notification) return;
        
        // Optimistic UI update
        notification.classList.remove('unread');
        notification.classList.add('read');
        notification.dataset.read = 'true';
        
        const unreadDot = notification.querySelector('.unread-dot');
        if (unreadDot) unreadDot.remove();
        
        const markReadBtn = notification.querySelector('button[onclick*="markAsRead"]');
        if (markReadBtn) markReadBtn.remove();
        
        // Update stats
        updateStats();
        
        // Send AJAX request
        fetch(`/student/notifications/${id}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                notification.classList.remove('read');
                notification.classList.add('unread');
                notification.dataset.read = 'false';
                alert('Failed to mark notification as read. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            notification.classList.remove('read');
            notification.classList.add('unread');
            notification.dataset.read = 'false';
            alert('An error occurred. Please try again.');
        });
    }
    
    // Mark all as read with AJAX
    function markAllAsRead() {
        const unreadNotifications = document.querySelectorAll('.notification-list-item.unread');
        if (unreadNotifications.length === 0) {
            return;
        }
        
        // Optimistic UI update
        unreadNotifications.forEach(notification => {
            notification.classList.remove('unread');
            notification.classList.add('read');
            notification.dataset.read = 'true';
            
            const unreadDot = notification.querySelector('.unread-dot');
            if (unreadDot) unreadDot.remove();
            
            const markReadBtn = notification.querySelector('button[onclick*="markAsRead"]');
            if (markReadBtn) markReadBtn.remove();
        });
        
        // Update stats
        updateStats();
        
        // Send AJAX request
        fetch('/student/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Failed to mark all notifications as read. Please refresh the page.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please refresh the page.');
        });
    }
    
    // Delete notification with AJAX
    function deleteNotification(id) {
        if (!confirm('Are you sure you want to delete this notification?')) {
            return;
        }
        
        const notification = document.querySelector(`[data-id="${id}"]`);
        if (!notification) return;
        
        // Optimistic UI update
        notification.style.transition = 'all 0.3s ease';
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(-20px)';
        
        // Send AJAX request
        fetch(`/student/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                setTimeout(() => {
                    notification.remove();
                    updateStats();
                    applyFilters();
                }, 300);
            } else {
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
                alert('Failed to delete notification. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
            alert('An error occurred. Please try again.');
        });
    }
    
    // Refresh notifications
    function refreshNotifications() {
        const refreshBtn = event.target.closest('button');
        const originalHTML = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<svg class="w-5 h-5 text-slate-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        refreshBtn.disabled = true;
        
        setTimeout(() => {
            location.reload();
        }, 500);
    }
    
    // Update stats dynamically
    function updateStats() {
        const notifications = document.querySelectorAll('.notification-list-item');
        const total = notifications.length;
        const unread = document.querySelectorAll('.notification-list-item.unread').length;
        const urgent = document.querySelectorAll('.notification-list-item[data-priority="urgent"]').length;
        const application = document.querySelectorAll('.notification-list-item[data-type="application_status"]').length;
        
        // Update filter button counts
        const totalEl = document.getElementById('count-all');
        const unreadEl = document.getElementById('count-unread');
        const urgentEl = document.getElementById('count-urgent');
        const applicationEl = document.getElementById('count-application');
        
        if (totalEl) totalEl.textContent = total;
        if (unreadEl) unreadEl.textContent = unread;
        if (urgentEl) urgentEl.textContent = urgent;
        if (applicationEl) applicationEl.textContent = application;
        
        // Update stat boxes
        const statBoxes = document.querySelectorAll('.stat-box');
        if (statBoxes.length >= 3) {
            statBoxes[0].querySelector('.text-2xl').textContent = total;
            statBoxes[1].querySelector('.text-2xl').textContent = unread;
            statBoxes[2].querySelector('.text-2xl').textContent = urgent;
        }
    }
    
    // Clear search on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const searchInput = document.getElementById('searchNotifications');
            if (searchInput && document.activeElement === searchInput) {
                searchInput.value = '';
                searchNotifications('');
            }
        }
    });
    
    // Animate on load
    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.notification-list-item');
        items.forEach((item, index) => {
            setTimeout(() => {
                item.style.opacity = '1';
            }, index * 30);
        });
    });
</script>
@endpush
@endsection
