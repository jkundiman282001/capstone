<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NCIP Staff Login & Signup</title>
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
  </style>
</head>
<body>
  <!-- Hero Image Section -->
  <div class="hero-image-container">
    <!-- Background Image -->
    <img 
      src="{{ asset('images/Dashboard.png') }}" 
      alt="NCIP Staff Portal" 
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
        NCIP-EAP<br/>
        <span class="text-amber-300">Portal</span>
      </h1>
      <p class="text-xl font-semibold text-white/90 leading-relaxed">
        Manage scholarship applications and empower indigenous students through the NCIP Educational Assistance Program.
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
          <h1 class="text-2xl font-black text-slate-900 mb-1">NCIP Staff</h1>
          <p class="text-sm text-slate-600 font-medium">Management Portal</p>
        </div>

        <!-- Login Form -->
        <div id="loginForm" class="space-y-6">
          <div class="text-center">
            <h2 class="text-3xl font-black text-slate-900 mb-2">Admin Login</h2>
            <p class="text-sm text-slate-600">
              Don't have an account? 
              <button onclick="toggleForm()" class="text-orange-600 font-bold hover:text-orange-700 transition-colors">
                Sign up here
              </button>
            </p>
          </div>

          @if($errors->any() && session('form') !== 'register')
            <div class="p-4 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl">
              <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <ul class="text-sm text-red-700 space-y-1">
                  @foreach($errors->all() as $error)
                    <li class="font-medium">{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif

          <form method="POST" action="{{ url('staff/login') }}" class="space-y-5">
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
                  id="email" 
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
                  id="password" 
                  required 
                  placeholder="Enter your password" 
                  class="form-input w-full pl-12 pr-12 py-3.5 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
                <button 
                  type="button" 
                  onclick="togglePassword('password', 'passwordEye', 'passwordEyeSlash')" 
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="passwordEye">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="passwordEyeSlash">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary w-full text-white py-4 rounded-xl font-bold text-lg shadow-lg relative z-10">
              Sign In
            </button>
          </form>

          <div class="text-center text-sm text-slate-600">
            <span>Don't have an account? </span>
            <button onclick="toggleForm()" class="text-orange-600 font-bold hover:text-orange-700 transition-colors">
              Sign up
            </button>
          </div>
        </div>

        <!-- Register Form -->
        <div id="registerForm" class="space-y-6 hidden">
          <div class="text-center">
            <h2 class="text-3xl font-black text-slate-900 mb-2">Admin Registration</h2>
            <p class="text-sm text-slate-600">
              Already have an account? 
              <button onclick="toggleForm()" class="text-orange-600 font-bold hover:text-orange-700 transition-colors">
                Sign in here
              </button>
            </p>
          </div>

          @if($errors->any() && session('form') === 'register')
            <div class="p-4 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl">
              <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <ul class="text-sm text-red-700 space-y-1">
                  @foreach($errors->all() as $error)
                    <li class="font-medium">{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif

          <form method="POST" action="{{ url('staff/register') }}" class="space-y-4">
            @csrf
            
            <!-- First Name -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">First Name</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input 
                  type="text" 
                  name="first_name" 
                  id="first_name" 
                  required 
                  placeholder="First Name" 
                  class="form-input w-full pl-12 pr-4 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
              </div>
            </div>

            <!-- Last Name -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Last Name</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input 
                  type="text" 
                  name="last_name" 
                  id="last_name" 
                  required 
                  placeholder="Last Name" 
                  class="form-input w-full pl-12 pr-4 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
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
                  onclick="togglePassword('register_password', 'registerPasswordEye', 'registerPasswordEyeSlash')" 
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="registerPasswordEye">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="registerPasswordEyeSlash">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
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
                  id="password_confirmation" 
                  required 
                  placeholder="Confirm your password" 
                  class="form-input w-full pl-12 pr-12 py-3 rounded-xl focus:ring-4 focus:ring-orange-500/20 outline-none"
                />
                <button 
                  type="button" 
                  onclick="togglePassword('password_confirmation', 'confirmPasswordEye', 'confirmPasswordEyeSlash')" 
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="confirmPasswordEye">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="confirmPasswordEyeSlash">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary w-full text-white py-4 rounded-xl font-bold text-lg shadow-lg relative z-10">
              Create Account
            </button>
          </form>

          <div class="text-center text-sm text-slate-600">
            <span>Already have an account? </span>
            <button onclick="toggleForm()" class="text-orange-600 font-bold hover:text-orange-700 transition-colors">
              Sign in
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleForm() {
      const loginForm = document.getElementById('loginForm');
      const registerForm = document.getElementById('registerForm');

      loginForm.classList.toggle('hidden');
      registerForm.classList.toggle('hidden');
      
      // Add slide animation
      if (!loginForm.classList.contains('hidden')) {
        loginForm.classList.add('slide-in-right');
      } else {
        registerForm.classList.add('slide-in-right');
      }
    }

    function togglePassword(inputId, eyeId, eyeSlashId) {
      const passwordInput = document.getElementById(inputId);
      const eyeIcon = document.getElementById(eyeId);
      const eyeSlashIcon = document.getElementById(eyeSlashId);
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeSlashIcon.classList.remove('hidden');
      } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeSlashIcon.classList.add('hidden');
      }
    }
  </script>
</body>
</html>
