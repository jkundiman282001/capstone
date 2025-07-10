<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Application - IP Scholar Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fbeee6 0%, #f7caca 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255,255,255,0.7);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.25);
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
        }
        .form-title, .decor-title {
            font-family: 'Montserrat', sans-serif;
            color: #d97706;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
        }
        .submit-btn {
            background: linear-gradient(90deg, #fbbf24 0%, #f87171 100%);
            color: #fff;
            font-weight: 700;
            padding: 0.75rem 2.5rem;
            border-radius: 0.75rem;
            border: none;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(251,191,36,0.12);
            transition: background 0.2s, transform 0.2s;
        }
        .submit-btn:hover {
            background: linear-gradient(90deg, #f59e42 0%, #f43f5e 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .vertical-stepper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 2.5rem;
        }
        .step-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: #fff7ed;
            border: 2.5px solid #fbbf24;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #f59e42;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 8px rgba(251,191,36,0.10);
        }
        /* Only the current step (active) is dark, completed steps are lighter */
        .step-icon.active {
            background: #b45309 !important; /* dark orange */
            color: #fff !important;
            border-color: #92400e !important;
            box-shadow: 0 2px 8px rgba(180,83,9,0.18) !important;
        }
        .step-icon.completed {
            background: #fbbf24;
            color: #fff;
            border-color: #f59e42;
        }
        .step-line {
            width: 4px;
            height: 2.5rem;
            background: #fde68a;
            margin-bottom: 0.5rem;
            border-radius: 2px;
        }
        .decor-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2.5rem;
        }
        .decor-logo {
            width: 5rem;
            height: 5rem;
        }
        .form-label { font-weight: 500; color: #d97706; }
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; margin-bottom: 1.25rem;
            background: rgba(255,255,255,0.85);
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none; border-color: #fbbf24; box-shadow: 0 0 0 2px #fde68a;
        }
        .stepper-mobile { display: none; }
        @media (max-width: 900px) {
            .decor-header { flex-direction: column; }
            .vertical-stepper { flex-direction: row; margin: 0 0 2rem 0; }
            .step-line { width: 2.5rem; height: 4px; margin: 0 0.5rem 0 0.5rem; }
            .step-icon { margin-bottom: 0; margin-right: 0.5rem; }
            .stepper-desktop { display: none; }
            .stepper-mobile { display: flex; }
        }
        .stepper-circle {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: #e5e7eb;
            color: #b45309;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            border: 3px solid #e5e7eb;
            transition: background 0.3s, color 0.3s, border 0.3s;
            z-index: 10;
        }
        .stepper-circle.active {
            background: #b45309;
            color: #fff;
            border-color: #b45309;
        }
        .stepper-circle.completed {
            background: #fbbf24;
            color: #fff;
            border-color: #fbbf24;
        }
        .stepper-label {
            margin-top: 0.5rem;
            font-size: 0.95rem;
            color: #b45309;
            font-weight: 500;
            text-align: center;
        }
        #modern-stepper .absolute.bg-gray-200 {
            z-index: 0;
        }
        @media (max-width: 900px) {
            .stepper-label { font-size: 0.8rem; }
            .stepper-circle { width: 2rem; height: 2rem; font-size: 1rem; }
        }
        .modern-vertical-stepper {
            width: 5rem;
        }
        .vstepper-circle {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: #e5e7eb;
            color: #b45309;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            border: 3px solid #e5e7eb;
            transition: background 0.3s, color 0.3s, border 0.3s;
            z-index: 10;
        }
        .vstepper-circle.active {
            background: #b45309;
            color: #fff;
            border-color: #b45309;
        }
        .vstepper-circle.completed {
            background: #fbbf24 !important;
            color: #fff !important;
            border-color: #fbbf24 !important;
        }
        .vstepper-label {
            margin-top: 0.5rem;
            font-size: 0.95rem;
            color: #b45309;
            font-weight: 500;
            text-align: center;
        }
        .modern-vertical-stepper .absolute.bg-gray-200 {
            z-index: 0;
        }
        @media (max-width: 900px) {
            .vstepper-label { font-size: 0.8rem; }
            .vstepper-circle { width: 2rem; height: 2rem; font-size: 1rem; }
            .modern-vertical-stepper { width: 3.5rem; }
        }
        .section-header {
            font-size: 1.4rem;
            font-weight: 700;
            color: #b45309;
            letter-spacing: 0.5px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="decor-header">
        <img src="/National_Commission_on_Indigenous_Peoples_(NCIP).png" alt="" class="decor-logo">
    </div>
    <div class="flex max-w-5xl mx-auto py-10 px-2">
        <!-- Modern Vertical Stepper -->
        <div class="modern-vertical-stepper mr-8 flex flex-col items-center">
            <div class="relative flex flex-col items-center h-full" style="min-height: 420px;">
                <div class="absolute left-1/2 top-6 bottom-6 w-1 bg-gray-200 z-0" style="transform: translateX(-50%);"></div>
                <div class="flex flex-col items-center z-10 h-full justify-between stepper-steps">
                    <div class="flex flex-col items-center mb-2">
                        <div id="vstepper-1" class="vstepper-circle">1</div>
                        <span class="vstepper-label">Personal Info</span>
                    </div>
                    <div class="flex flex-col items-center mb-2">
                        <div id="vstepper-2" class="vstepper-circle">2</div>
                        <span class="vstepper-label">Address</span>
                    </div>
                    <div class="flex flex-col items-center mb-2">
                        <div id="vstepper-3" class="vstepper-circle">3</div>
                        <span class="vstepper-label">Education</span>
                    </div>
                    <div class="flex flex-col items-center mb-2">
                        <div id="vstepper-4" class="vstepper-circle">4</div>
                        <span class="vstepper-label">Family</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div id="vstepper-5" class="vstepper-circle">5</div>
                        <span class="vstepper-label">School Pref</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full">
            <a href="{{ route('student.dashboard') }}" class="inline-block mb-6 px-5 py-2 rounded-lg border border-yellow-400 text-yellow-700 bg-white hover:bg-yellow-50 font-semibold transition-all">&larr; Back to Dashboard</a>
            <div class="glass-card">
                <div class="form-title text-center">SCHOLARSHIP APPLICATION FORM</div>
                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('student.apply') }}" id="multiStepForm">
                    @csrf
                    <!-- Step 1: Personal Info -->
                    <div class="form-step" id="step1">
                        <div class="section-header mb-6">Personal Info</div>
                        <!-- Progress Steps (already above) -->
                        <!-- Personal Info Section (Merged Design) -->
                        <div class="space-y-6">
                            <!-- Type of Assistance -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Type of Assistance <span class="text-red-500">*</span>
                                </label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="type_of_assistance[]" value="Regular" class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500 required-assist" {{ in_array('Regular', old('type_of_assistance', [])) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">Regular</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="type_of_assistance[]" value="Pamana" class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500 required-assist" {{ in_array('Pamana', old('type_of_assistance', [])) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">Pamana</span>
                                    </label>
                                </div>
                            </div>
                            <!-- First Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" name="first_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 form-input" required value="{{ old('first_name', auth()->user()->first_name ?? '') }}">
                            </div>
                            <!-- Middle Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                <input type="text" name="middle_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 form-input" value="{{ old('middle_name', auth()->user()->middle_name ?? '') }}">
                            </div>
                            <!-- Last Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" name="last_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 form-input" required value="{{ old('last_name', auth()->user()->last_name ?? '') }}">
                            </div>
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 form-input" required value="{{ old('email', auth()->user()->email ?? '') }}">
                            </div>
                            <!-- Contact Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                                <input type="text" name="contact_num" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 form-input" required value="{{ old('contact_num', auth()->user()->contact_num ?? '') }}">
                            </div>
                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                <input type="date" name="birthdate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 form-input" required value="{{ old('birthdate') }}">
                            </div>
                            <!-- Place of Birth -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Place of Birth</label>
                                <input type="text" name="birthplace" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 form-input" required value="{{ old('birthplace') }}">
                            </div>
                            <!-- Gender -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 form-select" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <!-- Civil Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Civil Status</label>
                                <select name="civil_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 form-select" required>
                                    <option value="">Select Civil Status</option>
                                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                </select>
                            </div>
                            <!-- Ethnolinguistic Group -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ethnolinguistic Group</label>
                                <select name="ethno_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 form-select" required>
                                    <option value="">Select Ethnolinguistic Group</option>
                                    @foreach($ethnicities as $ethno)
                                        <option value="{{ $ethno->id }}" {{ old('ethno_id') == $ethno->id ? 'selected' : '' }}>{{ $ethno->ethnicity }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Step 2: Address/Origin Info -->
                    <div class="form-step hidden" id="step2">
                        <div class="section-header mb-6">Address</div>
                        <div class="mb-6">
                            <div class="form-title text-lg mb-2">Mailing Address</div>
                            <label class="form-label">Province</label>
                            <select name="mailing_province" class="form-select" required>
                                <option value="">Select Province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province }}" {{ old('mailing_province') == $province ? 'selected' : '' }}>{{ $province }}</option>
                                @endforeach
                            </select>
                            <label class="form-label">Municipality</label>
                            <select name="mailing_municipality" class="form-select" required>
                                <option value="">Select Municipality</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality }}" {{ old('mailing_municipality') == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                                @endforeach
                            </select>
                            <label class="form-label">Barangay</label>
                            <select name="mailing_barangay" class="form-select" required>
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ old('mailing_barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                @endforeach
                            </select>
                            <label class="form-label">House No. and Street/Sitio</label>
                            <input type="text" name="mailing_house_num" class="form-input" value="{{ old('mailing_house_num') }}">
                        </div>
                        <div class="mb-6">
                            <div class="form-title text-lg mb-2">Permanent Address</div>
                            <label class="form-label">Province</label>
                            <select name="permanent_province" class="form-select" required>
                                <option value="">Select Province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province }}" {{ old('permanent_province') == $province ? 'selected' : '' }}>{{ $province }}</option>
                                @endforeach
                            </select>
                            <label class="form-label">Municipality</label>
                            <select name="permanent_municipality" class="form-select" required>
                                <option value="">Select Municipality</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality }}" {{ old('permanent_municipality') == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                                @endforeach
                            </select>
                            <label class="form-label">Barangay</label>
                            <select name="permanent_barangay" class="form-select" required>
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ old('permanent_barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                @endforeach
                            </select>
                            <label class="form-label">House No. and Street/Sitio</label>
                            <input type="text" name="permanent_house_num" class="form-input" value="{{ old('permanent_house_num') }}">
                        </div>
                        <div class="mb-6">
                            <div class="form-title text-lg mb-2">Place of Origin</div>
                            <label class="form-label">Province</label>
                            <select name="origin_province" class="form-select" required>
                                <option value="">Select Province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province }}" {{ old('origin_province') == $province ? 'selected' : '' }}>{{ $province }}</option>
                                @endforeach
                            </select>
                            <label class="form-label">Municipality</label>
                            <select name="origin_municipality" class="form-select" required>
                                <option value="">Select Municipality</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality }}" {{ old('origin_municipality') == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                                @endforeach
                            </select>
                            <label class="form-label">Barangay</label>
                            <select name="origin_barangay" class="form-select" required>
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ old('origin_barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                @endforeach
                            </select>
                            <label class="form-label">House No. and Street/Sitio</label>
                            <input type="text" name="origin_house_num" class="form-input" value="{{ old('origin_house_num') }}">
                        </div>
                    </div>
                    <!-- Step 3: Educational Background Info -->
                    <div class="form-step hidden" id="step3">
                        <div class="section-header mb-6">Education</div>
                        <h2 class="text-xl font-semibold text-orange-500 mb-6">Educational Attainment</h2>
                        <div class="space-y-8">
                            <!-- Elementary -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Elementary</label>
                                <input type="text" name="elem_school" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" placeholder="School Name" value="{{ old('elem_school') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Type</label>
                                <select name="elem_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Select Type</option>
                                    <option value="Public" {{ old('elem_type') == 'Public' ? 'selected' : '' }}>Public</option>
                                    <option value="Private" {{ old('elem_type') == 'Private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            <div class="flex space-x-4">
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Year Graduate</label>
                                    <input type="text" name="elem_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('elem_year') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade Average</label>
                                    <input type="text" name="elem_avg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('elem_avg') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rank</label>
                                    <input type="text" name="elem_rank" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('elem_rank') }}">
                                </div>
                            </div>
                            <!-- High School -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">High School</label>
                                <input type="text" name="hs_school" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" placeholder="School Name" value="{{ old('hs_school') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Type</label>
                                <select name="hs_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Select Type</option>
                                    <option value="Public" {{ old('hs_type') == 'Public' ? 'selected' : '' }}>Public</option>
                                    <option value="Private" {{ old('hs_type') == 'Private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            <div class="flex space-x-4">
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Year Graduate</label>
                                    <input type="text" name="hs_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('hs_year') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade Average</label>
                                    <input type="text" name="hs_avg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('hs_avg') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rank</label>
                                    <input type="text" name="hs_rank" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('hs_rank') }}">
                                </div>
                            </div>
                            <!-- Vocational -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vocational</label>
                                <input type="text" name="voc_school" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" placeholder="School Name" value="{{ old('voc_school') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Type</label>
                                <select name="voc_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Select Type</option>
                                    <option value="Public" {{ old('voc_type') == 'Public' ? 'selected' : '' }}>Public</option>
                                    <option value="Private" {{ old('voc_type') == 'Private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            <div class="flex space-x-4">
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Year Graduate</label>
                                    <input type="text" name="voc_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('voc_year') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade Average</label>
                                    <input type="text" name="voc_avg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('voc_avg') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rank</label>
                                    <input type="text" name="voc_rank" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('voc_rank') }}">
                                </div>
                            </div>
                            <!-- College/Undergraduate -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">College/Undergraduate</label>
                                <input type="text" name="college_school" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" placeholder="School Name" value="{{ old('college_school') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Type</label>
                                <select name="college_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Select Type</option>
                                    <option value="Public" {{ old('college_type') == 'Public' ? 'selected' : '' }}>Public</option>
                                    <option value="Private" {{ old('college_type') == 'Private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            <div class="flex space-x-4">
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Year Graduate</label>
                                    <input type="text" name="college_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('college_year') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade Average</label>
                                    <input type="text" name="college_avg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('college_avg') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rank</label>
                                    <input type="text" name="college_rank" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('college_rank') }}">
                                </div>
                            </div>
                            <!-- Masteral -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Masteral</label>
                                <input type="text" name="masteral_school" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" placeholder="School Name" value="{{ old('masteral_school') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Type</label>
                                <select name="masteral_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Select Type</option>
                                    <option value="Public" {{ old('masteral_type') == 'Public' ? 'selected' : '' }}>Public</option>
                                    <option value="Private" {{ old('masteral_type') == 'Private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            <div class="flex space-x-4">
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Year Graduate</label>
                                    <input type="text" name="masteral_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('masteral_year') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade Average</label>
                                    <input type="text" name="masteral_avg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('masteral_avg') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rank</label>
                                    <input type="text" name="masteral_rank" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('masteral_rank') }}">
                                </div>
                            </div>
                            <!-- Doctorate -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Doctorate</label>
                                <input type="text" name="doctorate_school" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" placeholder="School Name" value="{{ old('doctorate_school') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Type</label>
                                <select name="doctorate_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Select Type</option>
                                    <option value="Public" {{ old('doctorate_type') == 'Public' ? 'selected' : '' }}>Public</option>
                                    <option value="Private" {{ old('doctorate_type') == 'Private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            <div class="flex space-x-4">
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Year Graduate</label>
                                    <input type="text" name="doctorate_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('doctorate_year') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade Average</label>
                                    <input type="text" name="doctorate_avg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('doctorate_avg') }}">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rank</label>
                                    <input type="text" name="doctorate_rank" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('doctorate_rank') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Step 4: Family Background Info -->
                    <div class="form-step hidden" id="step4">
                        <div class="section-header mb-6">Family</div>
                        <!-- Father's Info -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">FATHER'S STATUS <span class="text-red-500">*</span></label>
                            <div class="flex space-x-4 mb-2">
                                <label class="flex items-center">
                                    <input type="radio" name="father_status" value="Living" class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500" {{ old('father_status', isset($family_father) ? $family_father->status : 'Living') == 'Living' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Living</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="father_status" value="Deceased" class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500" {{ old('father_status', isset($family_father) ? $family_father->status : '') == 'Deceased' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Deceased</span>
                                </label>
                            </div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="father_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2" value="{{ old('father_name', isset($family_father) ? $family_father->name : '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" name="father_address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2" value="{{ old('father_address', isset($family_father) ? $family_father->address : '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                            <input type="text" name="father_occupation" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2" value="{{ old('father_occupation', isset($family_father) ? $family_father->occupation : '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Office Address</label>
                            <input type="text" name="father_office_address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2" value="{{ old('father_office_address', isset($family_father) ? $family_father->office_address : '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Educational Attainment</label>
                            <input type="text" name="father_education" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2" value="{{ old('father_education', isset($family_father) ? $family_father->educational_attainment : '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ethnolinguistic Group</label>
                            <select name="father_ethno" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2">
                                <option value="">Select Ethnolinguistic Group</option>
                                @foreach($ethnicities as $ethno)
                                    <option value="{{ $ethno->id }}" {{ old('father_ethno', isset($family_father) ? $family_father->ethno_id : '') == $ethno->id ? 'selected' : '' }}>{{ $ethno->ethnicity }}</option>
                                @endforeach
                            </select>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Parent's Annual Income: (Year of ITR Attached)</label>
                            <input type="text" name="father_income" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('father_income', isset($family_father) ? $family_father->income : '') }}">
                        </div>
                        <!-- Mother's Info -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">MOTHER'S STATUS <span class="text-red-500">*</span></label>
                            <div class="flex space-x-4 mb-2">
                                <label class="flex items-center">
                                    <input type="radio" name="mother_status" value="Living" class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500" {{ old('mother_status', isset($family_mother) ? $family_mother->status : 'Living') == 'Living' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Living</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="mother_status" value="Deceased" class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500" {{ old('mother_status', isset($family_mother) ? $family_mother->status : '') == 'Deceased' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Deceased</span>
                                </label>
                            </div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="mother_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2" value="{{ old('mother_name', isset($family_mother) ? $family_mother->name : '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" name="mother_address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2" value="{{ old('mother_address', isset($family_mother) ? $family_mother->address : '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                            <input type="text" name="mother_occupation" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2" value="{{ old('mother_occupation', isset($family_mother) ? $family_mother->occupation : '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Office Address</label>
                            <input type="text" name="mother_office_address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2" value="{{ old('mother_office_address', isset($family_mother) ? $family_mother->office_address : '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Educational Attainment</label>
                            <input type="text" name="mother_education" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2" value="{{ old('mother_education', isset($family_mother) ? $family_mother->educational_attainment : '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ethnolinguistic Group</label>
                            <select name="mother_ethno" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 mb-2">
                                <option value="">Select Ethnolinguistic Group</option>
                                @foreach($ethnicities as $ethno)
                                    <option value="{{ $ethno->id }}" {{ old('mother_ethno', isset($family_mother) ? $family_mother->ethno_id : '') == $ethno->id ? 'selected' : '' }}>{{ $ethno->ethnicity }}</option>
                                @endforeach
                            </select>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Parent's Annual Income: (Year of ITR Attached)</label>
                            <input type="text" name="mother_income" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('mother_income', isset($family_mother) ? $family_mother->income : '') }}">
                        </div>
                        <!-- Siblings Section -->
                        <label class="block text-sm font-medium text-gray-700 mb-2">BROTHER/SISTER IN THE FAMILY (from eldest to youngest): <span class="text-red-500">*</span></label>
                        <div id="siblings-container" class="space-y-6">
                            @if(isset($siblings) && count($siblings) > 0)
                                @foreach($siblings as $sibling)
                                    <div class="sibling-entry border border-orange-200 p-4 rounded relative">
                                        <button type="button" onclick="removeSibling(this)" class="absolute top-2 right-2 text-sm text-red-500 hover:underline">Delete</button>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                            <input type="text" name="sibling_name[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('sibling_name.' . $loop->index, $sibling->name) }}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                                            <input type="text" name="sibling_age[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('sibling_age.' . $loop->index, $sibling->age) }}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Scholarship (if any)</label>
                                            <input type="text" name="sibling_scholarship[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('sibling_scholarship.' . $loop->index, $sibling->scholarship) }}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Course and/or Year Level</label>
                                            <input type="text" name="sibling_course[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('sibling_course.' . $loop->index, $sibling->course_year) }}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Present Status</label>
                                            <input type="text" name="sibling_status[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" value="{{ old('sibling_status.' . $loop->index, $sibling->present_status) }}">
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="sibling-entry border border-orange-200 p-4 rounded relative">
                                    <button type="button" onclick="removeSibling(this)" class="absolute top-2 right-2 text-sm text-red-500 hover:underline">Delete</button>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                        <input type="text" name="sibling_name[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                                        <input type="text" name="sibling_age[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Scholarship (if any)</label>
                                        <input type="text" name="sibling_scholarship[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Course and/or Year Level</label>
                                        <input type="text" name="sibling_course[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Present Status</label>
                                        <input type="text" name="sibling_status[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="pt-2">
                            <button type="button" onclick="addSibling()" class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-2 rounded hover:bg-orange-200 text-sm font-medium">+ Add Sibling</button>
                        </div>
                        <script>
                            function addSibling() {
                                const container = document.getElementById('siblings-container');
                                const entry = container.querySelector('.sibling-entry');
                                const clone = entry.cloneNode(true);
                                clone.querySelectorAll('input').forEach(input => input.value = '');
                                container.appendChild(clone);
                            }
                            function removeSibling(button) {
                                const entry = button.closest('.sibling-entry');
                                const container = document.getElementById('siblings-container');
                                if (container.children.length > 1) {
                                    entry.remove();
                                } else {
                                    alert('You must have at least one sibling entry.');
                                }
                            }
                        </script>
                    </div>
                    <!-- Step 5: Intended School Preference Info -->
                    <div class="form-step hidden" id="step5">
                        <div class="section-header mb-6">School Pref</div>
                        <div class="mb-4 font-semibold text-orange-700">INDICATE INTENDED SCHOOL AND COURSE BY PREFERENCE: <span class="text-red-500">*</span></div>
                        <!-- School's First Preference -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">A. School's First Preference: <span class="text-red-500">*</span></label>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" name="school1_address" class="form-input" value="{{ old('school1_address', $school_pref->address ?? '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Degree Program/Course</label>
                            <input type="text" name="school1_course1" class="form-input mb-2" value="{{ old('school1_course1', $school_pref->degree ?? '') }}">
                            <div class="flex space-x-4 mt-2">
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">School Type</label>
                                    <select name="school1_type" class="form-select">
                                        <option value="">Select Type</option>
                                        <option value="Public" {{ old('school1_type', $school_pref->school_type ?? '') == 'Public' ? 'selected' : '' }}>Public</option>
                                        <option value="Private" {{ old('school1_type', $school_pref->school_type ?? '') == 'Private' ? 'selected' : '' }}>Private</option>
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No. of Years</label>
                                    <input type="text" name="school1_years" class="form-input" value="{{ old('school1_years', $school_pref->num_years ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <!-- School's Second Preference -->
                        <div class="mb-4 mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">B. School's Second Preference: <span class="text-red-500">*</span></label>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" name="school2_address" class="form-input" value="{{ old('school2_address', $school_pref->address2 ?? '') }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Degree Program/Course</label>
                            <input type="text" name="school2_course1" class="form-input mb-2" value="{{ old('school2_course1', $school_pref->degree2 ?? '') }}">
                            <div class="flex space-x-4 mt-2">
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">School Type</label>
                                    <select name="school2_type" class="form-select">
                                        <option value="">Select Type</option>
                                        <option value="Public" {{ old('school2_type', $school_pref->school_type2 ?? '') == 'Public' ? 'selected' : '' }}>Public</option>
                                        <option value="Private" {{ old('school2_type', $school_pref->school_type2 ?? '') == 'Private' ? 'selected' : '' }}>Private</option>
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No. of Years</label>
                                    <input type="text" name="school2_years" class="form-input" value="{{ old('school2_years', $school_pref->num_years2 ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <!-- Brief Statement Questions -->
                        <div class="mb-4 mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">In brief statement answer the following: <span class="text-red-500">*</span></label>
                            <label class="block text-sm font-medium text-gray-700 mb-2">What possible contribution/s that you may extend to your community ICCs/IPs while studying?</label>
                            <textarea name="contribution" class="form-textarea" rows="4">{{ old('contribution', $school_pref->ques_answer1 ?? '') }}</textarea>
                            <label class="block text-sm font-medium text-gray-700 mb-2 mt-4">What are your plans after graduation?</label>
                            <textarea name="plans_after_grad" class="form-textarea" rows="4">{{ old('plans_after_grad', $school_pref->ques_answer2 ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-between mt-6">
                        <button type="button" class="submit-btn" id="prevBtn">Back</button>
                        <button type="button" class="submit-btn" id="nextBtn">Next</button>
                        <button type="submit" class="submit-btn hidden" id="submitBtn" onclick="return confirm('Are you sure you want to submit your application?')">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        const steps = [1,2,3,4,5];
        let currentStep = 1;
        const showStep = (step) => {
            steps.forEach(s => {
                document.getElementById('step'+s).classList.add('hidden');
            });
            document.getElementById('step'+step).classList.remove('hidden');
            document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-block';
            document.getElementById('nextBtn').style.display = step === 5 ? 'none' : 'inline-block';
            document.getElementById('submitBtn').classList.toggle('hidden', step !== 5);
            updateStepper(step);
        };

        // Restrict barangay options based on selected municipality
        function setupBarangayDropdown(muniSelector, brgySelector) {
            const muni = document.querySelector(muniSelector);
            const brgy = document.querySelector(brgySelector);
            if (!muni || !brgy) return;
            muni.addEventListener('change', function() {
                const municipality = this.value;
                brgy.innerHTML = '<option value="">Loading...</option>';
                fetch(`/address/barangays?municipality=${encodeURIComponent(municipality)}`)
                    .then(res => res.json())
                    .then(data => {
                        let options = '<option value="">Select Barangay</option>';
                        data.forEach(b => {
                            options += `<option value="${b}">${b}</option>`;
                        });
                        brgy.innerHTML = options;
                    });
            });
        }
        setupBarangayDropdown('[name=mailing_municipality]', '[name=mailing_barangay]');
        setupBarangayDropdown('[name=permanent_municipality]', '[name=permanent_barangay]');
        setupBarangayDropdown('[name=origin_municipality]', '[name=origin_barangay]');

        // Validation for required fields in the current step
        function validateStep(step) {
            const stepDiv = document.getElementById('step'+step);
            let valid = true;
            // Remove previous highlight
            stepDiv.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(input => {
                input.classList.remove('border-red-500');
            });
            // Check required fields
            stepDiv.querySelectorAll('[required]').forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    valid = false;
                }
            });
            // Special validation for type_of_assistance checkboxes in step 1
            if(step === 1) {
                const checkboxes = stepDiv.querySelectorAll('.required-assist');
                let oneChecked = false;
                checkboxes.forEach(cb => {
                    cb.classList.remove('ring-2','ring-red-500');
                    if(cb.checked) oneChecked = true;
                });
                if(!oneChecked) {
                    checkboxes.forEach(cb => cb.classList.add('ring-2','ring-red-500'));
                    valid = false;
                }
            }
            if (!valid) {
                alert('Please fill in all required fields before proceeding.');
            }
            return valid;
        }

        document.getElementById('prevBtn').onclick = () => { if(currentStep > 1) { currentStep--; showStep(currentStep); } };
        document.getElementById('nextBtn').onclick = () => {
            if(validateStep(currentStep)) {
                if(currentStep < 5) { currentStep++; showStep(currentStep); }
            }
        };

        // Restrict type_of_assistance checkboxes to only one checked at a time
        document.querySelectorAll('.required-assist').forEach(cb => {
            cb.addEventListener('change', function() {
                if(this.checked) {
                    document.querySelectorAll('.required-assist').forEach(other => {
                        if(other !== this) other.checked = false;
                    });
                }
            });
        });

        // Update progress stepper UI
        const updateStepper = (step) => {
            for (let i = 1; i <= 5; i++) {
                const circle = document.getElementById('stepper-' + i);
                const bar = document.getElementById('bar-' + (i));
                if (circle) {
                    if (i < step) {
                        circle.classList.add('bg-orange-500', 'text-white', 'border-orange-500');
                        circle.classList.remove('bg-white', 'text-orange-500', 'border-orange-400');
                    } else if (i === step) {
                        circle.classList.add('bg-orange-500', 'text-white', 'border-orange-500');
                        circle.classList.remove('bg-white', 'text-orange-500', 'border-orange-400');
                    } else {
                        circle.classList.remove('bg-orange-500', 'text-white', 'border-orange-500');
                        circle.classList.add('bg-white', 'text-orange-500', 'border-orange-400');
                    }
                }
                if (bar) {
                    if (i < step) {
                        bar.classList.add('bg-orange-400');
                        bar.classList.remove('bg-orange-200');
                    } else {
                        bar.classList.remove('bg-orange-400');
                        bar.classList.add('bg-orange-200');
                    }
                }
            }
        };

        // Modern vertical stepper logic
        const updateModernVerticalStepper = (step) => {
            for (let i = 1; i <= 5; i++) {
                const circle = document.getElementById('vstepper-' + i);
                if (circle) {
                    // Remove all Tailwind background, text, and border classes that could override our style
                    circle.classList.remove(
                        'bg-white', 'bg-orange-500', 'bg-orange-400', 'bg-orange-200',
                        'text-orange-500', 'text-white', 'border-orange-500', 'border-orange-400'
                    );
                    circle.classList.remove('active', 'completed');
                    if (i < step) {
                        circle.classList.add('completed');
                    } else if (i === step) {
                        circle.classList.add('active');
                    }
                }
            }
        };
        // Patch showStep to update modern vertical stepper
        const origShowStepVertical = showStep;
        showStep = (step) => {
            origShowStepVertical(step);
            updateModernVerticalStepper(step);
        };
        // Initial call
        updateModernVerticalStepper(currentStep);
    </script>
</body>
</html> 