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
        Schema::table('user_teacher', function (Blueprint $table) {
            // Drop foreign key constraint on teacher_assignments first
            DB::statement('ALTER TABLE teacher_assignments DROP FOREIGN KEY teacher_assignments_teacher_id_foreign');
            
            // Drop the current primary key
            $table->dropPrimary('user_id');
            
            // Add new auto-increment id column as primary key
            $table->id()->first();
            
            // Rename user_id to auth_user_id (to reference users.id for authentication)
            $table->renameColumn('user_id', 'auth_user_id');
            
            // Add foreign key constraint to users table
            $table->foreign('auth_user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Recreate foreign key on teacher_assignments to reference the new id column
        Schema::table('teacher_assignments', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('user_teacher')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_assignments', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
        });
        
        Schema::table('user_teacher', function (Blueprint $table) {
            $table->dropForeign(['auth_user_id']);
            $table->dropColumn('id');
            $table->renameColumn('auth_user_id', 'user_id');
            $table->primary('user_id');
        });
        
        Schema::table('teacher_assignments', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('user_id')->on('user_teacher')->onDelete('cascade');
        });
    }
};
