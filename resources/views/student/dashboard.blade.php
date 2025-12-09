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
        <div class="w-full max-w-2xl mx-auto space-y-6 slide-in-right">
            <!-- Welcome Section -->
            <div class="space-y-3 mb-6">
                <h2 class="text-4xl lg:text-5xl font-black text-slate-900">
                    Welcome to<br/>
                    <span class="gradient-text">NCIP-EAP</span>
                </h2>
                <p class="text-lg text-slate-600 leading-relaxed">
                    Receive personalized recommendations and guidance for your academic journey as an Indigenous Youth. Apply, track, and succeed with the NCIP-EAP.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
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

            <!-- Analytics Section -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Slots Left - Prominent -->
                <div class="rounded-2xl p-5 text-center fade-in col-span-2 mb-2 {{ $stats['isFull'] ? 'bg-red-500/20 border-2 border-red-500/50' : 'bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200' }}" style="animation-delay: 0s">
                    <div class="text-5xl font-black {{ $stats['isFull'] ? 'text-red-600' : 'text-green-600' }} mb-1">
                        {{ number_format($stats['slotsLeft']) }}
                    </div>
                    <div class="text-sm text-slate-700 font-medium mb-0.5">Slots Left</div>
                    <div class="text-xs text-slate-500">of {{ number_format($stats['maxSlots']) }} maximum</div>
                    @if($stats['isFull'])
                        <div class="mt-2 px-3 py-1 bg-red-500/30 rounded-lg text-red-700 text-xs font-bold">
                            Scholarship Slots Full
                        </div>
                    @endif
                </div>
                <div class="rounded-2xl p-5 text-center fade-in bg-gradient-to-br from-orange-50 to-amber-50 border-2 border-orange-200" style="animation-delay: 0.2s">
                    <div class="text-4xl font-black text-orange-600 mb-1.5">{{ number_format($stats['applicantsApplied']) }}</div>
                    <div class="text-sm text-slate-700 font-medium">Applicants Applied</div>
                </div>
                <div class="rounded-2xl p-5 text-center fade-in bg-gradient-to-br from-amber-50 to-yellow-50 border-2 border-amber-200" style="animation-delay: 0.4s">
                    <div class="text-4xl font-black text-amber-600 mb-1.5">{{ number_format($stats['applicantsApproved']) }}</div>
                    <div class="text-sm text-slate-700 font-medium">Applicants Approved</div>
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
        @if(isset($announcements) && $announcements->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($announcements as $announcement)
                    <div class="modern-card p-6 group">
                        @if($announcement->image_path)
                            <img src="{{ $announcement->image_url }}" alt="{{ $announcement->title }}" class="w-full h-48 object-cover rounded-xl mb-4">
                        @endif
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br {{ $announcement->priority === 'urgent' ? 'from-red-500 to-rose-600' : ($announcement->priority === 'high' ? 'from-orange-500 to-red-600' : 'from-cyan-500 to-blue-600') }} rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-black text-slate-900 mb-2 group-hover:text-cyan-600 transition-colors">{{ $announcement->title }}</h3>
                                <p class="text-sm text-slate-600 leading-relaxed mb-3">{{ Str::limit($announcement->content, 120) }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">{{ $announcement->created_at->diffForHumans() }}</span>
                                    <span class="badge {{ $announcement->priority === 'urgent' ? 'bg-red-100 text-red-700' : ($announcement->priority === 'high' ? 'bg-orange-100 text-orange-700' : 'bg-cyan-100 text-cyan-700') }}">
                                        {{ ucfirst($announcement->priority) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 rounded-2xl">
                <div class="w-20 h-20 bg-slate-200 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>
                <h3 class="text-slate-900 font-bold text-lg mb-2">No announcements yet</h3>
                <p class="text-slate-500 text-sm">Check back later for updates and important information.</p>
            </div>
        @endif
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

