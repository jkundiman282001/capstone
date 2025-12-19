<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BasicInfo;

class LandingController extends Controller
{
    public function index()
    {
        // Get statistics for the landing page
        $maxSlots = \App\Models\Setting::get('max_slots', 120);
        $validatedCount = BasicInfo::where('application_status', 'validated')->count();
        $availableSlots = max(0, $maxSlots - $validatedCount);
        $isFull = $availableSlots === 0;
        
        // Count applicants who have applied (have type_assist filled)
        $applicantsApplied = BasicInfo::whereNotNull('type_assist')->count();
        
        // Count applicants who are approved/validated
        $applicantsApproved = $validatedCount;
        
        // Count pending applications (applied but not yet reviewed/approved)
        $applicantsPending = BasicInfo::whereNotNull('type_assist')
            ->where(function($query) {
                $query->whereNull('application_status')
                      ->orWhere('application_status', 'pending');
            })
            ->count();
        
        // Count distinct IP groups represented
        $ipGroupsRepresented = \App\Models\User::whereHas('basicInfo', function($query) {
                $query->whereNotNull('type_assist');
            })
            ->whereNotNull('ethno_id')
            ->distinct('ethno_id')
            ->count('ethno_id');
        
        // Calculate success rate (approved / applied * 100)
        $successRate = $applicantsApplied > 0 
            ? round(($applicantsApproved / $applicantsApplied) * 100, 1) 
            : 0;
        
        // Count geographic coverage (provinces)
        $provincesCovered = \App\Models\BasicInfo::whereNotNull('type_assist')
            ->whereHas('fullAddress.address', function($query) {
                $query->where('province', '!=', '');
            })
            ->get()
            ->map(function($basicInfo) {
                return optional(optional($basicInfo->fullAddress)->address)->province;
            })
            ->filter()
            ->unique()
            ->count();
        
        $stats = [
            'slotsLeft' => $availableSlots,
            'applicantsApplied' => $applicantsApplied,
            'applicantsApproved' => $applicantsApproved,
            'applicantsPending' => $applicantsPending,
            'ipGroupsRepresented' => $ipGroupsRepresented,
            'successRate' => $successRate,
            'provincesCovered' => $provincesCovered,
            'maxSlots' => $maxSlots,
            'availableSlots' => $availableSlots, // Keep for backward compatibility
            'isFull' => $isFull,
        ];

        return view('landing', compact('stats'));
    }
}

