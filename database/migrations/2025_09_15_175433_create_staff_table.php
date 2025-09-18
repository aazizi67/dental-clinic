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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('national_id', 10)->unique()->nullable(); // National ID (کد ملی)
            $table->string('phone', 15);
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 15)->nullable();
            $table->text('address')->nullable();
            $table->enum('role', ['doctor', 'secretary', 'assistant', 'nurse', 'cleaner', 'other'])->default('other');
            $table->decimal('hourly_rate', 10, 2)->default(0); // Hourly wage for salary calculation
            $table->date('hire_date')->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('phone');
            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};