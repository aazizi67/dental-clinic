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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // کد حساب
            $table->string('name'); // نام حساب
            $table->enum('type', ['asset', 'liability', 'equity', 'income', 'expense']); // نوع حساب
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade'); // حساب والد
            $table->text('description')->nullable(); // توضیحات
            $table->boolean('is_active')->default(true); // فعال یا غیرفعال
            $table->integer('level')->default(1); // سطح حساب
            $table->decimal('balance', 15, 2)->default(0); // مانده حساب
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
