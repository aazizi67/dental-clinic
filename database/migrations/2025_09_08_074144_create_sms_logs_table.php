<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 15);
            $table->text('message');
            $table->enum('type', ['verification', 'appointment', 'reminder', 'marketing'])->default('reminder');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            $table->index('phone');
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_logs');
    }
};