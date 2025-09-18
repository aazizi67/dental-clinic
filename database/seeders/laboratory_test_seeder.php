<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\Laboratory;
use App\Models\Patient;
use App\Models\User;
use App\Models\LaboratoryTransaction;
use Carbon\Carbon;

// Setup Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create a test laboratory
$laboratory = Laboratory::create([
    'name' => 'لابراتوار دندانپزشکی تهران',
    'phone' => '02112345678',
    'is_active' => true
]);

// Get a test patient
$patient = Patient::first();
if (!$patient) {
    $patient = Patient::create([
        'first_name' => 'محمد',
        'last_name' => 'احمدی',
        'national_id' => '1234567890',
        'phone' => '09121234567'
    ]);
}

// Get a test doctor
$doctor = User::where('role', 'doctor')->first();
if (!$doctor) {
    $doctor = User::create([
        'name' => 'دکتر رضا محمدی',
        'phone' => '09129876543',
        'role' => 'doctor',
        'password' => bcrypt('password')
    ]);
}

// Create some test transactions
$transactions = [
    [
        'laboratory_id' => $laboratory->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => Carbon::yesterday(),
        'time' => '09:30:00',
        'type' => 'entry',
        'category' => 'crown',
        'description' => 'روکش موقت برای دندان ۲۱',
        'price' => 500000
    ],
    [
        'laboratory_id' => $laboratory->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => Carbon::today(),
        'time' => '14:15:00',
        'type' => 'exit',
        'category' => 'crown',
        'description' => 'دریافت روکش نهایی برای دندان ۲۱',
        'price' => 0
    ]
];

foreach ($transactions as $transactionData) {
    LaboratoryTransaction::create($transactionData);
}

echo "Test laboratory data created successfully\n";