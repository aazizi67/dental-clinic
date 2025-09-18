<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // نام دسته بندی
            $table->string('code', 10)->unique(); // کد دسته بندی
            $table->text('description')->nullable(); // توضیحات
            $table->boolean('is_active')->default(true); // فعال یا غیرفعال
            $table->foreignId('account_id')->nullable()->constrained('chart_of_accounts'); // حساب پیش فرض
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
