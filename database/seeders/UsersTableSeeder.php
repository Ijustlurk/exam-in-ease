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
            'class_enrolment',
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
        // TEACHER ACCOUNTS
        // ============================================
        $teachers = [
            [
                'name' => 'Teacher 1',
                'email' => 'teacher1@gmail.com',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'middle_name' => 'Santos',
            ],
            [
                'name' => 'Teacher 2',
                'email' => 'teacher2@gmail.com',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'middle_name' => 'Cruz',
            ],
            [
                'name' => 'Teacher 3',
                'email' => 'teacher3@gmail.com',
                'first_name' => 'Jose',
                'last_name' => 'Garcia',
                'middle_name' => 'Reyes',
            ],
        ];

        $teacherUsers = [];
        foreach ($teachers as $teacherData) {
            $teacher = User::create([
                'name' => $teacherData['name'],
                'email' => $teacherData['email'],
                'password' => Hash::make('1234'),
            ]);

            DB::table('user_teacher')->insert([
                'user_id' => $teacher->id,
                'first_name' => $teacherData['first_name'],
                'last_name' => $teacherData['last_name'],
                'middle_name' => $teacherData['middle_name'],
                'email_address' => $teacherData['email'],
                'username' => $teacherData['name'],
                'password_hash' => Hash::make('1234'),
                'status' => 'Active',
            ]);

            $teacher->roles()->attach($instructorRole);
            $teacherUsers[] = $teacher;
        }

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
        // STUDENT ACCOUNTS
        // ============================================
        $students = [
            [
                'name' => 'Student One',
                'email' => 'student1@gmail.com',
                'first_name' => 'Pedro',
                'last_name' => 'Santos',
                'middle_name' => 'Garcia',
                'id_number' => 'STU2025-001',
            ],
        ];

        $studentUsers = [];
        foreach ($students as $studentData) {
            $student = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => Hash::make('1234'),
            ]);

            DB::table('user_student')->insert([
                'user_id' => $student->id,
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'middle_name' => $studentData['middle_name'],
                'email_address' => $studentData['email'],
                'id_number' => $studentData['id_number'],
                'password_hash' => Hash::make('1234'),
                'status' => 'Enrolled',
            ]);

            $student->roles()->attach($studentRole);
            $studentUsers[] = $student;
        }

        // ============================================
        // GET OR CREATE SUBJECTS
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
            // CS101 Classes (Programming Fundamentals) - Year 1, Semester 1
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
            // CS102 Classes (Advanced Programming) - Year 2, Semester 1
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
            // CS201 Class (Data Structures) - Year 2, Semester 2
            [
                'title' => 'Data Structures',
                'subject_id' => $cs201->subject_id,
                'year_level' => 2,
                'section' => 'A',
                'semester' => '2',
                'school_year' => '2024-2025',
                'status' => 'Active',
                'created_at' => now()
            ],
        ];

        $classIds = [];
        foreach ($classes as $class) {
            $insertedId = DB::table('class')->insertGetId($class);
            $classIds[] = $insertedId;
        }

        // ============================================
        // ASSIGN TEACHERS TO CLASSES
        // ============================================
        
        // Teacher 1 (index 0): CS101 sections A, B, C, G, F (class indices 0-4)
        for ($i = 0; $i < 5; $i++) {
            DB::table('teacher_assignments')->insert([
                'class_id' => $classIds[$i],
                'teacher_id' => $teacherUsers[0]->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Teacher 1 (index 0): CS102 sections A, B (class indices 5-6)
        for ($i = 5; $i < 7; $i++) {
            DB::table('teacher_assignments')->insert([
                'class_id' => $classIds[$i],
                'teacher_id' => $teacherUsers[0]->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Teacher 2 (index 1): CS102 Section B (class index 6) - multiple teachers per class
        DB::table('teacher_assignments')->insert([
            'class_id' => $classIds[6],
            'teacher_id' => $teacherUsers[1]->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Teacher 3 (index 2): CS201 Section A (class index 7)
        DB::table('teacher_assignments')->insert([
            'class_id' => $classIds[7],
            'teacher_id' => $teacherUsers[2]->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info('✓ Users seeded: 1 Admin, 3 Teachers, 1 Program Chair, 1 Student');
        $this->command->info('✓ Classes seeded: 8 classes with teacher assignments');
    }
}