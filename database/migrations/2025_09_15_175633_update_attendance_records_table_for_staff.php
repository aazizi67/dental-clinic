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
        Schema::table('attendance_records', function (Blueprint $table) {
            // Remove the foreign key constraint to users table
            $table->dropForeign(['user_id']);
            
            // Rename user_id to staff_id and update the foreign key
            $table->renameColumn('user_id', 'staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            // Remove the foreign key constraint to staff table
            $table->dropForeign(['staff_id']);
            
            // Rename staff_id back to user_id and update the foreign key
            $table->renameColumn('staff_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};