<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffAuthController extends Controller
{
    public function showForm()
    {
        return view('auth.staff-modern');
    }

    public function login(Request $request)
    {
        $request->validate([
            'access_code' => ['required', 'string'],
        ]);

        $staff = Staff::where('access_code', $request->access_code)->first();

        if ($staff) {
            Auth::guard('staff')->login($staff);
            $request->session()->regenerate();

            return redirect()->route('staff.dashboard');
        }

        return back()->withErrors(['access_code' => 'Invalid access code'])->withInput();
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

        Auth::guard('staff')->login($staff);
        $request->session()->regenerate();

        return redirect()->route('staff.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login');
    }
}
