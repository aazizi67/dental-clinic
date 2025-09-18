<?php

require_once 'vendor/autoload.php';

use Gemini\Factory;
use Gemini\Enums\ModelType;

// Function to parse .env file
function getEnvValue($key) {
    $envFile = file_get_contents('.env');
    $lines = explode("\n", $envFile);
    
    foreach ($lines as $line) {
        if (strpos($line, $key) === 0) {
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                return trim($parts[1]);
            }
        }
    }
    
    return null;
}

// Get the API key from the .env file
$apiKey = getEnvValue('GEMINI_API_KEY');

if (!$apiKey) {
    echo "API key not found in .env file\n";
    exit(1);
}

try {
    echo "Testing Gemini API with name: ارسلان\n";
    
    $client = (new Factory())->withApiKey($apiKey)->make();
    $prompt = "Based on the Persian or English name 'ارسلان', determine the gender. " .
              "Respond with ONLY one word: 'male' if the name is typically masculine, " .
              "'female' if the name is typically feminine, or 'unknown' if uncertain. " .
              "Do not include any other text in your response.";
    
    $response = $client->generativeModel(ModelType::GEMINI_FLASH)->generateContent($prompt);
    $result = trim(strtolower($response->text()));
    
    echo "Gemini API response: " . $result . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}