<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\User;      // Assuming User model for Students/Users    // Assuming Exam model
use App\Models\Subject;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {

        if (Auth::user()->roles[0]->name == "admin") {

            // 2. Fetch Dynamic Data
            $totalExams = Exam::count();

            // Count: Students (Assumes User model has a 'role' column)
            $totalStudents = User::whereHas('roles', function ($query) {
                $query->where('name', 'student');
            })->count();

            // Count: Subjects (Assumes Subject model)
            $totalSubjects = Subject::count();

            // Count: Active Users (Assumes all logged-in users are in the User model, and we count active ones)
            // If 'is_active' column is used in User model:
            $totalActiveUsers = User::count();


            // Fetch recent exams:
            $recentExams = Exam::with('user', 'subject')
                ->orderBy('created_at', 'desc')
                ->limit(5) // Increased limit for better visibility
                ->get();

            // --- PASS DATA TO THE VIEW ---
            $data = [
                'totalExams' => $totalExams,
                'totalStudents' => $totalStudents,
                'totalSubjects' => $totalSubjects,
                'totalActiveUsers' => $totalActiveUsers,
                'recentExams' => $recentExams,
            ];

            return view('admin.dashboard', $data);

        }

        if (Auth::user()->roles[0]->name == "programchair") {
            $totalExams = Exam::count();

            // Count: Students (Assumes User model has a 'role' column)
            $totalStudents = User::whereHas('roles', function ($query) {
                $query->where('name', 'student');
            })->count();

            // Count: Subjects (Assumes Subject model)
            $totalSubjects = Subject::count();

            // Count: Active Users (Assumes all logged-in users are in the User model, and we count active ones)
            // If 'is_active' column is used in User model:
            $totalActiveUsers = User::count();


            // Fetch recent exams:
            $recentExams = Exam::with('user', 'subject')
                ->orderBy('created_at', 'desc')
                ->limit(5) // Increased limit for better visibility
                ->get();

            // --- PASS DATA TO THE VIEW ---
            $data = [
                'totalExams' => $totalExams,
                'totalStudents' => $totalStudents,
                'totalSubjects' => $totalSubjects,
                'totalActiveUsers' => $totalActiveUsers,
                'recentExams' => $recentExams,
            ];

            return view('program-chair.dashboard', $data);

        } else {
            $search = $request->input('search');

            $exams = Exam::with(['user', 'subject'])
                ->where('user_id', Auth::id())
                ->when($search, function ($query, $search) {
                    return $query->where('exam_title', 'like', "%{$search}%")
                        ->orWhere('exam_desc', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Get the first exam for initial display
            $selectedExam = $exams->first();
            return view('instructor.dashboard', compact('exams', 'selectedExam'));
            ;
        }

    }
}
