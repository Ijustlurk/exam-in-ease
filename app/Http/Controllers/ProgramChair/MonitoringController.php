<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamAssignment;
use App\Models\ExamAttempt; 
use Illuminate\Database\Eloquent\ModelNotFoundException;
class MonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $examId)
    {
        try {
            // 1. Kunin ang Exam details
            $exam = Exam::with('subject')->where('exam_id', $examId)->firstOrFail();

            // 2. Hanapin ang lahat ng Assignments para sa Exam na ito.
            // I-a-assume natin na ang monitoring ay para sa LAHAT ng classes na inassign-an ng exam na ito.
            $assignments = ExamAssignment::where('exam_id', $exam->exam_id)->get();

            if ($assignments->isEmpty()) {
                // Handle case na walang assignment pa ang exam na ito
                return view('admin.monitoring', [
                    'class' => (object) [
                        'title' => $exam->exam_title,
                        'avatar_url' => 'https://cdn-icons-png.flaticon.com/512/847/847969.png',
                    ],
                    'students' => collect(), // Empty collection
                    'warning' => 'This exam has not been assigned to any class yet.',
                ]);
            }

            // 3. Kunin ang LAHAT ng Attempts (unique students) mula sa mga assignments na ito
            $assignmentIds = $assignments->pluck('assignment_id');

            // Kukunin natin ang pinakabagong (latest) attempt ng BAWAT student
            $studentsAttempts = ExamAttempt::whereIn('exam_assignment_id', $assignmentIds)
                // I-load ang user para sa 'name'
                ->with(['user', 'studentDetails'])
                ->orderBy('start_time', 'desc') // Kunin ang pinakabagong attempt
                ->get()
                ->unique('student_id'); // Unique by student_id

            // 4. I-map ang data para sa Blade
            $students = $studentsAttempts->map(function ($attempt) use ($exam) {

                // Student name from the main users table
                $name = $attempt->user->name ?? ($attempt->studentDetails->first_name ?? 'Unknown') . ' ' . ($attempt->studentDetails->last_name ?? 'Student');

                // Class Title from the Exam
                $classTitle = $exam->exam_title ?? 'N/A';

                return (object) [
                    'id' => $attempt->student_id,
                    'name' => $name,
                    'class_name' => $classTitle,
                    'status' => ucwords(str_replace('_', ' ', $attempt->status)), // I-capitalize ang Status
                ];
            });

            // 5. I-pass ang data sa view
            return view('admin.monitoring', [
                'class' => (object) [
                    'title' => $exam->exam_title,
                    // Gamitin ang subject code para sa generic na avatar/display
                    'avatar_url' => 'https://cdn-icons-png.flaticon.com/512/847/847969.png',
                ],
                'students' => $students,
                'warning' => null,
            ]);

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Exam not found.');
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
