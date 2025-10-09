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
        Schema::create('exam_assignments', function (Blueprint $table) {
            $table->id('assignment_id'); // Primary Key
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('exam_id');

            // Optional foreign keys
             $table->foreign('class_id')->references('class_id')->on('class')->onDelete('cascade');
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_assignments');
    }
};
