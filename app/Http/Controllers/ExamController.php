<?php

namespace App\Http\Controllers;
use App\Models\Exam; 
use Illuminate\Http\Request;

class ExamController extends Controller
{
    //
    public function index(){
        return view("admin.examinies");
    }
     public function statistics(Exam $exam)
    {
        // load related data
        $exam->load(['subject','questions.submittedAnswers']);

        // number of students who took the exam
        $studentsCount = $exam->submittedAnswers()->distinct('student_id')->count('student_id');

        // total items
        $totalItems = $exam->questions()->count();

        // average score per student
        $averageScore = $exam->submittedAnswers()
                            ->groupBy('student_id')
                            ->selectRaw('avg(score) as avg_score')
                            ->pluck('avg_score')
                            ->avg(); // average of averages

        return view('exams.statistics', compact('exam','studentsCount','totalItems','averageScore'));
    }
}
