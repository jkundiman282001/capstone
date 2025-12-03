<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login & Signup</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-image: url('{{ asset('mountain.png') }}');
    }

    .workspace-bg::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.4) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.1) 0%, transparent 50%);
      pointer-events: none;
    }

    .wavy-curtain {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 120px;
      overflow: hidden;
      z-index: 1;
    }

    .curtain-wave {
      position: absolute;
      top: 0;
      left: 0;
      width: 200%;
      height: 120px;
      background: linear-gradient(
        135deg,
        rgba(0, 0, 0, 0.8) 0%,
        rgba(220, 38, 38, 0.8) 25%,
        rgba(234, 179, 8, 0.8) 50%,
        rgba(249, 115, 22, 0.8) 75%,
        rgba(101, 67, 33, 0.8) 100%
      );
      clip-path: polygon(0 0, 100% 0, 100% 60%, 95% 65%, 90% 55%, 85% 70%, 80% 50%, 75% 75%, 70% 45%, 65% 80%, 60% 40%, 55% 85%, 50% 35%, 45% 90%, 40% 30%, 35% 95%, 30% 25%, 25% 100%, 20% 20%, 15% 90%, 10% 30%, 5% 85%, 0 40%);
      animation: curtainWave 8s ease-in-out infinite;
    }

    .curtain-wave::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        135deg,
        rgba(0, 0, 0, 0.6) 0%,
        rgba(220, 38, 38, 0.6) 25%,
        rgba(234, 179, 8, 0.6) 50%,
        rgba(249, 115, 22, 0.6) 75%,
        rgba(101, 67, 33, 0.6) 100%
      );
      clip-path: polygon(0 0, 100% 0, 100% 50%, 95% 55%, 90% 45%, 85% 60%, 80% 40%, 75% 65%, 70% 35%, 65% 70%, 60% 30%, 55% 75%, 50% 25%, 45% 80%, 40% 20%, 35% 85%, 30% 15%, 25% 90%, 20% 10%, 15% 80%, 10% 20%, 5% 75%, 0 30%);
      animation: curtainWave 6s ease-in-out infinite reverse;
    }

    @keyframes curtainWave {
      0%, 100% {
        transform: translateX(0) scaleY(1);
      }
      50% {
        transform: translateX(-5%) scaleY(1.1);
      }
    }

    /* Custom checkbox styling for orange color */
    .checkbox-orange {
      accent-color: #ea580c; /* orange-600 */
    }

    /* Fallback for browsers that don't support accent-color */
    .checkbox-orange:checked {
      background-color: #ea580c;
      border-color: #ea580c;
    }

    .checkbox-orange:focus {
      outline: none;
    }
  </style>
