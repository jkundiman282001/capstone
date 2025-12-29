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
        
        .profile-dropdown {
            z-index: 60;
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
                
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('student.dashboard') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Home</a>
                    @auth
                        @php
                            $hasApplied = \App\Models\BasicInfo::where('user_id', auth()->id())->exists();
                        @endphp
                        
                        @if($hasApplied)
                            <a href="{{ route('student.performance') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Application</a>
                        @else
                            <button onclick="document.getElementById('performance-lock-overlay')?.classList.remove('hidden'); document.body.classList.add('overflow-hidden');" class="text-white/50 hover:text-orange-400 transition-colors cursor-not-allowed flex items-center gap-1 px-2 py-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 10-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V9a3 3 0 016 0v2H9z"/></svg>
                                Application
                            </button>
                        @endif
                    @else
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Profile</a>
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Application</a>
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors px-2 py-1">Notification</a>
                    @endauth
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        @php
                            $user = auth()->user();
                        @endphp
                        <!-- Notification Bell Icon -->
                        <a href="{{ route('student.notifications') }}" class="relative text-white hover:text-orange-400 transition-colors p-2 rounded-lg hover:bg-white/10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </a>
                        
                        <!-- Profile Picture Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center focus:outline-none">
                                @if($user->profile_pic)
                                    <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Profile" class="w-10 h-10 rounded-full border-2 border-orange-400 hover:border-orange-300 transition-colors object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center text-white font-semibold text-sm border-2 border-orange-400 hover:border-orange-300 transition-colors">
                                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1) . substr($user->last_name ?? 'S', 0, 1)) }}
                                    </div>
                                @endif
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 rounded-lg shadow-xl bg-black/90 backdrop-blur-md border border-white/10 overflow-hidden profile-dropdown"
                                 style="display: none;">
                                <div class="py-2">
                                    <!-- User Info -->
                                    <div class="px-4 py-3 border-b border-white/10">
                                        <p class="text-sm font-semibold text-white">{{ $user->first_name }} {{ $user->last_name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                                    </div>
                                    
                                    <!-- Profile Link -->
                                    <a href="{{ route('student.profile') }}" class="flex items-center px-4 py-3 text-sm text-white hover:bg-white/10 transition-colors">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Profile
                                    </a>
                                    
                                    <!-- Support/Help Link -->
                                    <a href="{{ route('student.support') }}" class="flex items-center px-4 py-3 text-sm text-white hover:bg-white/10 transition-colors">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Support/Help
                                    </a>
                                    
                                    <!-- Logout -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center px-4 py-3 text-sm text-orange-400 hover:bg-white/10 transition-colors text-left">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ url('/auth') }}" class="px-6 py-2 border border-orange-500 text-orange-400 rounded-lg hover:bg-orange-700 hover:text-white transition-all">Login</a>
                        <a href="{{ url('/auth') }}" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-700 transition-all glow-effect">Sign Up</a>
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
                        $userMobile = auth()->user();
                    @endphp
                    
                    @if($hasAppliedMobile)
                        <a href="{{ route('student.performance') }}" class="text-white hover:text-orange-400 transition-colors py-2">Application</a>
                    @else
                        <button onclick="document.getElementById('performance-lock-overlay')?.classList.remove('hidden'); document.body.classList.add('overflow-hidden');" class="text-white/50 hover:text-orange-400 transition-colors cursor-not-allowed text-left flex items-center gap-2 py-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 10-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V9a3 3 0 016 0v2H9z"/></svg>
                            Application (Locked)
                        </button>
                    @endif

                    <!-- Notification Bell Icon (Mobile) -->
                    <a href="{{ route('student.notifications') }}" class="flex items-center text-white hover:text-orange-400 transition-colors py-2">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notification
                    </a>
                    
                    <!-- Profile Section (Mobile) -->
                    <div class="border-t border-white/10 pt-4 mt-2">
                        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-white/10">
                            @if($userMobile->profile_pic)
                                <img src="{{ asset('storage/' . $userMobile->profile_pic) }}" alt="Profile" class="w-12 h-12 rounded-full border-2 border-orange-400 object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center text-white font-semibold border-2 border-orange-400">
                                    {{ strtoupper(substr($userMobile->first_name ?? 'U', 0, 1) . substr($userMobile->last_name ?? 'S', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $userMobile->first_name }} {{ $userMobile->last_name }}</p>
                                <p class="text-xs text-gray-400">{{ $userMobile->email }}</p>
                            </div>
                        </div>
                        
                        <a href="{{ route('student.profile') }}" class="flex items-center text-white hover:text-orange-400 transition-colors py-2">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center text-orange-400 hover:text-white transition-all py-2">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors py-2">Profile</a>
                    <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors py-2">Application</a>
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
