<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;

class ExamStatisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teacherId = Auth::id();
        
        $query = Exam::with(['subject', 'examAssignments.class'])
            ->where('teacher_id', $teacherId)
            ->where('status', 'approved'); // Only show approved exams

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('exam_title', 'like', "%{$search}%")
                  ->orWhereHas('subject', function($subQuery) use ($search) {
                      $subQuery->where('subject_name', 'like', "%{$search}%");
                  });
            });
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(10);

        // Add additional data to each exam
        foreach ($exams as $exam) {
            // Get teacher name (current user)
            $teacher = Auth::user();
            $exam->teacher_name = $teacher->first_name . ' ' . $teacher->last_name;
            
            // Get all class IDs assigned to this exam and count unique ones
            $classIds = $exam->examAssignments->pluck('class_id')->unique()->filter();
            $exam->classes_count = $classIds->count();
            
            // Get the actual class display names (Year-Section Subject)
            $classTitles = $exam->examAssignments
                ->whereNotNull('class')
                ->map(function($assignment) {
                    $class = $assignment->class;
                    return ($class->year_level ?? '') . '-' . $class->section . ' ' . $class->title;
                })
                ->unique()
                ->sort()
                ->values();
            
            $exam->classes_list = $classTitles->count() > 0 ? $classTitles->implode(', ') : 'No classes assigned';
            
            // Map status display
            $exam->status_display = match($exam->status) {
                'draft' => 'Draft',
                'approved' => 'Approved',
                'ongoing' => 'Ongoing',
                'archived' => 'Archived',
                default => ucfirst($exam->status)
            };
        }

        return view('instructor.exam-statistics.index', compact('exams'));
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
    public function show($id)
    {
        $exam = Exam::with(['subject', 'examItems', 'examAssignments.class'])
            ->where('exam_id', $id)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();
        
        // Get total items and points from exam table
        $totalItems = $exam->no_of_items;
        $totalPoints = $exam->total_points;
        
        // Get assigned classes
        $assignedClasses = $exam->examAssignments
            ->whereNotNull('class')
            ->map(function($assignment) {
                $class = $assignment->class;
                return [
                    'class_id' => $class->class_id,
                    'display_name' => ($class->year_level ?? '') . '-' . $class->section . ' ' . $class->title
                ];
            })
            ->unique('class_id')
            ->values();
        
        // Get total number of students enrolled in all assigned classes
        $totalStudents = 0;
        foreach ($exam->examAssignments as $assignment) {
            if ($assignment->class) {
                $totalStudents += $assignment->class->students()->count();
            }
        }
        
        // Count unique students who have submitted (attempted) the exam
        $submittedCount = $exam->examAttempts()
            ->distinct('student_id')
            ->count('student_id');
        
        // Calculate completion rate
        $completionRate = $totalStudents > 0 ? round(($submittedCount / $totalStudents) * 100) : 0;
        
        // Get highest score
        $highestScore = $exam->examAttempts()
            ->where('status', 'submitted')
            ->max('score') ?? 0;
        
        // Get lowest score (only from submitted attempts)
        $lowestScore = $exam->examAttempts()
            ->where('status', 'submitted')
            ->min('score') ?? 0;
        
        // Calculate average completion time
        $averageTime = null;
        $completedAttempts = $exam->examAttempts()
            ->where('status', 'submitted')
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get(['start_time', 'end_time']);
        
        if ($completedAttempts->count() > 0) {
            $totalMinutes = 0;
            foreach ($completedAttempts as $attempt) {
                $start = \Carbon\Carbon::parse($attempt->start_time);
                $end = \Carbon\Carbon::parse($attempt->end_time);
                $totalMinutes += $start->diffInMinutes($end);
            }
            $avgMinutes = round($totalMinutes / $completedAttempts->count());
            $hours = floor($avgMinutes / 60);
            $minutes = $avgMinutes % 60;
            $averageTime = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
        } else {
            $averageTime = 'N/A';
        }
        
        // Format schedule
        $schedule = '';
        if ($exam->schedule_start && $exam->schedule_end) {
            $scheduleStart = \Carbon\Carbon::parse($exam->schedule_start)->format('F d, Y, H:i');
            $scheduleEnd = \Carbon\Carbon::parse($exam->schedule_end)->format('F d, Y, H:i');
            $schedule = "{$scheduleStart} to {$scheduleEnd}";
        } else {
            $schedule = 'Not scheduled';
        }
        
        // Get top 3 highest scores and all students with those scores
        $topScores = $exam->examAttempts()
            ->where('status', 'submitted')
            ->orderBy('score', 'desc')
            ->distinct('score')
            ->pluck('score')
            ->take(3);
        
        $highestScoringStudents = collect();
        if ($topScores->isNotEmpty()) {
            $highestScoringStudents = $exam->examAttempts()
                ->with(['student', 'examAssignment.class'])
                ->where('status', 'submitted')
                ->whereIn('score', $topScores)
                ->orderBy('score', 'desc')
                ->get()
                ->map(function($attempt) {
                    $student = $attempt->student;
                    $class = $attempt->examAssignment && $attempt->examAssignment->class 
                        ? $attempt->examAssignment->class 
                        : null;
                    
                    return [
                        'name' => $student ? $student->full_name : 'Unknown',
                        'class' => $class 
                            ? ($class->year_level ?? '') . '-' . $class->section 
                            : 'N/A',
                        'score' => $attempt->score
                    ];
                });
        }
        
        // Find hardest question (most students got it wrong)
        $hardestQuestion = null;
        $submittedAttempts = $exam->examAttempts()->where('status', 'submitted')->pluck('attempt_id');
        
        if ($submittedAttempts->isNotEmpty()) {
            $questionStats = \DB::table('exam_answers')
                ->join('exam_items', 'exam_answers.item_id', '=', 'exam_items.item_id')
                ->whereIn('exam_answers.attempt_id', $submittedAttempts)
                ->where('exam_items.exam_id', $exam->exam_id)
                ->select(
                    'exam_items.item_id',
                    'exam_items.order',
                    'exam_items.question',
                    \DB::raw('COUNT(*) as total_attempts'),
                    \DB::raw('SUM(CASE WHEN exam_answers.is_correct = 0 THEN 1 ELSE 0 END) as wrong_count')
                )
                ->groupBy('exam_items.item_id', 'exam_items.order', 'exam_items.question')
                ->orderBy('wrong_count', 'desc')
                ->first();
            
            if ($questionStats) {
                $successRate = $questionStats->total_attempts > 0 
                    ? round((($questionStats->total_attempts - $questionStats->wrong_count) / $questionStats->total_attempts) * 100) 
                    : 0;
                    
                $hardestQuestion = [
                    'number' => $questionStats->order,
                    'question' => $questionStats->question,
                    'wrong_count' => $questionStats->wrong_count,
                    'success_rate' => $successRate
                ];
            }
        }
        
        // Find easiest question (most students got it right)
        $easiestQuestion = null;
        
        if ($submittedAttempts->isNotEmpty()) {
            $questionStats = \DB::table('exam_answers')
                ->join('exam_items', 'exam_answers.item_id', '=', 'exam_items.item_id')
                ->whereIn('exam_answers.attempt_id', $submittedAttempts)
                ->where('exam_items.exam_id', $exam->exam_id)
                ->select(
                    'exam_items.item_id',
                    'exam_items.order',
                    'exam_items.question',
                    \DB::raw('COUNT(*) as total_attempts'),
                    \DB::raw('SUM(CASE WHEN exam_answers.is_correct = 1 THEN 1 ELSE 0 END) as correct_count')
                )
                ->groupBy('exam_items.item_id', 'exam_items.order', 'exam_items.question')
                ->orderBy('correct_count', 'desc')
                ->first();
            
            if ($questionStats) {
                $successRate = $questionStats->total_attempts > 0 
                    ? round(($questionStats->correct_count / $questionStats->total_attempts) * 100) 
                    : 0;
                    
                $easiestQuestion = [
                    'number' => $questionStats->order,
                    'question' => $questionStats->question,
                    'correct_count' => $questionStats->correct_count,
                    'success_rate' => $successRate
                ];
            }
        }
        
        return view('instructor.exam-statistics.show', compact(
            'exam', 
            'totalItems', 
            'totalPoints', 
            'assignedClasses', 
            'schedule',
            'totalStudents',
            'submittedCount',
            'completionRate',
            'highestScore',
            'lowestScore',
            'averageTime',
            'highestScoringStudents',
            'hardestQuestion',
            'easiestQuestion'
        ));
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
