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
        Schema::create('user_student', function (Blueprint $table) {
            $table->id('user_id'); 
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('email_address', 150)->unique();
            $table->string('id_number', 50)->unique();
            $table->string('password_hash', 255);
            $table->enum('status', ['Enrolled', 'Graduated', 'Archived'])->default('Enrolled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_student');
    }
};
