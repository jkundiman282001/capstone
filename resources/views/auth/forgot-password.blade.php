<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCIP-EAP | Forgot Password</title>
    <link rel="icon" type="image/png" href="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.85) 0%, rgba(220, 38, 38, 0.75) 50%, rgba(101, 67, 33, 0.80) 100%);
            z-index: 1;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            z-index: 10;
        }
        .btn-primary {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            transition: all 0.4s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(249, 115, 22, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-100 relative overflow-hidden">
    <div class="hero-overlay"></div>
    
    <div class="glass-card rounded-3xl p-8 md:p-10 w-full max-w-md relative">
        <div class="text-center mb-8">
            <img src="{{ asset('images/National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" alt="NCIP Logo" class="h-20 w-20 mx-auto mb-4">
            <h2 class="text-3xl font-black text-slate-900 mb-2">Forgot Password?</h2>
            <p class="text-sm text-slate-600">No worries! Just enter your email and we'll send you a reset link.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-xl">
                <p class="text-sm font-semibold text-emerald-700">{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-envelope text-slate-400"></i>
                    </div>
                    <input type="email" name="email" required autofocus placeholder="Enter your registered email" 
                           class="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-slate-200 outline-none focus:border-orange-500 transition-all @error('email') border-red-500 @enderror">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full text-white py-4 rounded-xl font-bold text-lg shadow-lg">
                Send Reset Link
            </button>

            <div class="text-center mt-6">
                <a href="{{ url('/auth') }}" class="text-sm font-bold text-orange-600 hover:text-orange-700 transition-colors">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back to Login
                </a>
            </div>
        </form>
    </div>
</body>
</html>
