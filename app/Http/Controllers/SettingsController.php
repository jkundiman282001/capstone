<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $maxSlots = Setting::get('max_slots', 120);

        // Get all student users for the deletion management
        $applicants = User::orderBy('last_name')
            ->get();

        return view('staff.settings', compact('maxSlots', 'applicants'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'max_slots' => ['required', 'integer', 'min:1'],
        ]);

        Setting::set('max_slots', $validated['max_slots']);

        return redirect()->route('staff.settings')->with('success', 'Settings updated successfully!');
    }
}
