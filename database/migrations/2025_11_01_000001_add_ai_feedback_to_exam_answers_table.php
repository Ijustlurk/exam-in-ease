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
        Schema::table('exam_answers', function (Blueprint $table) {
            // AI grading feedback and metadata
            $table->text('ai_feedback')->nullable()->after('points_earned');
            $table->tinyInteger('ai_confidence')->nullable()->after('ai_feedback')->comment('AI confidence score 0-100');
            $table->boolean('requires_manual_review')->default(false)->after('ai_confidence')->comment('Flag for low confidence or AI errors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropColumn(['ai_feedback', 'ai_confidence', 'requires_manual_review']);
        });
    }
};
