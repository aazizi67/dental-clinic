<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Gemini\Factory;
use Gemini\Enums\ModelType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeminiGenderController extends Controller
{
    protected $geminiClient;

    public function __construct()
    {
        $apiKey = env('GEMINI_API_KEY');
        if ($apiKey) {
            $this->geminiClient = (new Factory())->withApiKey($apiKey)->make();
        }
    }

    public function detectGender(Request $request)
    {
        $firstName = $request->input('first_name');
        
        // Validate input
        if (empty($firstName) || strlen($firstName) < 2) {
            return response()->json(['gender' => null]);
        }
        
        // Return null if no API key is configured
        if (!$this->geminiClient) {
            return response()->json(['gender' => null]);
        }
        
        try {
            // Prepare the prompt for Gemini
            $prompt = "Based on the Persian or English name '{$firstName}', determine the gender. " .
                     "Respond with ONLY one word: 'male' if the name is typically masculine, " .
                     "'female' if the name is typically feminine, or 'unknown' if uncertain. " .
                     "Do not include any other text in your response.";
            
            // Send request to Gemini using the correct model
            $response = $this->geminiClient->generativeModel(ModelType::GEMINI_FLASH)->generateContent($prompt);
            $result = trim(strtolower($response->text()));
            
            // Map the response to our expected values
            if ($result === 'male') {
                return response()->json(['gender' => 'male']);
            } elseif ($result === 'female') {
                return response()->json(['gender' => 'female']);
            } else {
                return response()->json(['gender' => null]);
            }
        } catch (\Exception $e) {
            // Log the error for debugging but don't expose it to the user
            Log::error('Gemini API error: ' . $e->getMessage());
            return response()->json(['gender' => null]);
        }
    }
}