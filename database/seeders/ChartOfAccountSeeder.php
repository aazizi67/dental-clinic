<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use App\Models\ExpenseCategory;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حساب‌های اصلی - دارایی‌ها
        $assets = ChartOfAccount::create([
            'code' => '1000',
            'name' => 'دارایی‌ها',
            'type' => 'asset',
            'level' => 1,
            'description' => 'کلیه دارایی‌های مطب'
        ]);

        // حساب‌های فرعی دارایی
        ChartOfAccount::create([
            'code' => '1100',
            'name' => 'نقد در صندوق',
            'type' => 'asset',
            'parent_id' => $assets->id,
            'level' => 2,
            'description' => 'وجه نقد موجود در صندوق مطب'
        ]);

        ChartOfAccount::create([
            'code' => '1200',
            'name' => 'بانک',
            'type' => 'asset',
            'parent_id' => $assets->id,
            'level' => 2,
            'description' => 'حساب بانکی مطب'
        ]);

        ChartOfAccount::create([
            'code' => '1300',
            'name' => 'چک‌های دریافتی',
            'type' => 'asset',
            'parent_id' => $assets->id,
            'level' => 2,
            'description' => 'چک‌های دریافتی از بیماران'
        ]);

        ChartOfAccount::create([
            'code' => '1400',
            'name' => 'تجهیزات پزشکی',
            'type' => 'asset',
            'parent_id' => $assets->id,
            'level' => 2,
            'description' => 'تجهیزات و ابزار پزشکی'
        ]);

        // حساب‌های اصلی - درآمدها
        $income = ChartOfAccount::create([
            'code' => '4000',
            'name' => 'درآمدها',
            'type' => 'income',
            'level' => 1,
            'description' => 'کلیه درآمدهای مطب'
        ]);

        ChartOfAccount::create([
            'code' => '4100',
            'name' => 'دریافتی از بیماران',
            'type' => 'income',
            'parent_id' => $income->id,
            'level' => 2,
            'description' => 'دریافتی بابت خدمات درمانی'
        ]);

        // حساب‌های اصلی - هزینه‌ها
        $expenses = ChartOfAccount::create([
            'code' => '5000',
            'name' => 'هزینه‌ها',
            'type' => 'expense',
            'level' => 1,
            'description' => 'کلیه هزینه‌های مطب'
        ]);

        $dentalMaterials = ChartOfAccount::create([
            'code' => '5100',
            'name' => 'مواد دندانی',
            'type' => 'expense',
            'parent_id' => $expenses->id,
            'level' => 2,
            'description' => 'هزینه خرید مواد دندانی'
        ]);

        $equipment = ChartOfAccount::create([
            'code' => '5200',
            'name' => 'هزینه تجهیزات',
            'type' => 'expense',
            'parent_id' => $expenses->id,
            'level' => 2,
            'description' => 'هزینه خرید و تعمیر تجهیزات'
        ]);

        $laboratory = ChartOfAccount::create([
            'code' => '5300',
            'name' => 'هزینه لابراتوار',
            'type' => 'expense',
            'parent_id' => $expenses->id,
            'level' => 2,
            'description' => 'هزینه خدمات لابراتوار'
        ]);

        ChartOfAccount::create([
            'code' => '5400',
            'name' => 'هزینه‌های عمومی',
            'type' => 'expense',
            'parent_id' => $expenses->id,
            'level' => 2,
            'description' => 'هزینه‌های عمومی مطب'
        ]);

        // ایجاد دسته بندی‌های هزینه
        ExpenseCategory::create([
            'name' => 'مواد دندانی',
            'code' => 'DM001',
            'description' => 'هزینه خرید مواد دندانی',
            'account_id' => $dentalMaterials->id
        ]);

        ExpenseCategory::create([
            'name' => 'تجهیزات پزشکی',
            'code' => 'EQ001',
            'description' => 'هزینه خرید تجهیزات',
            'account_id' => $equipment->id
        ]);

        ExpenseCategory::create([
            'name' => 'لابراتوار',
            'code' => 'LAB001',
            'description' => 'هزینه خدمات لابراتوار',
            'account_id' => $laboratory->id
        ]);
    }
}
