<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_enrolment', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('student_id');
            $table->enum('status', ['Active','Archived'])->default('Active');

            // Composite primary key
            $table->primary(['class_id', 'student_id']);

            // Optional FKs
            $table->foreign('class_id')->references('class_id')->on('class')->onDelete('cascade');
            $table->foreign('student_id')->references('user_id')->on('user_student')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_enrolment');
    }
};
