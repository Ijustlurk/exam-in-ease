<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert sample subjects
        $subjects = [
            [
                'subject_code' => 'CS101',
                'subject_name' => 'Computer Programming 1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'subject_code' => 'CS102',
                'subject_name' => 'Computer Programming 2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'subject_code' => 'CS201',
                'subject_name' => 'Data Structures and Algorithms',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('subjects')->insert($subjects);

        // Insert a sample exam
        Exam::create([
            'exam_title'   => 'Computer Programming 1 Prelim',
            'exam_desc'    => 'Exam on programming basics',
            'subject_id'   => 1, // Make sure this subject exists
            'schedule_start' => Carbon::now()->addDays(7)->setTime(9, 0, 0),  // Start at 9:00 AM, 7 days later
            'schedule_end'   => Carbon::now()->addDays(7)->setTime(11, 0, 0), // End at 11:00 AM
            'duration'     => 120, // minutes
            'total_points' => 100,
            'no_of_items'  => 50,
            'teacher_id'   => 2, // Make sure this teacher exists in users/teachers table
            'status'       => 'draft',
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),
        ]);
    }
}
