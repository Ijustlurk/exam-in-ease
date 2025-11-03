<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Make subject_id nullable to support draft exams
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['subject_id']);

            // Modify column to be nullable
            $table->unsignedBigInteger('subject_id')->nullable()->change();

            // Re-add foreign key
            $table->foreign('subject_id')
                  ->references('subject_id')
                  ->on('subjects')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['subject_id']);

            // Revert to NOT NULL
            $table->unsignedBigInteger('subject_id')->nullable(false)->change();

            // Re-add foreign key
            $table->foreign('subject_id')
                  ->references('subject_id')
                  ->on('subjects')
                  ->onDelete('cascade');
        });
    }
};
