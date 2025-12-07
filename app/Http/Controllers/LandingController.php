<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BasicInfo;

class LandingController extends Controller
{
    const MAX_SLOTS = 120;

    public function index()
    {
        // Get statistics for the landing page
        $validatedCount = BasicInfo::where('application_status', 'validated')->count();
        $availableSlots = max(0, self::MAX_SLOTS - $validatedCount);
        $isFull = $availableSlots === 0;
        
        $stats = [
            'totalScholars' => $validatedCount,
            'totalApplicants' => BasicInfo::count(),
            'totalPrograms' => \App\Models\User::whereNotNull('course')
                ->select('course')
                ->distinct()
                ->count(),
            'maxSlots' => self::MAX_SLOTS,
            'availableSlots' => $availableSlots,
            'isFull' => $isFull,
        ];

        return view('landing', compact('stats'));
    }
}

