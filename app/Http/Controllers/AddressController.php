<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function barangaysByMunicipality(Request $request)
    {
        $municipality = $request->input('municipality');
        $barangays = Address::where('municipality', $municipality)
            ->where('barangay', '!=', '')
            ->orderBy('barangay')
            ->pluck('barangay');
        return response()->json($barangays);
    }

    public function municipalitiesByProvince(Request $request)
    {
        $province = $request->input('province');
        $municipalities = Address::where('province', $province)
            ->where('municipality', '!=', '')
            ->orderBy('municipality')
            ->pluck('municipality');
        return response()->json($municipalities);
    }
}
