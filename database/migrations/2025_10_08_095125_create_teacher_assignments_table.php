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
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id('teacher_assignment_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('teacher_id');
            $table->timestamps(); // Optional: adds created_at and updated_at

            // Foreign key constraints (uncomment if you have these tables)
             $table->foreign('class_id')->references('class_id')->on('class')->onDelete('cascade');
             $table->foreign('teacher_id')->references('user_id')->on('user_teacher')->onDelete('cascade');
            
 
            
            // Unique constraint to prevent duplicate assignments
            $table->unique(['class_id', 'teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_assignments');
    }
};