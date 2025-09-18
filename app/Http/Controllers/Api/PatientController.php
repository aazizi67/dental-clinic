<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\GenderDetector;

class PatientController extends Controller
{
    /**
     * Detect gender based on first name
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detectGender(Request $request)
    {
        // Since this is an API endpoint, we'll disable CSRF for it
        // by not checking the token and removing it if present
        
        // Remove CSRF token from request if present
        $request->request->remove('_token');
        
        $request->validate([
            'first_name' => 'required|string|max:100'
        ]);
        
        $gender = GenderDetector::detectGender($request->first_name);
        
        return response()->json([
            'gender' => $gender
        ]);
    }
}