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
        Schema::create('user_program_chair', function (Blueprint $table) {
            $table->id('user_id'); // Primary Key
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('email_address', 150)->unique();
            $table->string('username', 100)->unique();
            $table->string('password_hash', 255);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_program_chair');
    }
};
