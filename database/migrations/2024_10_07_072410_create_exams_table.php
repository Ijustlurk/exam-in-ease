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
        Schema::create('exams', function (Blueprint $table) {
            $table->id('exam_id'); // Primary Key
            $table->string('exam_title', 200);
            $table->text('exam_desc')->nullable();
            $table->unsignedBigInteger('subject_id');
            $table->dateTime('schedule_date')->nullable();
            $table->integer('duration'); // in minutes
            $table->integer('total_points')->default(0);
            $table->integer('no_of_items')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate(); // Added this
            $table->enum('status', ['draft','approved','ongoing','archived'])->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_date')->nullable();
            $table->text('revision_notes')->nullable();

            // Foreign keys
            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('user_teacher')->onDelete('cascade');
            $table->foreign('approved_by')->references('user_id')->on('user_admin')->onDelete('set null');
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