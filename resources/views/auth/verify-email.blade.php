<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Your Email</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
  <div class="w-full max-w-xl bg-white shadow-2xl rounded-3xl p-10 space-y-6">
    <div class="text-center space-y-3">
      <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 text-orange-600 text-3xl font-bold">
        ✉️
      </div>
      <h1 class="text-3xl font-semibold text-gray-900">Check your inbox</h1>
      <p class="text-gray-600">
        We emailed a verification link to <span class="font-medium text-gray-900">{{ auth()->user()->email }}</span>.
        Please click the link to activate your account.
      </p>
    </div>

    @if (session('status') === 'verification-link-sent')
      <div class="p-4 rounded-2xl bg-green-50 text-green-700 border border-green-200">
        A new verification link has been sent. Please check your email.
      </div>
    @endif

    <div class="space-y-3 text-sm text-gray-600">
      <p>If you do not see the email, check your spam folder or request another link below.</p>
      <p>Once verified, you will automatically gain access to your dashboard.</p>
    </div>

    <form method="POST" action="{{ route('verification.send') }}" class="space-y-3">
      @csrf
      <button type="submit" class="w-full bg-orange-600 hover:bg-red-700 text-white font-semibold py-3 rounded-2xl transition-colors">
        Resend verification email
      </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="w-full text-center text-sm text-gray-500 hover:text-gray-700 underline">
        Wrong email? Sign out and try again
      </button>
    </form>
  </div>
</body>
</html>

