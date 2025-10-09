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
        //
        
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

        Exam::create([
            'exam_title' => 'Computer Programming 1 Prelim',
            'exam_desc' => 'Exam on programming',
            'subject_id' => 1, // Make sure this subject exists
            'schedule_date' => Carbon::now()->addDays(7),
            'duration' => 90,
            'total_points' => 100,
            'no_of_items' => 50,
            'user_id' => 2, // Make sure this user exists
            'status' => 'draft',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);



    }
}
