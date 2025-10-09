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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id('attempt_id'); // Primary Key (INT NOT NULL AUTO_INCREMENT)
            
            // Foreign Keys
            $table->unsignedBigInteger('exam_assignment_id'); 
            $table->unsignedBigInteger('student_id'); 
            
            $table->dateTime('start_time')->useCurrent(); // DEFAULT current_timestamp()
            $table->dateTime('end_time')->nullable();    // DEFAULT NULL
            
            // ENUM Column
            $table->enum('status', ['in_progress', 'submitted'])->default('in_progress');
            
            $table->integer('score')->default(0);
            
            // Adding Foreign Key Constraints (Assuming the tables exist and use 'id' as primary key)
            $table->foreign('exam_assignment_id')->references('assignment_id')->on('exam_assignments')->onDelete('cascade');
            $table->foreign('student_id')->references('user_id')->on('user_student')->onDelete('cascade');

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};