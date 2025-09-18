<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TreatmentType;

class TreatmentTypeSeeder extends Seeder
{
    public function run()
    {
        $treatmentTypes = [
            [
                'name' => 'جراحی نسج نرم',
                'category' => 'جراحی',
                'default_price' => 750000,
                'description' => 'انجام جراحی روی بافت‌های نرم دهان و لثه'
            ],
            [
                'name' => 'جراحی نسج سخت',
                'category' => 'جراحی',
                'default_price' => 1400000,
                'description' => 'جراحی استخوان و بافت‌های سخت'
            ],
            [
                'name' => 'کشیدن',
                'category' => 'جراحی',
                'default_price' => 400000,
                'description' => 'خارج کردن دندان'
            ],
            [
                'name' => 'درمان ریشه',
                'category' => 'اندودنتیکس',
                'default_price' => 2250000,
                'description' => 'درمان عصب و ریشه دندان'
            ],
            [
                'name' => 'ترمیم',
                'category' => 'ترمیمی',
                'default_price' => 700000,
                'description' => 'ترمیم دندان با مواد مختلف'
            ],
            [
                'name' => 'پست',
                'category' => 'پروتز ثابت',
                'default_price' => 1100000,
                'description' => 'نصب پست در ریشه دندان'
            ],
            [
                'name' => 'روکش',
                'category' => 'پروتز ثابت',
                'default_price' => 2750000,
                'description' => 'نصب روکش دندان'
            ],
            [
                'name' => 'پروتز پارسیل متحرک',
                'category' => 'پروتز متحرک',
                'default_price' => 8000000,
                'description' => 'ساخت پروتز متحرک قسمتی'
            ],
            [
                'name' => 'جراحی افزایش طول تاج',
                'category' => 'جراحی',
                'default_price' => 1200000,
                'description' => 'جراحی برای افزایش طول قابل مشاهده تاج دندان'
            ],
            [
                'name' => 'ایمپلنت',
                'category' => 'ایمپلنت',
                'default_price' => 15000000,
                'description' => 'کاشت ایمپلنت دندان'
            ],
            [
                'name' => 'بلیچینگ',
                'category' => 'زیبایی',
                'default_price' => 3000000,
                'description' => 'سفید کردن دندان‌ها'
            ],
            [
                'name' => 'لمینت',
                'category' => 'زیبایی',
                'default_price' => 4000000,
                'description' => 'نصب لمینت سرامیکی'
            ]
        ];

        foreach ($treatmentTypes as $type) {
            TreatmentType::create($type);
        }
    }
}