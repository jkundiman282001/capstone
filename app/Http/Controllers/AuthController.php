<?php

namespace App\Http\Controllers;

use App\Models\BasicInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showFlipForm()
    {
        $ethnicities = \App\Models\Ethno::orderBy('ethnicity', 'asc')->get();
        
        // Move "Other" or "Others" to the end of the collection
        $others = $ethnicities->filter(function($e) {
            $name = strtolower($e->ethnicity);
            return $name === 'other' || $name === 'others';
        });
        
        $ethnicities = $ethnicities->reject(function($e) {
            $name = strtolower($e->ethnicity);
            return $name === 'other' || $name === 'others';
        })->concat($others);

        return view('auth.modern', compact('ethnicities'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validateWithBag('login', [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user && ! $user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // Redirect to apply if not yet applied, otherwise dashboard
            if (! BasicInfo::where('user_id', $user->id)->exists()) {
                return redirect()->route('student.apply');
            }

            return redirect()->route('student.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ], 'login');
    }

    public function register(Request $request)
    {
        $validated = $request->validateWithBag('register', [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_num' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'ethno_id' => ['required', 'exists:ethno,id'],
            'course' => ['nullable', 'string', 'max:150'],
            'course_other' => ['nullable', 'string', 'max:150'],
            'terms_accepted' => ['required', 'accepted'],
        ], [
            'terms_accepted.required' => 'You must accept the Terms and Conditions and Privacy Policy to create an account.',
            'terms_accepted.accepted' => 'You must accept the Terms and Conditions and Privacy Policy to create an account.',
        ]);

        $course = $request->input('course');
        if ($course === 'Other') {
            $course = trim((string) $request->input('course_other')) ?: null;
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'contact_num' => $validated['contact_num'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'ethno_id' => $validated['ethno_id'],
            'course' => $course,
        ]);

        Auth::login($user);
        $user->sendEmailVerificationNotification();

        // Notify the student
        $user->notify(new \App\Notifications\TransactionNotification(
            'general',
            'Welcome to the Scholarship System!',
            'Welcome to the Scholarship Management System! Thank you for registering. Please proceed to complete your application profile to begin your scholarship journey.',
            'normal'
        ));

        return redirect()->route('verification.notice')->with('status', 'verification-link-sent');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Password Reset Methods
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // We use the built-in Password broker
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
