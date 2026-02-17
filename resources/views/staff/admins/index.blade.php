@extends('layouts.staff')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-8">
    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-orange-700 to-orange-500 rounded-2xl md:rounded-3xl shadow-2xl p-6 md:p-8 mb-6 md:mb-8 text-white overflow-hidden">
        <!-- Decorative Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-black mb-2 md:mb-3 tracking-tight">Admin Accounts</h1>
                <p class="text-orange-100 text-sm md:text-base font-medium max-w-2xl">Manage staff access and permissions.</p>
            </div>
            
            <a href="{{ route('staff.admins.create') }}" class="group relative inline-flex items-center gap-2 px-6 py-3 bg-white text-orange-600 rounded-xl font-bold shadow-lg hover:shadow-xl hover:bg-orange-50 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-orange-600 focus:ring-white">
                <div class="absolute inset-0 rounded-xl border-2 border-white/20 group-hover:border-orange-200/50 transition-colors"></div>
                <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Create New Admin</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Admins Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-500">Name</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-500">Access Code</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-500">Date Created</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($admins as $admin)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-sm">
                                    {{ substr($admin->first_name, 0, 1) }}{{ substr($admin->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $admin->first_name }} {{ $admin->last_name }}</div>
                                    @if(auth()->guard('staff')->id() == $admin->id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            You
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-sm text-slate-600 font-medium">{{ $admin->email }}</td>
                        <td class="p-4 text-sm font-mono text-slate-700 bg-slate-100 rounded px-2 py-1 inline-block">{{ $admin->access_code ?? 'N/A' }}</td>
                        <td class="p-4 text-sm text-slate-500">{{ $admin->created_at->format('M d, Y') }}</td>
                        <td class="p-4 text-right">
                            @if(auth()->guard('staff')->id() != $admin->id)
                                <form action="{{ route('staff.admins.destroy', $admin->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this admin account? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors p-2 rounded-lg hover:bg-red-50" title="Delete Account">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-400 text-xs italic">Current User</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-slate-500">
                            No admin accounts found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
