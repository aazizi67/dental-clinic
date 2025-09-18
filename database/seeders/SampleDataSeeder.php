<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // بیماران نمونه
        Patient::create([
            'first_name' => 'محمد',
            'last_name' => 'محمدی',
            'phone' => '09123456789',
            'national_code' => '0123456789',
            'gender' => 'male',
            'address' => 'تهران، خیابان ولیعصر',
        ]);

        Patient::create([
            'first_name' => 'فاطمه',
            'last_name' => 'احمدی',
            'phone' => '09987654321',
            'national_code' => '9876543210',
            'gender' => 'female',
            'address' => 'تهران، خیابان انقلاب',
        ]);
    }
}