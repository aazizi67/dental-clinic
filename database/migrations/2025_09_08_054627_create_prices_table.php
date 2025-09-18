<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('tooth_number');
            $table->foreignId('treatment_type_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 0);
            $table->timestamps();
            
            $table->unique(['tooth_number', 'treatment_type_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('prices');
    }
};