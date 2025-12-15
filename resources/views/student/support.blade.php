@extends('layouts.student')

@section('title', 'Support & Help - IP Scholar Portal')

@push('head-scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
@endpush

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    body { 
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    /* Glassmorphism effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    details summary {
        list-style: none;
        cursor: pointer;
        user-select: none;
    }
    
    details summary::-webkit-details-marker {
        display: none;
    }
    
    details summary::marker {
        display: none;
    }
    
    details[open] summary .chevron {
        transform: rotate(180deg);
    }
    
    .priority-tab {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .priority-tab::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }
    
    .priority-tab:hover::before {
        left: 100%;
    }
    
    .priority-tab.active {
        background: linear-gradient(135deg, #3b82f6 0%, #f59e0b 100%);
        color: white;
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3), 0 0 0 3px rgba(59, 130, 246, 0.1);
        border-color: transparent !important;
    }
    
    .priority-content {
        display: none;
    }
    
    .priority-content.active {
        display: block;
        animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    @keyframes slideIn {
        from { 
            opacity: 0; 
            transform: translateY(20px) scale(0.95); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0) scale(1); 
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card-hover {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .card-hover::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        padding: 2px;
        background: linear-gradient(135deg, #3b82f6, #f59e0b);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .card-hover:hover {
        transform: translateY(-6px) scale(1.01);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }
    
    .card-hover:hover::after {
        opacity: 1;
    }
    
    /* Modern input focus */
    input:focus, textarea:focus {
        transform: scale(1.01);
    }
    
    /* Smooth scroll */
    html {
        scroll-behavior: smooth;
    }
    
    /* Gradient text */
    .gradient-text {
        background: linear-gradient(135deg, #3b82f6 0%, #f59e0b 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Modern button */
    .btn-modern {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-modern::before {
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
    
    .btn-modern:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.4);
    }
    
    /* Table row hover */
    tbody tr {
        transition: all 0.2s ease;
    }
    
    tbody tr:hover {
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen pt-16 sm:pt-20 relative overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-orange-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-orange-400/20 to-blue-400/20 rounded-full blur-3xl"></div>
    </div>
    
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-white via-blue-50/30 to-orange-50/30 border-b border-slate-200/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-blue-500 via-blue-600 to-orange-500 rounded-3xl mb-8 shadow-2xl transform hover:scale-110 transition-transform duration-300 border-4 border-white/50">
                    <i data-lucide="help-circle" class="w-10 h-10 sm:w-12 sm:h-12 text-white"></i>
                </div>
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black mb-6">
                    <span class="gradient-text">Support Center</span>
                </h1>
                <p class="text-xl sm:text-2xl text-slate-700 font-medium leading-relaxed">
                    Find answers, understand our priority system, and get the help you need.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 relative z-10">
        
        <!-- Quick Navigation Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8 mb-12 sm:mb-16">
            <a href="#faq" class="group card-hover glass-card rounded-2xl p-8 shadow-xl border border-white/50 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex items-center gap-5">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 group-hover:from-blue-600 group-hover:to-blue-700 rounded-2xl flex items-center justify-center transition-all duration-300 shadow-lg group-hover:shadow-xl group-hover:scale-110">
                        <i data-lucide="message-circle-question" class="w-8 h-8 text-white"></i>
                    </div>
                    <div class="text-left flex-1">
                        <h3 class="font-bold text-slate-900 text-lg mb-1 group-hover:text-blue-600 transition-colors">FAQs</h3>
                        <p class="text-sm text-slate-600 font-medium">Common questions</p>
                    </div>
                    <i data-lucide="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>
            
            <a href="#rubrics" class="group card-hover glass-card rounded-2xl p-8 shadow-xl border border-white/50 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex items-center gap-5">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 group-hover:from-orange-600 group-hover:to-orange-700 rounded-2xl flex items-center justify-center transition-all duration-300 shadow-lg group-hover:shadow-xl group-hover:scale-110">
                        <i data-lucide="award" class="w-8 h-8 text-white"></i>
                    </div>
                    <div class="text-left flex-1">
                        <h3 class="font-bold text-slate-900 text-lg mb-1 group-hover:text-orange-600 transition-colors">Rubrics</h3>
                        <p class="text-sm text-slate-600 font-medium">Priority scoring</p>
                    </div>
                    <i data-lucide="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-orange-600 group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>
            
            <a href="#contact" class="group card-hover glass-card rounded-2xl p-8 shadow-xl border border-white/50 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex items-center gap-5">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 group-hover:from-indigo-600 group-hover:to-indigo-700 rounded-2xl flex items-center justify-center transition-all duration-300 shadow-lg group-hover:shadow-xl group-hover:scale-110">
                        <i data-lucide="mail" class="w-8 h-8 text-white"></i>
                    </div>
                    <div class="text-left flex-1">
                        <h3 class="font-bold text-slate-900 text-lg mb-1 group-hover:text-indigo-600 transition-colors">Contact</h3>
                        <p class="text-sm text-slate-600 font-medium">Get in touch</p>
                    </div>
                    <i data-lucide="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
            
            <!-- Left Column: FAQ & Rubrics -->
            <div class="lg:col-span-2 space-y-6 sm:space-y-8">
                
                <!-- FAQ Section -->
                <section id="faq" class="glass-card rounded-2xl shadow-2xl border border-white/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 sm:px-8 py-6 border-b border-blue-400/30">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30">
                                <i data-lucide="message-circle-question" class="w-6 h-6 text-white"></i>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-white">Frequently Asked Questions</h2>
                        </div>
                    </div>
                    <div class="p-6 sm:p-8 space-y-4">
                        <details class="group bg-gradient-to-r from-slate-50 to-white rounded-xl border-2 border-slate-200 hover:border-blue-400 transition-all duration-300 shadow-sm hover:shadow-md overflow-hidden">
                            <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-blue-50/50 transition-colors">
                                <span class="font-semibold text-slate-900 pr-4 text-base">How do I apply for a scholarship?</span>
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center transition-colors">
                                    <i data-lucide="chevron-down" class="w-5 h-5 text-blue-600 chevron transition-transform"></i>
                                </div>
                            </summary>
                            <div class="px-5 pb-5 text-slate-700 leading-relaxed text-sm">
                                Go to the Dashboard and click the "Apply for Scholarship" button. Fill out the required information and submit your application. You can track your application status on your dashboard.
                            </div>
                        </details>
                        <details class="group bg-gradient-to-r from-slate-50 to-white rounded-xl border-2 border-slate-200 hover:border-orange-400 transition-all duration-300 shadow-sm hover:shadow-md overflow-hidden">
                            <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-orange-50/50 transition-colors">
                                <span class="font-semibold text-slate-900 pr-4 text-base">What documents are required?</span>
                                <div class="flex-shrink-0 w-8 h-8 bg-orange-100 group-hover:bg-orange-200 rounded-lg flex items-center justify-center transition-colors">
                                    <i data-lucide="chevron-down" class="w-5 h-5 text-orange-600 chevron transition-transform"></i>
                                </div>
                            </summary>
                            <div class="px-5 pb-5 text-slate-700 leading-relaxed text-sm">
                                You will need to submit a Certificate of Low Income, proof of Indigenous status, and your latest academic records. Check the requirements section on your dashboard for more details.
                            </div>
                        </details>
                        <details class="group bg-gradient-to-r from-slate-50 to-white rounded-xl border-2 border-slate-200 hover:border-blue-400 transition-all duration-300 shadow-sm hover:shadow-md overflow-hidden">
                            <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-blue-50/50 transition-colors">
                                <span class="font-semibold text-slate-900 pr-4 text-base">How will I know if my application is approved?</span>
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center transition-colors">
                                    <i data-lucide="chevron-down" class="w-5 h-5 text-blue-600 chevron transition-transform"></i>
                                </div>
                            </summary>
                            <div class="px-5 pb-5 text-slate-700 leading-relaxed text-sm">
                                You will receive a notification on your portal and an email once your application status is updated. You can also check the Notifications page for updates.
                            </div>
                        </details>
                        <details class="group bg-gradient-to-r from-slate-50 to-white rounded-xl border-2 border-slate-200 hover:border-orange-400 transition-all duration-300 shadow-sm hover:shadow-md overflow-hidden">
                            <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-orange-50/50 transition-colors">
                                <span class="font-semibold text-slate-900 pr-4 text-base">Who can I contact for urgent help?</span>
                                <div class="flex-shrink-0 w-8 h-8 bg-orange-100 group-hover:bg-orange-200 rounded-lg flex items-center justify-center transition-colors">
                                    <i data-lucide="chevron-down" class="w-5 h-5 text-orange-600 chevron transition-transform"></i>
                                </div>
                            </summary>
                            <div class="px-5 pb-5 text-slate-700 leading-relaxed text-sm">
                                You can use the contact form below or email our support team at <a href="mailto:support@ipscholar.com" class="text-blue-600 font-semibold hover:text-blue-700 hover:underline transition-colors">support@ipscholar.com</a>. For urgent issues, please indicate "URGENT" in your message subject.
                            </div>
                        </details>
                    </div>
                </section>

                <!-- Priority Rubrics Section -->
                <section id="rubrics" class="glass-card rounded-2xl shadow-2xl border border-white/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-600 via-orange-500 to-amber-600 px-6 sm:px-8 py-6 border-b border-orange-400/30">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30">
                                    <i data-lucide="award" class="w-6 h-6 text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl sm:text-3xl font-bold text-white">Priority Scoring Rubrics</h2>
                                    <p class="text-sm sm:text-base text-white/90 mt-1 font-medium">Understand how each criterion affects your score</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Priority Tabs -->
                        <div class="flex flex-wrap gap-3">
                            <button onclick="showPriorityTab('ip-group', this)" class="priority-tab active px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white shadow-lg">
                                <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                                IP Group (30%)
                            </button>
                            <button onclick="showPriorityTab('course', this)" class="priority-tab px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 border border-white/20 transition-all">
                                <i data-lucide="graduation-cap" class="w-4 h-4 inline mr-2"></i>
                                Course (25%)
                            </button>
                            <button onclick="showPriorityTab('tribal-cert', this)" class="priority-tab px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 border border-white/20 transition-all">
                                <i data-lucide="file-check" class="w-4 h-4 inline mr-2"></i>
                                Tribal Cert (20%)
                            </button>
                            <button onclick="showPriorityTab('income-tax', this)" class="priority-tab px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 border border-white/20 transition-all">
                                <i data-lucide="receipt" class="w-4 h-4 inline mr-2"></i>
                                Income Tax (15%)
                            </button>
                            <button onclick="showPriorityTab('academic', this)" class="priority-tab px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 border border-white/20 transition-all">
                                <i data-lucide="award" class="w-4 h-4 inline mr-2"></i>
                                Academic (5%)
                            </button>
                            <button onclick="showPriorityTab('other', this)" class="priority-tab px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 border border-white/20 transition-all">
                                <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i>
                                Other (5%)
                            </button>
                        </div>
                    </div>
                    
                    <!-- IP Group Priority Content -->
                    <div id="priority-ip-group" class="priority-content active">
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="bg-blue-50 border-l-4 border-blue-900 rounded-r-lg p-5">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="info" class="w-5 h-5 text-blue-900"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-slate-900 mb-2">IP Group Priority (30% of total score)</h3>
                                        <p class="text-slate-700 text-sm leading-relaxed mb-3">
                                            Your IP Group priority is evaluated based on documentation quality using a 0-10 scale. 
                                            Priority IP groups (B'laan, Bagobo, Kalagan, Kaulo) receive a +2 point bonus.
                                        </p>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="px-3 py-1 bg-blue-200 text-blue-900 rounded-lg text-xs font-semibold border border-blue-300">B'laan</span>
                                            <span class="px-3 py-1 bg-blue-200 text-blue-900 rounded-lg text-xs font-semibold border border-blue-300">Bagobo</span>
                                            <span class="px-3 py-1 bg-blue-200 text-blue-900 rounded-lg text-xs font-semibold border border-blue-300">Kalagan</span>
                                            <span class="px-3 py-1 bg-blue-200 text-blue-900 rounded-lg text-xs font-semibold border border-blue-300">Kaulo</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto rounded-xl border-2 border-slate-200 shadow-lg">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                                            <th class="px-5 py-4 text-left font-bold text-sm">Score</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Quality</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Requirements</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-green-700 text-base">10/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Validated</td>
                                            <td class="px-5 py-4 text-slate-700">All 3 docs approved OR Tribal Cert + Endorsement approved</td>
                                        </tr>
                                        <tr class="hover:bg-blue-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-blue-700 text-base">8/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Missing 1</td>
                                            <td class="px-5 py-4 text-slate-700">2 approved docs OR 1 approved (Tribal Cert)</td>
                                        </tr>
                                        <tr class="hover:bg-yellow-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-yellow-700 text-base">6/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Partial</td>
                                            <td class="px-5 py-4 text-slate-700">1 approved (not Tribal Cert) OR pending/rejected docs</td>
                                        </tr>
                                        <tr class="hover:bg-orange-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-orange-700 text-base">4/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Self-Declared</td>
                                            <td class="px-5 py-4 text-slate-700">Claims IP group but no documents submitted</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-slate-700 text-base">0/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">No Affiliation</td>
                                            <td class="px-5 py-4 text-slate-700">No ethnicity declared</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Course Priority Content -->
                    <div id="priority-course" class="priority-content">
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="bg-orange-50 border-l-4 border-orange-600 rounded-r-lg p-5">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-orange-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="info" class="w-5 h-5 text-orange-900"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 mb-2">Course Priority (25% of total score)</h3>
                                        <p class="text-slate-700 text-sm leading-relaxed">
                                            Your course is evaluated on a 0-10 scale: Priority courses (10/10), Related courses (6/10), 
                                            or Not priority/related (0/10).
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto rounded-xl border-2 border-slate-200 shadow-lg">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-orange-600 to-amber-600 text-white">
                                            <th class="px-5 py-4 text-left font-bold text-sm">Score</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Level</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-green-700 text-base">10/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">High Scale</td>
                                            <td class="px-5 py-4 text-slate-700">Exact match with one of 18 priority courses</td>
                                        </tr>
                                        <tr class="hover:bg-yellow-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-yellow-700 text-base">6/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Mid Scale</td>
                                            <td class="px-5 py-4 text-slate-700">Related/relevant to priority courses</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-slate-700 text-base">0/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Low Scale</td>
                                            <td class="px-5 py-4 text-slate-700">Not priority and not related</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl p-6 border-2 border-orange-200 shadow-md">
                                <h4 class="font-bold text-slate-900 mb-5 text-base flex items-center gap-2">
                                    <span class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center text-white text-sm font-bold">18</span>
                                    <span>Priority Courses:</span>
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Agriculture</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Aqua-Culture</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Anthropology</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Business Admin</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Civil Engineering</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Community Dev</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Criminology</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Education</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Foreign Service</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Forestry</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Geodetic Eng</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Geology</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Law</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Medicine</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Mechanical Eng</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Mining Eng</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Social Sciences</span>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl border-2 border-orange-200 text-slate-900 shadow-sm hover:shadow-md hover:border-orange-400 transition-all cursor-pointer group">
                                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-semibold">Social Work</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tribal Certificate Priority Content -->
                    <div id="priority-tribal-cert" class="priority-content">
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="bg-blue-50 border-l-4 border-blue-900 rounded-r-lg p-5">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="info" class="w-5 h-5 text-blue-900"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 mb-2">Tribal Certificate Priority (20% of total score)</h3>
                                        <p class="text-slate-700 text-sm leading-relaxed">
                                            The Tribal Certificate (Certificate of Tribal Membership/Confirmation) is a critical document 
                                            that demonstrates your IP affiliation.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto rounded-xl border-2 border-slate-200 shadow-lg">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                                            <th class="px-5 py-4 text-left font-bold text-sm">Score</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Status</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-green-700 text-base">10/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Approved</td>
                                            <td class="px-5 py-4 text-slate-700">Tribal Certificate submitted and approved by staff</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-slate-700 text-base">0/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Not Approved</td>
                                            <td class="px-5 py-4 text-slate-700">Not submitted, pending, or rejected</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Income Tax Priority Content -->
                    <div id="priority-income-tax" class="priority-content">
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="bg-orange-50 border-l-4 border-orange-600 rounded-r-lg p-5">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-orange-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="info" class="w-5 h-5 text-orange-900"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 mb-2">Income Tax Priority (15% of total score)</h3>
                                        <p class="text-slate-700 text-sm leading-relaxed">
                                            Income Tax documents (Certificate of Low Income) demonstrate your financial need 
                                            and eligibility for scholarship assistance.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto rounded-xl border-2 border-slate-200 shadow-lg">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-orange-600 to-amber-600 text-white">
                                            <th class="px-5 py-4 text-left font-bold text-sm">Score</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Status</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-green-700 text-base">10/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Approved</td>
                                            <td class="px-5 py-4 text-slate-700">Income Tax document submitted and approved by staff</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-slate-700 text-base">0/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Not Approved</td>
                                            <td class="px-5 py-4 text-slate-700">Not submitted, pending, or rejected</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Performance Priority Content -->
                    <div id="priority-academic" class="priority-content">
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="bg-blue-50 border-l-4 border-blue-900 rounded-r-lg p-5">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="info" class="w-5 h-5 text-blue-900"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 mb-2">Academic Performance Priority (5% of total score)</h3>
                                        <p class="text-slate-700 text-sm leading-relaxed">
                                            Academic records (grades, transcripts, report cards) demonstrate your academic standing 
                                            and commitment to education.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto rounded-xl border-2 border-slate-200 shadow-lg">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                                            <th class="px-5 py-4 text-left font-bold text-sm">Score</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Status</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-green-700 text-base">10/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Approved</td>
                                            <td class="px-5 py-4 text-slate-700">Academic records submitted and approved by staff</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-slate-700 text-base">0/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Not Approved</td>
                                            <td class="px-5 py-4 text-slate-700">Not submitted, pending, or rejected</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Other Requirements Priority Content -->
                    <div id="priority-other" class="priority-content">
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="bg-orange-50 border-l-4 border-orange-600 rounded-r-lg p-5">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-orange-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="info" class="w-5 h-5 text-orange-900"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 mb-2">Other Requirements Priority (5% of total score)</h3>
                                        <p class="text-slate-700 text-sm leading-relaxed">
                                            All three documents must be approved: Birth Certificate, Endorsement, and Good Moral Certificate.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl p-6 border-2 border-orange-200 shadow-md">
                                <h4 class="font-bold text-slate-900 mb-5 text-base">Required Documents:</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-4 bg-white p-5 rounded-xl border-2 border-orange-200 shadow-sm hover:shadow-md transition-all">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                                        </div>
                                        <div>
                                            <strong class="text-slate-900 text-base">Birth Certificate</strong>
                                            <p class="text-sm text-slate-600 mt-1.5">Shows birthplace and personal information</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4 bg-white p-5 rounded-xl border-2 border-orange-200 shadow-sm hover:shadow-md transition-all">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                                        </div>
                                        <div>
                                            <strong class="text-slate-900 text-base">Endorsement</strong>
                                            <p class="text-sm text-slate-600 mt-1.5">Endorsement from IP/IP Traditional Leaders</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4 bg-white p-5 rounded-xl border-2 border-orange-200 shadow-sm hover:shadow-md transition-all">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                                        </div>
                                        <div>
                                            <strong class="text-slate-900 text-base">Good Moral Certificate</strong>
                                            <p class="text-sm text-slate-600 mt-1.5">Demonstrates good character and conduct</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto rounded-xl border-2 border-slate-200 shadow-lg">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-orange-600 to-amber-600 text-white">
                                            <th class="px-5 py-4 text-left font-bold text-sm">Score</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Status</th>
                                            <th class="px-5 py-4 text-left font-bold text-sm">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-green-700 text-base">10/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">All Approved</td>
                                            <td class="px-5 py-4 text-slate-700">All 3 documents (Birth Cert, Endorsement, Good Moral) approved</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-all cursor-pointer">
                                            <td class="px-5 py-4 font-bold text-slate-700 text-base">0/10</td>
                                            <td class="px-5 py-4 font-semibold text-slate-800">Not All Approved</td>
                                            <td class="px-5 py-4 text-slate-700">One or more documents missing, pending, or rejected</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right Column: Contact & Quick Links -->
            <div class="space-y-6 sm:space-y-8">
                <!-- Contact Form -->
                <section id="contact" class="glass-card rounded-2xl shadow-2xl border border-white/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 via-indigo-500 to-blue-600 px-6 sm:px-8 py-6 border-b border-indigo-400/30">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30">
                                <i data-lucide="mail" class="w-6 h-6 text-white"></i>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-white">Contact Support</h2>
                        </div>
                    </div>
                    <form method="POST" action="#" class="p-6 sm:p-8 space-y-6">
                        @csrf
                        <div>
                            <label class="block text-slate-700 mb-2.5 font-bold text-sm">Your Email</label>
                            <input type="email" name="email" value="{{ $student->email }}" 
                                class="w-full border-2 border-slate-200 rounded-xl px-4 py-3.5 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition-all text-slate-700 font-medium shadow-sm" 
                                required readonly>
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-2.5 font-bold text-sm">Subject</label>
                            <input type="text" name="subject" 
                                class="w-full border-2 border-slate-200 rounded-xl px-4 py-3.5 bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20 transition-all text-slate-700 placeholder-slate-400 font-medium shadow-sm" 
                                placeholder="What's this about?" required>
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-2.5 font-bold text-sm">Message</label>
                            <textarea name="message" rows="5" 
                                class="w-full border-2 border-slate-200 rounded-xl px-4 py-3.5 bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition-all text-slate-700 placeholder-slate-400 resize-none font-medium shadow-sm" 
                                placeholder="Describe your issue or question..." required></textarea>
                        </div>
                        <button type="submit" 
                            class="btn-modern w-full bg-gradient-to-r from-indigo-600 via-indigo-500 to-blue-600 hover:from-indigo-700 hover:via-indigo-600 hover:to-blue-700 text-white font-bold py-4 rounded-xl shadow-xl relative overflow-hidden">
                            <span class="relative z-10 flex items-center justify-center gap-2.5">
                                <i data-lucide="send" class="w-5 h-5"></i>
                                Send Message
                            </span>
                        </button>
                    </form>
                </section>

                <!-- Quick Links -->
                <div class="glass-card rounded-2xl shadow-2xl border border-white/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 via-purple-500 to-pink-600 px-6 sm:px-8 py-6 border-b border-purple-400/30">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30">
                                <i data-lucide="link" class="w-6 h-6 text-white"></i>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-white">Quick Links</h2>
                        </div>
                    </div>
                    <div class="p-6 sm:p-8 space-y-4">
                        <a href="{{ route('student.dashboard') }}" 
                            class="group flex items-center gap-4 px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 text-slate-900 rounded-xl font-bold transition-all duration-300 border-2 border-blue-200 hover:border-blue-400 shadow-sm hover:shadow-md transform hover:scale-[1.02]">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i data-lucide="layout-dashboard" class="w-5 h-5 text-white"></i>
                            </div>
                            <span class="flex-1">Go to Dashboard</span>
                            <i data-lucide="arrow-right" class="w-5 h-5 text-blue-600 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="{{ route('student.notifications') }}" 
                            class="group flex items-center gap-4 px-5 py-4 bg-gradient-to-r from-orange-50 to-amber-50 hover:from-orange-100 hover:to-amber-100 text-slate-900 rounded-xl font-bold transition-all duration-300 border-2 border-orange-200 hover:border-orange-400 shadow-sm hover:shadow-md transform hover:scale-[1.02]">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i data-lucide="bell" class="w-5 h-5 text-white"></i>
                            </div>
                            <span class="flex-1">View Notifications</span>
                            <i data-lucide="arrow-right" class="w-5 h-5 text-orange-600 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="{{ route('student.profile') }}" 
                            class="group flex items-center gap-4 px-5 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 hover:from-indigo-100 hover:to-purple-100 text-slate-900 rounded-xl font-bold transition-all duration-300 border-2 border-indigo-200 hover:border-indigo-400 shadow-sm hover:shadow-md transform hover:scale-[1.02]">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i data-lucide="user" class="w-5 h-5 text-white"></i>
                            </div>
                            <span class="flex-1">Edit Profile</span>
                            <i data-lucide="arrow-right" class="w-5 h-5 text-indigo-600 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
    
    function showPriorityTab(tabName, buttonElement) {
        // Hide all content
        document.querySelectorAll('.priority-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.priority-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Show selected content
        document.getElementById('priority-' + tabName).classList.add('active');
        
        // Add active class to clicked tab
        if (buttonElement) {
            buttonElement.classList.add('active');
        }
        
        // Reinitialize icons
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }
</script>
@endpush
@endsection



