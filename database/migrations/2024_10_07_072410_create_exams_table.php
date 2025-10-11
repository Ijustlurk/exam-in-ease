<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id('exam_id');
            $table->string('exam_title', 200);
            $table->text('exam_desc')->nullable();

            // Foreign keys
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('approved_by')->nullable();

            // Scheduling
            $table->dateTime('schedule_start')->nullable();
            $table->dateTime('schedule_end')->nullable();
            $table->integer('duration')->comment('Duration in minutes');

            // Exam scoring
            $table->integer('total_points')->default(0);
            $table->integer('no_of_items')->default(0);

            // Workflow
            $table->enum('status', ['draft', 'approved', 'ongoing', 'archived'])->default('draft');
            $table->dateTime('approved_date')->nullable();
            $table->text('revision_notes')->nullable();

            // Timestamps
            $table->timestamps();
       

            // Foreign key constraints (adjust table names as needed)
            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('user_id')->on('user_teacher')->onDelete('cascade');
            $table->foreign('approved_by')->references('user_id')->on('user_program_chair')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
