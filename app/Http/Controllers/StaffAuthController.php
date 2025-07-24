<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;

class StaffAuthController extends Controller
{
    public function showForm()
    {
        return view('auth.staff-modern');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $staff = Staff::where('email', $credentials['email'])->first();
        if ($staff && Hash::check($credentials['password'], $staff->password)) {
            // Login staff (manual session)
            $request->session()->put('staff_id', $staff->id);
            $request->session()->put('staff_name', $staff->first_name . ' ' . $staff->last_name);
            return redirect()->route('staff.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:staff,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $staff = Staff::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Auto-login after registration
        $request->session()->put('staff_id', $staff->id);
        $request->session()->put('staff_name', $staff->first_name . ' ' . $staff->last_name);
        return redirect()->route('staff.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['staff_id', 'staff_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('staff.login');
    }
} 