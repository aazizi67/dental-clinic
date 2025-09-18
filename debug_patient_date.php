<?php

require 'vendor/autoload.php';

use App\Models\Patient;
use App\Helpers\PersianDateHelper;

// Get a patient from the database
$patient = Patient::first();

if ($patient) {
    echo "Patient ID: " . $patient->id . "\n";
    echo "Created at: " . $patient->created_at . "\n";
    echo "Formatted date: " . $patient->created_at->format('Y-m-d') . "\n";
    
    // Test our helper function
    $formattedDate = $patient->created_at->format('Y-m-d');
    $convertedDate = PersianDateHelper::toPersian($formattedDate);
    echo "Converted date: " . $convertedDate . "\n";
    
    // Test with Verta directly
    $year = (int) $patient->created_at->format('Y');
    $month = (int) $patient->created_at->format('m');
    $day = (int) $patient->created_at->format('d');
    
    $verta = \Hekmatinasser\Verta\Verta::createGregorianDate($year, $month, $day);
    echo "Direct Verta conversion: " . $verta->format('Y/m/d') . "\n";
} else {
    echo "No patients found in database\n";
}