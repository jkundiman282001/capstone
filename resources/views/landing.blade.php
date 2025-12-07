<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCIP Educational Assistance Program - IP Scholar Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
        }
        
        .hero-text-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #fbbf24 50%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-in-left {
            animation: slideInLeft 0.8s ease-out;
        }
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .slide-in-right {
            animation: slideInRight 0.8s ease-out;
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .gradient-overlay {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.8) 0%, rgba(30, 41, 59, 0.95) 100%);
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <img 
                        src="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" 
                        alt="NCIP Logo" 
                        class="h-10 w-10"
                    />
                    <span class="text-xl font-bold text-gray-900">NCIP-EAP</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('student.dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-orange-600 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ url('/auth') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-orange-600 transition">
                            Log In
                        </a>
                        <a href="{{ url('/auth') }}" class="px-6 py-2 text-sm font-semibold text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition shadow-md">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen flex items-center relative overflow-hidden pt-16">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-orange-500 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-amber-500 rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left slide-in-left">
                    <div class="float-animation inline-block mb-6">
                        <img 
                            src="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" 
                            alt="NCIP Logo" 
                            class="h-24 w-24 mx-auto lg:mx-0 drop-shadow-2xl"
                        />
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-black mb-6 leading-tight">
                        <span class="text-white">Empowering</span><br/>
                        <span class="hero-text-gradient">Indigenous</span><br/>
                        <span class="text-white">Futures</span>
                    </h1>
                    <p class="text-xl lg:text-2xl text-gray-200 mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Join the NCIP Educational Assistance Program and unlock your potential through quality education and scholarship opportunities.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @auth
                            <a href="{{ route('student.dashboard') }}" class="px-8 py-4 bg-orange-600 text-white font-bold rounded-lg hover:bg-orange-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                Go to Dashboard
                            </a>
                        @else
                            @if($stats['isFull'])
                                <div class="px-8 py-4 bg-gray-500 text-white font-bold rounded-lg cursor-not-allowed opacity-75">
                                    Applications Currently Full
                                </div>
                                <a href="{{ url('/auth') }}" class="px-8 py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-lg border-2 border-white/20 hover:bg-white/20 transition">
                                    Log In
                                </a>
                            @else
                                <a href="{{ url('/auth') }}" class="px-8 py-4 bg-orange-600 text-white font-bold rounded-lg hover:bg-orange-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    Apply Now
                                </a>
                                <a href="{{ url('/auth') }}" class="px-8 py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-lg border-2 border-white/20 hover:bg-white/20 transition">
                                    Log In
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
                
                <!-- Right Content - Stats -->
                <div class="grid grid-cols-2 gap-6 slide-in-right">
                    <!-- Slot Availability - Prominent -->
                    <div class="stat-card rounded-2xl p-6 text-center fade-in col-span-2 {{ $stats['isFull'] ? 'bg-red-500/20 border-red-500/50' : '' }}" style="animation-delay: 0s">
                        <div class="text-5xl font-black {{ $stats['isFull'] ? 'text-red-400' : 'text-green-400' }} mb-2">
                            {{ number_format($stats['availableSlots']) }}
                        </div>
                        <div class="text-sm text-gray-300 font-medium mb-1">Available Slots</div>
                        <div class="text-xs text-gray-400">of {{ number_format($stats['maxSlots']) }} maximum</div>
                        @if($stats['isFull'])
                            <div class="mt-3 px-3 py-1 bg-red-500/30 rounded-lg text-red-200 text-xs font-bold">
                                Scholarship Slots Full
                            </div>
                        @endif
                    </div>
                    <div class="stat-card rounded-2xl p-6 text-center fade-in" style="animation-delay: 0.2s">
                        <div class="text-4xl font-black text-orange-400 mb-2">{{ number_format($stats['totalScholars']) }}</div>
                        <div class="text-sm text-gray-300 font-medium">Active Scholars</div>
                    </div>
                    <div class="stat-card rounded-2xl p-6 text-center fade-in" style="animation-delay: 0.4s">
                        <div class="text-4xl font-black text-amber-400 mb-2">{{ number_format($stats['totalApplicants']) }}</div>
                        <div class="text-sm text-gray-300 font-medium">Total Applicants</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    Why Choose <span class="text-orange-600">NCIP-EAP</span>?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Comprehensive support for Indigenous students pursuing higher education
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="card-hover bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-8 border border-orange-100">
                    <div class="w-16 h-16 bg-orange-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Quality Education</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Access to accredited educational institutions and programs that align with your career goals.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="card-hover bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-8 border border-orange-100">
                    <div class="w-16 h-16 bg-orange-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Financial Support</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Comprehensive financial assistance covering tuition, books, and other educational expenses.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="card-hover bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-8 border border-orange-100">
                    <div class="w-16 h-16 bg-orange-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Community Support</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Join a network of Indigenous scholars and receive mentorship throughout your academic journey.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    How It <span class="text-orange-600">Works</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Simple steps to start your educational journey
                </p>
            </div>
            
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center fade-in">
                    <div class="w-20 h-20 bg-orange-600 rounded-full flex items-center justify-center text-white text-3xl font-black mx-auto mb-6">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Register</h3>
                    <p class="text-gray-600">
                        Create your account and verify your email address
                    </p>
                </div>
                
                <div class="text-center fade-in" style="animation-delay: 0.2s">
                    <div class="w-20 h-20 bg-orange-600 rounded-full flex items-center justify-center text-white text-3xl font-black mx-auto mb-6">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Apply</h3>
                    <p class="text-gray-600">
                        Complete your scholarship application with all required documents
                    </p>
                </div>
                
                <div class="text-center fade-in" style="animation-delay: 0.4s">
                    <div class="w-20 h-20 bg-orange-600 rounded-full flex items-center justify-center text-white text-3xl font-black mx-auto mb-6">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Review</h3>
                    <p class="text-gray-600">
                        Our team reviews your application and documents
                    </p>
                </div>
                
                <div class="text-center fade-in" style="animation-delay: 0.6s">
                    <div class="w-20 h-20 bg-orange-600 rounded-full flex items-center justify-center text-white text-3xl font-black mx-auto mb-6">
                        4
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Get Started</h3>
                    <p class="text-gray-600">
                        Receive approval and begin your educational journey
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-orange-600 to-amber-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl lg:text-5xl font-black text-white mb-6">
                Ready to Transform Your Future?
            </h2>
            <p class="text-xl text-white/90 mb-4 max-w-2xl mx-auto">
                Join hundreds of Indigenous students who are already benefiting from the NCIP Educational Assistance Program.
            </p>
            @if(!$stats['isFull'])
                <p class="text-lg text-white/80 mb-8 max-w-2xl mx-auto font-semibold">
                    ⚠️ Limited slots available: Only {{ number_format($stats['availableSlots']) }} of {{ number_format($stats['maxSlots']) }} slots remaining!
                </p>
            @else
                <p class="text-lg text-white/80 mb-8 max-w-2xl mx-auto font-semibold bg-red-500/30 px-6 py-3 rounded-lg inline-block">
                    ⚠️ All {{ number_format($stats['maxSlots']) }} scholarship slots are currently full. Please check back later for availability.
                </p>
            @endif
            @auth
                <a href="{{ route('student.dashboard') }}" class="inline-block px-8 py-4 bg-white text-orange-600 font-bold rounded-lg hover:bg-gray-100 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Go to Dashboard
                </a>
            @else
                @if(!$stats['isFull'])
                    <a href="{{ url('/auth') }}" class="inline-block px-8 py-4 bg-white text-orange-600 font-bold rounded-lg hover:bg-gray-100 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Start Your Application
                    </a>
                @else
                    <a href="{{ url('/auth') }}" class="inline-block px-8 py-4 bg-white/50 text-white font-bold rounded-lg cursor-not-allowed opacity-75">
                        Applications Currently Full
                    </a>
                @endif
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <img 
                            src="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" 
                            alt="NCIP Logo" 
                            class="h-10 w-10"
                        />
                        <span class="text-xl font-bold text-white">NCIP-EAP</span>
                    </div>
                    <p class="text-sm leading-relaxed">
                        Empowering Indigenous communities through education and scholarship opportunities.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-orange-400 transition">Home</a></li>
                        @auth
                            <li><a href="{{ route('student.dashboard') }}" class="hover:text-orange-400 transition">Dashboard</a></li>
                        @else
                            <li><a href="{{ url('/auth') }}" class="hover:text-orange-400 transition">Log In</a></li>
                            <li><a href="{{ url('/auth') }}" class="hover:text-orange-400 transition">Register</a></li>
                        @endauth
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">Contact</h4>
                    <p class="text-sm leading-relaxed">
                        National Commission on Indigenous Peoples<br/>
                        Educational Assistance Program<br/>
                        <a href="mailto:support@ncip-eap.gov.ph" class="text-orange-400 hover:text-orange-300">support@ncip-eap.gov.ph</a>
                    </p>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} NCIP Educational Assistance Program. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

