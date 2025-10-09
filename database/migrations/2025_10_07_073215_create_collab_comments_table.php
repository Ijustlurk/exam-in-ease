<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collab_comments', function (Blueprint $table) {
            $table->id('comment_id'); // PK
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('question_id')->nullable();
            $table->unsignedBigInteger('teacher_id');
            $table->text('comment_text');
            $table->timestamp('created_at')->useCurrent();
            $table->boolean('resolved')->default(0);

            // Optional FKs
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
            $table->foreign('question_id')->references('item_id')->on('exam_items')->onDelete('set null');
            $table->foreign('teacher_id')->references('user_id')->on('user_teacher')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collab_comments');
    }
};
