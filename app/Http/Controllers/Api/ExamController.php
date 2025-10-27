<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAssignment;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\ExamItem;
use App\Models\ClassEnrolment;
use App\Models\UserStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Get all available exams for the authenticated student
     */
    public function index(Request $request)
    {
        $studentId = $request->user()->id;

        Log::debug('API Get Exams Request', [
            'student_id' => $studentId,
            'ip' => $request->ip()
        ]);

        // Get classes the student is enrolled in
        $enrolledClassIds = ClassEnrolment::where('student_id', $studentId)
            ->where('status', 'Active')
            ->pluck('class_id');

        Log::debug('API Get Exams - Enrolled Classes', [
            'student_id' => $studentId,
            'class_count' => $enrolledClassIds->count(),
            'class_ids' => $enrolledClassIds->toArray()
        ]);

        // Get exam assignments for those classes
        $examAssignments = ExamAssignment::whereIn('class_id', $enrolledClassIds)
            ->with(['exam.subject', 'class'])
            ->whereHas('exam', function($query) {
                $query->whereIn('status', ['approved', 'ongoing']);
            })
            ->get();

        Log::debug('API Get Exams - Exam Assignments (Approved & Ongoing)', [
            'student_id' => $studentId,
            'assignment_count' => $examAssignments->count()
        ]);

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
                'duration_seconds' => $exam->duration * 60, // in seconds
                'total_points' => $exam->total_points,
                'no_of_items' => $exam->no_of_items,
                'status' => $status,
                // Additional fields for Flutter integration guide compatibility
                'requiresOtp' => !empty($exam->exam_password),
                'resultsReleased' => $status === 'completed' && $attempt && $attempt->score !== null,
                'allowReview' => false, // Set based on your review policy
                'attempt' => $attempt ? [
                    'attempt_id' => $attempt->attempt_id,
                    'start_time' => $attempt->start_time,
                    'end_time' => $attempt->end_time,
                    'score' => $attempt->score,
                    'status' => $attempt->status,
                ] : null,
            ];
        });

        Log::info('API Get Exams Success', [
            'student_id' => $studentId,
            'exam_count' => $exams->count()
        ]);

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

        Log::debug('API Get Exam Details Request', [
            'student_id' => $studentId,
            'exam_id' => $examId,
            'ip' => $request->ip()
        ]);

        // Verify student has access to this exam
        $enrolledClassIds = ClassEnrolment::where('student_id', $studentId)
            ->where('status', 'Active')
            ->pluck('class_id');

        $assignment = ExamAssignment::whereIn('class_id', $enrolledClassIds)
            ->where('exam_id', $examId)
            ->with(['exam.subject', 'exam.sections.items'])
            ->whereHas('exam', function($query) {
                $query->whereIn('status', ['approved', 'ongoing']);
            })
            ->first();

        if (!$assignment) {
            Log::warning('API Get Exam Details Failed - Not accessible', [
                'student_id' => $studentId,
                'exam_id' => $examId
            ]);
            return response()->json([
                'message' => 'Exam not found or not accessible',
            ], 404);
        }

        Log::debug('API Get Exam Details - Access Verified', [
            'student_id' => $studentId,
            'exam_id' => $examId,
            'assignment_id' => $assignment->assignment_id
        ]);

        $exam = $assignment->exam;

        // Check if student has an attempt
        $attempt = ExamAttempt::where('exam_assignment_id', $assignment->assignment_id)
            ->where('student_id', $studentId)
            ->first();

        // Format exam data
        $sections = $exam->sections->map(function ($section) {
            $items = $section->items->sortBy('order')->map(function ($item) {
                // For enum questions, append type (ordered/unordered) to item_type
                $itemType = $item->item_type;
                if ($itemType === 'enum' && $item->enum_type) {
                    $itemType = 'enum_' . $item->enum_type; // e.g., 'enum_ordered' or 'enum_unordered'
                }
                
                return [
                    'item_id' => $item->item_id,
                    'question' => $item->question,
                    'item_type' => $itemType,
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

        Log::info('API Get Exam Details Success', [
            'student_id' => $studentId,
            'exam_id' => $examId,
            'section_count' => $sections->count(),
            'has_attempt' => $attempt !== null
        ]);

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
                'duration_seconds' => $exam->duration * 60,
                'total_points' => $exam->total_points,
                'no_of_items' => $exam->no_of_items,
                'requiresOtp' => !empty($exam->exam_password),
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
     * Verify exam password (OTP) before allowing access
     */
    public function verifyOtp(Request $request, $examId)
    {
        $request->validate([
            'studentId' => 'required',
            'otp' => 'required|string',
        ]);

        $studentId = $request->user()->id;

        Log::debug('API Verify OTP Request', [
            'student_id' => $studentId,
            'exam_id' => $examId,
            'provided_student_id' => $request->studentId,
            'otp_provided' => !empty($request->otp),
            'ip' => $request->ip()
        ]);

        // Verify the provided studentId matches authenticated user
        if ($request->studentId != $studentId) {
            Log::warning('API Verify OTP Failed - Student ID mismatch', [
                'authenticated_student_id' => $studentId,
                'provided_student_id' => $request->studentId,
                'exam_id' => $examId
            ]);
            
            return response()->json([
                'verified' => false,
                'message' => 'Student ID mismatch',
            ], 403);
        }

        // Get the exam
        $exam = Exam::find($examId);

        if (!$exam || !in_array($exam->status, ['approved', 'ongoing'])) {
            Log::warning('API Verify OTP Failed - Exam not found or not available', [
                'student_id' => $studentId,
                'exam_id' => $examId,
                'exam_found' => $exam ? 'yes' : 'no',
                'exam_status' => $exam ? $exam->status : 'N/A'
            ]);
            
            return response()->json([
                'verified' => false,
                'message' => 'Exam not found or not available',
            ], 404);
        }

        // Check if exam requires password
        if (empty($exam->exam_password)) {
            Log::info('API Verify OTP - No password required', [
                'student_id' => $studentId,
                'exam_id' => $examId
            ]);
            
            // No password required, auto-verify
            return response()->json([
                'verified' => true,
                'message' => 'No password required for this exam',
            ]);
        }

        // Verify password
        if ($request->otp === $exam->exam_password) {
            Log::info('API Verify OTP Success - Password verified', [
                'student_id' => $studentId,
                'exam_id' => $examId,
                'exam_title' => $exam->exam_title
            ]);
            
            return response()->json([
                'verified' => true,
                'message' => 'Password verified successfully',
            ]);
        }

        Log::warning('API Verify OTP Failed - Invalid password', [
            'student_id' => $studentId,
            'exam_id' => $examId,
            'exam_title' => $exam->exam_title
        ]);

        return response()->json([
            'verified' => false,
            'message' => 'Invalid exam password',
        ], 400);
    }

    /**
     * Get all completed exams for a student
     */
    public function completedExams(Request $request)
    {
        $studentId = $request->user()->id;

        // Get all submitted attempts for this student
        $attempts = ExamAttempt::where('student_id', $studentId)
            ->where('status', 'submitted')
            ->with(['examAssignment.exam.subject'])
            ->whereHas('examAssignment.exam', function($query) {
                $query->whereIn('status', ['approved', 'ongoing']);
            })
            ->orderBy('end_time', 'desc')
            ->get();

        $completedExams = $attempts->map(function ($attempt) {
            $exam = $attempt->examAssignment->exam;
            
            return [
                'attempt_id' => $attempt->attempt_id,
                'exam_id' => $exam->exam_id,
                'title' => $exam->exam_title,
                'subject' => [
                    'id' => $exam->subject->subject_id ?? null,
                    'code' => $exam->subject->subject_code ?? null,
                    'name' => $exam->subject->subject_name ?? null,
                ],
                'score' => $attempt->score,
                'total_points' => $exam->total_points,
                'percentage' => $exam->total_points > 0
                    ? round(($attempt->score / $exam->total_points) * 100, 2)
                    : 0,
                'completed_at' => $attempt->end_time,
                'submitted_at' => $attempt->end_time,
                'resultsReleased' => $attempt->score !== null,
            ];
        });

        return response()->json([
            'completed_exams' => $completedExams,
        ]);
    }

    /**
     * Start an exam attempt
     */
    public function startAttempt(Request $request)
    {
        try {
            $request->validate([
                'exam_assignment_id' => 'required|integer|exists:exam_assignments,assignment_id',
            ]);

            $studentId = $request->user()->id;
            $assignmentId = $request->exam_assignment_id;

            Log::debug('API Start Attempt Request', [
                'student_id' => $studentId,
                'exam_assignment_id' => $assignmentId,
                'request_data' => $request->all(),
            ]);

            // Verify student has access
            $assignment = ExamAssignment::find($assignmentId);
            
            if (!$assignment || !$assignment->exam || !in_array($assignment->exam->status, ['approved', 'ongoing'])) {
                Log::warning('API Start Attempt Failed - Exam not found or not available', [
                    'student_id' => $studentId,
                    'exam_assignment_id' => $assignmentId,
                    'assignment_found' => $assignment ? 'yes' : 'no',
                    'exam_found' => ($assignment && $assignment->exam) ? 'yes' : 'no',
                    'exam_status' => ($assignment && $assignment->exam) ? $assignment->exam->status : 'N/A',
                ]);
                
                return response()->json([
                    'message' => 'Exam not found or not available',
                ], 404);
            }
        
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

            Log::info('API Start Attempt - Resuming Existing', [
                'student_id' => $studentId,
                'attempt_id' => $existingAttempt->attempt_id,
                'exam_assignment_id' => $assignmentId,
                'message' => '🔄 RETURNING EXISTING ATTEMPT ID TO MOBILE APP',
            ]);

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

        Log::info('API Start Attempt - New Attempt Created', [
            'student_id' => $studentId,
            'attempt_id' => $attempt->attempt_id,
            'exam_assignment_id' => $assignmentId,
            'exam_id' => $assignment->exam->exam_id,
            'message' => '✅ UNIQUE ATTEMPT ID GENERATED AND RETURNED TO MOBILE APP',
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
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('API Start Attempt Failed - Validation Error', [
                'student_id' => $request->user()->id ?? 'unknown',
                'request_data' => $request->all(),
                'errors' => $e->errors(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('API Start Attempt Failed - Exception', [
                'student_id' => $request->user()->id ?? 'unknown',
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'An error occurred while starting the exam attempt',
                'error' => $e->getMessage(),
            ], 500);
        }
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
            'duration_taken' => 'required|integer|min:0', // Duration in seconds from mobile app
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

        // Get the exam to check duration
        $exam = $attempt->examAssignment->exam;
        
        // Get duration from mobile app (in seconds)
        $durationTakenSeconds = $request->duration_taken;
        $durationTakenMinutes = round($durationTakenSeconds / 60, 2);
        $allowedMinutes = $exam->duration;
        $allowedSeconds = $allowedMinutes * 60;
        
        // Check if time limit was exceeded (but still accept the submission)
        $timeExceeded = $durationTakenSeconds > $allowedSeconds;
        
        if ($timeExceeded) {
            Log::warning('API Submit Attempt - Time limit exceeded (auto-submitted)', [
                'attempt_id' => $attemptId,
                'student_id' => $studentId,
                'duration_taken_seconds' => $durationTakenSeconds,
                'duration_taken_minutes' => $durationTakenMinutes,
                'allowed_minutes' => $allowedMinutes,
                'allowed_seconds' => $allowedSeconds,
                'exceeded_by_seconds' => $durationTakenSeconds - $allowedSeconds
            ]);
        } else {
            Log::debug('API Submit Attempt Request', [
                'attempt_id' => $attemptId,
                'student_id' => $studentId,
                'duration_taken_seconds' => $durationTakenSeconds,
                'duration_taken_minutes' => $durationTakenMinutes,
                'allowed_minutes' => $allowedMinutes
            ]);
        }

        // Calculate score and store answers
        $totalScore = 0;
        $answers = $request->answers;

        foreach ($answers as $answerData) {
            $item = ExamItem::find($answerData['item_id']);

            if (!$item) {
                continue;
            }

            $studentAnswer = $answerData['answer'];
            $originalStudentAnswer = $studentAnswer; // Keep original for logging

            // Auto-grade based on item type
            $isCorrect = false;
            $pointsEarned = 0;

            // STEP 1: Convert/normalize student answer to match database format
            switch ($item->item_type) {
                case 'mcq':
                    // Mobile app sends option KEY (A, B, C, D)
                    // Convert to index (0, 1, 2, 3) to match database format
                    
                    if (is_string($studentAnswer) && preg_match('/^[A-Z]$/i', $studentAnswer)) {
                        // Convert letter to index: A=0, B=1, C=2, D=3
                        $studentAnswer = ord(strtoupper($studentAnswer)) - ord('A');
                    } elseif (is_numeric($studentAnswer)) {
                        // Already a number, convert to int
                        $studentAnswer = (int)$studentAnswer;
                    } else {
                        // Invalid format
                        $studentAnswer = null;
                    }
                    break;

                case 'torf':
                    // Mobile app SHOULD send "True" or "False" (or "true"/"false")
                    // BUT if mobile app treats it as MCQ, it might send "A"/"B" or "0"/"1"
                    // Handle both cases for backward compatibility
                    
                    if (is_string($studentAnswer)) {
                        $trimmed = trim($studentAnswer);
                        
                        // Check if it's MCQ format (A, B, 0, 1)
                        if (preg_match('/^[AB]$/i', $trimmed)) {
                            // Convert A=True, B=False
                            $studentAnswer = (strtoupper($trimmed) === 'A') ? 'true' : 'false';
                        } elseif ($trimmed === '0' || $trimmed === '1') {
                            // Convert 0=True, 1=False
                            $studentAnswer = ($trimmed === '0') ? 'true' : 'false';
                        } else {
                            // Already in True/False format, just normalize
                            $studentAnswer = strtolower($trimmed);
                        }
                    } else {
                        $studentAnswer = null;
                    }
                    break;

                case 'enum':
                    // Mobile app might send comma-separated string: "Red, Blue, Green"
                    // Convert to array and normalize
                    if (is_string($studentAnswer)) {
                        if (strpos($studentAnswer, ',') !== false) {
                            $studentAnswer = array_map('trim', explode(',', $studentAnswer));
                        } else {
                            $studentAnswer = [trim($studentAnswer)];
                        }
                    } elseif (!is_array($studentAnswer)) {
                        $studentAnswer = [$studentAnswer];
                    }
                    // Normalize array values (lowercase and trim)
                    $studentAnswer = array_map('trim', $studentAnswer);
                    break;

                case 'iden':
                    // Normalize: lowercase and trim
                    if (is_string($studentAnswer)) {
                        $studentAnswer = strtolower(trim($studentAnswer));
                    } else {
                        $studentAnswer = null;
                    }
                    break;

                case 'essay':
                    // Keep as-is, will need manual grading
                    break;
            }

            // STEP 2: Validate the normalized answer against the answer key
            switch ($item->item_type) {
                case 'mcq':
                    // Get correct answer indices from 'answer' field
                    $correctIndices = $item->answer ?? [];
                    if (!is_array($correctIndices)) {
                        $correctIndices = json_decode($correctIndices, true) ?? [];
                    }
                    
                    // Check if student's answer index is in the correct answers array
                    $isCorrect = ($studentAnswer !== null && in_array($studentAnswer, $correctIndices));
                    break;

                case 'torf':
                    // Get correct answer from 'answer' field: can be string ("True"/"False") or object {"correct":"true"}
                    $dbAnswer = $item->answer ?? null;
                    $correctValue = null;
                    if (is_array($dbAnswer)) {
                        // Eloquent cast: array/object
                        if (isset($dbAnswer['correct'])) {
                            $correctValue = strtolower(trim($dbAnswer['correct']));
                        }
                    } elseif (is_string($dbAnswer)) {
                        // Could be JSON or plain string
                        $decoded = json_decode($dbAnswer, true);
                        if (is_array($decoded) && isset($decoded['correct'])) {
                            $correctValue = strtolower(trim($decoded['correct']));
                        } else {
                            // Plain string: "True" or "False"
                            $correctValue = strtolower(trim($dbAnswer));
                        }
                    }
                    // Debug logging
                    Log::debug('TORF Validation', [
                        'item_id' => $item->item_id,
                        'answer_raw' => $item->answer,
                        'answer_type' => gettype($item->answer),
                        'correctValue' => $correctValue,
                        'studentAnswer' => $studentAnswer,
                        'student_type' => gettype($studentAnswer)
                    ]);
                    // Direct comparison (both are now lowercase)
                    $isCorrect = ($studentAnswer !== null && $correctValue !== null && $studentAnswer === $correctValue);
                    break;

                case 'enum':
                    // Get correct answer from database
                    $correctAnswerData = $item->answer ?? [];
                    if (!is_array($correctAnswerData)) {
                        $correctAnswerData = json_decode($correctAnswerData, true) ?? [];
                    }
                    
                    // Normalize correct answers (lowercase and trim)
                    $normalizedCorrectAnswers = array_map('strtolower', array_map('trim', $correctAnswerData));
                    $normalizedStudentAnswers = array_map('strtolower', $studentAnswer);
                    
                    // Debug logging
                    Log::debug('ENUM Validation', [
                        'item_id' => $item->item_id,
                        'enum_type' => $item->enum_type,
                        'answer_raw' => $item->answer,
                        'answer_type' => gettype($item->answer),
                        'correctAnswerData' => $correctAnswerData,
                        'normalizedCorrectAnswers' => $normalizedCorrectAnswers,
                        'studentAnswer_raw' => $studentAnswer,
                        'student_type' => gettype($studentAnswer),
                        'normalizedStudentAnswers' => $normalizedStudentAnswers
                    ]);
                    
                    // Check if ordered or unordered enumeration
                    $enumType = $item->enum_type ?? 'ordered';
                    
                    if ($enumType === 'ordered') {
                        // ORDERED: Must match in exact order (case-insensitive)
                        $isCorrect = count($normalizedStudentAnswers) === count($normalizedCorrectAnswers);
                        if ($isCorrect) {
                            foreach ($normalizedStudentAnswers as $index => $answer) {
                                if (!isset($normalizedCorrectAnswers[$index]) || 
                                    $answer !== $normalizedCorrectAnswers[$index]) {
                                    $isCorrect = false;
                                    break;
                                }
                            }
                        }
                    } else {
                        // UNORDERED: Order doesn't matter (case-insensitive)
                        // Robust normalization: trim, lowercase, remove extra whitespace
                        $normalizedCorrectAnswers = array_unique(array_map(function($ans) {
                            return strtolower(trim(preg_replace('/\s+/', ' ', $ans)));
                        }, $correctAnswerData));
                        $normalizedStudentAnswers = array_unique(array_map(function($ans) {
                            return strtolower(trim(preg_replace('/\s+/', ' ', $ans)));
                        }, $studentAnswer));

                        $totalCorrectAnswers = count($normalizedCorrectAnswers);

                        // Only count unique correct matches
                        $matchingAnswers = array_intersect($normalizedStudentAnswers, $normalizedCorrectAnswers);
                        $correctCount = count($matchingAnswers);

                        // Calculate partial points
                        if ($correctCount > 0 && $totalCorrectAnswers > 0) {
                            // Award points proportionally: (correct answers / total answers) * total points
                            $pointsEarned = ($correctCount / $totalCorrectAnswers) * $item->points_awarded;
                            $pointsEarned = round($pointsEarned, 2); // Round to 2 decimal places
                            // Mark as correct if AT LEAST ONE answer is correct
                            $isCorrect = true;
                        } else {
                            $pointsEarned = 0;
                            $isCorrect = false;
                        }
                    }
                    break;

                case 'iden':
                    // Get expected answer
                    $correctAnswer = $item->expected_answer;
                    
                    if ($correctAnswer) {
                        $normalizedCorrectAnswer = strtolower(trim($correctAnswer));
                        // Direct comparison (both already normalized)
                        $isCorrect = ($studentAnswer !== null && $studentAnswer === $normalizedCorrectAnswer);
                    } else {
                        $isCorrect = false;
                    }
                    break;

                case 'essay':
                    // Essays need manual grading - don't auto-grade
                    $isCorrect = null; // null indicates needs manual grading
                    break;
            }

            // Award points based on correctness
            // For unordered enum, $pointsEarned is already calculated with partial credit
            // For other types, award full points if correct
            if ($isCorrect && $pointsEarned === 0) {
                $pointsEarned = $item->points_awarded;
            }
            
            // Add to total score (even partial points)
            if ($pointsEarned > 0) {
                $totalScore += $pointsEarned;
            }

            // Log answer validation for debugging
            Log::debug('Answer Validation', [
                'item_id' => $item->item_id,
                'item_type' => $item->item_type,
                'original_answer' => $originalStudentAnswer,
                'normalized_answer' => $studentAnswer,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
                'points_possible' => $item->points_awarded
            ]);

            // Store student answer in exam_answers table
            // Store the NORMALIZED/CONVERTED answer for consistency with database format
            ExamAnswer::create([
                'attempt_id' => $attemptId,
                'item_id' => $item->item_id,
                'answer_text' => is_array($studentAnswer) ? json_encode($studentAnswer) : $studentAnswer,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ]);
        }

        // Update attempt
        $attempt->update([
            'end_time' => Carbon::now(),
            'status' => 'submitted',
            'score' => $totalScore,
        ]);

        Log::info('API Submit Attempt Success', [
            'attempt_id' => $attemptId,
            'student_id' => $studentId,
            'score' => $totalScore,
            'total_points' => $exam->total_points,
            'duration_taken_seconds' => $durationTakenSeconds,
            'duration_taken_minutes' => $durationTakenMinutes,
            'allowed_minutes' => $allowedMinutes,
            'time_exceeded' => $timeExceeded
        ]);

        return response()->json([
            'message' => $timeExceeded 
                ? 'Exam auto-submitted due to time limit' 
                : 'Exam submitted successfully',
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

        $attempt = ExamAttempt::with(['examAssignment.exam.subject', 'answers.examItem'])
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

        // Format answers with question details
        $answersData = $attempt->answers->map(function ($answer) {
            $item = $answer->examItem;
            return [
                'item_id' => $item->item_id,
                'question' => $item->item_content,
                'item_type' => $item->item_type,
                'student_answer' => json_decode($answer->answer_text) ?? $answer->answer_text,
                'correct_answer' => $item->expected_answer,
                'is_correct' => $answer->is_correct,
                'points_awarded' => $item->points_awarded,
                'points_earned' => $answer->points_earned,
            ];
        });

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
            'answers' => $answersData,
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
