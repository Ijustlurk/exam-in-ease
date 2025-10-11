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
        Schema::create('sections', function (Blueprint $table) {
            $table->id('section_id');
            $table->unsignedBigInteger('exam_id');
            $table->string('section_title', 200)->nullable();
            $table->text('section_directions')->nullable();
            $table->integer('section_order')->default(1);

            $table->foreign('exam_id')
                ->references('exam_id')
                ->on('exams')
                ->onDelete('cascade');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
