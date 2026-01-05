<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCIP-EAP | Reset Password</title>
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
            <h2 class="text-3xl font-black text-slate-900 mb-2">Reset Password</h2>
            <p class="text-sm text-slate-600">Choose a new, strong password for your account.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" required readonly 
                       class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-200 bg-slate-50 outline-none">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">New Password</label>
                <input type="password" name="password" required autofocus placeholder="At least 8 characters"
                       class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-200 outline-none focus:border-orange-500 transition-all @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required placeholder="Repeat your new password"
                       class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-200 outline-none focus:border-orange-500 transition-all">
            </div>

            <button type="submit" class="btn-primary w-full text-white py-4 rounded-xl font-bold text-lg shadow-lg">
                Reset Password
            </button>
        </form>
    </div>
</body>
</html>
