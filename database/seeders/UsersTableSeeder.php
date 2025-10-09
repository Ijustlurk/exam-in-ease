<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate all relevant tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = ['role_user', 'user_admin', 'user_teacher', 'user_program_chair', 'user_student', 'users'];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $instructorRole = Role::where('name', 'instructor')->first();
        $programChairRole = Role::where('name', 'programchair')->first();
        $studentRole = Role::where('name', 'student')->first(); // ✅ add student role

        // --- Admin Account ---
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        DB::table('user_admin')->insert([
            'user_id' => $admin->id,
            'username' => $admin->name,
            'password_hash' => Hash::make('1234'),
        ]);

        $admin->roles()->attach($adminRole);

        // --- Teacher Account ---
        $teacher = User::create([
            'name' => 'Teacher 1',
            'email' => 'teacher1@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        DB::table('user_teacher')->insert([
            'user_id' => $teacher->id,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'middle_name' => 'Santos',
            'email_address' => 'teacher1@gmail.com',
            'username' => $teacher->name,
            'password_hash' => Hash::make('1234'),
            'status' => 'Active',
        ]);

        $teacher->roles()->attach($instructorRole);

        // --- Program Chair Account ---
        $chair = User::create([
            'name' => 'Program Chair 1',
            'email' => 'programchair1@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        DB::table('user_program_chair')->insert([
            'user_id' => $chair->id,
            'first_name' => 'Maria',
            'last_name' => 'Reyes',
            'middle_name' => 'Lopez',
            'email_address' => 'programchair1@gmail.com',
            'username' => $chair->name,
            'password_hash' => Hash::make('1234'),
            'status' => 'Active',
        ]);

        $chair->roles()->attach($programChairRole);

        // --- Student Account ✅ ---
        $student = User::create([
            'name' => 'Student One',
            'email' => 'student1@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        DB::table('user_student')->insert([
            'user_id' => $student->id,
            'first_name' => 'Pedro',
            'last_name' => 'Santos',
            'middle_name' => 'Garcia',
            'email_address' => 'student1@gmail.com',
            'id_number' => 'STU2025-001',
            'password_hash' => Hash::make('1234'),
            'status' => 'Enrolled',
        ]);

        $student->roles()->attach($studentRole);
    }
}
