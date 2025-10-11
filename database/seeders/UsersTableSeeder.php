<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Subject;
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

        $tables = [
            'role_user', 
            'user_admin', 
            'user_teacher', 
            'user_program_chair', 
            'user_student', 
            'teacher_assignments',
            'class',
            'users'
        ];
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $instructorRole = Role::where('name', 'instructor')->first();
        $programChairRole = Role::where('name', 'programchair')->first();
        $studentRole = Role::where('name', 'student')->first();

        // ============================================
        // ADMIN ACCOUNT
        // ============================================
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

        // ============================================
        // TEACHER ACCOUNT
        // ============================================
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

        // ============================================
        // ADDITIONAL TEACHER ACCOUNTS (for collaboration testing)
        // ============================================
        $teacher2 = User::create([
            'name' => 'Teacher 2',
            'email' => 'teacher2@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        DB::table('user_teacher')->insert([
            'user_id' => $teacher2->id,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'middle_name' => 'Cruz',
            'email_address' => 'teacher2@gmail.com',
            'username' => $teacher2->name,
            'password_hash' => Hash::make('1234'),
            'status' => 'Active',
        ]);

        $teacher2->roles()->attach($instructorRole);

        $teacher3 = User::create([
            'name' => 'Teacher 3',
            'email' => 'teacher3@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        DB::table('user_teacher')->insert([
            'user_id' => $teacher3->id,
            'first_name' => 'Jose',
            'last_name' => 'Garcia',
            'middle_name' => 'Reyes',
            'email_address' => 'teacher3@gmail.com',
            'username' => $teacher3->name,
            'password_hash' => Hash::make('1234'),
            'status' => 'Active',
        ]);

        $teacher3->roles()->attach($instructorRole);

        // ============================================
        // PROGRAM CHAIR ACCOUNT
        // ============================================
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

        // ============================================
        // STUDENT ACCOUNT
        // ============================================
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

        // ============================================
        // GET SUBJECTS (assuming they exist from SubjectsSeeder)
        // ============================================
        $cs101 = Subject::where('subject_code', 'CS101')->first();
        $cs102 = Subject::where('subject_code', 'CS102')->first();
        $cs201 = Subject::where('subject_code', 'CS201')->first();

        // If subjects don't exist, create them
        if (!$cs101) {
            $cs101 = Subject::create([
                'subject_code' => 'CS101',
                'subject_name' => 'Computer Programming 1'
            ]);
        }

        if (!$cs102) {
            $cs102 = Subject::create([
                'subject_code' => 'CS102',
                'subject_name' => 'Computer Programming 2'
            ]);
        }

        if (!$cs201) {
            $cs201 = Subject::create([
                'subject_code' => 'CS201',
                'subject_name' => 'Data Structures and Algorithms'
            ]);
        }

        // ============================================
        // CREATE CLASSES
        // ============================================
        $classes = [
            [
                'title' => 'Programming Fundamentals',
                'subject_id' => $cs101->subject_id,
                'year_level' => 1,
                'section' => 'A',
                'semester' => '1',
                'school_year' => '2024-2025',
                'status' => 'Active',
                'created_at' => now()
            ],
            [
                'title' => 'Programming Fundamentals',
                'subject_id' => $cs101->subject_id,
                'year_level' => 1,
                'section' => 'B',
                'semester' => '1',
                'school_year' => '2024-2025',
                'status' => 'Active',
                'created_at' => now()
            ],
            [
                'title' => 'Programming Fundamentals',
                'subject_id' => $cs101->subject_id,
                'year_level' => 1,
                'section' => 'C',
                'semester' => '1',
                'school_year' => '2024-2025',
                'status' => 'Active',
                'created_at' => now()
            ],
            [
                'title' => 'Programming Fundamentals',
                'subject_id' => $cs101->subject_id,
                'year_level' => 1,
                'section' => 'G',
                'semester' => '1',
                'school_year' => '2024-2025',
                'status' => 'Active',
                'created_at' => now()
            ],
            [
                'title' => 'Programming Fundamentals',
                'subject_id' => $cs101->subject_id,
                'year_level' => 1,
                'section' => 'F',
                'semester' => '1',
                'school_year' => '2024-2025',
                'status' => 'Active',
                'created_at' => now()
            ],
            [
                'title' => 'Advanced Programming',
                'subject_id' => $cs102->subject_id,
                'year_level' => 2,
                'section' => 'A',
                'semester' => '1',
                'school_year' => '2024-2025',
                'status' => 'Active',
                'created_at' => now()
            ],
            [
                'title' => 'Advanced Programming',
                'subject_id' => $cs102->subject_id,
                'year_level' => 2,
                'section' => 'B',
                'semester' => '1',
                'school_year' => '2024-2025',
                'status' => 'Active',
                'created_at' => now()
            ],
            [
                'title' => 'Data Structures',
                'subject_id' => $cs201->subject_id,
                'year_level' => 2,
                'section' => 'A',
                'semester' => '2',
                'school_year' => '2024-2025',
                'status' => 'Active',
                'created_at' => now()
            ]
        ];

        $classIds = [];
        foreach ($classes as $class) {
            $insertedId = DB::table('class')->insertGetId($class);
            $classIds[] = $insertedId;
        }

        // ============================================
        // ASSIGN TEACHER TO CLASSES
        // ============================================
        
        // Assign Teacher 1 to CS101 classes (sections A, B, C, G, F)
        foreach (array_slice($classIds, 0, 5) as $classId) {
            DB::table('teacher_assignments')->insert([
                'class_id' => $classId,
                'teacher_id' => $teacher->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Assign Teacher 1 to CS102 classes (sections A, B)
        foreach (array_slice($classIds, 5, 2) as $classId) {
            DB::table('teacher_assignments')->insert([
                'class_id' => $classId,
                'teacher_id' => $teacher->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Assign Teacher 2 to CS102 Section B (for testing multiple teachers)
        if (isset($classIds[6])) {
            DB::table('teacher_assignments')->insert([
                'class_id' => $classIds[6],
                'teacher_id' => $teacher2->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Assign Teacher 3 to CS201 Section A
        if (isset($classIds[7])) {
            DB::table('teacher_assignments')->insert([
                'class_id' => $classIds[7],
                'teacher_id' => $teacher3->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}