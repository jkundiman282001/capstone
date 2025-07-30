<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NCIP Staff Login & Signup</title>
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
    .checkbox-orange {
      accent-color: #ea580c;
    }
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
  <!-- Form Container -->
  <div class="w-full max-w-xl bg-white flex items-center justify-start p-8 relative z-10">
    <div class="w-full transition-all duration-500">
      <div class="text-center mb-6">
        <div class="inline-block mb-4">
          <img src="{{ asset('National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" alt="NCIP Logo" class="h-20 mx-auto" />
        </div>
      </div>
      <div class="flex justify-center mb-6">
        <button id="showLogin" class="px-4 py-2 font-semibold rounded-l bg-orange-600 text-white focus:outline-none">Login</button>
        <button id="showRegister" class="px-4 py-2 font-semibold rounded-r bg-orange-100 text-orange-600 focus:outline-none">Sign Up</button>
      </div>
      <!-- Login Form -->
      <div id="loginForm">
        <h2 class="text-2xl font-bold mb-6 text-center text-orange-600">NCIP Staff Login</h2>
        @if($errors->any() && session('form') !== 'register')
          <div class="mb-4 text-red-600 text-sm">
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <form method="POST" action="{{ url('staff/login') }}">
          <div class="mb-4">
            <label class="block text-gray-700 mb-2" for="email">Email</label>
            <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" type="email" name="email" id="email" required autofocus>
          </div>
          <div class="mb-6">
            <label class="block text-gray-700 mb-2" for="password">Password</label>
            <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" type="password" name="password" id="password" required>
          </div>
          <button class="w-full bg-orange-600 text-white py-2 rounded hover:bg-orange-700 transition-colors font-semibold" type="submit">Login</button>
        </form>
        <div class="mt-4 text-center text-sm">
          <span>Don't have an account?</span>
          <a href="#" id="toRegister" class="text-orange-600 hover:underline">Sign up</a>
        </div>
      </div>
      <!-- Register Form -->
      <div id="registerForm" style="display:none;">
        <h2 class="text-2xl font-bold mb-6 text-center text-orange-600">NCIP Staff Registration</h2>
        @if($errors->any() && session('form') === 'register')
          <div class="mb-4 text-red-600 text-sm">
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <form method="POST" action="{{ url('staff/register') }}">
          <div class="mb-4">
            <label class="block text-gray-700 mb-2" for="first_name">First Name</label>
            <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" type="text" name="first_name" id="first_name" required>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-2" for="last_name">Last Name</label>
            <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" type="text" name="last_name" id="last_name" required>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-2" for="email">Email</label>
            <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" type="email" name="email" id="email" required>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-2" for="password">Password</label>
            <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" type="password" name="password" id="password" required>
          </div>
          <div class="mb-6">
            <label class="block text-gray-700 mb-2" for="password_confirmation">Confirm Password</label>
            <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" type="password" name="password_confirmation" id="password_confirmation" required>
          </div>
          <button class="w-full bg-orange-600 text-white py-2 rounded hover:bg-orange-700 transition-colors font-semibold" type="submit">Sign Up</button>
        </form>
        <div class="mt-4 text-center text-sm">
          <span>Already have an account?</span>
          <a href="#" id="toLogin" class="text-orange-600 hover:underline">Login</a>
        </div>
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
    document.getElementById('showLogin').onclick = function() {
      document.getElementById('loginForm').style.display = '';
      document.getElementById('registerForm').style.display = 'none';
      this.classList.add('bg-orange-600', 'text-white');
      this.classList.remove('bg-orange-100', 'text-orange-600');
      document.getElementById('showRegister').classList.remove('bg-orange-600', 'text-white');
      document.getElementById('showRegister').classList.add('bg-orange-100', 'text-orange-600');
    };
    document.getElementById('showRegister').onclick = function() {
      document.getElementById('loginForm').style.display = 'none';
      document.getElementById('registerForm').style.display = '';
      this.classList.add('bg-orange-600', 'text-white');
      this.classList.remove('bg-orange-100', 'text-orange-600');
      document.getElementById('showLogin').classList.remove('bg-orange-600', 'text-white');
      document.getElementById('showLogin').classList.add('bg-orange-100', 'text-orange-600');
    };
    document.getElementById('toRegister').onclick = function(e) {
      e.preventDefault();
      document.getElementById('showRegister').click();
    };
    document.getElementById('toLogin').onclick = function(e) {
      e.preventDefault();
      document.getElementById('showLogin').click();
    };
  </script>
</body>
</html> 