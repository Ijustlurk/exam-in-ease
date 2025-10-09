<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSection;
use App\Models\ExamItem;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Display exam dashboard
     */
    public function index(Request $request)
    {
        $query = Exam::with(['user', 'subject'])
            ->where('user_id', Auth::id());

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('exam_title', 'like', "%{$search}%");
        }

        $exams = $query->orderBy('updated_at', 'desc')->get();
        
        // Get first exam for details panel
        $selectedExam = $exams->first();
        
        // Add formatted dates
        if ($selectedExam) {
            $selectedExam->formatted_created_at = $selectedExam->created_at->format('F j, Y');
        }

        return view('instructor.exams.dashboard', compact('exams', 'selectedExam'));
    }

    /**
     * Get exam details (AJAX)
     */
    public function show($id)
    {
        $exam = Exam::with(['user', 'subject'])->findOrFail($id);
        
        $creatorInitials = '';
        if ($exam->user) {
            $nameParts = explode(' ', $exam->user->name);
            $creatorInitials = strtoupper(substr($nameParts[0], 0, 1));
            if (count($nameParts) > 1) {
                $creatorInitials .= strtoupper(substr($nameParts[1], 0, 1));
            }
        }

        return response()->json([
            'exam' => $exam,
            'creator_name' => $exam->user->name ?? 'Unknown',
            'creator_initials' => $creatorInitials,
            'subject_name' => $exam->subject->subject_name ?? 'N/A',
            'formatted_created_at' => $exam->created_at->format('F j, Y'),
            'formatted_updated_at' => $exam->updated_at->format('F j, Y'),
        ]);
    }

    /**
     * Show create exam page
     */
    public function create($examId = null)
    {
        if ($examId) {
            $exam = Exam::with(['sections.items'])->findOrFail($examId);
        } else {
            // Create new exam
            $exam = Exam::create([
                'exam_title' => 'Untitled Exam',
                'exam_desc' => null,
                'subject_id' => 1, // Default subject
                'duration' => 60,
                'total_points' => 0,
                'no_of_items' => 0,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            // Create default section
            $section = ExamSection::create([
                'exam_id' => $exam->exam_id,
                'section_title' => '',
                'section_directions' => '',
                'section_order' => 1
            ]);
        }

        $subjects = Subject::all();
        
        return view('instructor.exams.create', compact('exam', 'subjects'));
    }

    /**
     * Store new exam from dashboard
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_title' => 'required|string|max:200',
            'exam_desc' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,subject_id',
            'duration' => 'required|integer|min:0',
        ]);

        $exam = Exam::create([
            'exam_title' => $validated['exam_title'],
            'exam_desc' => $validated['exam_desc'],
            'subject_id' => $validated['subject_id'],
            'duration' => $validated['duration'],
            'total_points' => 0,
            'no_of_items' => 0,
            'user_id' => Auth::id(),
            'status' => 'draft'
        ]);

        // Create default section
        ExamSection::create([
            'exam_id' => $exam->exam_id,
            'section_title' => '',
            'section_directions' => '',
            'section_order' => 1
        ]);

        return redirect()->route('instructor.exams.create', $exam->exam_id)
            ->with('success', 'Exam created successfully!');
    }

    /**
     * Update exam details
     */
    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'exam_title' => 'sometimes|string|max:200',
            'exam_desc' => 'nullable|string',
            'subject_id' => 'sometimes|exists:subjects,subject_id',
            'duration' => 'sometimes|integer|min:0',
        ]);

        $exam->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Exam updated successfully'
        ]);
    }

    /**
     * Add question to exam
     */
    public function addQuestion(Request $request, $examId)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,section_id',
            'question' => 'required|string',
            'item_type' => 'required|in:mcq,torf,enum,iden,essay',
            'options' => 'nullable|json',
            'answer' => 'nullable|json',
            'expected_answer' => 'nullable|string',
            'points_awarded' => 'required|integer|min:1',
        ]);

        $exam = Exam::findOrFail($examId);
        
        // Get max order
        $maxOrder = ExamItem::where('exam_id', $examId)->max('order') ?? 0;

        $item = ExamItem::create([
            'exam_id' => $examId,
            'exam_section_id' => $validated['section_id'],
            'question' => $validated['question'],
            'item_type' => $validated['item_type'],
            'options' => $validated['options'] ?? null,
            'answer' => $validated['answer'] ?? null,
            'expected_answer' => $validated['expected_answer'] ?? null,
            'points_awarded' => $validated['points_awarded'],
            'order' => $maxOrder + 1
        ]);

        // Update exam totals
        $exam->increment('no_of_items');
        $exam->increment('total_points', $validated['points_awarded']);

        return response()->json([
            'success' => true,
            'item' => $item,
            'message' => 'Question added successfully'
        ]);
    }

    /**
     * Update question
     */
    public function updateQuestion(Request $request, $examId, $itemId)
    {
        $item = ExamItem::where('exam_id', $examId)->findOrFail($itemId);
        $oldPoints = $item->points_awarded;

        $validated = $request->validate([
            'question' => 'sometimes|string',
            'item_type' => 'sometimes|in:mcq,torf,enum,iden,essay',
            'options' => 'nullable|json',
            'answer' => 'nullable|json',
            'expected_answer' => 'nullable|string',
            'points_awarded' => 'sometimes|integer|min:1',
        ]);

        $item->update($validated);

        // Update exam total points if points changed
        if (isset($validated['points_awarded']) && $validated['points_awarded'] != $oldPoints) {
            $exam = Exam::findOrFail($examId);
            $exam->total_points = $exam->total_points - $oldPoints + $validated['points_awarded'];
            $exam->save();
        }

        return response()->json([
            'success' => true,
            'item' => $item,
            'message' => 'Question updated successfully'
        ]);
    }

    /**
     * Delete question
     */
    public function deleteQuestion($examId, $itemId)
    {
        $item = ExamItem::where('exam_id', $examId)->findOrFail($itemId);
        $exam = Exam::findOrFail($examId);

        $exam->decrement('no_of_items');
        $exam->decrement('total_points', $item->points_awarded);

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully'
        ]);
    }

    /**
     * Duplicate question
     */
    public function duplicateQuestion($examId, $itemId)
    {
        $item = ExamItem::where('exam_id', $examId)->findOrFail($itemId);
        $exam = Exam::findOrFail($examId);

        $newItem = $item->replicate();
        $newItem->order = ExamItem::where('exam_id', $examId)->max('order') + 1;
        $newItem->save();

        $exam->increment('no_of_items');
        $exam->increment('total_points', $item->points_awarded);

        return response()->json([
            'success' => true,
            'item' => $newItem,
            'message' => 'Question duplicated successfully'
        ]);
    }

    /**
     * Reorder questions
     */
    public function reorderQuestions(Request $request, $examId)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:exam_items,item_id',
            'direction' => 'required|in:up,down'
        ]);

        $item = ExamItem::where('exam_id', $examId)->findOrFail($validated['item_id']);
        $currentOrder = $item->order;

        if ($validated['direction'] === 'up') {
            $swapItem = ExamItem::where('exam_id', $examId)
                ->where('order', '<', $currentOrder)
                ->orderBy('order', 'desc')
                ->first();
        } else {
            $swapItem = ExamItem::where('exam_id', $examId)
                ->where('order', '>', $currentOrder)
                ->orderBy('order', 'asc')
                ->first();
        }

        if ($swapItem) {
            $tempOrder = $item->order;
            $item->order = $swapItem->order;
            $swapItem->order = $tempOrder;

            $item->save();
            $swapItem->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Question reordered successfully'
        ]);
    }

    /**
     * Update section
     */
    public function updateSection(Request $request, $examId, $sectionId)
    {
        $section = ExamSection::where('exam_id', $examId)->findOrFail($sectionId);

        $validated = $request->validate([
            'section_title' => 'sometimes|string|max:200',
            'section_directions' => 'nullable|string',
        ]);

        $section->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully'
        ]);
    }

    /**
     * Duplicate exam
     */
    public function duplicate($id)
    {
        $exam = Exam::with(['sections.items'])->findOrFail($id);

        $newExam = $exam->replicate();
        $newExam->exam_title = $exam->exam_title . ' (Copy)';
        $newExam->save();

        foreach ($exam->sections as $section) {
            $newSection = $section->replicate();
            $newSection->exam_id = $newExam->exam_id;
            $newSection->save();

            foreach ($section->items as $item) {
                $newItem = $item->replicate();
                $newItem->exam_id = $newExam->exam_id;
                $newItem->exam_section_id = $newSection->section_id;
                $newItem->save();
            }
        }

        return redirect()->route('instructor.exams.index')
            ->with('success', 'Exam duplicated successfully!');
    }

    /**
     * Download exam as PDF or Word document
     */
    public function download($id)
    {
        $exam = Exam::with(['sections.items', 'subject'])->findOrFail($id);
        
        // For now, return a simple text file
        // You can integrate with libraries like DomPDF or PhpWord for better formatting
        
        $content = "EXAM: " . $exam->exam_title . "\n";
        $content .= "Subject: " . ($exam->subject->subject_name ?? 'N/A') . "\n";
        $content .= "Duration: " . $exam->duration . " minutes\n";
        $content .= "Total Points: " . $exam->total_points . "\n";
        $content .= "Total Items: " . $exam->no_of_items . "\n";
        $content .= str_repeat("=", 80) . "\n\n";
        
        foreach ($exam->sections as $sectionIndex => $section) {
            $content .= "SECTION " . ($sectionIndex + 1) . "\n";
            if ($section->section_title) {
                $content .= $section->section_title . "\n";
            }
            if ($section->section_directions) {
                $content .= $section->section_directions . "\n";
            }
            $content .= str_repeat("-", 80) . "\n\n";
            
            foreach ($section->items as $itemIndex => $item) {
                $content .= ($itemIndex + 1) . ". " . $item->question . " (" . $item->points_awarded . " points)\n";
                
                if ($item->item_type === 'mcq') {
                    $options = json_decode($item->options, true);
                    $answers = json_decode($item->answer, true);
                    foreach ($options as $key => $option) {
                        $isCorrect = in_array($key, $answers ?? []) ? ' [CORRECT]' : '';
                        $content .= "   " . chr(65 + $key) . ". " . $option . $isCorrect . "\n";
                    }
                } elseif ($item->item_type === 'torf') {
                    $answer = json_decode($item->answer, true);
                    $content .= "   True [" . ($answer['correct'] === 'true' ? 'CORRECT' : '') . "]\n";
                    $content .= "   False [" . ($answer['correct'] === 'false' ? 'CORRECT' : '') . "]\n";
                } elseif ($item->item_type === 'iden') {
                    $content .= "   Answer: " . $item->expected_answer . "\n";
                } elseif ($item->item_type === 'enum') {
                    $answers = json_decode($item->answer, true);
                    foreach ($answers as $idx => $answer) {
                        $content .= "   " . ($idx + 1) . ". " . $answer . "\n";
                    }
                }
                
                $content .= "\n";
            }
            
            $content .= "\n";
        }
        
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $exam->exam_title) . '.txt';
        
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}