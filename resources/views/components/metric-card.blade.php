@props(['title', 'value', 'icon'])

@php
    $gradients = [
        'users' => 'from-blue-500 to-cyan-600',
        'user-plus' => 'from-emerald-500 to-teal-600',
        'user-check' => 'from-purple-500 to-pink-600',
        'user-x' => 'from-orange-500 to-red-600',
    ];
    $gradient = $gradients[$icon] ?? 'from-indigo-500 to-purple-600';
    
    $iconSvgs = [
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
        'user-plus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />',
        'user-check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'user-x' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />',
    ];
    $iconSvg = $iconSvgs[$icon] ?? '<circle cx="12" cy="12" r="10" />';
@endphp

<div class="group relative bg-white/70 backdrop-blur-xl rounded-3xl shadow-xl border border-slate-200 p-6 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
    <!-- Decorative gradient background -->
    <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }} opacity-0 group-hover:opacity-5 transition-opacity duration-300"></div>
    
    <div class="relative z-10 flex items-start justify-between">
        <div class="flex-1">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">{{ $title }}</p>
            <p class="text-4xl font-black text-slate-900 mb-1 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:{{ $gradient }} transition-all duration-300">{{ $value }}</p>
        </div>
        <div class="w-14 h-14 bg-gradient-to-br {{ $gradient }} rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-2xl group-hover:scale-110 transition-all duration-300">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $iconSvg !!}
            </svg>
        </div>
    </div>
    
    <!-- Animated bottom accent -->
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r {{ $gradient }} transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 rounded-full"></div>
</div> 