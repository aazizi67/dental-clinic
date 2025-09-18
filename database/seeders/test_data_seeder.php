<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\Staff;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

// Setup Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create a test staff member
$staff = Staff::create([
    'first_name' => 'علی',
    'last_name' => 'رضایی',
    'phone' => '09123456789',
    'role' => 'doctor',
    'hourly_rate' => 50000,
    'is_active' => true
]);

// Create some test attendance records
$dates = [
    Carbon::yesterday()->subDays(2),
    Carbon::yesterday()->subDays(1),
    Carbon::yesterday(),
];

foreach ($dates as $date) {
    AttendanceRecord::create([
        'staff_id' => $staff->id,
        'date' => $date,
        'check_in_time' => '08:00:00',
        'check_out_time' => '16:00:00',
        'check_in_method' => 'manual',
        'check_out_method' => 'manual'
    ]);
}

echo "Test data created successfully\n";