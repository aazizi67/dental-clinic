<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('treatment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_plan_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('tooth_number');
            $table->foreignId('treatment_type_id')->constrained()->onDelete('cascade');
            $table->string('treatment_type');
            $table->decimal('cost', 10, 0);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('tooth_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('treatment_items');
    }
};