@extends('layouts.student')

@section('title', 'Support & Help - IP Scholar Portal')

@push('head-scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
@endpush

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    body { font-family: 'Inter', sans-serif; }
    
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
    }
    
    .priority-tab.active {
        background: linear-gradient(135deg, #dbeafe 0%, #fed7aa 100%);
        color: #1e3a8a;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30, 58, 138, 0.2);
        border-color: #3b82f6 !important;
    }
    
    .priority-content {
        display: none;
    }
    
    .priority-content.active {
        display: block;
        animation: fadeIn 0.4s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card-hover {
        transition: all 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 pt-16 sm:pt-20">
    
    <!-- Hero Section -->
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-100 to-orange-100 rounded-2xl mb-6 shadow-lg border-2 border-blue-200">
                    <i data-lucide="help-circle" class="w-8 h-8 sm:w-10 sm:h-10 text-blue-900"></i>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 mb-4">
                    Support Center
                </h1>
                <p class="text-lg sm:text-xl text-slate-600">
                    Find answers, understand our priority system, and get the help you need.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        
        <!-- Quick Navigation Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8 sm:mb-12">
            <a href="#faq" class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-all border border-slate-200 hover:border-blue-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-xl flex items-center justify-center transition-colors">
                        <i data-lucide="message-circle-question" class="w-6 h-6 text-blue-900"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="font-bold text-slate-900 text-base">FAQs</h3>
                        <p class="text-sm text-slate-600">Common questions</p>
                    </div>
                </div>
            </a>
            
            <a href="#rubrics" class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-all border border-slate-200 hover:border-orange-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-100 group-hover:bg-orange-200 rounded-xl flex items-center justify-center transition-colors">
                        <i data-lucide="award" class="w-6 h-6 text-orange-900"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="font-bold text-slate-900 text-base">Rubrics</h3>
                        <p class="text-sm text-slate-600">Priority scoring</p>
                    </div>
                </div>
            </a>
            
            <a href="#contact" class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-all border border-slate-200 hover:border-blue-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-xl flex items-center justify-center transition-colors">
                        <i data-lucide="mail" class="w-6 h-6 text-blue-900"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="font-bold text-slate-900 text-base">Contact</h3>
                        <p class="text-sm text-slate-600">Get in touch</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
            
            <!-- Left Column: FAQ & Rubrics -->
            <div class="lg:col-span-2 space-y-6 sm:space-y-8">
                
                <!-- FAQ Section -->
                <section id="faq" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-100 to-blue-50 px-6 sm:px-8 py-5 border-b border-blue-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center">
                                <i data-lucide="message-circle-question" class="w-5 h-5 text-blue-900"></i>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Frequently Asked Questions</h2>
                        </div>
                    </div>
                    <div class="p-6 sm:p-8 space-y-3">
                        <details class="group bg-slate-50 rounded-lg border border-slate-200 hover:border-blue-300 transition-colors">
                            <summary class="flex items-center justify-between p-4 cursor-pointer">
                                <span class="font-semibold text-slate-900 pr-4">How do I apply for a scholarship?</span>
                                <i data-lucide="chevron-down" class="w-5 h-5 text-blue-600 chevron transition-transform flex-shrink-0"></i>
                            </summary>
                            <div class="px-4 pb-4 text-slate-600 leading-relaxed">
                                Go to the Dashboard and click the "Apply for Scholarship" button. Fill out the required information and submit your application. You can track your application status on your dashboard.
                            </div>
                        </details>
                        <details class="group bg-slate-50 rounded-lg border border-slate-200 hover:border-orange-300 transition-colors">
                            <summary class="flex items-center justify-between p-4 cursor-pointer">
                                <span class="font-semibold text-slate-900 pr-4">What documents are required?</span>
                                <i data-lucide="chevron-down" class="w-5 h-5 text-orange-600 chevron transition-transform flex-shrink-0"></i>
                            </summary>
                            <div class="px-4 pb-4 text-slate-600 leading-relaxed">
                                You will need to submit a Certificate of Low Income, proof of Indigenous status, and your latest academic records. Check the requirements section on your dashboard for more details.
                            </div>
                        </details>
                        <details class="group bg-slate-50 rounded-lg border border-slate-200 hover:border-blue-300 transition-colors">
                            <summary class="flex items-center justify-between p-4 cursor-pointer">
                                <span class="font-semibold text-slate-900 pr-4">How will I know if my application is approved?</span>
                                <i data-lucide="chevron-down" class="w-5 h-5 text-blue-600 chevron transition-transform flex-shrink-0"></i>
                            </summary>
                            <div class="px-4 pb-4 text-slate-600 leading-relaxed">
                                You will receive a notification on your portal and an email once your application status is updated. You can also check the Notifications page for updates.
                            </div>
                        </details>
                        <details class="group bg-slate-50 rounded-lg border border-slate-200 hover:border-orange-300 transition-colors">
                            <summary class="flex items-center justify-between p-4 cursor-pointer">
                                <span class="font-semibold text-slate-900 pr-4">Who can I contact for urgent help?</span>
                                <i data-lucide="chevron-down" class="w-5 h-5 text-orange-600 chevron transition-transform flex-shrink-0"></i>
                            </summary>
                            <div class="px-4 pb-4 text-slate-600 leading-relaxed">
                                You can use the contact form below or email our support team at <a href="mailto:support@ipscholar.com" class="text-blue-900 font-semibold hover:underline">support@ipscholar.com</a>. For urgent issues, please indicate "URGENT" in your message subject.
                            </div>
                        </details>
                    </div>
                </section>

                <!-- Priority Rubrics Section -->
                <section id="rubrics" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 via-orange-50 to-blue-50 px-6 sm:px-8 py-5 border-b border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center">
                                    <i data-lucide="award" class="w-5 h-5 text-blue-900"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Priority Scoring Rubrics</h2>
                                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5">Understand how each criterion affects your score</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Priority Tabs -->
                        <div class="flex flex-wrap gap-2">
                            <button onclick="showPriorityTab('ip-group', this)" class="priority-tab active px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold bg-blue-100 text-blue-900 hover:bg-blue-200 border-2 border-blue-300">
                                <i data-lucide="users" class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1.5"></i>
                                IP Group (30%)
                            </button>
                            <button onclick="showPriorityTab('course', this)" class="priority-tab px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 border-2 border-slate-300">
                                <i data-lucide="graduation-cap" class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1.5"></i>
                                Course (25%)
                            </button>
                            <button onclick="showPriorityTab('tribal-cert', this)" class="priority-tab px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 border-2 border-slate-300">
                                <i data-lucide="file-check" class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1.5"></i>
                                Tribal Cert (20%)
                            </button>
                            <button onclick="showPriorityTab('income-tax', this)" class="priority-tab px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 border-2 border-slate-300">
                                <i data-lucide="receipt" class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1.5"></i>
                                Income Tax (15%)
                            </button>
                            <button onclick="showPriorityTab('academic', this)" class="priority-tab px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 border-2 border-slate-300">
                                <i data-lucide="award" class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1.5"></i>
                                Academic (5%)
                            </button>
                            <button onclick="showPriorityTab('other', this)" class="priority-tab px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 border-2 border-slate-300">
                                <i data-lucide="file-text" class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1.5"></i>
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
                            
                            <div class="overflow-x-auto rounded-lg border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-blue-100 to-blue-200 text-slate-900">
                                            <th class="px-4 py-3 text-left font-semibold">Score</th>
                                            <th class="px-4 py-3 text-left font-semibold">Quality</th>
                                            <th class="px-4 py-3 text-left font-semibold">Requirements</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-green-700">10/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Validated</td>
                                            <td class="px-4 py-3 text-slate-600">All 3 docs approved OR Tribal Cert + Endorsement approved</td>
                                        </tr>
                                        <tr class="hover:bg-blue-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-blue-700">8/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Missing 1</td>
                                            <td class="px-4 py-3 text-slate-600">2 approved docs OR 1 approved (Tribal Cert)</td>
                                        </tr>
                                        <tr class="hover:bg-yellow-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-yellow-700">6/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Partial</td>
                                            <td class="px-4 py-3 text-slate-600">1 approved (not Tribal Cert) OR pending/rejected docs</td>
                                        </tr>
                                        <tr class="hover:bg-orange-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-orange-700">4/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Self-Declared</td>
                                            <td class="px-4 py-3 text-slate-600">Claims IP group but no documents submitted</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-slate-700">0/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">No Affiliation</td>
                                            <td class="px-4 py-3 text-slate-600">No ethnicity declared</td>
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
                            
                            <div class="overflow-x-auto rounded-lg border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-orange-100 to-orange-200 text-slate-900">
                                            <th class="px-4 py-3 text-left font-semibold">Score</th>
                                            <th class="px-4 py-3 text-left font-semibold">Level</th>
                                            <th class="px-4 py-3 text-left font-semibold">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-green-700">10/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">High Scale</td>
                                            <td class="px-4 py-3 text-slate-600">Exact match with one of 18 priority courses</td>
                                        </tr>
                                        <tr class="hover:bg-yellow-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-yellow-700">6/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Mid Scale</td>
                                            <td class="px-4 py-3 text-slate-600">Related/relevant to priority courses</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-slate-700">0/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Low Scale</td>
                                            <td class="px-4 py-3 text-slate-600">Not priority and not related</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="bg-orange-50 rounded-lg p-5 border border-orange-200">
                                <h4 class="font-bold text-slate-900 mb-4 text-sm">18 Priority Courses:</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 text-xs sm:text-sm">
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Agriculture</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Aqua-Culture</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Anthropology</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Business Admin</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Civil Engineering</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Community Dev</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Criminology</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Education</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Foreign Service</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Forestry</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Geodetic Eng</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Geology</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Law</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Medicine</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Mechanical Eng</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Mining Eng</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Social Sciences</span></div>
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-orange-200 text-slate-900"><i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600"></i><span class="font-medium">Social Work</span></div>
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
                            
                            <div class="overflow-x-auto rounded-lg border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-blue-100 to-blue-200 text-slate-900">
                                            <th class="px-4 py-3 text-left font-semibold">Score</th>
                                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                                            <th class="px-4 py-3 text-left font-semibold">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-green-700">10/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Approved</td>
                                            <td class="px-4 py-3 text-slate-600">Tribal Certificate submitted and approved by staff</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-slate-700">0/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Not Approved</td>
                                            <td class="px-4 py-3 text-slate-600">Not submitted, pending, or rejected</td>
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
                            
                            <div class="overflow-x-auto rounded-lg border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-orange-100 to-orange-200 text-slate-900">
                                            <th class="px-4 py-3 text-left font-semibold">Score</th>
                                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                                            <th class="px-4 py-3 text-left font-semibold">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-green-700">10/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Approved</td>
                                            <td class="px-4 py-3 text-slate-600">Income Tax document submitted and approved by staff</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-slate-700">0/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Not Approved</td>
                                            <td class="px-4 py-3 text-slate-600">Not submitted, pending, or rejected</td>
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
                            
                            <div class="overflow-x-auto rounded-lg border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-blue-100 to-blue-200 text-slate-900">
                                            <th class="px-4 py-3 text-left font-semibold">Score</th>
                                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                                            <th class="px-4 py-3 text-left font-semibold">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-green-700">10/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Approved</td>
                                            <td class="px-4 py-3 text-slate-600">Academic records submitted and approved by staff</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-slate-700">0/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Not Approved</td>
                                            <td class="px-4 py-3 text-slate-600">Not submitted, pending, or rejected</td>
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
                            
                            <div class="bg-orange-50 rounded-lg p-5 border border-orange-200">
                                <h4 class="font-bold text-slate-900 mb-4 text-sm">Required Documents:</h4>
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3 bg-white p-4 rounded-lg border border-orange-200">
                                        <i data-lucide="check-circle" class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <strong class="text-slate-900">Birth Certificate</strong>
                                            <p class="text-sm text-slate-600 mt-1">Shows birthplace and personal information</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 bg-white p-4 rounded-lg border border-orange-200">
                                        <i data-lucide="check-circle" class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <strong class="text-slate-900">Endorsement</strong>
                                            <p class="text-sm text-slate-600 mt-1">Endorsement from IP/IP Traditional Leaders</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 bg-white p-4 rounded-lg border border-orange-200">
                                        <i data-lucide="check-circle" class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <strong class="text-slate-900">Good Moral Certificate</strong>
                                            <p class="text-sm text-slate-600 mt-1">Demonstrates good character and conduct</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto rounded-lg border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-orange-100 to-orange-200 text-slate-900">
                                            <th class="px-4 py-3 text-left font-semibold">Score</th>
                                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                                            <th class="px-4 py-3 text-left font-semibold">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        <tr class="hover:bg-green-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-green-700">10/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">All Approved</td>
                                            <td class="px-4 py-3 text-slate-600">All 3 documents (Birth Cert, Endorsement, Good Moral) approved</td>
                                        </tr>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-slate-700">0/10</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">Not All Approved</td>
                                            <td class="px-4 py-3 text-slate-600">One or more documents missing, pending, or rejected</td>
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
                <section id="contact" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-100 to-blue-50 px-6 sm:px-8 py-5 border-b border-blue-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center">
                                <i data-lucide="mail" class="w-5 h-5 text-blue-900"></i>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Contact Support</h2>
                        </div>
                    </div>
                    <form method="POST" action="#" class="p-6 sm:p-8 space-y-5">
                        @csrf
                        <div>
                            <label class="block text-slate-700 mb-2 font-semibold">Your Email</label>
                            <input type="email" name="email" value="{{ $student->email }}" 
                                class="w-full border-2 border-slate-200 rounded-lg px-4 py-3 bg-slate-50 focus:bg-white focus:border-blue-900 focus:ring-2 focus:ring-blue-900/20 transition-all text-slate-700" 
                                required readonly>
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-2 font-semibold">Subject</label>
                            <input type="text" name="subject" 
                                class="w-full border-2 border-slate-200 rounded-lg px-4 py-3 bg-white focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 transition-all text-slate-700 placeholder-slate-400" 
                                placeholder="What's this about?" required>
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-2 font-semibold">Message</label>
                            <textarea name="message" rows="5" 
                                class="w-full border-2 border-slate-200 rounded-lg px-4 py-3 bg-white focus:border-blue-900 focus:ring-2 focus:ring-blue-900/20 transition-all text-slate-700 placeholder-slate-400 resize-none" 
                                placeholder="Describe your issue or question..." required></textarea>
                        </div>
                        <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-200 to-orange-200 hover:from-blue-300 hover:to-orange-300 text-slate-900 font-bold py-3.5 rounded-lg shadow-md hover:shadow-lg transition-all border-2 border-blue-300">
                            <span class="flex items-center justify-center gap-2">
                                <i data-lucide="send" class="w-5 h-5"></i>
                                Send Message
                            </span>
                        </button>
                    </form>
                </section>

                <!-- Quick Links -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-100 to-blue-50 px-6 sm:px-8 py-5 border-b border-blue-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center">
                                <i data-lucide="link" class="w-5 h-5 text-blue-900"></i>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Quick Links</h2>
                        </div>
                    </div>
                    <div class="p-6 sm:p-8 space-y-3">
                        <a href="{{ route('student.dashboard') }}" 
                            class="flex items-center gap-3 px-5 py-3.5 bg-blue-50 hover:bg-blue-100 text-slate-900 rounded-lg font-semibold transition-all border-2 border-blue-200 hover:border-blue-300">
                            <i data-lucide="layout-dashboard" class="w-5 h-5 text-blue-900"></i>
                            <span>Go to Dashboard</span>
                        </a>
                        <a href="{{ route('student.notifications') }}" 
                            class="flex items-center gap-3 px-5 py-3.5 bg-orange-50 hover:bg-orange-100 text-slate-900 rounded-lg font-semibold transition-all border-2 border-orange-200 hover:border-orange-300">
                            <i data-lucide="bell" class="w-5 h-5 text-orange-600"></i>
                            <span>View Notifications</span>
                        </a>
                        <a href="{{ route('student.profile') }}" 
                            class="flex items-center gap-3 px-5 py-3.5 bg-blue-50 hover:bg-blue-100 text-slate-900 rounded-lg font-semibold transition-all border-2 border-blue-200 hover:border-blue-300">
                            <i data-lucide="user" class="w-5 h-5 text-blue-900"></i>
                            <span>Edit Profile</span>
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
