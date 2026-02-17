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
                <h1 class="text-2xl md:text-4xl font-black mb-2 md:mb-3 tracking-tight">Create Admin Account</h1>
                <p class="text-orange-100 text-sm md:text-base font-medium max-w-2xl">Add a new staff member with administrative access.</p>
            </div>
            
            <a href="{{ route('staff.admins.index') }}" class="group relative inline-flex items-center gap-2 px-6 py-3 bg-white/10 text-white rounded-xl font-bold border border-white/20 hover:bg-white/20 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-orange-600 focus:ring-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to List</span>
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
            <div class="p-6 md:p-8">
                <form action="{{ route('staff.admins.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-bold text-slate-700 mb-2">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required autofocus
                                class="w-full rounded-xl border-slate-200 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors @error('first_name') border-red-500 @enderror">
                            @error('first_name')
                                <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-bold text-slate-700 mb-2">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                class="w-full rounded-xl border-slate-200 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors @error('last_name') border-red-500 @enderror">
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <!-- Email Address -->
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full rounded-xl border-slate-200 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                            <input type="password" name="password" id="password" required
                                class="w-full rounded-xl border-slate-200 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full rounded-xl border-slate-200 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                        <a href="{{ route('staff.admins.index') }}" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-50 transition-colors">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white rounded-xl font-bold shadow-lg shadow-orange-200 hover:bg-orange-700 hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
