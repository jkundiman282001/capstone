<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminManagementController extends Controller
{
    /**
     * Display a listing of the admin accounts.
     */
    public function index()
    {
        $admins = Staff::latest()->get();
        return view('staff.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin account.
     */
    public function create()
    {
        return view('staff.admins.create');
    }

    /**
     * Store a newly created admin account in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:staff'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin = Staff::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('staff.admins.index')->with('success', 'New admin account created successfully.');
    }

    /**
     * Remove the specified admin account from storage.
     */
    public function destroy($id)
    {
        // Prevent deleting your own account to avoid lockout
        if (auth()->guard('staff')->id() == $id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $admin = Staff::findOrFail($id);
        $admin->delete();

        return redirect()->route('staff.admins.index')->with('success', 'Admin account deleted successfully.');
    }
}
