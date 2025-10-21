<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamItem;
use App\Models\Section;
use App\Models\ExamAssignment;
use Illuminate\Support\Facades\DB;

class ExamStatisticsController extends Controller
{
    /**
     * Display a listing of exams
     */
    public function index(Request $request)
    {
        $query = Exam::with(['subject', 'teacher', 'examAssignments.class']);

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
            // Get teacher name
            $exam->teacher_name = $exam->teacher ? 
                $exam->teacher->first_name . ' ' . $exam->teacher->last_name : 
                'No Teacher';
            
            // Get classes
            $classes = $exam->examAssignments->pluck('class.title')->unique();
            $exam->classes_display = $classes->take(2)->implode(', ');
            if ($classes->count() > 2) {
                $exam->classes_display .= ' and more...';
            }
            
            // Map status display
            $exam->status_display = match($exam->status) {
                'draft' => 'Draft',
                'approved' => 'Ongoing',
                'ongoing' => 'Ongoing',
                'archived' => 'Archived',
                default => ucfirst($exam->status)
            };
        }

        return view('admin.exam-statistics.index', compact('exams'));
    }

    /**
     * View exam details with questions
     */
    public function show($id)
    {
        $exam = Exam::with([
            'subject',
            'teacher',
            'sections' => function($query) {
                $query->orderBy('section_order');
            },
            'sections.examItems' => function($query) {
                $query->orderBy('order');
            }
        ])->findOrFail($id);

        // Parse options and answers for each item
        foreach ($exam->sections as $section) {
            foreach ($section->examItems as $item) {
                // Decode JSON fields
                if ($item->options) {
                    $item->options_array = json_decode($item->options, true);
                }
                if ($item->answer) {
                    $item->answer_array = json_decode($item->answer, true);
                }
            }
        }

        return view('admin.exam-statistics.show', compact('exam'));
    }

    /**
     * View exam statistics (for archived exams)
     */
    public function stats($id)
    {
        $exam = Exam::with([
            'subject',
            'examAssignments.class',
            'examAssignments.attempts.student'
        ])->findOrFail($id);

        // Calculate statistics
        $totalAttempts = 0;
        $completedAttempts = 0;
        $averageScore = 0;
        $scores = [];

        foreach ($exam->examAssignments as $assignment) {
            foreach ($assignment->attempts as $attempt) {
                $totalAttempts++;
                if ($attempt->status === 'submitted') {
                    $completedAttempts++;
                    $scores[] = $attempt->score;
                }
            }
        }

        if (count($scores) > 0) {
            $averageScore = array_sum($scores) / count($scores);
        }

        $stats = [
            'total_attempts' => $totalAttempts,
            'completed_attempts' => $completedAttempts,
            'average_score' => round($averageScore, 2),
            'highest_score' => count($scores) > 0 ? max($scores) : 0,
            'lowest_score' => count($scores) > 0 ? min($scores) : 0,
        ];

        return view('admin.exam-statistics.stats', compact('exam', 'stats'));
    }

    /**
     * Approve an exam
     */
    public function approve($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            
            $exam->update([
                'status' => 'approved',
                'approved_date' => now(),
                'approved_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exam approved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve exam: ' . $e->getMessage()
            ], 500);
        }
    }
}