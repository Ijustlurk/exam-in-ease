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
            $table->text('ai_feedback')->nullable()->after('points_earned');
            $table->decimal('ai_confidence', 5, 2)->nullable()->after('ai_feedback');
            $table->boolean('requires_manual_review')->default(false)->after('ai_confidence');
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
