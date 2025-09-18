<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 15);
            $table->string('national_code', 10)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact', 15)->nullable();
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable();
            $table->enum('insurance_type', ['none', 'social', 'health', 'supplementary'])->default('none');
            $table->string('insurance_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('phone');
            $table->index('national_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
};