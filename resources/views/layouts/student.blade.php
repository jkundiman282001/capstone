<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IP Scholar Portal')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}">
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
                            @php
                                $unreadCount = auth()->user()->unreadNotifications->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span id="notification-badge-desktop" class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border border-black/20"></span>
                                </span>
                            @endif
                        </a>
                        
                        <!-- Profile Picture Dropdown (Desktop) -->
                        <div class="relative hidden md:block" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center focus:outline-none">
                                @if($user->profile_pic_url)
                        <img id="nav-profile-pic" src="{{ $user->profile_pic_url }}" alt="Profile" 
                             class="w-10 h-10 rounded-full border-2 border-orange-400 hover:border-orange-300 transition-colors object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');">
                        <div id="nav-profile-initials" class="hidden w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-amber-600 items-center justify-center text-white font-black text-sm border-2 border-orange-400 hover:border-orange-300 transition-colors">
                            {{ $user->initials }}
                        </div>
                    @else
                        <div id="nav-profile-initials" class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center text-white font-black text-sm border-2 border-orange-400 hover:border-orange-300 transition-colors">
                            {{ $user->initials }}
                        </div>
                        <img id="nav-profile-pic" src="" alt="Profile" 
                             class="hidden w-10 h-10 rounded-full border-2 border-orange-400 hover:border-orange-300 transition-colors object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');">
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

                    <!-- Mobile Menu Button -->
                    <button @click="open = !open" class="md:hidden p-2 rounded-lg text-white hover:bg-white/10 transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!open">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="open" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="md:hidden mt-4 pb-4"
                 style="display: none;">
                <div class="flex flex-col gap-2">
                    <a href="{{ route('student.dashboard') }}" class="text-white hover:text-orange-400 transition-colors px-4 py-3 rounded-lg hover:bg-white/5 {{ request()->routeIs('student.dashboard') ? 'bg-white/10 text-orange-400' : '' }}">Home</a>
                    
                    @auth
                        @php
                            $hasApplied = \App\Models\BasicInfo::where('user_id', auth()->id())->exists();
                        @endphp
                        
                        @if($hasApplied)
                            <a href="{{ route('student.performance') }}" class="text-white hover:text-orange-400 transition-colors px-4 py-3 rounded-lg hover:bg-white/5 {{ request()->routeIs('student.performance') ? 'bg-white/10 text-orange-400' : '' }}">Application</a>
                        @else
                            <button onclick="document.getElementById('performance-lock-overlay')?.classList.remove('hidden'); document.body.classList.add('overflow-hidden');" class="text-white/50 flex items-center gap-2 px-4 py-3 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 10-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V9a3 3 0 016 0v2H9z"/></svg>
                                Application
                            </button>
                        @endif

                        <div class="border-t border-white/10 my-2 pt-2">
                            <div class="px-4 py-3 mb-2">
                                <p class="text-sm font-semibold text-white">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('student.profile') }}" class="flex items-center px-4 py-3 text-sm text-white hover:bg-white/5 rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </a>
                            <a href="{{ route('student.support') }}" class="flex items-center px-4 py-3 text-sm text-white hover:bg-white/5 rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Support/Help
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-3 text-sm text-orange-400 hover:bg-white/5 rounded-lg transition-colors text-left">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors px-4 py-3 rounded-lg hover:bg-white/5">Profile</a>
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors px-4 py-3 rounded-lg hover:bg-white/5">Application</a>
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors px-4 py-3 rounded-lg hover:bg-white/5">Notification</a>
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
