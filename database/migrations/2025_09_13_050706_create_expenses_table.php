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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // دسته‌بندی هزینه
            $table->text('description'); // شرح هزینه
            $table->decimal('amount', 15, 2); // مبلغ
            $table->enum('payment_method', ['cash', 'card', 'check', 'bank_transfer']); // نحوه پرداخت
            $table->enum('status', ['paid', 'pending', 'cancelled'])->default('paid'); // وضعیت
            
            // اطلاعات چک (در صورت انتخاب پرداخت با چک)
            $table->string('check_number')->nullable(); // شماره چک
            $table->date('check_date')->nullable(); // تاریخ چک
            $table->string('sayad_id')->nullable(); // شناسه صیاد
            
            $table->text('notes')->nullable(); // یادداشت
            $table->foreignId('created_by')->constrained('users'); // کاربر ثبت کننده
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
