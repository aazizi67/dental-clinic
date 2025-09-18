<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // دکتر (ادمین)
        User::create([
            'name' => 'دکتر علی عزیزی',
            'email' => 'doctor@draliazizi.net',
            'phone' => '09121234567',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        // منشی
        User::create([
            'name' => 'خانم احمدی',
            'email' => 'secretary@draliazizi.net', 
            'phone' => '09129876543',
            'password' => Hash::make('password123'),
            'role' => 'secretary',
            'is_active' => true,
        ]);

        // دستیار
        User::create([
            'name' => 'آقای محمدی',
            'phone' => '09125555555',
            'password' => Hash::make('password123'),
            'role' => 'assistant',
            'is_active' => true,
        ]);
    }
}