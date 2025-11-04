<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamAnswer;
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
     * Get filtered statistics for a specific class
     */
    public function getFilteredStats(Request $request, $id)
    {
        // Validate inputs
        $validated = $request->validate([
            'class_id' => 'nullable|string|max:255'
        ]);
        
        $classId = $validated['class_id'] ?? null;
        
        // Validate exam ID
        if (!is_numeric($id)) {
            return response()->json(['error' => 'Invalid exam ID'], 400);
        }
        
        try {
            $exam = Exam::with(['subject', 'examItems', 'examAssignments.class'])
                ->where('exam_id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();
            
            // Get total items and points from exam table
            $totalItems = $exam->no_of_items;
            $totalPoints = $exam->total_points;
            
            // Filter by class if specified
            $assignmentsQuery = $exam->examAssignments();
            if ($classId && $classId !== 'all') {
                // Verify class belongs to this exam
                $validClass = $exam->examAssignments()
                    ->where('class_id', $classId)
                    ->exists();
                
                if (!$validClass) {
                    return response()->json(['error' => 'Unauthorized access to class data'], 403);
                }
                
                $assignmentsQuery->where('class_id', $classId);
            }
            $assignments = $assignmentsQuery->get();
        
        // Get total number of students enrolled in filtered classes
        $totalStudents = 0;
        foreach ($assignments as $assignment) {
            if ($assignment->class) {
                $totalStudents += $assignment->class->students()->count();
            }
        }
        
        // Get assignment IDs for filtering attempts
        $assignmentIds = $assignments->pluck('assignment_id');
        
        // Count unique students who have submitted
        $submittedCount = \DB::table('exam_attempts')
            ->whereIn('exam_assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->distinct('student_id')
            ->count('student_id');
        
        // Calculate completion rate
        $completionRate = $totalStudents > 0 ? round(($submittedCount / $totalStudents) * 100) : 0;
        
        // Get highest score
        $highestScore = \DB::table('exam_attempts')
            ->whereIn('exam_assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->max('score') ?? 0;
        
        // Get lowest score
        $lowestScore = \DB::table('exam_attempts')
            ->whereIn('exam_assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->min('score') ?? 0;
        
        // Calculate average completion time
        $averageTime = 'N/A';
        $completedAttempts = \DB::table('exam_attempts')
            ->whereIn('exam_assignment_id', $assignmentIds)
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
        }
        
        // Get top 3 highest scores and all students with those scores
        $topScores = \DB::table('exam_attempts')
            ->whereIn('exam_assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->orderBy('score', 'desc')
            ->distinct('score')
            ->pluck('score')
            ->take(3);
        
        $highestScoringStudents = collect();
        if ($topScores->isNotEmpty()) {
            $highestScoringStudents = \DB::table('exam_attempts')
                ->join('user_student', 'exam_attempts.student_id', '=', 'user_student.user_id')
                ->join('exam_assignments', 'exam_attempts.exam_assignment_id', '=', 'exam_assignments.assignment_id')
                ->leftJoin('class', 'exam_assignments.class_id', '=', 'class.class_id')
                ->whereIn('exam_attempts.exam_assignment_id', $assignmentIds)
                ->where('exam_attempts.status', 'submitted')
                ->whereIn('exam_attempts.score', $topScores)
                ->select(
                    'user_student.first_name',
                    'user_student.middle_name',
                    'user_student.last_name',
                    'class.year_level',
                    'class.section',
                    'exam_attempts.score'
                )
                ->orderBy('exam_attempts.score', 'desc')
                ->get()
                ->map(function($attempt) {
                    $middleInitial = $attempt->middle_name ? ' ' . substr($attempt->middle_name, 0, 1) . '.' : '';
                    return [
                        'name' => $attempt->first_name . $middleInitial . ' ' . $attempt->last_name,
                        'class' => ($attempt->year_level ?? '') . '-' . ($attempt->section ?? 'N/A'),
                        'score' => $attempt->score
                    ];
                });
        }
        
        // Find hardest question
        $hardestQuestion = null;
        $submittedAttemptIds = \DB::table('exam_attempts')
            ->whereIn('exam_assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->pluck('attempt_id');
        
        if ($submittedAttemptIds->isNotEmpty()) {
            $questionStats = \DB::table('exam_answers')
                ->join('exam_items', 'exam_answers.item_id', '=', 'exam_items.item_id')
                ->whereIn('exam_answers.attempt_id', $submittedAttemptIds)
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
        
        // Find easiest question
        $easiestQuestion = null;
        if ($submittedAttemptIds->isNotEmpty()) {
            $questionStats = \DB::table('exam_answers')
                ->join('exam_items', 'exam_answers.item_id', '=', 'exam_items.item_id')
                ->whereIn('exam_answers.attempt_id', $submittedAttemptIds)
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
        
        return response()->json([
            'totalStudents' => $totalStudents,
            'submittedCount' => $submittedCount,
            'completionRate' => $completionRate,
            'highestScore' => $highestScore,
            'lowestScore' => $lowestScore,
            'averageTime' => $averageTime,
            'highestScoringStudents' => $highestScoringStudents,
            'hardestQuestion' => $hardestQuestion,
            'easiestQuestion' => $easiestQuestion,
            'totalPoints' => $totalPoints
        ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Exam not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error in getFilteredStats: ' . $e->getMessage(), [
                'exam_id' => $id,
                'class_id' => $classId,
                'instructor_id' => Auth::id()
            ]);
            
            return response()->json(['error' => 'Failed to fetch statistics'], 500);
        }
    }

    /**
     * Get detailed question statistics
     */
    public function getQuestionStats(Request $request, $id)
    {
        // Validate inputs
        $validated = $request->validate([
            'class_id' => 'nullable|string|max:255'
        ]);
        
        $classId = $validated['class_id'] ?? null;
        
        // Validate exam ID
        if (!is_numeric($id)) {
            return response()->json(['error' => 'Invalid exam ID'], 400);
        }
        
        try {
            $exam = Exam::with(['subject', 'examItems', 'examAssignments.class'])
                ->where('exam_id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();
            
            // Filter by class if specified
            $assignmentsQuery = $exam->examAssignments();
            if ($classId && $classId !== 'all') {
                // Verify class belongs to this exam
                $validClass = $exam->examAssignments()
                    ->where('class_id', $classId)
                    ->exists();
                
                if (!$validClass) {
                    return response()->json(['error' => 'Unauthorized access to class data'], 403);
                }
                
                $assignmentsQuery->where('class_id', $classId);
            }
            $assignments = $assignmentsQuery->get();
        
        // Get assignment IDs for filtering attempts
        $assignmentIds = $assignments->pluck('assignment_id');
        
        // Get submitted attempt IDs
        $submittedAttemptIds = \DB::table('exam_attempts')
            ->whereIn('exam_assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->pluck('attempt_id');
        
        // Get all exam items with their statistics
        $questions = $exam->examItems()
            ->orderBy('order')
            ->get()
            ->map(function($item) use ($submittedAttemptIds) {
                $totalResponses = \DB::table('exam_answers')
                    ->whereIn('attempt_id', $submittedAttemptIds)
                    ->where('item_id', $item->item_id)
                    ->count();
                
                $correctCount = \DB::table('exam_answers')
                    ->whereIn('attempt_id', $submittedAttemptIds)
                    ->where('item_id', $item->item_id)
                    ->where('is_correct', 1)
                    ->count();
                
                $wrongCount = $totalResponses - $correctCount;
                $successRate = $totalResponses > 0 ? round(($correctCount / $totalResponses) * 100, 1) : 0;
                
                // Response breakdown based on item type
                $responseBreakdown = [];
                $options = []; // Initialize as empty array
                
                if ($item->item_type === 'mcq' || $item->item_type === 'torf') {
                    // Get options from exam_items.options field
                    $itemOptions = $item->options; // Eloquent cast handles JSON
                    
                    // If options is still a string (cast didn't work), decode it manually
                    if (is_string($itemOptions)) {
                        $itemOptions = json_decode($itemOptions, true);
                    }
                    
                    // Ensure it's an array
                    if (!is_array($itemOptions)) {
                        $itemOptions = [];
                    }
                    
                    // Get correct answer(s) from exam_items.answer field
                    $correctAnswers = $item->answer; // Eloquent cast handles JSON
                    
                    // If answer is still a string, decode it manually
                    if (is_string($correctAnswers)) {
                        $correctAnswers = json_decode($correctAnswers, true);
                    }
                    
                    // For MCQ: answer is array of indices [0, 2] or single index
                    // For T/F: answer is {"correct":"true"} or {"correct":"false"}
                    $correctIndices = [];
                    $correctValue = null;
                    
                    if ($item->item_type === 'mcq') {
                        if (is_array($correctAnswers)) {
                            $correctIndices = $correctAnswers;
                        } else {
                            $correctIndices = [$correctAnswers];
                        }
                    } elseif ($item->item_type === 'torf') {
                        if (is_array($correctAnswers) && isset($correctAnswers['correct'])) {
                            $correctValue = strtolower($correctAnswers['correct']);
                        }
                    }
                    
                    // Count student responses per answer_text
                    $responses = \DB::table('exam_answers')
                        ->whereIn('attempt_id', $submittedAttemptIds)
                        ->where('item_id', $item->item_id)
                        ->select('answer_text', \DB::raw('COUNT(*) as count'))
                        ->groupBy('answer_text')
                        ->get()
                        ->keyBy('answer_text');
                    
                    // Build response breakdown with all options
                    if (is_array($itemOptions)) {
                        foreach ($itemOptions as $index => $optionText) {
                            $optionKey = (string)$index; // "0", "1", "2", "3"
                            $optionLabel = chr(65 + $index); // "A", "B", "C", "D"
                            
                            // Check if this option is correct
                            $isCorrect = false;
                            if ($item->item_type === 'mcq') {
                                $isCorrect = in_array($index, $correctIndices) || in_array((string)$index, $correctIndices);
                            } elseif ($item->item_type === 'torf') {
                                // For T/F, check if the option text matches the correct value
                                $isCorrect = strtolower(trim($optionText)) === $correctValue;
                            }
                            
                            // Get response count for this option (check both index and label)
                            $count = 0;
                            if (isset($responses[$optionKey])) {
                                $count = $responses[$optionKey]->count;
                            } elseif (isset($responses[$optionLabel])) {
                                $count = $responses[$optionLabel]->count;
                            }
                            
                            $percentage = $totalResponses > 0 ? round(($count / $totalResponses) * 100, 1) : 0;
                            
                            $responseBreakdown[] = [
                                'option' => $optionLabel,
                                'text' => $optionText,
                                'count' => $count,
                                'percentage' => $percentage,
                                'is_correct' => $isCorrect
                            ];
                            
                            $options[] = [
                                'option' => $optionLabel,
                                'text' => $optionText,
                                'is_correct' => $isCorrect
                            ];
                        }
                    }
                }
                
                // Add expected answer for IDEN and ENUM types
                $expectedAnswer = null;
                if ($item->item_type === 'iden') {
                    $expectedAnswer = $item->expected_answer;
                } elseif ($item->item_type === 'enum') {
                    $expectedAnswer = $item->answer;
                    // If it's a string, decode it
                    if (is_string($expectedAnswer)) {
                        $expectedAnswer = json_decode($expectedAnswer, true);
                    }
                }
                
                return [
                    'item_id' => $item->item_id,
                    'order' => $item->order,
                    'question' => $item->question,
                    'item_type' => $item->item_type,
                    'points_awarded' => $item->points_awarded,
                    'total_responses' => $totalResponses,
                    'correct_count' => $correctCount,
                    'wrong_count' => $wrongCount,
                    'success_rate' => $successRate,
                    'response_breakdown' => $responseBreakdown,
                    'options' => $options,
                    'expected_answer' => $expectedAnswer,
                    'enum_type' => $item->enum_type ?? null,
                    'rubric' => $item->item_type === 'essay' ? $item->expected_answer : null
                ];
            });
        
        return response()->json([
            'questions' => $questions
        ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Exam not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error in getQuestionStats: ' . $e->getMessage(), [
                'exam_id' => $id,
                'class_id' => $classId,
                'instructor_id' => Auth::id()
            ]);
            
            return response()->json(['error' => 'Failed to fetch question statistics'], 500);
        }
    }

    /**
     * Get individual student performance data
     */
    public function getIndividualStats(Request $request, $id)
    {
        // Validate inputs
        $validated = $request->validate([
            'class_id' => 'nullable|string|max:255'
        ]);
        
        $classId = $validated['class_id'] ?? null;
        
        // Validate exam ID
        if (!is_numeric($id)) {
            return response()->json(['error' => 'Invalid exam ID'], 400);
        }
        
        try {
            $exam = Exam::with(['subject', 'examItems', 'examAssignments.class'])
                ->where('exam_id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();
            
            // Filter by class if specified
            $assignmentsQuery = $exam->examAssignments();
            if ($classId && $classId !== 'all') {
                // Verify class belongs to this exam
                $validClass = $exam->examAssignments()
                    ->where('class_id', $classId)
                    ->exists();
                
                if (!$validClass) {
                    return response()->json(['error' => 'Unauthorized access to class data'], 403);
                }
                
                $assignmentsQuery->where('class_id', $classId);
            }
            $assignments = $assignmentsQuery->get();
        
        // Get assignment IDs for filtering attempts
        $assignmentIds = $assignments->pluck('assignment_id');
        
        // Get all submitted attempts with student info
        $attempts = \DB::table('exam_attempts')
            ->join('user_student', 'exam_attempts.student_id', '=', 'user_student.user_id')
            ->join('exam_assignments', 'exam_attempts.exam_assignment_id', '=', 'exam_assignments.assignment_id')
            ->leftJoin('class', 'exam_assignments.class_id', '=', 'class.class_id')
            ->whereIn('exam_attempts.exam_assignment_id', $assignmentIds)
            ->where('exam_attempts.status', 'submitted')
            ->select(
                'exam_attempts.attempt_id',
                'exam_attempts.student_id',
                'exam_attempts.score',
                'exam_attempts.start_time',
                'exam_attempts.end_time',
                'user_student.first_name',
                'user_student.middle_name',
                'user_student.last_name',
                'user_student.id_number',
                'class.year_level',
                'class.section'
            )
            ->orderBy('user_student.last_name')
            ->orderBy('user_student.first_name')
            ->get();
        
        // Get exam items
        $examItems = $exam->examItems()->orderBy('order')->get();
        
        // Format student data with their answers
        $students = $attempts->map(function($attempt) use ($examItems) {
            $middleInitial = $attempt->middle_name ? ' ' . substr($attempt->middle_name, 0, 1) . '.' : '';
            
            // Calculate duration
            $duration = 'N/A';
            if ($attempt->start_time && $attempt->end_time) {
                $start = \Carbon\Carbon::parse($attempt->start_time);
                $end = \Carbon\Carbon::parse($attempt->end_time);
                $minutes = $start->diffInMinutes($end);
                $hours = floor($minutes / 60);
                $mins = $minutes % 60;
                $duration = $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
            }
            
            // Get student's answers for all questions
            $answers = \DB::table('exam_answers')
                ->where('attempt_id', $attempt->attempt_id)
                ->get()
                ->keyBy('item_id');
            
            // Build question-answer pairs
            $questionAnswers = $examItems->map(function($item) use ($answers) {
                $studentAnswer = $answers->get($item->item_id);
                
                // Get correct answer based on item type
                $correctAnswer = null;
                if ($item->item_type === 'mcq' || $item->item_type === 'torf') {
                    // Decode options and answer
                    $options = is_string($item->options) ? json_decode($item->options, true) : $item->options;
                    $answer = is_string($item->answer) ? json_decode($item->answer, true) : $item->answer;
                    
                    if ($item->item_type === 'mcq' && is_array($options)) {
                        $correctIndices = is_array($answer) ? $answer : [$answer];
                        $correctOptions = [];
                        foreach ($correctIndices as $index) {
                            if (isset($options[$index])) {
                                $label = chr(65 + $index);
                                $correctOptions[] = "$label. " . $options[$index];
                            }
                        }
                        $correctAnswer = implode(', ', $correctOptions);
                    } elseif ($item->item_type === 'torf' && is_array($answer)) {
                        $correctAnswer = isset($answer['correct']) ? ucfirst($answer['correct']) : null;
                    }
                } elseif ($item->item_type === 'iden') {
                    $correctAnswer = $item->expected_answer;
                } elseif ($item->item_type === 'enum') {
                    $answer = is_string($item->answer) ? json_decode($item->answer, true) : $item->answer;
                    $correctAnswer = is_array($answer) ? implode(', ', $answer) : $answer;
                }
                
                // Format student's answer
                $studentAnswerText = $studentAnswer ? $studentAnswer->answer_text : 'No answer';
                
                // For MCQ, convert index to letter if needed
                if ($item->item_type === 'mcq' && $studentAnswer && is_numeric($studentAnswer->answer_text)) {
                    $options = is_string($item->options) ? json_decode($item->options, true) : $item->options;
                    $index = (int)$studentAnswer->answer_text;
                    if (isset($options[$index])) {
                        $label = chr(65 + $index);
                        $studentAnswerText = "$label. " . $options[$index];
                    }
                }
                
                // For enumeration, format as list if it's an array
                if ($item->item_type === 'enum' && $studentAnswer) {
                    $answerData = $studentAnswer->answer_text;
                    if (strpos($answerData, '[') === 0) {
                        $decoded = json_decode($answerData, true);
                        if (is_array($decoded)) {
                            $studentAnswerText = implode(', ', $decoded);
                        }
                    }
                }
                
                return [
                    'item_id' => $item->item_id,
                    'answer_id' => $studentAnswer ? $studentAnswer->answer_id : null,
                    'question_number' => $item->order,
                    'question' => $item->question,
                    'item_type' => $item->item_type,
                    'options' => ($item->item_type === 'mcq' || $item->item_type === 'torf') 
                        ? (is_string($item->options) ? json_decode($item->options, true) : $item->options)
                        : null,
                    'correct_indices' => ($item->item_type === 'mcq' || $item->item_type === 'torf')
                        ? (is_string($item->answer) ? json_decode($item->answer, true) : $item->answer)
                        : null,
                    'student_answer' => $studentAnswerText,
                    'student_answer_raw' => $studentAnswer ? $studentAnswer->answer_text : null,
                    'correct_answer' => $correctAnswer,
                    'is_correct' => $studentAnswer ? $studentAnswer->is_correct : 0,
                    'points_earned' => $studentAnswer ? $studentAnswer->points_earned : 0,
                    'points_possible' => $item->points_awarded,
                    'ai_feedback' => $studentAnswer && isset($studentAnswer->ai_feedback) ? $studentAnswer->ai_feedback : null,
                    'ai_confidence' => $studentAnswer && isset($studentAnswer->ai_confidence) ? $studentAnswer->ai_confidence : null,
                    'requires_manual_review' => $studentAnswer && isset($studentAnswer->requires_manual_review) ? $studentAnswer->requires_manual_review : false
                ];
            });
            
            return [
                'attempt_id' => $attempt->attempt_id,
                'student_id' => $attempt->student_id,
                'name' => $attempt->first_name . $middleInitial . ' ' . $attempt->last_name,
                'id_number' => $attempt->id_number,
                'class' => ($attempt->year_level ?? '') . '-' . ($attempt->section ?? 'N/A'),
                'score' => $attempt->score,
                'duration' => $duration,
                'answers' => $questionAnswers
            ];
        });
        
        return response()->json([
            'students' => $students,
            'total_points' => $exam->total_points
        ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Exam not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error in getIndividualStats: ' . $e->getMessage(), [
                'exam_id' => $id,
                'class_id' => $classId,
                'instructor_id' => Auth::id()
            ]);
            
            return response()->json(['error' => 'Failed to fetch individual statistics'], 500);
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
     * Override student answer correctness and points
     */
    public function overrideAnswer(Request $request, $examId, $answerId)
    {
        // Validate inputs
        $validated = $request->validate([
            'is_correct' => 'required|boolean',
            'points_earned' => 'required|numeric|min:0'
        ]);
        
        // Validate IDs are numeric
        if (!is_numeric($examId) || !is_numeric($answerId)) {
            return response()->json(['error' => 'Invalid ID format'], 400);
        }
        
        try {
            // Use database transaction with row locking to prevent race conditions
            return \DB::transaction(function () use ($examId, $answerId, $validated) {
                // Get the exam to verify ownership
                $exam = Exam::where('exam_id', $examId)
                    ->where('teacher_id', Auth::id())
                    ->lockForUpdate() // Lock the exam row
                    ->firstOrFail();
                
                // Get the answer and verify it belongs to this exam
                $answer = ExamAnswer::where('answer_id', $answerId)
                    ->whereHas('attempt.examAssignment', function($query) use ($examId) {
                        $query->where('exam_id', $examId);
                    })
                    ->lockForUpdate() // Lock the answer row
                    ->firstOrFail();
                
                // Get the item to validate max points
                $item = $answer->item;
                if (!$item) {
                    return response()->json(['error' => 'Question not found'], 404);
                }
                
                // Validate points don't exceed maximum
                if ($validated['points_earned'] > $item->points_awarded) {
                    return response()->json([
                        'error' => 'Points earned cannot exceed maximum points',
                        'max_points' => $item->points_awarded
                    ], 422);
                }
                
                // Log the override for audit trail
                \Log::info('Answer override', [
                    'exam_id' => $examId,
                    'answer_id' => $answerId,
                    'old_correct' => $answer->is_correct,
                    'new_correct' => $validated['is_correct'],
                    'old_points' => $answer->points_earned,
                    'new_points' => $validated['points_earned'],
                    'instructor_id' => Auth::id(),
                    'student_id' => $answer->attempt->student_id
                ]);
                
                // Update the answer
                $answer->is_correct = $validated['is_correct'];
                $answer->points_earned = $validated['points_earned'];
                $answer->save();
                
                // Recalculate attempt score with lock
                $attempt = $answer->attempt()->lockForUpdate()->first();
                $totalScore = ExamAnswer::where('attempt_id', $attempt->attempt_id)
                    ->sum('points_earned');
                
                $attempt->score = $totalScore;
                $attempt->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Answer updated successfully',
                    'new_score' => $totalScore
                ]);
            });
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Resource not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error in overrideAnswer: ' . $e->getMessage(), [
                'exam_id' => $examId,
                'answer_id' => $answerId,
                'instructor_id' => Auth::id()
            ]);
            
            return response()->json(['error' => 'Failed to update answer'], 500);
        }
    }

    /**
     * Delete an exam attempt
     */
    public function deleteAttempt(Request $request, $examId, $attemptId)
    {
        // Validate IDs are numeric
        if (!is_numeric($examId) || !is_numeric($attemptId)) {
            return response()->json(['error' => 'Invalid ID format'], 400);
        }
        
        try {
            // Use database transaction to ensure data integrity
            return \DB::transaction(function () use ($examId, $attemptId) {
                // Get the exam to verify ownership
                $exam = Exam::where('exam_id', $examId)
                    ->where('teacher_id', Auth::id())
                    ->lockForUpdate()
                    ->firstOrFail();
                
                // Get the attempt and verify it belongs to this exam
                $attempt = \App\Models\ExamAttempt::where('attempt_id', $attemptId)
                    ->whereHas('examAssignment', function($query) use ($examId) {
                        $query->where('exam_id', $examId);
                    })
                    ->lockForUpdate()
                    ->firstOrFail();
                
                // Get student info for logging
                $studentId = $attempt->student_id;
                $attemptScore = $attempt->score;
                
                // Log the deletion for audit trail
                \Log::info('Exam attempt deleted', [
                    'exam_id' => $examId,
                    'attempt_id' => $attemptId,
                    'student_id' => $studentId,
                    'score' => $attemptScore,
                    'instructor_id' => Auth::id(),
                    'deleted_at' => now()
                ]);
                
                // Delete all answers associated with this attempt
                ExamAnswer::where('attempt_id', $attemptId)->delete();
                
                // Delete the attempt
                $attempt->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Exam attempt deleted successfully'
                ]);
            });
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Attempt not found or you do not have permission to delete it'], 404);
        } catch (\Exception $e) {
            \Log::error('Error in deleteAttempt: ' . $e->getMessage(), [
                'exam_id' => $examId,
                'attempt_id' => $attemptId,
                'instructor_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to delete attempt'], 500);
        }
    }

    /**
     * Calculate optimal score ranges based on total points
     * Returns array of range labels and boundaries
     */
    private function calculateScoreRanges($totalPoints)
    {
        // Validate and sanitize input
        $totalPoints = (int) $totalPoints;
        
        if ($totalPoints <= 0) {
            return [];
        }
        
        // Prevent extremely large values that could cause performance issues
        if ($totalPoints > 10000) {
            $totalPoints = 10000; // Cap at reasonable maximum
        }
        
        // Determine optimal range width
        if ($totalPoints <= 25) {
            // For small exams (≤25 points): 5-point ranges
            $rangeWidth = 5;
        } elseif ($totalPoints <= 50) {
            // For medium exams (26-50 points): 10-point ranges
            $rangeWidth = 10;
        } elseif ($totalPoints <= 100) {
            // For standard exams (51-100 points): 10-point ranges
            $rangeWidth = 10;
        } else {
            // For large exams (>100 points): 20-point ranges
            $rangeWidth = 20;
        }
        
        // Calculate number of ranges
        $numRanges = ceil($totalPoints / $rangeWidth);
        
        // Ensure minimum 5 ranges, maximum 10 ranges
        if ($numRanges < 5) {
            $rangeWidth = ceil($totalPoints / 5);
            $numRanges = 5;
        } elseif ($numRanges > 10) {
            $rangeWidth = ceil($totalPoints / 10);
            $numRanges = 10;
        }
        
        // Generate range labels and boundaries
        $ranges = [];
        for ($i = 0; $i < $numRanges; $i++) {
            $start = $i * $rangeWidth;
            $end = min(($i + 1) * $rangeWidth - 1, $totalPoints);
            
            // Last range should always end at totalPoints
            if ($i === $numRanges - 1) {
                $end = $totalPoints;
            }
            
            $label = "{$start}-{$end}";
            $ranges[$label] = [
                'label' => $label,
                'start' => $start,
                'end' => $end,
                'count' => 0
            ];
        }
        
        return $ranges;
    }

    /**
     * Categorize a score into the appropriate range
     */
    private function categorizeScore($score, $ranges)
    {
        foreach ($ranges as $label => $range) {
            if ($score >= $range['start'] && $score <= $range['end']) {
                return $label;
            }
        }
        return null;
    }

    /**
     * Get score distribution for histogram
     */
    public function getScoreDistribution(Request $request, $examId)
    {
        // Validate inputs
        $validated = $request->validate([
            'class_id' => 'nullable|string|max:255'
        ]);
        
        $classId = $validated['class_id'] ?? 'all';
        
        // Validate exam_id is numeric
        if (!is_numeric($examId)) {
            return response()->json(['error' => 'Invalid exam ID'], 400);
        }
        
        try {
            // Get exam and verify ownership (authorization check)
            $exam = Exam::where('exam_id', $examId)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();
            
            $totalPoints = $exam->total_points;
            
            // Validate total points
            if (!$totalPoints || $totalPoints <= 0) {
                return response()->json([
                    'error' => 'Invalid exam configuration',
                    'distribution' => [],
                    'totalPoints' => 0,
                    'rangeWidth' => 0,
                    'statistics' => [
                        'totalStudents' => 0,
                        'average' => 0,
                        'median' => 0,
                        'passRate' => 0,
                        'passingScore' => 0,
                        'highestScore' => 0,
                        'lowestScore' => 0
                    ]
                ], 200);
            }
            
            // Calculate dynamic ranges based on total points
            $ranges = $this->calculateScoreRanges($totalPoints);
            
            // Get all scores for this exam
            $assignmentsQuery = $exam->examAssignments();
            
            // Verify class_id belongs to this exam's assignments (authorization check)
            if ($classId !== 'all') {
                // Verify the class is actually assigned to this exam
                $validClass = $exam->examAssignments()
                    ->where('class_id', $classId)
                    ->exists();
                
                if (!$validClass) {
                    return response()->json(['error' => 'Unauthorized access to class data'], 403);
                }
                
                $assignmentsQuery->where('class_id', $classId);
            }
            
            $assignmentIds = $assignmentsQuery->pluck('assignment_id');
            
            // Check if there are any assignments
            if ($assignmentIds->isEmpty()) {
                return response()->json([
                    'distribution' => array_fill_keys(array_keys($ranges), 0),
                    'totalPoints' => $totalPoints,
                    'rangeWidth' => !empty($ranges) ? ($ranges[array_key_first($ranges)]['end'] - $ranges[array_key_first($ranges)]['start'] + 1) : 0,
                    'statistics' => [
                        'totalStudents' => 0,
                        'average' => 0,
                        'median' => 0,
                        'passRate' => 0,
                        'passingScore' => round($totalPoints * 0.6, 2),
                        'highestScore' => 0,
                        'lowestScore' => 0
                    ]
                ]);
            }
            
            // Get submitted attempts with proper binding to prevent SQL injection
            $scores = \DB::table('exam_attempts')
                ->whereIn('exam_assignment_id', $assignmentIds)
                ->where('status', 'submitted')
                ->pluck('score')
                ->toArray();
            
            // Distribute scores into ranges
            foreach ($scores as $score) {
                // Sanitize score value
                if (!is_numeric($score) || $score < 0 || $score > $totalPoints) {
                    continue; // Skip invalid scores
                }
                
                $rangeLabel = $this->categorizeScore($score, $ranges);
                if ($rangeLabel && isset($ranges[$rangeLabel])) {
                    $ranges[$rangeLabel]['count']++;
                }
            }
            
            // Format distribution for frontend (label => count)
            $distribution = [];
            foreach ($ranges as $label => $range) {
                $distribution[$label] = $range['count'];
            }
            
            // Calculate statistics
            $total = count($scores);
            $average = $total > 0 ? round(array_sum($scores) / $total, 2) : 0;
            
            // Calculate pass rate (60% of total points)
            $passingScore = $totalPoints * 0.6;
            $passing = array_filter($scores, fn($s) => $s >= $passingScore);
            $passRate = $total > 0 ? round((count($passing) / $total) * 100, 2) : 0;
            
            // Calculate median
            sort($scores);
            $median = 0;
            if ($total > 0) {
                if ($total % 2 === 0) {
                    $median = ($scores[$total/2 - 1] + $scores[$total/2]) / 2;
                } else {
                    $median = $scores[floor($total/2)];
                }
            }
            
            return response()->json([
                'distribution' => $distribution,
                'totalPoints' => $totalPoints,
                'rangeWidth' => !empty($ranges) ? ($ranges[array_key_first($ranges)]['end'] - $ranges[array_key_first($ranges)]['start'] + 1) : 0,
                'statistics' => [
                    'totalStudents' => $total,
                    'average' => $average,
                    'median' => round($median, 2),
                    'passRate' => $passRate,
                    'passingScore' => round($passingScore, 2),
                    'highestScore' => $total > 0 ? max($scores) : 0,
                    'lowestScore' => $total > 0 ? min($scores) : 0
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Exam not found or doesn't belong to this instructor
            return response()->json(['error' => 'Exam not found'], 404);
        } catch (\Exception $e) {
            // Log the error for debugging but don't expose details to user
            \Log::error('Error in getScoreDistribution: ' . $e->getMessage(), [
                'exam_id' => $examId,
                'class_id' => $classId,
                'teacher_id' => Auth::id()
            ]);
            
            return response()->json(['error' => 'An error occurred while fetching score distribution'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
