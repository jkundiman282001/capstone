<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IP Scholar Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #767575 0%, #C0C0C0 50%, #FFFFFF 100%);
        }
        
        .hero-text {
            background: linear-gradient(135deg, #FFFFFF 0%, #F97316 50%, #DC2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-animation-delayed {
            animation: float 6s ease-in-out infinite 2s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .glow-effect {
            box-shadow: 0 0 30px rgba(251, 146, 60, 0.3);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(251, 146, 60, 0.4); }
            50% { box-shadow: 0 0 40px rgba(251, 146, 60, 0.8); }
        }
        
        .video-overlay {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.8) 0%, rgba(220, 38, 38, 0.8) 50%, rgba(153, 27, 27, 0.6) 100%);
        }

        .hero-bg-overlay {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 41, 59, 0.6) 100%);
        }
        
        .hero-bg-image {
            min-height: 100vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .hero-bg-image img {
            min-height: 100vh;
            object-fit: cover;
            object-position: center;
        }
    </style>
    @stack('head-scripts')
</head>
<body class="min-h-screen text-white">
    <!-- Navigation -->
    <nav x-data="{ open: false }" class="fixed top-0 w-full z-50 bg-black/20 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="text-2xl font-bold">
                        <span class="text-orange-400">NCIP-EAP</span>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('student.dashboard') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Home</a>
                    @auth
                        @php
                            $hasApplied = \App\Models\BasicInfo::where('user_id', auth()->id())->exists();
                        @endphp
                        <a href="{{ route('student.profile') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Profile</a>
                        
                        @if($hasApplied)
                            <a href="{{ route('student.performance') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Performance</a>
                        @else
                            <button onclick="document.getElementById('performance-lock-overlay')?.classList.remove('hidden'); document.body.classList.add('overflow-hidden');" class="text-white/50 hover:text-orange-400 transition-colors cursor-not-allowed flex items-center gap-1 px-2 py-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 10-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V9a3 3 0 016 0v2H9z"/></svg>
                                Performance
                            </button>
                        @endif

                        <a href="{{ route('student.notifications') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Notification</a>
                        <a href="{{ route('student.support') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Support/Help</a>
                    @else
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Profile</a>
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Performance</a>
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Notification</a>
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Support/Help</a>
                    @endauth
                </div>
                
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ url('/auth') }}" class="px-6 py-2 border border-orange-500 text-orange-400 rounded-lg hover:bg-orange-700 hover:text-white transition-all">Login</a>
                        <a href="{{ url('/auth') }}" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-700 transition-all glow-effect">Sign Up</a>
                    @endguest
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-6 py-2 border border-orange-500 text-orange-400 rounded-lg hover:bg-orange-700 hover:text-white transition-all">Log Out</button>
                        </form>
                    @endauth
                </div>

                <!-- Hamburger Button (Mobile) -->
                <button @click="open = !open" class="md:hidden text-orange-400 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="open" @click.away="open = false" class="md:hidden bg-black/90 backdrop-blur-md border-t border-white/10 px-6 py-4">
            <div class="flex flex-col gap-4">
                <a href="{{ route('student.dashboard') }}" class="text-white hover:text-orange-400 transition-colors py-2">Home</a>
                @auth
                    @php
                        $hasAppliedMobile = \App\Models\BasicInfo::where('user_id', auth()->id())->exists();
                    @endphp
                    <a href="{{ route('student.profile') }}" class="text-white hover:text-orange-400 transition-colors py-2">Profile</a>
                    
                    @if($hasAppliedMobile)
                        <a href="{{ route('student.performance') }}" class="text-white hover:text-orange-400 transition-colors py-2">Performance</a>
                    @else
                        <button onclick="document.getElementById('performance-lock-overlay')?.classList.remove('hidden'); document.body.classList.add('overflow-hidden');" class="text-white/50 hover:text-orange-400 transition-colors cursor-not-allowed text-left flex items-center gap-2 py-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 10-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V9a3 3 0 016 0v2H9z"/></svg>
                            Performance (Locked)
                        </button>
                    @endif

                    <a href="{{ route('student.notifications') }}" class="text-white hover:text-orange-400 transition-colors py-2">Notification</a>
                    <a href="{{ route('student.support') }}" class="text-white hover:text-orange-400 transition-colors py-2">Support/Help</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left text-orange-400 hover:text-white transition-all py-2">Log Out</button>
                    </form>
                @else
                    <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors py-2">Profile</a>
                    <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors py-2">Performance</a>
                    <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors py-2">Notification</a>
                    <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors py-2">Support/Help</a>
                    <a href="{{ url('/auth') }}" class="px-6 py-2 border border-orange-500 text-orange-400 rounded-lg hover:bg-orange-700 hover:text-white transition-all text-center">Login</a>
                    <a href="{{ url('/auth') }}" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-700 transition-all glow-effect text-center">Sign Up</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="py-8 px-6 border-t border-white/10 bg-gray-800">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="text-orange-400 font-bold text-xl">NCIP-EAP</div>
                <div class="text-orange-400 text-sm">&copy; {{ date('Y') }} NCIP-EAP. All rights reserved.</div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
