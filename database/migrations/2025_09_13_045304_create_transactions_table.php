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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique(); // شماره تراکنش
            $table->date('transaction_date'); // تاریخ تراکنش
            $table->enum('type', ['income', 'expense']); // نوع: دریافتی یا پرداختی
            $table->enum('category', ['patient_payment', 'dental_materials', 'equipment', 'laboratory', 'other']); // دسته بندی
            
            // اطلاعات بیمار (اختیاری)
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');
            $table->foreignId('treatment_plan_id')->nullable()->constrained('treatment_plans')->onDelete('set null');
            
            // مبلغ و حساب
            $table->decimal('amount', 15, 2); // مبلغ
            $table->foreignId('account_id')->constrained('chart_of_accounts'); // حساب مربوطه
            
            // نحوه پرداخت
            $table->enum('payment_method', ['cash', 'card', 'pos', 'bank_transfer', 'check']); // نحوه پرداخت
            
            // اطلاعات چک (اختیاری)
            $table->string('check_number')->nullable(); // شماره چک
            $table->date('check_date')->nullable(); // تاریخ چک
            $table->string('check_bank')->nullable(); // بانک صادر کننده
            $table->string('sayad_id')->nullable(); // شناسه صیاد
            $table->enum('check_status', ['received', 'deposited', 'cleared', 'bounced'])->nullable(); // وضعیت چک
            
            // توضیحات
            $table->text('description')->nullable(); // توضیحات
            $table->text('notes')->nullable(); // یادداشت
            
            // وضعیت تراکنش
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            
            // کاربر ثبت کننده
            $table->foreignId('created_by')->constrained('users');
            
            $table->timestamps();
            
            // ایندکس ها
            $table->index(['transaction_date', 'type']);
            $table->index(['patient_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
