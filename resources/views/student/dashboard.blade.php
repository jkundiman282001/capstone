@extends('layouts.student')

@section('title', 'Home - IP Scholar Portal')

@push('head-scripts')
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
@endpush

@section('content')
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
@endsection
