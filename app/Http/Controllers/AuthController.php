<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
        $ethnicities = \App\Models\Ethno::all();
        return view('auth.modern', compact('ethnicities'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('student.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_num' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'ethno_id' => ['required', 'exists:ethno,id'], // Add validation for ethno_id
            'course' => ['nullable', 'string', 'max:150'],
            'course_other' => ['nullable', 'string', 'max:150'],
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
            'ethno_id' => $validated['ethno_id'], // Save ethno_id
            'course' => $course,
        ]);

        return redirect('/auth')->with('success', 'Account created successfully! Please log in.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
} 