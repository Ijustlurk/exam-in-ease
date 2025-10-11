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
        Schema::create('class', function (Blueprint $table) {
            $table->id('class_id');
            $table->string('title', 100);
            $table->unsignedBigInteger('subject_id');
            $table->unsignedTinyInteger('year_level')->nullable(); // 1-4 restriction
            $table->string('section', 10);
            $table->enum('semester', ['1', '2']);
            $table->string('school_year', 20);
            $table->timestamp('created_at')->useCurrent();
            $table->enum('status', ['Active', 'Archived'])->default('Active');

            // Optional: foreign key to subjects table
            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('cascade');
        });

        // Add check constraint for year_level (works on MySQL 8+)
        DB::statement("ALTER TABLE class ADD CONSTRAINT chk_year_level CHECK (year_level BETWEEN 1 AND 4)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class');
    }
};
