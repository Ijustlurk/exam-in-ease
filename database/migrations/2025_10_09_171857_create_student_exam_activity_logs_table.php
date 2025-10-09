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
        Schema::create('student_exam_activity_logs', function (Blueprint $table) {
            $table->id('activity_id'); // primary key auto-increment
            $table->unsignedBigInteger('attempt_id'); // FK to exam attempts
            $table->enum('event_type', ['TAB_OUT', 'IDLE']);
            $table->timestamp('timestamp')->useCurrent(); // default current_timestamp

            $table->foreign('attempt_id')->references('attempt_id')->on('exam_attempts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_exam_activity_logs');
    }
};
