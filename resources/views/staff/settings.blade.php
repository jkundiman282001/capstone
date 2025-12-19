@extends('layouts.app')

@section('content')
<!-- Decorative Background Elements -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-amber-200/30 to-orange-200/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-tr from-orange-200/30 to-red-200/30 rounded-full blur-3xl"></div>
</div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-orange-700 to-orange-500 rounded-3xl shadow-2xl p-8 mb-8 text-white overflow-hidden">
        <!-- Decorative Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
        </div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-black mb-3 tracking-tight">Settings</h1>
            <p class="text-orange-100 text-lg">Manage system configuration</p>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-green-700 font-semibold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Settings Form -->
    <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-8">
        <form method="POST" action="{{ route('staff.settings.update') }}" class="space-y-6">
            @csrf
            
            <!-- Max Slots Setting -->
            <div class="border-b border-slate-200 pb-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-black text-slate-900 text-xl">Maximum Scholarship Slots</h2>
                        <p class="text-sm text-slate-500 font-medium">Set the maximum number of scholarship slots available</p>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="max_slots" class="block text-sm font-bold text-slate-700 mb-2">Maximum Slots</label>
                    <input 
                        type="number" 
                        id="max_slots" 
                        name="max_slots" 
                        value="{{ old('max_slots', $maxSlots) }}" 
                        min="1" 
                        required
                        class="w-full md:w-64 border-slate-200 bg-slate-50 rounded-xl p-3.5 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm font-medium hover:bg-white @error('max_slots') border-red-500 @enderror"
                    >
                    @error('max_slots')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-slate-500">This value determines the total number of scholarship slots available in the system.</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('staff.dashboard') }}" class="px-6 py-3 rounded-xl border-2 border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

