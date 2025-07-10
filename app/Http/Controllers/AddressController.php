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
}
