<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAssignment;
use App\Models\ExamAttempt;
use App\Models\ExamItem;
use App\Models\ClassEnrolment;
use App\Models\UserStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Get all available exams for the authenticated student
     */
    public function index(Request $request)
    {
        $studentId = $request->user()->id;

        // Get classes the student is enrolled in
        $enrolledClassIds = ClassEnrolment::where('student_id', $studentId)
            ->where('status', 'Active')
            ->pluck('class_id');

        // Get exam assignments for those classes
        $examAssignments = ExamAssignment::whereIn('class_id', $enrolledClassIds)
            ->with(['exam.subject', 'class'])
            ->get();

        $exams = $examAssignments->map(function ($assignment) use ($studentId) {
            $exam = $assignment->exam;

            // Check if student has already attempted this exam
            $attempt = ExamAttempt::where('exam_assignment_id', $assignment->assignment_id)
                ->where('student_id', $studentId)
                ->first();

            // Determine exam status
            $status = 'available';
            $now = Carbon::now();

            if ($exam->schedule_start && $now->lt(Carbon::parse($exam->schedule_start))) {
                $status = 'scheduled';
            } elseif ($exam->schedule_end && $now->gt(Carbon::parse($exam->schedule_end))) {
                $status = 'expired';
            } elseif ($attempt && $attempt->status === 'submitted') {
                $status = 'completed';
            } elseif ($attempt && $attempt->status === 'in_progress') {
                $status = 'in_progress';
            }

            return [
                'assignment_id' => $assignment->assignment_id,
                'exam_id' => $exam->exam_id,
                'title' => $exam->exam_title,
                'description' => $exam->exam_desc,
                'subject' => [
                    'id' => $exam->subject->subject_id ?? null,
                    'code' => $exam->subject->subject_code ?? null,
                    'name' => $exam->subject->subject_name ?? null,
                ],
                'class' => [
                    'id' => $assignment->class->class_id ?? null,
                    'title' => $assignment->class->title ?? null,
                ],
                'schedule_start' => $exam->schedule_start,
                'schedule_end' => $exam->schedule_end,
                'duration' => $exam->duration, // in minutes
                'total_points' => $exam->total_points,
                'no_of_items' => $exam->no_of_items,
                'status' => $status,
                'attempt' => $attempt ? [
                    'attempt_id' => $attempt->attempt_id,
                    'start_time' => $attempt->start_time,
                    'end_time' => $attempt->end_time,
                    'score' => $attempt->score,
                    'status' => $attempt->status,
                ] : null,
            ];
        });

        return response()->json([
            'exams' => $exams,
        ]);
    }

    /**
     * Get specific exam details with questions
     */
    public function show(Request $request, $examId)
    {
        $studentId = $request->user()->id;

        // Verify student has access to this exam
        $enrolledClassIds = ClassEnrolment::where('student_id', $studentId)
            ->where('status', 'Active')
            ->pluck('class_id');

        $assignment = ExamAssignment::whereIn('class_id', $enrolledClassIds)
            ->where('exam_id', $examId)
            ->with(['exam.subject', 'exam.sections.items'])
            ->first();

        if (!$assignment) {
            return response()->json([
                'message' => 'Exam not found or not accessible',
            ], 404);
        }

        $exam = $assignment->exam;

        // Check if student has an attempt
        $attempt = ExamAttempt::where('exam_assignment_id', $assignment->assignment_id)
            ->where('student_id', $studentId)
            ->first();

        // Format exam data
        $sections = $exam->sections->map(function ($section) {
            $items = $section->items->sortBy('order')->map(function ($item) {
                return [
                    'item_id' => $item->item_id,
                    'question' => $item->question,
                    'item_type' => $item->item_type,
                    'options' => $item->options, // JSON decoded automatically
                    'points_awarded' => $item->points_awarded,
                    'order' => $item->order,
                    // Don't send expected_answer or answer to student
                ];
            })->values();

            return [
                'section_id' => $section->section_id,
                'title' => $section->section_title,
                'directions' => $section->section_directions,
                'order' => $section->section_order,
                'items' => $items,
            ];
        })->sortBy('order')->values();

        return response()->json([
            'exam' => [
                'exam_id' => $exam->exam_id,
                'title' => $exam->exam_title,
                'description' => $exam->exam_desc,
                'subject' => [
                    'id' => $exam->subject->subject_id ?? null,
                    'code' => $exam->subject->subject_code ?? null,
                    'name' => $exam->subject->subject_name ?? null,
                ],
                'schedule_start' => $exam->schedule_start,
                'schedule_end' => $exam->schedule_end,
                'duration' => $exam->duration,
                'total_points' => $exam->total_points,
                'no_of_items' => $exam->no_of_items,
                'sections' => $sections,
            ],
            'attempt' => $attempt ? [
                'attempt_id' => $attempt->attempt_id,
                'start_time' => $attempt->start_time,
                'end_time' => $attempt->end_time,
                'score' => $attempt->score,
                'status' => $attempt->status,
            ] : null,
        ]);
    }

    /**
     * Start an exam attempt
     */
    public function startAttempt(Request $request)
    {
        $request->validate([
            'exam_assignment_id' => 'required|integer|exists:exam_assignments,assignment_id',
        ]);

        $studentId = $request->user()->id;
        $assignmentId = $request->exam_assignment_id;

        // Verify student has access
        $assignment = ExamAssignment::find($assignmentId);
        $enrolledClassIds = ClassEnrolment::where('student_id', $studentId)
            ->where('status', 'Active')
            ->pluck('class_id');

        if (!$enrolledClassIds->contains($assignment->class_id)) {
            return response()->json([
                'message' => 'You do not have access to this exam',
            ], 403);
        }

        // Check for existing attempt
        $existingAttempt = ExamAttempt::where('exam_assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->status === 'submitted') {
                return response()->json([
                    'message' => 'You have already completed this exam',
                ], 400);
            }

            // Return existing in-progress attempt
            return response()->json([
                'attempt' => [
                    'attempt_id' => $existingAttempt->attempt_id,
                    'exam_assignment_id' => $existingAttempt->exam_assignment_id,
                    'student_id' => $existingAttempt->student_id,
                    'start_time' => $existingAttempt->start_time,
                    'status' => $existingAttempt->status,
                ],
                'message' => 'Resuming existing attempt',
            ]);
        }

        // Check schedule
        $exam = $assignment->exam;
        $now = Carbon::now();

        if ($exam->schedule_start && $now->lt(Carbon::parse($exam->schedule_start))) {
            return response()->json([
                'message' => 'Exam has not started yet',
            ], 400);
        }

        if ($exam->schedule_end && $now->gt(Carbon::parse($exam->schedule_end))) {
            return response()->json([
                'message' => 'Exam has ended',
            ], 400);
        }

        // Create new attempt
        $attempt = ExamAttempt::create([
            'exam_assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'start_time' => Carbon::now(),
            'status' => 'in_progress',
        ]);

        return response()->json([
            'attempt' => [
                'attempt_id' => $attempt->attempt_id,
                'exam_assignment_id' => $attempt->exam_assignment_id,
                'student_id' => $attempt->student_id,
                'start_time' => $attempt->start_time,
                'status' => $attempt->status,
            ],
            'message' => 'Exam attempt started successfully',
        ], 201);
    }

    /**
     * Submit exam attempt with answers
     */
    public function submitAttempt(Request $request, $attemptId)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.item_id' => 'required|integer|exists:exam_items,item_id',
            'answers.*.answer' => 'required',
        ]);

        $studentId = $request->user()->id;

        // Get attempt
        $attempt = ExamAttempt::find($attemptId);

        if (!$attempt) {
            return response()->json([
                'message' => 'Exam attempt not found',
            ], 404);
        }

        // Verify ownership
        if ($attempt->student_id !== $studentId) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Check if already submitted
        if ($attempt->status === 'submitted') {
            return response()->json([
                'message' => 'Exam already submitted',
            ], 400);
        }

        // Calculate score
        $totalScore = 0;
        $answers = $request->answers;

        foreach ($answers as $answerData) {
            $item = ExamItem::find($answerData['item_id']);

            if (!$item) {
                continue;
            }

            $studentAnswer = $answerData['answer'];
            $correctAnswer = $item->expected_answer;

            // Auto-grade based on item type
            $isCorrect = false;

            switch ($item->item_type) {
                case 'mcq':
                case 'torf':
                    // For MCQ and True/False, exact match
                    $isCorrect = $studentAnswer === $correctAnswer;
                    break;

                case 'enum':
                case 'iden':
                    // For enumeration and identification, case-insensitive comparison
                    if (is_array($correctAnswer) && is_array($studentAnswer)) {
                        $isCorrect = count(array_intersect(
                            array_map('strtolower', $studentAnswer),
                            array_map('strtolower', $correctAnswer)
                        )) === count($correctAnswer);
                    } else {
                        $isCorrect = strtolower(trim($studentAnswer)) === strtolower(trim($correctAnswer));
                    }
                    break;

                case 'essay':
                    // Essays need manual grading - don't auto-grade
                    $isCorrect = false;
                    break;
            }

            if ($isCorrect) {
                $totalScore += $item->points_awarded;
            }

            // Update item with student answer
            $item->answer = $studentAnswer;
            $item->save();
        }

        // Update attempt
        $attempt->update([
            'end_time' => Carbon::now(),
            'status' => 'submitted',
            'score' => $totalScore,
        ]);

        return response()->json([
            'message' => 'Exam submitted successfully',
            'attempt' => [
                'attempt_id' => $attempt->attempt_id,
                'start_time' => $attempt->start_time,
                'end_time' => $attempt->end_time,
                'score' => $attempt->score,
                'status' => $attempt->status,
            ],
        ]);
    }

    /**
     * Get exam results
     */
    public function getResults(Request $request, $attemptId)
    {
        $studentId = $request->user()->id;

        $attempt = ExamAttempt::with(['examAssignment.exam.subject'])
            ->find($attemptId);

        if (!$attempt) {
            return response()->json([
                'message' => 'Exam attempt not found',
            ], 404);
        }

        // Verify ownership
        if ($attempt->student_id !== $studentId) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Only show results if submitted
        if ($attempt->status !== 'submitted') {
            return response()->json([
                'message' => 'Exam not yet submitted',
            ], 400);
        }

        $exam = $attempt->examAssignment->exam;

        return response()->json([
            'attempt' => [
                'attempt_id' => $attempt->attempt_id,
                'start_time' => $attempt->start_time,
                'end_time' => $attempt->end_time,
                'score' => $attempt->score,
                'total_points' => $exam->total_points,
                'percentage' => $exam->total_points > 0
                    ? round(($attempt->score / $exam->total_points) * 100, 2)
                    : 0,
                'status' => $attempt->status,
            ],
            'exam' => [
                'exam_id' => $exam->exam_id,
                'title' => $exam->exam_title,
                'subject' => [
                    'code' => $exam->subject->subject_code ?? null,
                    'name' => $exam->subject->subject_name ?? null,
                ],
            ],
        ]);
    }

    /**
     * Get student's enrolled classes
     */
    public function getClasses(Request $request)
    {
        $studentId = $request->user()->id;

        $enrolments = ClassEnrolment::where('student_id', $studentId)
            ->where('status', 'Active')
            ->with(['class.subject'])
            ->get();

        $classes = $enrolments->map(function ($enrolment) {
            $class = $enrolment->class;
            return [
                'class_id' => $class->class_id,
                'title' => $class->title,
                'year_level' => $class->year_level,
                'section' => $class->section,
                'semester' => $class->semester,
                'school_year' => $class->school_year,
                'subject' => [
                    'id' => $class->subject->subject_id ?? null,
                    'code' => $class->subject->subject_code ?? null,
                    'name' => $class->subject->subject_name ?? null,
                ],
            ];
        });

        return response()->json([
            'classes' => $classes,
        ]);
    }
}
