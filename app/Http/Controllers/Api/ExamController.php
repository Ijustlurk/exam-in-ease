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
use App\Services\EssayGradingService;
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

            // Initialize AI grading fields
            $aiFeedback = null;
            $aiConfidence = null;
            $requiresManualReview = false;

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
                    // Try AI grading if available
                    if (EssayGradingService::isAvailable()) {
                        try {
                            $gradingService = new EssayGradingService();
                            $gradingResult = $gradingService->gradeEssay(
                                $item->question,
                                $item->expected_answer,
                                $originalStudentAnswer,
                                $item->points_awarded
                            );

                            // Store AI grading results
                            $isCorrect = $gradingResult['is_correct'];
                            $pointsEarned = $gradingResult['points_earned'];
                            $aiFeedback = $gradingResult['feedback'];
                            $aiConfidence = $gradingResult['confidence'];
                            $requiresManualReview = ($isCorrect === null);

                            Log::info('Essay graded by AI', [
                                'item_id' => $item->item_id,
                                'points_earned' => $pointsEarned,
                                'confidence' => $aiConfidence,
                                'requires_manual_review' => $requiresManualReview
                            ]);
                        } catch (\Exception $e) {
                            Log::error('AI grading failed, falling back to manual', [
                                'item_id' => $item->item_id,
                                'error' => $e->getMessage()
                            ]);
                            // Fallback to manual grading
                            $isCorrect = null;
                            $aiFeedback = 'AI grading failed. Requires manual review.';
                            $aiConfidence = 0;
                            $requiresManualReview = true;
                        }
                    } else {
                        // AI not available - manual grading needed
                        $isCorrect = null;
                        $aiFeedback = null;
                        $aiConfidence = null;
                        $requiresManualReview = true;
                    }
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
                'ai_feedback' => $aiFeedback,
                'ai_confidence' => $aiConfidence,
                'requires_manual_review' => $requiresManualReview,
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
        // Log incoming request
        \Log::debug('=== EXAM RESULTS API REQUEST ===', [
            'attempt_id' => $attemptId,
            'student_id' => $request->user()->id,
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
            'request_headers' => $request->headers->all(),
            'request_ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        try {
            $studentId = $request->user()->id;

            $attempt = ExamAttempt::with([
                'examAssignment.exam.sections.items',
                'answers'
            ])->find($attemptId);

            if (!$attempt) {
                $errorResponse = [
                    'message' => 'Exam attempt not found',
                ];
                \Log::debug('=== EXAM RESULTS API RESPONSE (ERROR) ===', [
                    'attempt_id' => $attemptId,
                    'student_id' => $studentId,
                    'status_code' => 404,
                    'error' => 'Exam attempt not found',
                    'response_data' => $errorResponse,
                    'timestamp' => now()->toDateTimeString(),
                ]);
                return response()->json($errorResponse, 404);
            }

            // Verify ownership
            if ($attempt->student_id !== $studentId) {
                $errorResponse = [
                    'message' => 'You do not have permission to view these results',
                ];
                \Log::debug('=== EXAM RESULTS API RESPONSE (ERROR) ===', [
                    'attempt_id' => $attemptId,
                    'student_id' => $studentId,
                    'attempt_owner_id' => $attempt->student_id,
                    'status_code' => 403,
                    'error' => 'Permission denied - ownership mismatch',
                    'response_data' => $errorResponse,
                    'timestamp' => now()->toDateTimeString(),
                ]);
                return response()->json($errorResponse, 403);
            }

            // Only show results if submitted
            if ($attempt->status !== 'submitted') {
                $errorResponse = [
                    'message' => 'Results not yet released',
                ];
                \Log::debug('=== EXAM RESULTS API RESPONSE (ERROR) ===', [
                    'attempt_id' => $attemptId,
                    'student_id' => $studentId,
                    'attempt_status' => $attempt->status,
                    'status_code' => 400,
                    'error' => 'Results not released - attempt not submitted',
                    'response_data' => $errorResponse,
                    'timestamp' => now()->toDateTimeString(),
                ]);
                return response()->json($errorResponse, 400);
            }

            $exam = $attempt->examAssignment->exam;

            // Create a map of student answers for quick lookup
            $studentAnswersMap = [];
            foreach ($attempt->answers as $answer) {
                $studentAnswersMap[$answer->item_id] = $answer;
            }

            // Build results array with correctness data
            $results = [];
            $totalCorrect = 0;
            $totalIncorrect = 0;
            $totalUnanswered = 0;
            $manuallyGraded = 0;
            $totalPointsAwarded = 0;
            $totalPointsPossible = 0;

            foreach ($exam->sections as $section) {
                foreach ($section->items as $item) {
                    $mobileType = $this->mapToMobileType($item->item_type);
                    $studentAnswer = $studentAnswersMap[$item->item_id] ?? null;
                    
                    // Determine correctness and points from database
                    $isCorrect = null;
                    $pointsAwarded = 0;
                    
                    if (!$studentAnswer || ($studentAnswer->answer_text === null || $studentAnswer->answer_text === '')) {
                        // Unanswered question
                        $isCorrect = false;
                        $pointsAwarded = 0;
                        $totalUnanswered++;
                    } elseif ($item->item_type === 'essay') {
                        // Essay - manually graded (isCorrect stays null)
                        $isCorrect = null;
                        $pointsAwarded = $studentAnswer->points_earned ?? 0;
                        $manuallyGraded++;
                    } else {
                        // Auto-graded questions - ALWAYS use database values
                        $isCorrect = $studentAnswer->is_correct !== null ? (bool) $studentAnswer->is_correct : false;
                        $pointsAwarded = $studentAnswer->points_earned ?? 0;
                        
                        \Log::debug('Item grading details', [
                            'item_id' => $item->item_id,
                            'type' => $item->item_type,
                            'db_is_correct' => $studentAnswer->is_correct,
                            'computed_is_correct' => $isCorrect,
                            'db_points_earned' => $studentAnswer->points_earned,
                            'computed_points' => $pointsAwarded
                        ]);
                        
                        // Count for statistics
                        if ($isCorrect) {
                            $totalCorrect++;
                        } else {
                            $totalIncorrect++;
                        }
                    }
                    
                    $totalPointsAwarded += $pointsAwarded;
                    $totalPointsPossible += $item->points_awarded;
                    
                    // Resolve student answer to actual text for MCQ/TORF
                    $studentAnswerText = null;
                    if ($studentAnswer && ($studentAnswer->answer_text !== null && $studentAnswer->answer_text !== '')) {
                        if (in_array($item->item_type, ['mcq', 'torf'])) {
                            // For MCQ/TORF, resolve the key to the actual choice text
                            $studentAnswerText = $this->resolveChoiceText(
                                $item,
                                $studentAnswer->answer_text
                            );
                            \Log::debug('Resolved student answer', [
                                'item_id' => $item->item_id,
                                'type' => $item->item_type,
                                'raw_answer' => $studentAnswer->answer_text,
                                'resolved_text' => $studentAnswerText
                            ]);
                        } else {
                            // For other types (iden, enum, essay), use the raw text
                            $studentAnswerText = $studentAnswer->answer_text;
                        }
                    }
                    
                    // Resolve correct answer to actual text for MCQ/TORF
                    $correctAnswerText = null;
                    if ($item->item_type !== 'essay' && ($item->expected_answer !== null && $item->expected_answer !== '')) {
                        if (in_array($item->item_type, ['mcq', 'torf'])) {
                            $correctAnswerText = $this->resolveChoiceText(
                                $item,
                                $item->expected_answer
                            );
                            \Log::debug('Resolved correct answer', [
                                'item_id' => $item->item_id,
                                'type' => $item->item_type,
                                'raw_answer' => $item->expected_answer,
                                'resolved_text' => $correctAnswerText,
                                'options' => $item->options
                            ]);
                        } else {
                            $correctAnswerText = $item->expected_answer;
                        }
                    }
                    
                    $resultItem = [
                        'id' => 'item_' . $item->item_id,
                        'itemId' => $item->item_id,
                        'sectionId' => $section->section_id,
                        'sectionTitle' => $section->section_title,
                        'type' => $mobileType,
                        'originalType' => $item->item_type,
                        'question' => $item->question,
                        'choices' => $this->formatChoices($item),
                        'correctAnswer' => $correctAnswerText,
                        'studentAnswer' => $studentAnswerText,
                        'isCorrect' => $isCorrect,
                        'pointsAwarded' => (float) $pointsAwarded,
                        'maxPoints' => (float) $item->points_awarded,
                        'order' => $item->order ?? 0,
                    ];

                    // Add directions if available
                    if (!empty($section->section_directions)) {
                        $resultItem['directions'] = $section->section_directions;
                    }

                    // Add feedback if available (for manually graded)
                    if ($studentAnswer && !empty($studentAnswer->feedback)) {
                        $resultItem['feedback'] = $studentAnswer->feedback;
                    }

                    $results[] = $resultItem;
                }
            }

            $responseData = [
                'attempt' => [
                    'attempt_id' => $attempt->attempt_id,
                    'student_id' => $attempt->student_id,
                    'exam_assignment_id' => $attempt->exam_assignment_id,
                    'status' => $attempt->status,
                    'score' => (float) $attempt->score,
                    'total_marks' => (float) $totalPointsPossible,
                    'started_at' => $attempt->start_time,
                    'submitted_at' => $attempt->end_time,
                    'duration_taken' => $attempt->end_time && $attempt->start_time
                        ? strtotime($attempt->end_time) - strtotime($attempt->start_time)
                        : 0,
                ],
                'results' => $results,
                'statistics' => [
                    'totalQuestions' => count($results),
                    'correctAnswers' => $totalCorrect,
                    'incorrectAnswers' => $totalIncorrect,
                    'unanswered' => $totalUnanswered,
                    'manuallyGraded' => $manuallyGraded,
                    'totalPointsAwarded' => (float) $totalPointsAwarded,
                    'totalPointsPossible' => (float) $totalPointsPossible,
                    'percentageScore' => $totalPointsPossible > 0 
                        ? round(($totalPointsAwarded / $totalPointsPossible) * 100, 2)
                        : 0,
                ],
            ];

            // Log successful response
            \Log::debug('=== EXAM RESULTS API RESPONSE (SUCCESS) ===', [
                'attempt_id' => $attemptId,
                'student_id' => $studentId,
                'status_code' => 200,
                'total_questions' => count($results),
                'correct_answers' => $totalCorrect,
                'incorrect_answers' => $totalIncorrect,
                'unanswered' => $totalUnanswered,
                'manually_graded' => $manuallyGraded,
                'score' => $attempt->score,
                'response_data' => $responseData,
                'timestamp' => now()->toDateTimeString(),
            ]);

            return response()->json($responseData);
        } catch (\Exception $e) {
            $errorResponse = [
                'message' => 'An error occurred while fetching results',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ];

            \Log::error('=== EXAM RESULTS API RESPONSE (EXCEPTION) ===', [
                'attempt_id' => $attemptId,
                'student_id' => $request->user()->id ?? null,
                'status_code' => 500,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'response_data' => $errorResponse,
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return response()->json($errorResponse, 500);
        }
    }

    /**
     * Map server question type to mobile app type
     */
    private function mapToMobileType($serverType)
    {
        $typeMap = [
            'mcq' => 'mcq',
            'torf' => 'true_false',
            'iden' => 'identification',
            'enum' => 'enumeration',
            'enum_ordered' => 'enumeration',
            'enum_unordered' => 'enumeration',
            'essay' => 'essay',
        ];

        return $typeMap[$serverType] ?? $serverType;
    }

    /**
     * Format choices for MCQ and True/False questions
     */
    private function formatChoices($item)
    {
        // Only MCQ and True/False have choices
        if (!in_array($item->item_type, ['mcq', 'torf'])) {
            return null;
        }

        if ($item->item_type === 'torf') {
            return [
                ['key' => 'True', 'text' => 'True'],
                ['key' => 'False', 'text' => 'False'],
            ];
        }

        // For MCQ, parse choices from options field
        $options = is_string($item->options) 
            ? json_decode($item->options, true) 
            : $item->options;

        if (!$options || !is_array($options)) {
            return null;
        }

        $choices = [];
        
        // If options is an indexed array [0, 1, 2, 3], generate keys A, B, C, D
        if (array_keys($options) === range(0, count($options) - 1)) {
            foreach ($options as $index => $text) {
                $key = chr(65 + $index); // 65 is ASCII for 'A'
                $choices[] = [
                    'key' => $key,
                    'text' => (string) $text,
                ];
            }
        } else {
            // Options has custom keys (might be letters or numbers)
            foreach ($options as $key => $text) {
                // Convert numeric keys to letters (0->A, 1->B, etc.)
                if (is_numeric($key)) {
                    $key = chr(65 + intval($key));
                }
                
                $choices[] = [
                    'key' => (string) $key,
                    'text' => (string) $text,
                ];
            }
        }

        return $choices;
    }

    /**
     * Check if student answer is correct
     */
    private function checkAnswer($correctAnswer, $studentAnswer, $itemType)
    {
        if (empty($correctAnswer) || empty($studentAnswer)) {
            return false;
        }

        // Normalize both answers
        $correct = trim(strtolower($correctAnswer));
        $student = trim(strtolower($studentAnswer));
        
        if ($itemType === 'mcq' || $itemType === 'torf') {
            // Exact match for MCQ and True/False (case-insensitive)
            return $correct === $student;
        } elseif ($itemType === 'iden') {
            // Case-insensitive match for identification
            return $correct === $student;
        } elseif (in_array($itemType, ['enum', 'enum_ordered', 'enum_unordered'])) {
            // For enumeration, check if all expected items are present
            $correctItems = array_map('trim', explode(',', $correct));
            $studentItems = array_map('trim', explode(',', $student));
            
            // For ordered enumeration, order matters
            if ($itemType === 'enum_ordered') {
                return $correctItems === $studentItems;
            }
            
            // For unordered enumeration, sort before comparing
            sort($correctItems);
            sort($studentItems);
            
            return $correctItems === $studentItems;
        }
        
        return false;
    }

    /**
     * Resolve choice key (A, B, C, True, False, 0, 1, 2) to actual text
     */
    private function resolveChoiceText($item, $answerKey)
    {
        if ($answerKey === null || $answerKey === '') {
            return null;
        }

        // For True/False questions
        if ($item->item_type === 'torf') {
            // Return the key as-is since "True" and "False" are already text
            return $answerKey;
        }

        // For MCQ questions, look up the text from options
        if ($item->item_type === 'mcq') {
            $options = is_string($item->options) 
                ? json_decode($item->options, true) 
                : $item->options;

            if (!$options || !is_array($options)) {
                \Log::warning('MCQ options not available for item', [
                    'item_id' => $item->item_id,
                    'answer_key' => $answerKey
                ]);
                return $answerKey; // Fallback to key if options unavailable
            }

            $answerKeyTrimmed = trim($answerKey);
            
            // Try direct lookup first (for both string and numeric keys)
            if (isset($options[$answerKeyTrimmed])) {
                return (string) $options[$answerKeyTrimmed];
            }

            // If answer key is numeric (like "0", "1", "2"), convert to integer and try
            if (is_numeric($answerKeyTrimmed)) {
                $numericKey = (int) $answerKeyTrimmed;
                if (isset($options[$numericKey])) {
                    return (string) $options[$numericKey];
                }
            }

            // If answer key is a letter (A, B, C, D), convert to index
            $normalizedKey = strtoupper($answerKeyTrimmed);
            if (strlen($normalizedKey) === 1 && $normalizedKey >= 'A' && $normalizedKey <= 'Z') {
                $index = ord($normalizedKey) - 65; // 'A' = 65 in ASCII
                
                // Try numeric index
                if (isset($options[$index])) {
                    return (string) $options[$index];
                }
                
                // Try letter key
                if (isset($options[$normalizedKey])) {
                    return (string) $options[$normalizedKey];
                }
                
                // Try lowercase letter key
                $lowerKey = strtolower($normalizedKey);
                if (isset($options[$lowerKey])) {
                    return (string) $options[$lowerKey];
                }
            }

            // Log if we couldn't resolve the choice
            \Log::warning('Could not resolve MCQ choice text', [
                'item_id' => $item->item_id,
                'answer_key' => $answerKey,
                'available_keys' => array_keys($options)
            ]);

            // Fallback: return the key itself if we can't resolve it
            return $answerKey;
        }

        // For other types, return as-is
        return $answerKey;
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
