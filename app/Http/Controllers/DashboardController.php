<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\User;
use App\Models\Subject;
use App\Models\ClassModel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userRole = Auth::user()->roles[0]->name;

        // ADMIN DASHBOARD
        if ($userRole == "admin") {
            $totalExams = Exam::count();
            $totalStudents = User::whereHas('roles', function ($query) {
                $query->where('name', 'student');
            })->count();
            $totalSubjects = Subject::count();
            $totalActiveUsers = User::count();
            $recentExams = Exam::with('user', 'subject')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $data = [
                'totalExams' => $totalExams,
                'totalStudents' => $totalStudents,
                'totalSubjects' => $totalSubjects,
                'totalActiveUsers' => $totalActiveUsers,
                'recentExams' => $recentExams,
            ];

            return view('admin.dashboard', $data);
        }

        // PROGRAM CHAIR DASHBOARD
        if ($userRole == "programchair") {
            $totalExams = Exam::count();
            $totalStudents = User::whereHas('roles', function ($query) {
                $query->where('name', 'student');
            })->count();
            $totalSubjects = Subject::count();
            $totalActiveUsers = User::count();
            $recentExams = Exam::with('user', 'subject')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $data = [
                'totalExams' => $totalExams,
                'totalStudents' => $totalStudents,
                'totalSubjects' => $totalSubjects,
                'totalActiveUsers' => $totalActiveUsers,
                'recentExams' => $recentExams,
            ];

            return view('program-chair.dashboard', $data);
        }

        // INSTRUCTOR DASHBOARD
        if ($userRole == "instructor") {
            $search = $request->input('search');
            $teacherId = Auth::id();

            // Get exams created by OR collaborated on by the current teacher
            $exams = Exam::with(['user', 'subject'])
                ->where(function($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId)
                          ->orWhereHas('collaborations', function($q) use ($teacherId) {
                              $q->where('teacher_id', $teacherId);
                          });
                })
                ->when($search, function ($query, $search) {
                    return $query->where('exam_title', 'like', "%{$search}%")
                        ->orWhere('exam_desc', 'like', "%{$search}%");
                })
                ->orderBy('updated_at', 'desc')
                ->get();

            // Get the first exam for initial display
            $selectedExam = $exams->first();
            
            // Format created_at for selected exam if it exists
            if ($selectedExam) {
                $selectedExam->formatted_created_at = $selectedExam->created_at->format('F j, Y');
            }

            // Get all subjects for the "New Exam" modal
            $subjects = Subject::all();

            // Get classes assigned to this teacher for the "New Exam" modal
            $classes = ClassModel::whereHas('teacherAssignments', function($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId);
                })
                ->where('status', 'Active')
                ->with('subject')
                ->get();

            return view('instructor.dashboard', compact('exams', 'selectedExam', 'subjects', 'classes'));
        }

        // DEFAULT: STUDENT OR OTHER ROLES
        return redirect()->route('login')->with('error', 'Unauthorized access');
    }
}