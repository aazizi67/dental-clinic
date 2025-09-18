<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('treatment_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 0);
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('payment_date')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'payment_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};