</head>
<body class="min-h-screen workspace-bg flex items-stretch relative">
  <!-- Wavy Curtain -->
  <div class="wavy-curtain">
    <div class="curtain-wave"></div>
  </div>

  <!-- Left Side: Form Container -->
  <div class="w-full max-w-xl bg-white flex items-center justify-start p-8 relative z-10">
    <div class="w-full transition-all duration-500">
      <div class="text-center mb-6">
        <div src="National_Commission_on_Indigenous_Peoples_(NCIP).png" class="inline-block mb-4">
          <img src="{{ asset('National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" alt="NCIP Logo" class="h-20 mx-auto" />
        </div>
      </div>

      <!-- Forms -->
      <div id="loginForm" class="space-y-6">
        <div class="text-center">
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Sign in to your account</h2>
          <p class="text-sm text-gray-600">Not a member? <button onclick="toggleForm()" class="text-orange-600 font-medium hover:underline">Create an account</button></p>
        </div>
        @if (session('success'))
          <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
          </div>
        @endif
        @if ($errors->login->any())
          <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
              @foreach ($errors->login->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <form method="POST" action="{{ url('/login') }}" class="space-y-4">
          @csrf
          <input type="email" name="email" id="login_email" required autofocus placeholder="Enter your email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" />
          <div class="relative">
            <input type="password" name="password" id="login_password" required placeholder="Enter your password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 pr-10" />
            <button type="button" onclick="togglePassword('login_password', this)" tabindex="-1" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
              <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' /></svg>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <label class="flex items-center text-sm text-gray-700">
              <input type="checkbox" name="remember" value="1" class="checkbox-orange h-4 w-4 border-gray-300 rounded mr-2"> Remember me
            </label>
            <a href="#" class="text-sm text-orange-600 hover:underline">Forgot password?</a>
          </div>
          <button type="submit" class="w-full bg-orange-600 text-white py-3 rounded-lg hover:bg-red-700">Log in</button>
        </form>
      </div>

      <div id="signupForm" class="space-y-6 hidden">
        <div class="text-center">
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Create your account</h2>
          <p class="text-sm text-gray-600">Already have an account? <button onclick="toggleForm()" class="text-orange-600 font-medium hover:underline">Log in here</button></p>
        </div>
        @if ($errors->register->any())
          <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
              @foreach ($errors->register->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <form method="POST" action="{{ url('/register') }}" class="space-y-4">
          @csrf
          <div class="grid grid-cols-2 gap-4">
            <input type="text" name="first_name" id="register_first_name" required placeholder="First Name" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" />
            <input type="text" name="last_name" id="register_last_name" required placeholder="Last Name" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" />
          </div>
          <input type="text" name="middle_name" id="register_middle_name" placeholder="Middle Name (optional)" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" />
          <input type="text" name="contact_num" id="register_contact_num" required placeholder="Contact Number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" />
          <select name="ethno_id" id="register_ethno_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">Select Ethnolinguistic Group</option>
            @foreach($ethnicities as $ethno)
              <option value="{{ $ethno->id }}">{{ $ethno->ethnicity }}</option>
            @endforeach
          </select>
          <div class="grid grid-cols-1">
            <select name="course" id="register_course" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
              <option value="">Select your course</option>
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
              <option value="BS Information Technology">BS Information Technology</option>
              <option value="BS Computer Science">BS Computer Science</option>
              <option value="BS Accountancy">BS Accountancy</option>
              <option value="BS Nursing">BS Nursing</option>
              <option value="BS Education">BS Education</option>
              <option value="BA Political Science">BA Political Science</option>
              <option value="Other">Other</option>
            </select>
            <input type="text" id="register_course_other" name="course_other" placeholder="If Other, please specify" class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 hidden" />
          </div>
          <input type="email" name="email" id="register_email" required autofocus placeholder="Enter your email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" />
          <div class="relative">
            <input type="password" name="password" id="register_password" required placeholder="Enter your password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 pr-10" />
            <button type="button" onclick="togglePassword('register_password', this)" tabindex="-1" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
              <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' /></svg>
            </button>
          </div>
          <div class="relative">
            <input type="password" name="password_confirmation" id="register_password_confirmation" required placeholder="Confirm your password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 pr-10" />
            <button type="button" onclick="togglePassword('register_password_confirmation', this)" tabindex="-1" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
              <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' /></svg>
            </button>
          </div>
          <button type="submit" class="w-full bg-orange-600 text-white py-3 rounded-lg hover:bg-red-700">Create account</button>
        </form>
      </div>
    </div>
  </div>

  <div class="hidden lg:block">
    <img 
      src="{{ asset('mountain.png') }}" 
      alt="Work desk" 
      class="h-full w-full object-cover" 
    />
  </div>

  <script>
    function toggleForm() {
      const loginForm = document.getElementById('loginForm');
      const signupForm = document.getElementById('signupForm');

      loginForm.classList.toggle('hidden');
      signupForm.classList.toggle('hidden');
    }

    function togglePassword(id, btn) {
      const input = document.getElementById(id);
      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = `<svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.32-2.69A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.043 5.306M15 12a3 3 0 11-6 0 3 3 0 016 0zm-6.364 6.364L4 20m16 0l-1.636-1.636'/></svg>`;
      } else {
        input.type = 'password';
        btn.innerHTML = `<svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' /></svg>`;
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
          } else {
            courseOther.classList.add('hidden');
          }
        });
      }
      if (signupForm && courseSelect && courseOther) {
        signupForm.addEventListener('submit', function() {
          if (courseSelect.value === 'Other' && courseOther.value.trim() !== '') {
            // inject other value into course field so backend receives it
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