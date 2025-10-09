<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_collaboration', function (Blueprint $table) {
            $table->id('collaboration_id'); // PK
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('role', 50);

            // Optional FKs
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
            $table->foreign('teacher_id')->references('user_id')->on('user_teacher')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_collaboration');
    }
};
