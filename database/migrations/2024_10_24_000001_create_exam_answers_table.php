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
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id('answer_id'); // Primary Key
            
            // Foreign Keys
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('item_id'); // References exam_items
            
            // Student's answer (store as TEXT to handle different answer types)
            $table->text('answer_text')->nullable();
            
            // For grading
            $table->boolean('is_correct')->nullable(); // null for essay/enumeration (manual grading)
            $table->decimal('points_earned', 5, 2)->default(0);
            
            // Timestamps
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('attempt_id')->references('attempt_id')->on('exam_attempts')->onDelete('cascade');
            $table->foreign('item_id')->references('item_id')->on('exam_items')->onDelete('cascade');
            
            // Unique constraint - one answer per item per attempt
            $table->unique(['attempt_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
