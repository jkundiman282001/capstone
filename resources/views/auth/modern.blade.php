<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NCIP-EAP | Login & Signup</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
    }

    /* Hero Image Section */
    .hero-image-container {
      position: relative;
      height: 100vh;
      overflow: hidden;
    }

    .hero-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      animation: zoomIn 20s ease-in-out infinite alternate;
    }

    @keyframes zoomIn {
      0% { transform: scale(1); }
      100% { transform: scale(1.05); }
    }

    /* Gradient Overlay */
    .hero-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(
        135deg,
        rgba(249, 115, 22, 0.85) 0%,
        rgba(220, 38, 38, 0.75) 50%,
        rgba(101, 67, 33, 0.80) 100%
      );
      z-index: 1;
    }

    /* Animated Pattern Overlay */
    .pattern-overlay {
      position: absolute;
      inset: 0;
      background-image: 
        radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(251, 191, 36, 0.15) 0%, transparent 50%);
      animation: patternMove 15s ease-in-out infinite;
      z-index: 2;
    }

    @keyframes patternMove {
      0%, 100% { transform: translate(0, 0) rotate(0deg); }
      50% { transform: translate(-20px, -20px) rotate(5deg); }
    }

    /* Content Overlay */
    .content-overlay {
      position: absolute;
      inset: 0;
      z-index: 3;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding: 2rem;
    }

    /* Glassmorphism Form Card */
    .glass-card {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(30px);
      -webkit-backdrop-filter: blur(30px);
      border: 1px solid rgba(255, 255, 255, 0.5);
      box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    }

    /* Logo Animation */
    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-10px) rotate(2deg); }
    }

    .float-animation {
      animation: float 4s ease-in-out infinite;
    }

    /* Form Input Styling */
    .form-input {
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.95);
      border: 2px solid rgba(229, 231, 235, 0.8);
    }

    .form-input:focus {
      background: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(249, 115, 22, 0.25);
      border-color: #f97316;
    }

    /* Button Styling */
    .btn-primary {
      background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .btn-primary::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.4);
      transform: translate(-50%, -50%);
      transition: width 0.8s, height 0.8s;
    }

    .btn-primary:hover::before {
      width: 400px;
      height: 400px;
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(249, 115, 22, 0.5);
    }

    .btn-primary:active {
      transform: translateY(-1px);
    }

    /* Checkbox Styling */
    .checkbox-orange {
      accent-color: #f97316;
      width: 18px;
      height: 18px;
      cursor: pointer;
    }

    /* Slide Animation */
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
      animation: slideInRight 0.6s ease-out;
    }

    /* Text on Image */
    .hero-text {
      position: absolute;
      left: 5%;
      top: 50%;
      transform: translateY(-50%);
      z-index: 3;
      color: white;
      max-width: 500px;
    }

    .hero-text h1 {
      text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
      animation: fadeInUp 1s ease-out;
    }

    .hero-text p {
      text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
      animation: fadeInUp 1.2s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Decorative Elements */
    .decorative-shape {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      z-index: 2;
    }

    .shape-1 {
      width: 400px;
      height: 400px;
      top: -200px;
      left: -200px;
      animation: float 8s ease-in-out infinite;
    }

    .shape-2 {
      width: 300px;
      height: 300px;
      bottom: -150px;
      right: 20%;
      animation: float 10s ease-in-out infinite reverse;
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
      .hero-text {
        display: none;
      }
      .content-overlay {
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <!-- Hero Image Section -->
  <div class="hero-image-container">
    <!-- Background Image -->
    <img 
      src="{{ asset('images/Dashboard.png') }}" 
      alt="NCIP Scholarship" 
      class="hero-image"
    />
    
    <!-- Gradient Overlay -->
    <div class="hero-overlay"></div>
    
    <!-- Pattern Overlay -->
    <div class="pattern-overlay"></div>
    
    <!-- Decorative Shapes -->
    <div class="decorative-shape shape-1"></div>
    <div class="decorative-shape shape-2"></div>

    <!-- Hero Text (Left Side) -->
    <div class="hero-text hidden lg:block">
      <div class="mb-6 float-animation">
        <img 
          src="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" 
          alt="NCIP Logo" 
          class="h-28 w-28 mb-6 drop-shadow-2xl"
        />
      </div>
      <h1 class="text-6xl font-black mb-4 leading-tight">
        Empowering<br/>
        <span class="text-amber-300">Indigenous</span><br/>
        Futures
      </h1>
      <p class="text-xl font-semibold text-white/90 leading-relaxed">
        Join the NCIP Educational Assistance Program and unlock your potential through quality education.
      </p>
    </div>

    <!-- Form Card (Right Side) -->
    <div class="content-overlay">
      <div class="glass-card rounded-3xl p-8 md:p-10 w-full max-w-md slide-in-right">
        <!-- Logo Section (Mobile) -->
        <div class="text-center mb-8 lg:hidden">
          <img 
            src="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" 
            alt="NCIP Logo" 
            class="h-20 w-20 mx-auto mb-4 drop-shadow-lg float-animation"
          />
          <h1 class="text-2xl font-black text-slate-900 mb-1">NCIP-EAP</h1>
          <p class="text-sm text-slate-600 font-medium">Scholarship Management Portal</p>
        </div>

        <!-- Login Form -->
        <div id="loginForm" class="space-y-6">
          <div class="text-center">
            <h2 class="text-3xl font-black text-slate-900 mb-2">Welcome to NCIP-EAP</h2>
            <p class="text-sm text-slate-600">
              Don't have an account? 
              <button onclick="toggleForm()" class="text-orange-600 font-bold hover:text-orange-700 transition-colors">
                Sign up here
              </button>
            </p>
          </div>

          @if (session('success'))
            <div class="p-4 bg-gradient-to-r from-emerald-50 to-green-50 border-l-4 border-emerald-500 rounded-xl">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-semibold text-emerald-700">{{ session('success') }}</p>
              </div>
            </div>
          @endif

          @if ($errors->login->any())
            <div class="p-4 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl">
              <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <ul class="text-sm text-red-700 space-y-1">
                  @foreach ($errors->login->all() as $error)
                    <li class="font-medium">{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif

          <form method="POST" action="{{ url('/login') }}" class="space-y-5">
            @csrf
            
            <!-- Email Input -->
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                </div>
                <input 
                  type="email" 
                  name="email" 
                  id="login_email" 
                  required 
                  autofocus 
                  placeholder="Enter your email" 
                  class="form-input w-full pl-12 pr-4 py-3.5 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
              </div>
            </div>

            <!-- Password Input -->
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input 
                  type="password" 
                  name="password" 
                  id="login_password" 
                  required 
                  placeholder="Enter your password" 
                  class="form-input w-full pl-12 pr-12 py-3.5 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
                <button 
                  type="button" 
                  onclick="togglePassword('login_password', this)" 
                  tabindex="-1" 
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-orange-600 transition-colors"
                >
                  <svg id="login_password_icon" xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' />
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
              <label class="flex items-center gap-2 cursor-pointer group">
                <input type="checkbox" name="remember" value="1" class="checkbox-orange">
                <span class="text-sm font-medium text-slate-700 group-hover:text-orange-600 transition-colors">Remember me</span>
              </label>
              <a href="#" class="text-sm font-bold text-orange-600 hover:text-orange-700 transition-colors">Forgot password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary w-full text-white py-4 rounded-xl font-bold text-lg shadow-lg relative z-10">
              Sign In
            </button>
          </form>
        </div>

        <!-- Signup Form -->
        <div id="signupForm" class="space-y-6 hidden">
          <div class="text-center">
            <h2 class="text-3xl font-black text-slate-900 mb-2">Create Account</h2>
            <p class="text-sm text-slate-600">
              Already have an account? 
              <button onclick="toggleForm()" class="text-orange-600 font-bold hover:text-orange-700 transition-colors">
                Sign in here
              </button>
            </p>
          </div>

          @if ($errors->register->any())
            <div class="p-4 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl">
              <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <ul class="text-sm text-red-700 space-y-1">
                  @foreach ($errors->register->all() as $error)
                    <li class="font-medium">{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif

          <form method="POST" action="{{ url('/register') }}" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
            @csrf
            
            <!-- Name Fields -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">First Name</label>
                <input 
                  type="text" 
                  name="first_name" 
                  id="register_first_name" 
                  required 
                  placeholder="First Name" 
                  class="form-input w-full px-4 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Last Name</label>
                <input 
                  type="text" 
                  name="last_name" 
                  id="register_last_name" 
                  required 
                  placeholder="Last Name" 
                  class="form-input w-full px-4 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
              </div>
            </div>

            <!-- Middle Name -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Middle Name <span class="text-slate-400 normal-case">(optional)</span></label>
              <input 
                type="text" 
                name="middle_name" 
                id="register_middle_name" 
                placeholder="Middle Name" 
                class="form-input w-full px-4 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
              />
            </div>

            <!-- Contact Number -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Contact Number</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 12.284 3 6V5z" />
                  </svg>
                </div>
                <input 
                  type="text" 
                  name="contact_num" 
                  id="register_contact_num" 
                  required 
                  placeholder="Contact Number" 
                  class="form-input w-full pl-12 pr-4 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
              </div>
            </div>

            <!-- Ethnicity -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Ethnolinguistic Group</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <select 
                  name="ethno_id" 
                  id="register_ethno_id" 
                  required 
                  class="form-input w-full pl-12 pr-10 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none appearance-none bg-white cursor-pointer"
                >
                  <option value="">Select Ethnolinguistic Group</option>
                  @foreach($ethnicities as $ethno)
                    <option value="{{ $ethno->id }}">{{ $ethno->ethnicity }}</option>
                  @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Course -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Course</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </div>
                <select 
                  name="course" 
                  id="register_course" 
                  class="form-input w-full pl-12 pr-10 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none appearance-none bg-white cursor-pointer"
                >
                  <option value="">Select your course</option>
                  <option value="Undecided">Undecided / Grade 12 exploring options</option>
                  <!-- Priority Courses -->
                  <option value="Agriculture">Agriculture</option>
                  <option value="Aqua-Culture and Fisheries">Aqua-Culture and Fisheries</option>
                  <option value="Anthropology">Anthropology</option>
                  <option value="Business Administration (Accounting, Marketing, Management, Economics, Entrepreneurship)">Business Administration (Accounting, Marketing, Management, Economics, Entrepreneurship)</option>
                  <option value="Civil Engineering">Civil Engineering</option>
                  <option value="Community Development">Community Development</option>
                  <option value="Criminology">Criminology</option>
                  <option value="Education">Education</option>
                  <option value="Foreign Service">Foreign Service</option>
                  <option value="Forestry and Environment Studies (Forestry, Environmental Science, Agro-Forestry)">Forestry and Environment Studies (Forestry, Environmental Science, Agro-Forestry)</option>
                  <option value="Geodetic Engineering">Geodetic Engineering</option>
                  <option value="Geology">Geology</option>
                  <option value="Law">Law</option>
                  <option value="Medicine and Allied Health Sciences (Nursing, Midwifery, Medical Technology, etc.)">Medicine and Allied Health Sciences (Nursing, Midwifery, Medical Technology, etc.)</option>
                  <option value="Mechanical Engineering">Mechanical Engineering</option>
                  <option value="Mining Engineering">Mining Engineering</option>
                  <option value="Social Sciences (AB courses)">Social Sciences (AB courses)</option>
                  <option value="Social Work">Social Work</option>
                  <!-- Related/Relevant Courses (Mid-scale) -->
                  <option value="Agricultural Engineering">Agricultural Engineering</option>
                  <option value="Agribusiness">Agribusiness</option>
                  <option value="Agricultural Economics">Agricultural Economics</option>
                  <option value="Animal Science">Animal Science</option>
                  <option value="Crop Science">Crop Science</option>
                  <option value="Agricultural Technology">Agricultural Technology</option>
                  <option value="Marine Biology">Marine Biology</option>
                  <option value="Fisheries">Fisheries</option>
                  <option value="Aquaculture">Aquaculture</option>
                  <option value="Marine Science">Marine Science</option>
                  <option value="Oceanography">Oceanography</option>
                  <option value="Sociology">Sociology</option>
                  <option value="Cultural Studies">Cultural Studies</option>
                  <option value="Ethnic Studies">Ethnic Studies</option>
                  <option value="Archaeology">Archaeology</option>
                  <option value="Business Management">Business Management</option>
                  <option value="Marketing">Marketing</option>
                  <option value="Economics">Economics</option>
                  <option value="Entrepreneurship">Entrepreneurship</option>
                  <option value="Finance">Finance</option>
                  <option value="Human Resource Management">Human Resource Management</option>
                  <option value="Operations Management">Operations Management</option>
                  <option value="Structural Engineering">Structural Engineering</option>
                  <option value="Environmental Engineering">Environmental Engineering</option>
                  <option value="Construction Engineering">Construction Engineering</option>
                  <option value="Transportation Engineering">Transportation Engineering</option>
                  <option value="Rural Development">Rural Development</option>
                  <option value="Urban Planning">Urban Planning</option>
                  <option value="Public Administration">Public Administration</option>
                  <option value="Development Studies">Development Studies</option>
                  <option value="Criminal Justice">Criminal Justice</option>
                  <option value="Forensic Science">Forensic Science</option>
                  <option value="Law Enforcement">Law Enforcement</option>
                  <option value="Security Management">Security Management</option>
                  <option value="Elementary Education">Elementary Education</option>
                  <option value="Secondary Education">Secondary Education</option>
                  <option value="Special Education">Special Education</option>
                  <option value="Educational Administration">Educational Administration</option>
                  <option value="Curriculum Development">Curriculum Development</option>
                  <option value="International Relations">International Relations</option>
                  <option value="Diplomatic Studies">Diplomatic Studies</option>
                  <option value="International Studies">International Studies</option>
                  <option value="Environmental Science">Environmental Science</option>
                  <option value="Ecology">Ecology</option>
                  <option value="Conservation">Conservation</option>
                  <option value="Natural Resource Management">Natural Resource Management</option>
                  <option value="Environmental Management">Environmental Management</option>
                  <option value="Surveying">Surveying</option>
                  <option value="Geomatics">Geomatics</option>
                  <option value="Land Surveying">Land Surveying</option>
                  <option value="Geographic Information Systems">Geographic Information Systems</option>
                  <option value="Geological Engineering">Geological Engineering</option>
                  <option value="Geophysics">Geophysics</option>
                  <option value="Earth Science">Earth Science</option>
                  <option value="Legal Studies">Legal Studies</option>
                  <option value="Jurisprudence">Jurisprudence</option>
                  <option value="Constitutional Law">Constitutional Law</option>
                  <option value="Public Health">Public Health</option>
                  <option value="Health Sciences">Health Sciences</option>
                  <option value="Medical Laboratory Science">Medical Laboratory Science</option>
                  <option value="Radiologic Technology">Radiologic Technology</option>
                  <option value="Physical Therapy">Physical Therapy</option>
                  <option value="Occupational Therapy">Occupational Therapy</option>
                  <option value="Pharmacy">Pharmacy</option>
                  <option value="Industrial Engineering">Industrial Engineering</option>
                  <option value="Manufacturing Engineering">Manufacturing Engineering</option>
                  <option value="Automotive Engineering">Automotive Engineering</option>
                  <option value="Aerospace Engineering">Aerospace Engineering</option>
                  <option value="Mineral Processing">Mineral Processing</option>
                  <option value="Mining Technology">Mining Technology</option>
                  <option value="Psychology">Psychology</option>
                  <option value="History">History</option>
                  <option value="Philosophy">Philosophy</option>
                  <option value="Literature">Literature</option>
                  <option value="Communication Arts">Communication Arts</option>
                  <option value="Journalism">Journalism</option>
                  <option value="Human Services">Human Services</option>
                  <option value="Community Services">Community Services</option>
                  <option value="Social Welfare">Social Welfare</option>
                  <option value="Counseling">Counseling</option>
                  <!-- Excluded Courses (Low-scale) -->
                  <option value="Information Technology">Information Technology</option>
                  <option value="Computer Science">Computer Science</option>
                  <option value="Accountancy">Accountancy</option>
                  <option value="Nursing">Nursing</option>
                  <option value="Education">Education</option>
                  <option value="Political Science">Political Science</option>
                  <!-- Other Common Courses -->
                  <option value="Architecture">Architecture</option>
                  <option value="Chemical Engineering">Chemical Engineering</option>
                  <option value="Electrical Engineering">Electrical Engineering</option>
                  <option value="Electronics Engineering">Electronics Engineering</option>
                  <option value="Computer Engineering">Computer Engineering</option>
                  <option value="Information Systems">Information Systems</option>
                  <option value="Data Science">Data Science</option>
                  <option value="Statistics">Statistics</option>
                  <option value="Mathematics">Mathematics</option>
                  <option value="Physics">Physics</option>
                  <option value="Chemistry">Chemistry</option>
                  <option value="Biology">Biology</option>
                  <option value="Biochemistry">Biochemistry</option>
                  <option value="Biotechnology">Biotechnology</option>
                  <option value="Food Technology">Food Technology</option>
                  <option value="Nutrition">Nutrition</option>
                  <option value="Hotel and Restaurant Management">Hotel and Restaurant Management</option>
                  <option value="Tourism">Tourism</option>
                  <option value="Hospitality Management">Hospitality Management</option>
                  <option value="Fine Arts">Fine Arts</option>
                  <option value="Music">Music</option>
                  <option value="Theater Arts">Theater Arts</option>
                  <option value="Dance">Dance</option>
                  <option value="Sports Science">Sports Science</option>
                  <option value="Physical Education">Physical Education</option>
                  <option value="Other">Other</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
              <input 
                type="text" 
                id="register_course_other" 
                name="course_other" 
                placeholder="If Other, please specify" 
                class="form-input w-full mt-3 px-4 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none hidden"
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Email Address</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                </div>
                <input 
                  type="email" 
                  name="email" 
                  id="register_email" 
                  required 
                  placeholder="Enter your email" 
                  class="form-input w-full pl-12 pr-4 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
              </div>
            </div>

            <!-- Password -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Password</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input 
                  type="password" 
                  name="password" 
                  id="register_password" 
                  required 
                  placeholder="Enter your password" 
                  class="form-input w-full pl-12 pr-12 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
                <button 
                  type="button" 
                  onclick="togglePassword('register_password', this)" 
                  tabindex="-1" 
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-orange-600 transition-colors"
                >
                  <svg id="register_password_icon" xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' />
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Confirm Password -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Confirm Password</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <input 
                  type="password" 
                  name="password_confirmation" 
                  id="register_password_confirmation" 
                  required 
                  placeholder="Confirm your password" 
                  class="form-input w-full pl-12 pr-12 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
                <button 
                  type="button" 
                  onclick="togglePassword('register_password_confirmation', this)" 
                  tabindex="-1" 
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-orange-600 transition-colors"
                >
                  <svg id="register_password_confirmation_icon" xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' />
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary w-full text-white py-4 rounded-xl font-bold text-lg shadow-lg relative z-10">
              Create Account
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleForm() {
      const loginForm = document.getElementById('loginForm');
      const signupForm = document.getElementById('signupForm');

      loginForm.classList.toggle('hidden');
      signupForm.classList.toggle('hidden');
      
      // Add slide animation
      if (!loginForm.classList.contains('hidden')) {
        loginForm.classList.add('slide-in-right');
      } else {
        signupForm.classList.add('slide-in-right');
      }
    }

    function togglePassword(id, btn) {
      const input = document.getElementById(id);
      const icon = document.getElementById(id + '_icon');
      
      if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.32-2.69A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.043 5.306M15 12a3 3 0 11-6 0 3 3 0 016 0zm-6.364 6.364L4 20m16 0l-1.636-1.636'/>`;
      } else {
        input.type = 'password';
        icon.innerHTML = `<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' />`;
      }
    }

    // Course selector: show "Other" input
    document.addEventListener('DOMContentLoaded', function() {
      const courseSelect = document.getElementById('register_course');
      const courseOther = document.getElementById('register_course_other');
      const signupForm = document.querySelector("form[action='{{ url('/register') }}']");
      
      if (courseSelect) {
        courseSelect.addEventListener('change', function() {
          if (this.value === 'Other') {
            courseOther.classList.remove('hidden');
            courseOther.required = true;
          } else {
            courseOther.classList.add('hidden');
            courseOther.required = false;
            courseOther.value = '';
          }
        });
      }
      
      if (signupForm && courseSelect && courseOther) {
        signupForm.addEventListener('submit', function(e) {
          if (courseSelect.value === 'Other' && courseOther.value.trim() !== '') {
            const opt = document.createElement('option');
            opt.value = courseOther.value.trim();
            opt.text = courseOther.value.trim();
            opt.selected = true;
            courseSelect.appendChild(opt);
          }
        });
      }
    });
  </script>
</body>
</html>
