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
<style>
    /* Split Screen Hero */
    .split-hero {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #334155 100%);
    }

    .hero-image-section {
        background-image: url('{{ asset('images/Dashboard.png') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        position: relative;
    }

    .hero-image-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(
            135deg,
            rgba(249, 115, 22, 0.4) 0%,
            rgba(220, 38, 38, 0.3) 50%,
            rgba(15, 23, 42, 0.6) 100%
        );
    }

    /* Content Section */
    .content-section {
        background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
    }

    /* Animated Gradient Text */
    .gradient-text {
        background: linear-gradient(135deg, #F97316 0%, #EA580C 50%, #DC2626 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        background-size: 200% 200%;
        animation: gradientShift 3s ease infinite;
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* Card Styles */
    .modern-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(229, 231, 235, 0.8);
    }

    .modern-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Button Styles */
    .btn-modern {
        background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-modern::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-modern:hover::after {
        width: 300px;
        height: 300px;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(249, 115, 22, 0.4);
    }

    /* Icon Container */
    .icon-container {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
        box-shadow: 0 4px 14px rgba(249, 115, 22, 0.3);
    }

    /* Floating Animation */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
    }

    .float-animation {
        animation: float 4s ease-in-out infinite;
    }

    /* Slide In Animation */
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .slide-in-left {
        animation: slideInLeft 0.8s ease-out;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .slide-in-right {
        animation: slideInRight 0.8s ease-out;
    }

    /* Badge Styles */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Modal Styles */
    .modal-backdrop {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
</style>
@endpush

@section('content')
<!-- Split Screen Hero Section -->
<div class="split-hero min-h-screen flex flex-col lg:flex-row">
    <!-- Left Side - Image -->
    <div class="hero-image-section lg:w-1/2 min-h-screen relative z-0">
        <div class="absolute inset-0 z-10 flex items-center justify-center p-8">
            <div class="text-center space-y-6 slide-in-left">
                <div class="float-animation inline-block">
                    <img 
                        src="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" 
                        alt="NCIP Logo" 
                        class="h-32 w-32 mx-auto drop-shadow-2xl"
                    />
                </div>
                <h1 class="text-4xl lg:text-6xl font-black text-white leading-tight">
                    NCIP<br/>
                    <span>EDUCATIONAL</span><br/>
                    ASSISTANCE PROGRAM
                </h1>
                <p class="text-lg text-white/90 max-w-md mx-auto">
                    Empowering Indigenous Youth through quality education and scholarship opportunities.
                </p>
            </div>
        </div>
    </div>

    <!-- Right Side - Content -->
    <div class="content-section lg:w-1/2 min-h-screen flex items-center p-8 lg:p-12">
        <div class="w-full max-w-2xl mx-auto space-y-8 slide-in-right">
            <!-- Welcome Section -->
            <div class="space-y-4">
                <h2 class="text-4xl lg:text-5xl font-black text-slate-900">
                    Welcome to Your<br/>
                    <span class="gradient-text">Scholarship Portal</span>
                </h2>
                <p class="text-lg text-slate-600 leading-relaxed">
                    Receive personalized recommendations and guidance for your academic journey as an Indigenous Youth. Apply, track, and succeed with the NCIP-EAP.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <button 
                    onclick="window.location.href='{{ url('student/apply') }}'" 
                    class="btn-modern text-white px-8 py-4 rounded-xl text-lg font-bold shadow-lg relative z-10 flex-1"
                >
                    Apply for Scholarship
                </button>
                <button 
                    onclick="document.getElementById('announcements-section')?.scrollIntoView({ behavior: 'smooth' })" 
                    class="border-2 border-orange-500 text-orange-600 px-8 py-4 rounded-xl text-lg font-bold hover:bg-orange-50 transition-all flex-1"
                >
                    View Updates
                </button>
            </div>

            <!-- Quick Stats or Features -->
            <div class="grid grid-cols-3 gap-4 pt-8">
                <div class="text-center p-4 rounded-xl bg-gradient-to-br from-orange-50 to-red-50">
                    <div class="text-3xl font-black gradient-text mb-1">{{ number_format($totalScholars) }}{{ $totalScholars >= 100 ? '+' : '' }}</div>
                    <div class="text-sm text-slate-600 font-medium">Scholars</div>
                </div>
                <div class="text-center p-4 rounded-xl bg-gradient-to-br from-orange-50 to-red-50">
                    <div class="text-3xl font-black gradient-text mb-1">{{ number_format($totalPrograms) }}{{ $totalPrograms >= 50 ? '+' : '' }}</div>
                    <div class="text-sm text-slate-600 font-medium">Programs</div>
                </div>
                <div class="text-center p-4 rounded-xl bg-gradient-to-br from-orange-50 to-red-50">
                    <div class="text-3xl font-black gradient-text mb-1">{{ $supportAvailable }}</div>
                    <div class="text-sm text-slate-600 font-medium">Support</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Lock Modal -->
@if (isset($hasApplied) && ! $hasApplied)
<div id="performance-lock-overlay" class="fixed inset-0 z-50 flex items-center justify-center px-4 hidden">
    <div class="absolute inset-0 bg-black/50 modal-backdrop"></div>
    <div id="performance-lock-modal" class="relative w-full max-w-lg modern-card p-8 space-y-6 z-10">
        <button id="performance-lock-close" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="flex items-center gap-4">
            <div class="icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 10-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V9a3 3 0 016 0v2H9z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">Performance Dashboard Locked</p>
                <p class="text-sm text-slate-600 mt-1">Submit your NCIP-EAP application to unlock your performance analytics.</p>
            </div>
        </div>
        <div class="space-y-3 text-sm text-slate-700 bg-slate-50 rounded-xl p-5">
            <p class="font-bold text-slate-900">Once your application is submitted, you'll gain access to:</p>
            <ul class="space-y-2">
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Real-time performance metrics and scoring insights</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Document checklist and tracking</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Priority status updates</span>
                </li>
            </ul>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button 
                onclick="window.location.href='{{ url('student/apply') }}'" 
                class="flex-1 btn-modern text-white font-bold px-6 py-4 rounded-xl shadow-lg relative z-10"
            >
                Complete Application
            </button>
            <button 
                id="performance-lock-later" 
                class="flex-1 border-2 border-orange-300 text-orange-600 font-bold px-6 py-4 rounded-xl hover:bg-orange-50 transition-all"
            >
                Maybe Later
            </button>
        </div>
    </div>
</div>
@endif

<!-- Announcements Section -->
<section id="announcements-section" class="py-20 px-6 bg-gradient-to-br from-slate-50 via-white to-orange-50/30">
    <div class="max-w-6xl mx-auto">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 icon-container mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <h2 class="text-4xl lg:text-5xl font-black text-slate-900 mb-3">Latest Announcements</h2>
            <p class="text-lg text-slate-600">Stay updated with the latest news and updates</p>
        </div>
        
        <!-- Announcements Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Announcement 1 -->
            <div class="modern-card p-6 group">
                <div class="flex items-start gap-4 mb-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-black text-slate-900 mb-2 group-hover:text-cyan-600 transition-colors">Deadline Extended</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-3">The deadline for the Indigenous Youth Scholarship Program has been extended to December 15, 2024.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">2 hours ago</span>
                            <span class="badge bg-cyan-100 text-cyan-700">Important</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Announcement 2 -->
            <div class="modern-card p-6 group">
                <div class="flex items-start gap-4 mb-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-black text-slate-900 mb-2 group-hover:text-green-600 transition-colors">Mentorship Program</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-3">Connect with successful Indigenous professionals through our new mentorship program.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">1 day ago</span>
                            <span class="badge bg-green-100 text-green-700">New</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Announcement 3 -->
            <div class="modern-card p-6 group md:col-span-2 lg:col-span-1">
                <div class="flex items-start gap-4 mb-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-black text-slate-900 mb-2 group-hover:text-orange-600 transition-colors">System Maintenance</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-3">The portal will be under maintenance on Saturday, December 7th from 2:00 AM to 6:00 AM.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">3 days ago</span>
                            <span class="badge bg-orange-100 text-orange-700">Maintenance</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- View All Button -->
        <div class="text-center">
            <button class="inline-flex items-center gap-2 text-orange-600 hover:text-orange-700 font-bold text-lg group transition-colors">
                View All Announcements
                <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </button>
        </div>
    </div>
</section>
@endsection

@if (isset($hasApplied) && ! $hasApplied)
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('performance-lock-overlay');
        const closeButtons = ['performance-lock-close', 'performance-lock-later']
            .map(id => document.getElementById(id))
            .filter(Boolean);

        const showOverlay = () => {
            if (!overlay) return;
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const hideOverlay = () => {
            if (!overlay) return;
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        closeButtons.forEach(btn => btn.addEventListener('click', hideOverlay));
        overlay?.addEventListener('click', (event) => {
            if (event.target === overlay) {
                hideOverlay();
            }
        });

        const performanceLinks = document.querySelectorAll('a[href="{{ route('student.performance') }}"]');
        performanceLinks.forEach(link => {
            link.classList.add('opacity-50', 'cursor-not-allowed');
            link.setAttribute('aria-disabled', 'true');
            link.addEventListener('click', (event) => {
                event.preventDefault();
                showOverlay();
            }, { capture: true });
        });
    });
</script>
@endpush
@endif

