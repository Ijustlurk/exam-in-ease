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
        Schema::create('exam_approvals', function (Blueprint $table) {
            $table->id('approval_id'); // Primary Key
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('approver_id');
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Optional foreign keys (uncomment if related tables exist)
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
            $table->foreign('approver_id')->references('user_id')->on('user_admin')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_approvals');
    }
};
