<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NCIP-EAP | Login & Signup</title>
  <link rel="icon" type="image/png" href="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}">
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

    /* Modal Styles */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      opacity: 0;
      transition: opacity 0.3s ease;
      pointer-events: none;
    }

    .modal-overlay.active {
      opacity: 1;
      pointer-events: auto;
    }

    .modal-content {
      background: white;
      border-radius: 1.5rem;
      max-width: 800px;
      max-height: 90vh;
      width: 100%;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      transform: scale(0.95);
      transition: transform 0.3s ease;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .modal-overlay.active .modal-content {
      transform: scale(1);
    }

    .modal-header {
      padding: 1.5rem 2rem;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
      color: white;
    }

    .modal-body {
      padding: 2rem;
      overflow-y: auto;
      flex: 1;
    }

    .modal-close {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      color: white;
      width: 2rem;
      height: 2rem;
      border-radius: 0.5rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
    }

    .modal-close:hover {
      background: rgba(255, 255, 255, 0.3);
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
              <a href="{{ route('password.request') }}" class="text-sm font-bold text-orange-600 hover:text-orange-700 transition-colors">Forgot password?</a>
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
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Course/Preferred Course</label>
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
                  <option value="Aerospace Engineering">Aerospace Engineering</option>
                  <option value="Agribusiness">Agribusiness</option>
                  <option value="Agricultural Economics">Agricultural Economics</option>
                  <option value="Agricultural Engineering">Agricultural Engineering</option>
                  <option value="Agricultural Technology">Agricultural Technology</option>
                  <option value="Agriculture">Agriculture</option>
                  <option value="Animal Science">Animal Science</option>
                  <option value="Anthropology">Anthropology</option>
                  <option value="Aqua-Culture and Fisheries">Aqua-Culture and Fisheries</option>
                  <option value="Aquaculture">Aquaculture</option>
                  <option value="Archaeology">Archaeology</option>
                  <option value="Architecture">Architecture</option>
                  <option value="Automotive Engineering">Automotive Engineering</option>
                  <option value="Biochemistry">Biochemistry</option>
                  <option value="Biology">Biology</option>
                  <option value="Biotechnology">Biotechnology</option>
                  <option value="Business Administration">Business Administration</option>
                  <option value="Business Management">Business Management</option>
                  <option value="Chemical Engineering">Chemical Engineering</option>
                  <option value="Chemistry">Chemistry</option>
                  <option value="Civil Engineering">Civil Engineering</option>
                  <option value="Communication Arts">Communication Arts</option>
                  <option value="Community Development">Community Development</option>
                  <option value="Community Services">Community Services</option>
                  <option value="Computer Engineering">Computer Engineering</option>
                  <option value="Computer Science">Computer Science</option>
                  <option value="Conservation">Conservation</option>
                  <option value="Construction Engineering">Construction Engineering</option>
                  <option value="Constitutional Law">Constitutional Law</option>
                  <option value="Counseling">Counseling</option>
                  <option value="Criminal Justice">Criminal Justice</option>
                  <option value="Criminology">Criminology</option>
                  <option value="Crop Science">Crop Science</option>
                  <option value="Cultural Studies">Cultural Studies</option>
                  <option value="Curriculum Development">Curriculum Development</option>
                  <option value="Dance">Dance</option>
                  <option value="Data Science">Data Science</option>
                  <option value="Development Studies">Development Studies</option>
                  <option value="Diplomatic Studies">Diplomatic Studies</option>
                  <option value="Earth Science">Earth Science</option>
                  <option value="Ecology">Ecology</option>
                  <option value="Economics">Economics</option>
                  <option value="Education">Education</option>
                  <option value="Educational Administration">Educational Administration</option>
                  <option value="Electrical Engineering">Electrical Engineering</option>
                  <option value="Electronics Engineering">Electronics Engineering</option>
                  <option value="Elementary Education">Elementary Education</option>
                  <option value="Entrepreneurship">Entrepreneurship</option>
                  <option value="Environmental Engineering">Environmental Engineering</option>
                  <option value="Environmental Management">Environmental Management</option>
                  <option value="Environmental Science">Environmental Science</option>
                  <option value="Ethnic Studies">Ethnic Studies</option>
                  <option value="Finance">Finance</option>
                  <option value="Fine Arts">Fine Arts</option>
                  <option value="Fisheries">Fisheries</option>
                  <option value="Food Technology">Food Technology</option>
                  <option value="Foreign Service">Foreign Service</option>
                  <option value="Forensic Science">Forensic Science</option>
                  <option value="Forestry and Environment Studies">Forestry and Environment Studies</option>
                  <option value="Geodetic Engineering">Geodetic Engineering</option>
                  <option value="Geographic Information Systems">Geographic Information Systems</option>
                  <option value="Geological Engineering">Geological Engineering</option>
                  <option value="Geology">Geology</option>
                  <option value="Geomatics">Geomatics</option>
                  <option value="Geophysics">Geophysics</option>
                  <option value="Health Sciences">Health Sciences</option>
                  <option value="History">History</option>
                  <option value="Hospitality Management">Hospitality Management</option>
                  <option value="Hotel and Restaurant Management">Hotel and Restaurant Management</option>
                  <option value="Human Resource Management">Human Resource Management</option>
                  <option value="Human Services">Human Services</option>
                  <option value="Industrial Engineering">Industrial Engineering</option>
                  <option value="Information Systems">Information Systems</option>
                  <option value="Information Technology">Information Technology</option>
                  <option value="International Relations">International Relations</option>
                  <option value="International Studies">International Studies</option>
                  <option value="Journalism">Journalism</option>
                  <option value="Jurisprudence">Jurisprudence</option>
                  <option value="Land Surveying">Land Surveying</option>
                  <option value="Law">Law</option>
                  <option value="Law Enforcement">Law Enforcement</option>
                  <option value="Legal Studies">Legal Studies</option>
                  <option value="Literature">Literature</option>
                  <option value="Manufacturing Engineering">Manufacturing Engineering</option>
                  <option value="Marine Biology">Marine Biology</option>
                  <option value="Marine Science">Marine Science</option>
                  <option value="Marketing">Marketing</option>
                  <option value="Mathematics">Mathematics</option>
                  <option value="Mechanical Engineering">Mechanical Engineering</option>
                  <option value="Medical Laboratory Science">Medical Laboratory Science</option>
                  <option value="Medicine and Allied Health Sciences">Medicine and Allied Health Sciences</option>
                  <option value="Mineral Processing">Mineral Processing</option>
                  <option value="Mining Engineering">Mining Engineering</option>
                  <option value="Mining Technology">Mining Technology</option>
                  <option value="Music">Music</option>
                  <option value="Natural Resource Management">Natural Resource Management</option>
                  <option value="Nutrition">Nutrition</option>
                  <option value="Occupational Therapy">Occupational Therapy</option>
                  <option value="Oceanography">Oceanography</option>
                  <option value="Operations Management">Operations Management</option>
                  <option value="Pharmacy">Pharmacy</option>
                  <option value="Philosophy">Philosophy</option>
                  <option value="Physical Education">Physical Education</option>
                  <option value="Physical Therapy">Physical Therapy</option>
                  <option value="Physics">Physics</option>
                  <option value="Political Science">Political Science</option>
                  <option value="Psychology">Psychology</option>
                  <option value="Public Administration">Public Administration</option>
                  <option value="Public Health">Public Health</option>
                  <option value="Radiologic Technology">Radiologic Technology</option>
                  <option value="Rural Development">Rural Development</option>
                  <option value="Secondary Education">Secondary Education</option>
                  <option value="Security Management">Security Management</option>
                  <option value="Social Sciences">Social Sciences</option>
                  <option value="Social Welfare">Social Welfare</option>
                  <option value="Social Work">Social Work</option>
                  <option value="Sociology">Sociology</option>
                  <option value="Special Education">Special Education</option>
                  <option value="Sports Science">Sports Science</option>
                  <option value="Statistics">Statistics</option>
                  <option value="Structural Engineering">Structural Engineering</option>
                  <option value="Surveying">Surveying</option>
                  <option value="Theater Arts">Theater Arts</option>
                  <option value="Tourism">Tourism</option>
                  <option value="Transportation Engineering">Transportation Engineering</option>
                  <option value="Urban Planning">Urban Planning</option>
                  <option value="Undecided">Undecided / Grade 12 exploring options</option>
                  <option value="None">None</option>
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

            <!-- Educational Status -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Educational Status</label>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center justify-center p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-orange-500 transition-all group has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                  <input type="radio" name="educational_status" value="SHS Graduate" required class="sr-only educational-status-radio">
                  <span class="text-sm font-semibold text-slate-600 group-hover:text-orange-600 group-has-[:checked]:text-orange-600">SHS Graduate</span>
                </label>
                <label class="relative flex items-center justify-center p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-orange-500 transition-all group has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                  <input type="radio" name="educational_status" value="Ongoing College" required class="sr-only educational-status-radio">
                  <span class="text-sm font-semibold text-slate-600 group-hover:text-orange-600 group-has-[:checked]:text-orange-600">Ongoing College</span>
                </label>
              </div>
            </div>

            <!-- Grade Scale (Conditional) -->
            <div id="grade_scale_section" class="hidden space-y-3 p-4 bg-orange-50/50 rounded-xl border border-orange-100 transition-all">
              <label class="block text-xs font-bold text-orange-800 uppercase tracking-wide">College Grade Scale</label>
              <div class="space-y-2">
                <label class="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-orange-400 transition-colors group has-[:checked]:border-orange-500">
                  <input type="radio" name="grade_scale" value="1.0" class="w-4 h-4 text-orange-600 focus:ring-orange-500">
                  <span class="text-sm text-slate-700 group-has-[:checked]:font-semibold">Is your college using a 1.0 as the highest grade scale?</span>
                </label>
                <label class="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-orange-400 transition-colors group has-[:checked]:border-orange-500">
                  <input type="radio" name="grade_scale" value="4.0" class="w-4 h-4 text-orange-600 focus:ring-orange-500">
                  <span class="text-sm text-slate-700 group-has-[:checked]:font-semibold">Is your college using a 4.0 as the highest grade scale?</span>
                </label>
              </div>
            </div>

            <!-- Numerical Grade Input (Conditional) -->
            <div id="numerical_grade_section" class="hidden space-y-3 p-4 bg-orange-50/50 rounded-xl border border-orange-100 transition-all">
              <label class="block text-xs font-bold text-orange-800 uppercase tracking-wide">Numerical Grade (GWA)</label>
              <div class="relative">
                <input 
                  type="number" 
                  name="numerical_grade" 
                  id="register_numerical_grade" 
                  min="0" 
                  max="100"
                  step="0.01"
                  placeholder="Enter GWA (e.g., 95.50)" 
                  class="form-input w-full px-4 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
                <p class="text-[10px] text-orange-600 font-medium mt-1">Input your numerical grade to see its scale equivalent.</p>
              </div>
              
              <!-- Conversion Result Area -->
              <div id="conversion_result" class="hidden p-4 bg-white rounded-xl border border-orange-200 shadow-sm transition-all animate-pulse-once">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Scale Equivalent</p>
                    <p id="converted_value" class="text-2xl font-black text-orange-600">--</p>
                  </div>
                  <div class="px-3 py-1 bg-orange-100 rounded-lg">
                    <span id="scale_badge" class="text-[10px] font-bold text-orange-700 uppercase tracking-tighter">--</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- College Year Level (Conditional) -->
            <div id="college_year_section" class="hidden space-y-3 p-4 bg-orange-50/50 rounded-xl border border-orange-100 transition-all">
              <label class="block text-xs font-bold text-orange-800 uppercase tracking-wide">College Year Level</label>
              <div class="relative">
                <input 
                  type="number" 
                  name="college_year" 
                  id="register_college_year" 
                  min="1" 
                  max="5"
                  placeholder="Enter Year Level (1-5)" 
                  class="form-input w-full px-4 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
                <p class="text-[10px] text-orange-600 font-medium">Please enter your current year level (e.g., 1, 2, 3, 4, 5)</p>
              </div>
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

            <!-- Terms and Conditions -->
            <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
              <input 
                type="checkbox" 
                name="terms_accepted" 
                id="terms_accepted" 
                required
                class="checkbox-orange mt-1 flex-shrink-0"
              />
              <label for="terms_accepted" class="text-sm text-slate-700 cursor-pointer">
                I agree to the 
                <button 
                  type="button" 
                  onclick="showTermsModal()" 
                  class="text-orange-600 font-bold hover:text-orange-700 underline transition-colors"
                >
                  Terms and Conditions
                </button>
                and 
                <button 
                  type="button" 
                  onclick="showPrivacyModal()" 
                  class="text-orange-600 font-bold hover:text-orange-700 underline transition-colors"
                >
                  Privacy Policy
                </button>
                <span class="text-red-500">*</span>
              </label>
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
      
      // Educational Status & Grade Scale logic
      const eduRadios = document.querySelectorAll('.educational-status-radio');
      const gradeScaleSection = document.getElementById('grade_scale_section');
      const collegeYearSection = document.getElementById('college_year_section');
      const collegeYearInput = document.getElementById('register_college_year');
      const gradeScaleRadios = document.querySelectorAll('input[name="grade_scale"]');
      const numericalGradeSection = document.getElementById('numerical_grade_section');
      const numericalGradeInput = document.getElementById('register_numerical_grade');
      const conversionResult = document.getElementById('conversion_result');
      const convertedValueDisplay = document.getElementById('converted_value');
      const scaleBadge = document.getElementById('scale_badge');

      function updateConversion() {
          const grade = parseFloat(numericalGradeInput.value);
          const selectedScale = document.querySelector('input[name="grade_scale"]:checked');
          
          if (!selectedScale || isNaN(grade)) {
              conversionResult.classList.add('hidden');
              return;
          }

          const scale = selectedScale.value;
          
          // Handle edge cases (above 100 or below 0)
          if (grade > 100 || grade < 0) {
              convertedValueDisplay.innerText = "Invalid";
              convertedValueDisplay.classList.replace('text-orange-600', 'text-red-500');
              scaleBadge.innerText = "OUT OF RANGE";
              conversionResult.classList.remove('hidden');
              return;
          }
          
          convertedValueDisplay.classList.replace('text-red-500', 'text-orange-600');
          let result = "--";
          
          if (scale === "1.0") {
              scaleBadge.innerText = "1.0 Scale";
              if (grade >= 97) result = "1.00";
              else if (grade >= 94) result = "1.25";
              else if (grade >= 91) result = "1.50";
              else if (grade >= 88) result = "1.75";
              else if (grade >= 85) result = "2.00";
              else if (grade >= 82) result = "2.25";
              else if (grade >= 79) result = "2.50";
              else if (grade >= 76) result = "2.75";
              else if (grade >= 75) result = "3.00";
              else result = "5.00";
          } else if (scale === "4.0") {
              scaleBadge.innerText = "4.0 Scale";
              if (grade >= 96) result = "4.00";
              else if (grade >= 90) result = "3.50";
              else if (grade >= 85) result = "3.00";
              else if (grade >= 80) result = "2.50";
              else if (grade >= 75) result = "2.00";
              else result = "1.00";
          }

          convertedValueDisplay.innerText = result;
          conversionResult.classList.remove('hidden');
      }

      eduRadios.forEach(radio => {
        radio.addEventListener('change', function() {
          if (this.value === 'Ongoing College' && this.checked) {
            gradeScaleSection.classList.remove('hidden');
            collegeYearSection.classList.remove('hidden');
            numericalGradeSection.classList.remove('hidden');
            gradeScaleRadios.forEach(r => r.required = true);
            collegeYearInput.required = true;
            numericalGradeInput.required = true;
          } else {
            gradeScaleSection.classList.add('hidden');
            collegeYearSection.classList.add('hidden');
            numericalGradeSection.classList.add('hidden');
            gradeScaleRadios.forEach(r => {
              r.required = false;
              r.checked = false;
            });
            collegeYearInput.required = false;
            collegeYearInput.value = '';
            numericalGradeInput.required = false;
            numericalGradeInput.value = '';
            conversionResult.classList.add('hidden');
          }
        });
      });

      gradeScaleRadios.forEach(radio => {
          radio.addEventListener('change', updateConversion);
      });

      numericalGradeInput.addEventListener('input', updateConversion);

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

    // Terms and Conditions Modal
    function showTermsModal() {
      const modal = document.getElementById('termsModal');
      if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    }

    function showPrivacyModal() {
      const modal = document.getElementById('privacyModal');
      if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    }

    function closeModal(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
      }
    }

    // Close modal on overlay click
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
        document.body.style.overflow = '';
      }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(modal => {
          modal.classList.remove('active');
          document.body.style.overflow = '';
        });
      }
    });
  </script>

  <!-- Terms and Conditions Modal -->
  <div id="termsModal" class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="text-2xl font-black">Terms and Conditions</h2>
        <button onclick="closeModal('termsModal')" class="modal-close">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="modal-body">
        <div class="prose max-w-none text-slate-700">
          <h3 class="text-xl font-bold text-slate-900 mb-4">1. Acceptance of Terms</h3>
          <p class="mb-4">
            By accessing and using the NCIP Educational Assistance Program (NCIP-EAP) Scholarship Management Portal, 
            you accept and agree to be bound by the terms and conditions set forth in this agreement. If you do not 
            agree to these terms, please do not use this portal.
          </p>

          <h3 class="text-xl font-bold text-slate-900 mb-4">2. Eligibility</h3>
          <p class="mb-4">
            To be eligible for the NCIP-EAP scholarship program, you must:
          </p>
          <ul class="list-disc pl-6 mb-4 space-y-2">
            <li>Be a member of an Indigenous Peoples (IP) community recognized by the National Commission on Indigenous Peoples (NCIP)</li>
            <li>Be a Filipino citizen</li>
            <li>Meet the academic requirements as specified by the program</li>
            <li>Submit all required documents and information accurately</li>
            <li>Comply with all application deadlines and requirements</li>
          </ul>

          <h3 class="text-xl font-bold text-slate-900 mb-4">3. Application Process</h3>
          <p class="mb-4">
            Applicants are required to:
          </p>
          <ul class="list-disc pl-6 mb-4 space-y-2">
            <li>Provide accurate and truthful information in their application</li>
            <li>Submit all required documents in the specified format</li>
            <li>Maintain eligibility throughout the application and scholarship period</li>
            <li>Notify NCIP-EAP of any changes to their information or status</li>
          </ul>

          <h3 class="text-xl font-bold text-slate-900 mb-4">4. Scholarship Obligations</h3>
          <p class="mb-4">
            Scholarship recipients must:
          </p>
          <ul class="list-disc pl-6 mb-4 space-y-2">
            <li>Maintain satisfactory academic performance as defined by the program</li>
            <li>Comply with all program rules and regulations</li>
            <li>Submit required reports and documentation on time</li>
            <li>Participate in program activities and community service as required</li>
            <li>Use scholarship funds solely for educational purposes</li>
          </ul>

          <h3 class="text-xl font-bold text-slate-900 mb-4">5. Data Privacy and Confidentiality</h3>
          <p class="mb-4">
            All personal information provided will be handled in accordance with the Data Privacy Act of 2012. 
            NCIP-EAP is committed to protecting your personal data and will only use it for program administration 
            and evaluation purposes.
          </p>

          <h3 class="text-xl font-bold text-slate-900 mb-4">6. Program Modifications</h3>
          <p class="mb-4">
            NCIP-EAP reserves the right to modify, suspend, or terminate the scholarship program at any time. 
            Changes will be communicated to all participants in a timely manner.
          </p>

          <h3 class="text-xl font-bold text-slate-900 mb-4">7. Termination</h3>
          <p class="mb-4">
            Scholarship may be terminated if the recipient:
          </p>
          <ul class="list-disc pl-6 mb-4 space-y-2">
            <li>Fails to maintain academic requirements</li>
            <li>Violates program rules or code of conduct</li>
            <li>Provides false or misleading information</li>
            <li>Engages in activities that bring disrepute to NCIP-EAP</li>
          </ul>

          <h3 class="text-xl font-bold text-slate-900 mb-4">8. Limitation of Liability</h3>
          <p class="mb-4">
            NCIP-EAP shall not be liable for any indirect, incidental, or consequential damages arising from 
            the use of this portal or participation in the scholarship program.
          </p>

          <h3 class="text-xl font-bold text-slate-900 mb-4">9. Contact Information</h3>
          <p class="mb-4">
            For questions or concerns regarding these terms and conditions, please contact NCIP-EAP at:
            <br>Email: support@ncip.gov.ph
            <br>Phone: (02) 888-1234
          </p>

          <p class="text-sm text-slate-500 mt-6">
            Last updated: {{ date('F d, Y') }}
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Privacy Policy Modal -->
  <div id="privacyModal" class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="text-2xl font-black">Privacy Policy</h2>
        <button onclick="closeModal('privacyModal')" class="modal-close">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="modal-body">
        <div class="prose max-w-none text-slate-700">
          <h3 class="text-xl font-bold text-slate-900 mb-4">1. Information We Collect</h3>
          <p class="mb-4">
            NCIP-EAP collects the following types of personal information:
          </p>
          <ul class="list-disc pl-6 mb-4 space-y-2">
            <li>Personal identification information (name, date of birth, contact details)</li>
            <li>Academic records and educational background</li>
            <li>Family information and financial data</li>
            <li>Ethnolinguistic group affiliation</li>
            <li>Documentation required for scholarship application</li>
          </ul>

          <h3 class="text-xl font-bold text-slate-900 mb-4">2. How We Use Your Information</h3>
          <p class="mb-4">
            Your personal information is used for:
          </p>
          <ul class="list-disc pl-6 mb-4 space-y-2">
            <li>Processing and evaluating scholarship applications</li>
            <li>Administering the scholarship program</li>
            <li>Communicating with applicants and recipients</li>
            <li>Program monitoring and evaluation</li>
            <li>Compliance with legal and regulatory requirements</li>
          </ul>

          <h3 class="text-xl font-bold text-slate-900 mb-4">3. Data Protection</h3>
          <p class="mb-4">
            NCIP-EAP implements appropriate technical and organizational measures to protect your personal data 
            against unauthorized access, alteration, disclosure, or destruction. This includes:
          </p>
          <ul class="list-disc pl-6 mb-4 space-y-2">
            <li>Secure data storage and transmission</li>
            <li>Access controls and authentication</li>
            <li>Regular security audits and updates</li>
            <li>Staff training on data protection</li>
          </ul>

          <h3 class="text-xl font-bold text-slate-900 mb-4">4. Data Sharing</h3>
          <p class="mb-4">
            We do not sell or rent your personal information. We may share your information with:
          </p>
          <ul class="list-disc pl-6 mb-4 space-y-2">
            <li>Authorized NCIP personnel involved in program administration</li>
            <li>Educational institutions for verification and coordination</li>
            <li>Government agencies as required by law</li>
            <li>Service providers under strict confidentiality agreements</li>
          </ul>

          <h3 class="text-xl font-bold text-slate-900 mb-4">5. Your Rights</h3>
          <p class="mb-4">
            Under the Data Privacy Act of 2012, you have the right to:
          </p>
          <ul class="list-disc pl-6 mb-4 space-y-2">
            <li>Access your personal data</li>
            <li>Request correction of inaccurate data</li>
            <li>Request deletion of your data (subject to legal retention requirements)</li>
            <li>Object to processing of your data</li>
            <li>File a complaint with the National Privacy Commission</li>
          </ul>

          <h3 class="text-xl font-bold text-slate-900 mb-4">6. Data Retention</h3>
          <p class="mb-4">
            We retain your personal data for as long as necessary to fulfill the purposes outlined in this policy, 
            or as required by applicable laws and regulations. Scholarship records may be retained for archival 
            and statistical purposes.
          </p>

          <h3 class="text-xl font-bold text-slate-900 mb-4">7. Cookies and Tracking</h3>
          <p class="mb-4">
            Our portal may use cookies and similar technologies to enhance user experience and analyze portal usage. 
            You can control cookie preferences through your browser settings.
          </p>

          <h3 class="text-xl font-bold text-slate-900 mb-4">8. Changes to This Policy</h3>
          <p class="mb-4">
            NCIP-EAP may update this Privacy Policy from time to time. Changes will be posted on this portal, 
            and significant changes will be communicated to registered users.
          </p>

          <h3 class="text-xl font-bold text-slate-900 mb-4">9. Contact Us</h3>
          <p class="mb-4">
            For questions or concerns about this Privacy Policy or to exercise your rights, please contact:
            <br>Email: privacy@ncip.gov.ph
            <br>Phone: (02) 888-1234
            <br>Address: NCIP-EAP Office, [Address]
          </p>

          <p class="text-sm text-slate-500 mt-6">
            Last updated: {{ date('F d, Y') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
