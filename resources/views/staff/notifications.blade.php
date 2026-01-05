@extends('layouts.app')

@section('content')
<div class="p-6 sm:p-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Notifications</h1>
            <p class="text-slate-500 font-medium mt-1">Manage and view your system alerts</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="markAllAsRead()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 hover:border-orange-300 hover:text-orange-700 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Mark all as read
            </button>
        </div>
    </div>

    <!-- Notification Filters -->
    <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm mb-6 flex flex-wrap items-center gap-4">
        <button onclick="filterNotifs('all')" id="filter-all" class="px-4 py-2 rounded-xl text-sm font-bold transition-all bg-orange-600 text-white shadow-lg shadow-orange-600/20">All</button>
        <button onclick="filterNotifs('unread')" id="filter-unread" class="px-4 py-2 rounded-xl text-sm font-bold transition-all text-slate-600 hover:bg-slate-50">Unread</button>
    </div>

    <!-- Notifications List -->
    <div class="space-y-4" id="notifications-container">
        @forelse($notifications as $notif)
            <div data-id="{{ $notif['id'] }}" data-read="{{ $notif['is_read'] ? 'true' : 'false' }}" class="notification-item group relative bg-white rounded-3xl border-2 {{ $notif['is_read'] ? 'border-slate-100' : 'border-orange-200 bg-orange-50/30' }} p-6 transition-all hover:shadow-xl hover:-translate-y-1">
                <div class="flex items-start gap-4">
                    <!-- Icon based on type/priority -->
                    <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center {{ $notif['is_read'] ? 'bg-slate-100 text-slate-500' : 'bg-orange-100 text-orange-600 animate-pulse' }}">
                        @if($notif['priority'] === 'urgent')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 15.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v4.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h3 class="text-lg font-bold text-slate-900 truncate group-hover:text-orange-600 transition-colors">
                                {{ $notif['title'] }}
                            </h3>
                            <span class="text-xs font-bold text-slate-400 whitespace-nowrap">
                                {{ $notif['created_at']->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-slate-600 leading-relaxed">
                            {{ $notif['message'] }}
                        </p>
                        
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            @if($notif['student_id'])
                                <a href="{{ route('staff.applications.view', $notif['student_id']) }}" class="inline-flex items-center gap-2 text-sm font-bold text-orange-600 hover:text-orange-700">
                                    View Application
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endif
                            
                            @unless($notif['is_read'])
                                <button onclick="markAsRead('{{ $notif['id'] }}')" class="text-sm font-bold text-slate-500 hover:text-orange-600">
                                    Mark as read
                                </button>
                            @endunless

                            <button onclick="deleteNotification('{{ $notif['id'] }}')" class="text-sm font-bold text-slate-400 hover:text-red-600">
                                Delete
                            </button>
                        </div>
                    </div>
                    
                    @unless($notif['is_read'])
                        <div class="absolute top-6 right-6 w-3 h-3 bg-orange-500 rounded-full shadow-lg shadow-orange-500/50 unread-dot"></div>
                    @endunless
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 15.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v4.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900">No notifications yet</h3>
                <p class="text-slate-500 mt-2">We'll notify you when there's an update on applications or documents.</p>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
    .notification-item[data-read="true"].hidden-unread {
        display: none;
    }
</style>
@endpush

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function filterNotifs(type) {
        const items = document.querySelectorAll('.notification-item');
        const btnAll = document.getElementById('filter-all');
        const btnUnread = document.getElementById('filter-unread');

        if (type === 'all') {
            items.forEach(item => item.classList.remove('hidden-unread'));
            btnAll.classList.add('bg-orange-600', 'text-white', 'shadow-lg', 'shadow-orange-600/20');
            btnAll.classList.remove('text-slate-600', 'hover:bg-slate-50');
            btnUnread.classList.remove('bg-orange-600', 'text-white', 'shadow-lg', 'shadow-orange-600/20');
            btnUnread.classList.add('text-slate-600', 'hover:bg-slate-50');
        } else {
            items.forEach(item => {
                if (item.dataset.read === 'true') {
                    item.classList.add('hidden-unread');
                } else {
                    item.classList.remove('hidden-unread');
                }
            });
            btnUnread.classList.add('bg-orange-600', 'text-white', 'shadow-lg', 'shadow-orange-600/20');
            btnUnread.classList.remove('text-slate-600', 'hover:bg-slate-50');
            btnAll.classList.remove('bg-orange-600', 'text-white', 'shadow-lg', 'shadow-orange-600/20');
            btnAll.classList.add('text-slate-600', 'hover:bg-slate-50');
        }
    }

    function markAsRead(id) {
        const item = document.querySelector(`.notification-item[data-id="${id}"]`);
        
        fetch(`/staff/notifications/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                item.dataset.read = 'true';
                item.classList.remove('border-orange-200', 'bg-orange-50/30');
                item.classList.add('border-slate-100');
                item.querySelector('.unread-dot')?.remove();
                item.querySelector('button[onclick^="markAsRead"]')?.remove();
                
                const iconContainer = item.querySelector('.flex-shrink-0');
                iconContainer.classList.remove('bg-orange-100', 'text-orange-600', 'animate-pulse');
                iconContainer.classList.add('bg-slate-100', 'text-slate-500');
            }
        });
    }

    function markAllAsRead() {
        fetch('/staff/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }

    function deleteNotification(id) {
        if (!confirm('Are you sure you want to delete this notification?')) return;

        const item = document.querySelector(`.notification-item[data-id="${id}"]`);
        
        fetch(`/staff/notifications/${id}`, {
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
                item.style.opacity = '0';
                item.style.transform = 'translateX(20px)';
                setTimeout(() => item.remove(), 300);
            }
        });
    }
</script>
@endsection
