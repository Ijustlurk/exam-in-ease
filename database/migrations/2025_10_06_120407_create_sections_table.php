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
            $table->id('section_id'); // int(11) primary key auto_increment
            $table->unsignedBigInteger('exam_id'); // foreign key to exams table
 //ADD COLUMN section_title VARCHAR(200) AFTER exam_id,
// ADD COLUMN section_directions TEXT AFTER section_title,
// ADD COLUMN section_order INT NOT NULL DEFAULT 1 AFTER section_directions;
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');

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
