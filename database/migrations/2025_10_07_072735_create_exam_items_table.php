<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_items', function (Blueprint $table) {
            $table->id('item_id'); // PK
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('exam_section_id')->nullable();
            $table->text('question');
            $table->enum('item_type', ['mcq','torf','enum','iden','essay']);
            $table->text('expected_answer')->nullable();
            $table->text('options')->nullable();
            $table->text('answer')->nullable();
            $table->integer('points_awarded');
            $table->integer('order');

            // Optional FKs
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
            $table->foreign('exam_section_id')->references('section_id')->on('sections')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_items');
    }
};
