<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - IP Scholar Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
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
  <script>
    // Ensure mountain background image loads properly
    document.addEventListener('DOMContentLoaded', function() {
      const mountainImg = document.querySelector('.hero-bg-image img');
      if (mountainImg) {
        mountainImg.onload = function() {
          this.style.opacity = '1';
        };
        mountainImg.onerror = function() {
          this.style.display = 'none';
          const fallback = this.nextElementSibling;
          if (fallback) {
            fallback.classList.remove('hidden');
          }
        };
        // Set initial opacity for smooth loading
        mountainImg.style.opacity = '0';
        mountainImg.style.transition = 'opacity 0.5s ease-in-out';
      }
    });
  </script>
</head>
<body class="min-h-screen text-white">
    <!-- Hero Background Image with Overlay -->
    <div class="relative min-h-screen flex flex-col">
        <!-- Background image -->
        <div class="absolute inset-0 z-0">
            <img 
                src="{{ asset('mountain.png') }}" 
                alt="Mountain landscape" 
                class="h-full w-full object-cover" 
                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
            />
            <!-- Fallback gradient when image fails to load -->
            <div class="hidden h-full gradient-bg"></div>
            <!-- Dark overlay for better text readability -->
            <div class="absolute inset-0 hero-bg-overlay"></div>
        </div>

        <!-- Navigation -->
        <nav x-data="{ open: false }" class="fixed top-0 w-full z-50 bg-black/20 backdrop-blur-md border-b border-white/10">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="text-2xl font-bold">
                            <span class="text-orange-400">NCIP-EAP</span>
                        </div>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="javascript:void(0)" onclick="location.reload()" class="text-white hover:text-orange-400 transition-colors">Home</a>
                        @auth
                            <a href="{{ route('student.profile') }}" class="text-white hover:text-orange-400 transition-colors">Profile</a>
                            <a href="{{ route('student.performance') }}" class="text-white hover:text-orange-400 transition-colors">Performance</a>
                            <a href="{{ route('student.notifications') }}" class="text-white hover:text-orange-400 transition-colors">Notification</a>
                            <a href="{{ route('student.support') }}" class="text-white hover:text-orange-400 transition-colors">Support/Help</a>
                        @else
                            <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors">Profile</a>
                            <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors">Performance</a>
                            <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors">Notification</a>
                            <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors">Support/Help</a>
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
                <div class="flex flex-col space-y-4">
                    <!-- Copy your nav links here, but stacked vertically -->
                    <a href="javascript:void(0)" onclick="location.reload()" class="text-white hover:text-orange-400 transition-colors">Home</a>
                    @auth
                        <a href="{{ route('student.profile') }}" class="text-white hover:text-orange-400 transition-colors">Profile</a>
                        <a href="{{ route('student.performance') }}" class="text-white hover:text-orange-400 transition-colors">Performance</a>
                        <a href="{{ route('student.notifications') }}" class="text-white hover:text-orange-400 transition-colors">Notification</a>
                        <a href="{{ route('student.support') }}" class="text-white hover:text-orange-400 transition-colors">Support/Help</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-0 text-orange-400 hover:text-white transition-all">Log Out</button>
                        </form>
                    @else
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors">Profile</a>
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors">Performance</a>
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors">Notification</a>
                        <a href="{{ url('/auth') }}" class="text-white hover:text-orange-400 transition-colors">Support/Help</a>
                        <a href="{{ url('/auth') }}" class="px-6 py-2 border border-orange-500 text-orange-400 rounded-lg hover:bg-orange-700 hover:text-white transition-all">Login</a>
                        <a href="{{ url('/auth') }}" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-700 transition-all glow-effect">Sign Up</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section with Mountain Background -->
        <section class="pt-32 pb-20 px-6 relative overflow-hidden min-h-screen">
            <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                
                <!-- Left Content -->
                <div class="space-y-8">
                <h1 class="text-5xl lg:text-7xl font-bold leading-tight">
                    <span class="hero-text">NCIP</span><br>
                    <span class="text-white">EDUCATIONAL</span><br>
                    <span class="hero-text">ASSISTANCE PROGRAM</span>
                </h1>
                
                <p class="text-xl text-gray-200 leading-relaxed max-w-2xl">
                    Receive personalized recommendations and guidance for your academic journey as an Indigenous Youth. Apply, track, and succeed with the NCIP-EAP.
                </p>
                
                <button onclick="window.location.href='{{ url('student/apply') }}'" class="bg-gradient-to-r from-orange-500 to-red-600 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:from-orange-600 hover:to-red-700 transition-all pulse-glow">
                    Apply for Scholarship
                </button>
                </div>
                
                <!-- Right Content - Video Section -->
                <div class="floating-animation">
                <div class="relative">
                    <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-1">
                    <div class="bg-ip-card rounded-xl overflow-hidden">
                        <div class="aspect-video bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center relative">
                        <div class="video-overlay absolute inset-0 flex items-center justify-center">
                            <div class="bg-white/20 backdrop-blur-sm rounded-full p-6 hover:bg-white/30 transition-all cursor-pointer">
                            <svg class="w-12 h-12 text-white ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                            </svg>
                            </div>
                        </div>
                        <div class="absolute bottom-4 right-4 bg-black/50 backdrop-blur-sm px-4 py-2 rounded-lg">
                            <span class="text-white font-medium">WATCH THE VIDEO</span>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                
            </div>
            </div>
            
            <!-- Background Elements -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-ip-cyan/10 rounded-full blur-3xl floating-animation z-5"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl floating-animation-delayed z-5"></div>
        </section>
    </div>

    <!-- Latest Announcements Section -->
    <section class="py-20 px-6 bg-gradient-to-br from-amber-50 via-orange-50 to-red-50">
        <div class="max-w-4xl mx-auto">
            
            <!-- Section Header -->
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-orange-400">Latest Announcements</h2>
            </div>
            
            <!-- Announcements List -->
            <div class="space-y-6">
                
                <!-- Announcement 1 -->
                <div class="bg-gray-800 rounded-xl p-6 border-l-4 border-ip-cyan card-hover">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-2">Scholarship Application Deadline Extended</h3>
                            <p class="text-gray-300 mb-4">The deadline for the Indigenous Youth Scholarship Program has been extended to December 15, 2024. Don't miss this opportunity!</p>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center text-gray-400 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Posted 2 hours ago
                                </div>
                                <span class="bg-ip-cyan/20 text-ip-cyan px-3 py-1 rounded-full text-sm font-medium">Important</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Announcement 2 -->
                <div class="bg-gray-800 rounded-xl p-6 border-l-4 border-green-500 card-hover">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-2">New Mentorship Program Available</h3>
                            <p class="text-gray-300 mb-4">Connect with successful Indigenous professionals through our new mentorship program. Applications open now!</p>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center text-gray-400 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Posted 1 day ago
                                </div>
                                <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm font-medium">New</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Announcement 3 -->
                <div class="bg-gray-800 rounded-xl p-6 border-l-4 border-orange-500 card-hover">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-2">System Maintenance Notice</h3>
                            <p class="text-gray-300 mb-4">The portal will be under maintenance on Saturday, December 7th from 2:00 AM to 6:00 AM. We apologize for any inconvenience.</p>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center text-gray-400 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Posted 3 days ago
                                </div>
                                <span class="bg-orange-500/20 text-orange-400 px-3 py-1 rounded-full text-sm font-medium">Maintenance</span>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- View All Button -->
            <div class="mt-8 text-center">
                <button class="text-orange-400 hover:text-red-300 font-medium flex items-center mx-auto group">
                    View All Announcements
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
            
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 px-6 border-t border-white/10 bg-gray-800">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="text-orange-400 font-bold text-xl">IndiGenSys</div>
                <div class="text-orange-400 text-sm">&copy; {{ date('Y') }} IndiGenSys. All rights reserved.</div>
            </div>
        </div>
    </footer>
</body>
</html> 