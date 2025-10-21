<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum column to include 'for approval' status
        DB::statement("ALTER TABLE `exams` MODIFY COLUMN `status` ENUM('draft', 'for approval', 'approved', 'ongoing', 'archived') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        // First update any 'for approval' status to 'draft'
        DB::statement("UPDATE `exams` SET `status` = 'draft' WHERE `status` = 'for approval'");
        
        // Then modify the column back to original enum
        DB::statement("ALTER TABLE `exams` MODIFY COLUMN `status` ENUM('draft', 'approved', 'ongoing', 'archived') DEFAULT 'draft'");
    }
};
