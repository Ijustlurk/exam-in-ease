<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamItem;
use App\Models\Section;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\ExamCollaboration;
use App\Models\ExamAssignment;
use App\Models\UserTeacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $teacherId = Auth::id();
        
        $exams = Exam::where(function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                      ->orWhereHas('collaborations', function($q) use ($teacherId) {
                          $q->where('teacher_id', $teacherId);
                      });
            })
            ->when($search, function($query, $search) {
                $query->where('exam_title', 'like', "%{$search}%");
            })
            ->with(['user', 'subject', 'collaborations.teacher'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Add flag to indicate if teacher is owner or collaborator
        $exams = $exams->map(function($exam) use ($teacherId) {
            $exam->is_owner = $exam->teacher_id == $teacherId;
            $exam->is_collaborator = !$exam->is_owner && $exam->collaborations->contains('teacher_id', $teacherId);
            return $exam;
        });

        $selectedExam = $exams->first();
        
        if ($selectedExam) {
            $selectedExam->formatted_created_at = $selectedExam->created_at->format('F j, Y');
        }

        $subjects = Subject::all();
        
        $classes = ClassModel::whereHas('teacherAssignments', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->where('status', 'Active')
            ->with('subject')
            ->get();

        return view('instructor.dashboard', compact('exams', 'selectedExam', 'subjects', 'classes'));
    }

    /**
     * Show exam editor for creating/editing questions
     */
    public function create($examId)
    {
        $exam = Exam::with(['sections.items' => function($query) {
                $query->withCount('comments')->orderBy('order', 'asc');
            }, 'subject'])
            ->findOrFail($examId);
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this exam.');
        }

        // Add ownership flags
        $exam->is_owner = ($exam->teacher_id == $teacherId);
        $exam->is_collaborator = !$exam->is_owner && $exam->collaborations->contains('teacher_id', $teacherId);

        // If no sections exist, create a default section
        if ($exam->sections->count() === 0) {
            Section::create([
                'exam_id' => $exam->exam_id,
                'section_title' => 'Part I',
                'section_directions' => '',
                'section_order' => 1
            ]);
            
            // Reload exam with sections
            $exam->load(['sections.items' => function($query) {
                $query->withCount('comments')->orderBy('order', 'asc');
            }]);
        }

        return view('instructor.exam.create', compact('exam'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_title' => 'required|string|max:200',
            'exam_desc' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,subject_id',
            'classes' => 'required|array|min:1',
            'classes.*' => 'exists:class,class_id',
            'duration' => 'required|integer|min:0',
            'schedule_start' => 'required|date',
            'schedule_end' => 'required|date|after:schedule_start',
            'exam_password' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $exam = Exam::create([
                'exam_title' => $validated['exam_title'],
                'exam_desc' => $validated['exam_desc'],
                'subject_id' => $validated['subject_id'],
                'schedule_start' => $validated['schedule_start'],
                'schedule_end' => $validated['schedule_end'],
                'duration' => $validated['duration'],
                'total_points' => 0,
                'no_of_items' => 0,
                'teacher_id' => Auth::id(),
                'status' => 'draft',
                'exam_password' => $validated['exam_password'] ?? null
            ]);

            // Create exam assignments for selected classes
            foreach ($validated['classes'] as $classId) {
                ExamAssignment::create([
                    'class_id' => $classId,
                    'exam_id' => $exam->exam_id
                ]);
            }

            // Create exam collaboration entry for the creator/author
            ExamCollaboration::create([
                'exam_id' => $exam->exam_id,
                'teacher_id' => Auth::id(),
                'role' => 'owner'
            ]);

            DB::commit();

            // ✅ Redirect to exam editor to add questions
            return redirect()
                ->route('instructor.exams.create', $exam->exam_id)
                ->with('success', 'Exam "' . $exam->exam_title . '" created successfully! Now add your questions.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create exam: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $exam = Exam::with(['user', 'subject', 'collaborations.teacher', 'examItems'])
            ->findOrFail($id);
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this exam.');
        }

        return view('instructor.exams.show', compact('exam'));
    }

    public function update(Request $request, $id)
    {
        try {
            $exam = Exam::findOrFail($id);
            
            $teacherId = Auth::id();
            $hasAccess = $exam->teacher_id == $teacherId || 
                         $exam->collaborations->contains('teacher_id', $teacherId);
            
            if (!$hasAccess) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Prevent editing archived exams
            if ($exam->status === 'archived') {
                return response()->json(['success' => false, 'message' => 'Cannot edit archived exams'], 403);
            }

            // Log incoming request for debugging
            \Log::info('Update Exam Request', [
                'exam_id' => $id,
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'exam_title' => 'sometimes|required|string|max:200',
                'exam_desc' => 'nullable|string',
                'subject_id' => 'sometimes|required|exists:subjects,subject_id',
                'term' => 'nullable|string',
                'duration' => 'sometimes|required|integer|min:0',
                'schedule_start' => 'sometimes|required|date',
                'schedule_end' => 'sometimes|required|date|after:schedule_start',
                'selected_classes' => 'nullable|string', // Comma-separated class IDs
                'status' => 'sometimes|required|in:draft,for approval,approved,ongoing,archived',
            ]);

            DB::beginTransaction();
            
            // Update exam fields
            $updateData = array_filter($validated, function($key) {
                return $key !== 'selected_classes';
            }, ARRAY_FILTER_USE_KEY);
            
            \Log::info('Update Data', ['data' => $updateData]);
            
            $exam->update($updateData);

            // Update class assignments if provided
            if (isset($validated['selected_classes']) && !empty($validated['selected_classes'])) {
                $classIds = explode(',', $validated['selected_classes']);
                $classIds = array_filter($classIds); // Remove empty values
                
                \Log::info('Updating class assignments', ['class_ids' => $classIds]);
                
                // Delete existing assignments
                ExamAssignment::where('exam_id', $exam->exam_id)->delete();
                
                // Create new assignments
                foreach ($classIds as $classId) {
                    ExamAssignment::create([
                        'exam_id' => $exam->exam_id,
                        'class_id' => trim($classId)
                    ]);
                }
            }

            DB::commit();
            
            \Log::info('Exam updated successfully', ['exam_id' => $id]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Exam updated successfully',
                'exam' => $exam->fresh()
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation Error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update Exam Error', [
                'exam_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update exam: ' . $e->getMessage()
            ], 500);
        }
    }

    public function duplicate($id)
    {
        $originalExam = Exam::with('examItems')->findOrFail($id);
        
        $teacherId = Auth::id();
        $hasAccess = $originalExam->teacher_id == $teacherId || 
                     $originalExam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return back()->with('error', 'Unauthorized to duplicate this exam.');
        }

        DB::beginTransaction();
        try {
            $newExam = $originalExam->replicate();
            $newExam->exam_title = $originalExam->exam_title . ' (Copy)';
            $newExam->teacher_id = Auth::id();
            $newExam->status = 'draft';
            $newExam->approved_by = null;
            $newExam->approved_date = null;
            $newExam->save();

            foreach ($originalExam->examItems as $item) {
                $newItem = $item->replicate();
                $newItem->exam_id = $newExam->exam_id;
                $newItem->save();
            }

            DB::commit();

            return redirect()
                ->route('instructor.exams.index')
                ->with('success', 'Exam duplicated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to duplicate exam: ' . $e->getMessage());
        }
    }

    // AJAX Methods for Question Editor

    public function addQuestion(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent editing archived exams
        if ($exam->status === 'archived') {
            return response()->json(['error' => 'Cannot edit archived exams'], 403);
        }

        $validated = $request->validate([
            'section_id' => 'required|exists:sections,section_id',
            'question' => 'required|string',
            'item_type' => 'required|in:mcq,torf,enum,iden,essay',
            'points_awarded' => 'required|integer|min:1',
            'options' => 'nullable|string',
            'answer' => 'nullable|string',
            'expected_answer' => 'nullable|string',
            'enum_type' => 'nullable|in:ordered,unordered',
            'after_item_id' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $newOrder = 0;
            
            // Check if after_item_id is provided
            if (!empty($validated['after_item_id'])) {
                // Special case: 'start' means insert at beginning
                if ($validated['after_item_id'] === 'start') {
                    // Add to start of section
                    // Increment all existing items' order
                    ExamItem::where('exam_section_id', $validated['section_id'])
                        ->increment('order');
                    $newOrder = 1;
                } else {
                    // Insert after a specific item
                    $afterItem = ExamItem::where('item_id', $validated['after_item_id'])
                        ->where('exam_section_id', $validated['section_id'])
                        ->first();
                    
                    if ($afterItem) {
                        // Get the order of the item we want to insert after
                        $afterOrder = $afterItem->order;
                        
                        // Increment order of all items after this one
                        ExamItem::where('exam_section_id', $validated['section_id'])
                            ->where('order', '>', $afterOrder)
                            ->increment('order');
                        
                        // Set new item's order to be right after the specified item
                        $newOrder = $afterOrder + 1;
                    } else {
                        // Fallback: add to end of section
                        $maxOrder = ExamItem::where('exam_section_id', $validated['section_id'])
                            ->max('order');
                        $newOrder = ($maxOrder ?? 0) + 1;
                    }
                }
            } else {
                // No after_item_id: add to end of section
                // Get the highest order in this section
                $maxOrder = ExamItem::where('exam_section_id', $validated['section_id'])
                    ->max('order');
                $newOrder = ($maxOrder ?? 0) + 1;
            }

            $item = ExamItem::create([
                'exam_id' => $examId,
                'exam_section_id' => $validated['section_id'],
                'question' => $validated['question'],
                'item_type' => $validated['item_type'],
                'points_awarded' => $validated['points_awarded'],
                'options' => $validated['options'] ?? null,
                'answer' => $validated['answer'] ?? null,
                'expected_answer' => $validated['expected_answer'] ?? null,
                'enum_type' => $validated['enum_type'] ?? null,
                'order' => $newOrder
            ]);

            $exam->increment('no_of_items');
            $exam->increment('total_points', $validated['points_awarded']);

            DB::commit();

            return response()->json([
                'success' => true,
                'item' => $item,
                'message' => 'Question added successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to add question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a question instantly with default MCQ configuration
     * For Google Forms-style inline editing
     */
    public function createQuestionInstantly(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent editing archived exams
        if ($exam->status === 'archived') {
            return response()->json(['error' => 'Cannot edit archived exams'], 403);
        }

        $validated = $request->validate([
            'section_id' => 'required|integer',
            'after_item_id' => 'nullable'
        ]);

        // Verify section belongs to this exam
        $section = Section::where('section_id', $validated['section_id'])
            ->where('exam_id', $examId)
            ->first();
            
        if (!$section) {
            return response()->json(['error' => 'Invalid section'], 400);
        }

        DB::beginTransaction();
        try {
            $newOrder = 0;
            
            // Check if after_item_id is provided
            if (!empty($validated['after_item_id'])) {
                // Special case: 'start' means insert at beginning
                if ($validated['after_item_id'] === 'start') {
                    ExamItem::where('exam_section_id', $validated['section_id'])
                        ->increment('order');
                    $newOrder = 1;
                } else {
                    // Insert after a specific item
                    $afterItem = ExamItem::where('item_id', $validated['after_item_id'])
                        ->where('exam_section_id', $validated['section_id'])
                        ->first();
                    
                    if ($afterItem) {
                        $afterOrder = $afterItem->order;
                        ExamItem::where('exam_section_id', $validated['section_id'])
                            ->where('order', '>', $afterOrder)
                            ->increment('order');
                        $newOrder = $afterOrder + 1;
                    } else {
                        $maxOrder = ExamItem::where('exam_section_id', $validated['section_id'])
                            ->max('order');
                        $newOrder = ($maxOrder ?? 0) + 1;
                    }
                }
            } else {
                // Add to end of section
                $maxOrder = ExamItem::where('exam_section_id', $validated['section_id'])
                    ->max('order');
                $newOrder = ($maxOrder ?? 0) + 1;
            }

            // Create question with default MCQ configuration
            $defaultOptions = json_encode(['', '', '', '']);  // 4 empty options
            $defaultAnswer = json_encode([]);  // No correct answer yet

            $item = ExamItem::create([
                'exam_id' => $examId,
                'exam_section_id' => $validated['section_id'],
                'question' => '',  // Empty question text
                'item_type' => 'mcq',  // Default to MCQ
                'points_awarded' => 1,  // Default 1 point
                'options' => $defaultOptions,
                'answer' => $defaultAnswer,
                'expected_answer' => null,
                'enum_type' => null,
                'order' => $newOrder
            ]);

            $exam->increment('no_of_items');
            $exam->increment('total_points', 1);

            DB::commit();

            // Render the question card component
            $questionCardHtml = view('instructor.exam.components.question-card', [
                'item' => $item
            ])->render();
            
            // Build the complete wrapper HTML inline
            $isOwner = $exam->is_owner;
            $commentsCount = 0;
            
            $html = '<div class="question-wrapper" data-item-id="' . $item->item_id . '">
                <div class="drag-handle" draggable="true" title="Drag to reorder">
                    <i class="bi bi-grip-vertical"></i>
                </div>
                
                ' . $questionCardHtml . '
                
                <div class="floating-action-pane">';
            
            if ($isOwner) {
                $html .= '<button class="floating-btn floating-btn-danger" title="Delete Question" onclick="event.stopPropagation(); deleteQuestion(' . $exam->exam_id . ', ' . $item->item_id . ')">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="floating-btn" title="Add Question After" onclick="event.stopPropagation(); addQuestionInstantly(' . $item->exam_section_id . ', ' . $item->item_id . ')">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    <button class="floating-btn" title="Duplicate" onclick="event.stopPropagation(); duplicateQuestion(' . $exam->exam_id . ', ' . $item->item_id . ')">
                        <i class="bi bi-files"></i>
                    </button>
                    <button class="floating-btn" title="Move Up" onclick="event.stopPropagation(); moveQuestion(' . $item->item_id . ', \'up\')">
                        <i class="bi bi-arrow-up"></i>
                    </button>
                    <button class="floating-btn" title="Move Down" onclick="event.stopPropagation(); moveQuestion(' . $item->item_id . ', \'down\')">
                        <i class="bi bi-arrow-down"></i>
                    </button>';
            }
            
            $html .= '<button class="floating-btn" title="View Comments" onclick="event.stopPropagation(); openCommentsModal(' . $item->item_id . ')" style="position: relative;">
                    <i class="bi bi-chat-left-text"></i>
                    <span id="commentBadge_' . $item->item_id . '" style="position: absolute; top: -4px; right: -4px; background-color: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.7rem; font-weight: 600; min-width: 18px; text-align: center; display: none;">0</span>
                </button>
            </div>
            
            <div class="floating-comments-box" id="commentsBox_' . $item->item_id . '">
                <div class="comments-header">
                    <span>Comments</span>
                    <button class="comments-close-btn" onclick="event.stopPropagation(); closeCommentsBox(' . $item->item_id . ')">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="comments-list" id="commentsList_' . $item->item_id . '">
                    <p class="text-muted">No comments yet</p>
                </div>
                <div class="comment-input-box">
                    <textarea id="commentInput_' . $item->item_id . '" class="comment-input" placeholder="Add a comment..."></textarea>
                    <button class="comment-submit-btn" onclick="submitComment(' . $item->item_id . ')">
                        <i class="bi bi-send"></i>
                    </button>
                </div>
            </div>
        </div>';

            return response()->json([
                'success' => true,
                'item' => $item,
                'html' => $html,
                'message' => 'Question created instantly'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getQuestion($examId, $itemId)
    {
        $exam = Exam::findOrFail($examId);
        $item = ExamItem::where('exam_id', $examId)->where('item_id', $itemId)->firstOrFail();
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'item' => $item
        ]);
    }

    public function updateQuestion(Request $request, $examId, $itemId)
    {
        $exam = Exam::findOrFail($examId);
        $item = ExamItem::where('exam_id', $examId)->where('item_id', $itemId)->firstOrFail();
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent editing archived exams
        if ($exam->status === 'archived') {
            return response()->json(['error' => 'Cannot edit archived exams'], 403);
        }

        $validated = $request->validate([
            'question' => 'required|string',
            'item_type' => 'required|in:mcq,torf,enum,iden,essay',
            'points_awarded' => 'required|integer|min:1',
            'options' => 'nullable|string',
            'answer' => 'nullable|string',
            'expected_answer' => 'nullable|string',
            'enum_type' => 'nullable|in:ordered,unordered'
        ]);

        DB::beginTransaction();
        try {
            $oldPoints = $item->points_awarded;
            
            $item->update($validated);

            $pointsDiff = $validated['points_awarded'] - $oldPoints;
            $exam->increment('total_points', $pointsDiff);

            DB::commit();

            return response()->json([
                'success' => true,
                'item' => $item,
                'message' => 'Question updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteQuestion($examId, $itemId)
    {
        $exam = Exam::findOrFail($examId);
        $item = ExamItem::where('exam_id', $examId)->where('item_id', $itemId)->firstOrFail();
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent editing archived exams
        if ($exam->status === 'archived') {
            return response()->json(['error' => 'Cannot edit archived exams'], 403);
        }

        DB::beginTransaction();
        try {
            $points = $item->points_awarded;
            $item->delete();

            $exam->decrement('no_of_items');
            $exam->decrement('total_points', $points);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to delete question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function duplicateQuestion($examId, $itemId)
    {
        $exam = Exam::findOrFail($examId);
        $item = ExamItem::where('exam_id', $examId)->where('item_id', $itemId)->firstOrFail();
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $maxOrder = ExamItem::where('exam_id', $examId)->max('order') ?? 0;

            $newItem = $item->replicate();
            $newItem->order = $maxOrder + 1;
            $newItem->save();

            $exam->increment('no_of_items');
            $exam->increment('total_points', $item->points_awarded);

            DB::commit();

            // Render just the question card component (wrapper will be added by JS)
            $questionCardHtml = view('instructor.exam.components.question-card', [
                'item' => $newItem
            ])->render();
            
            // Build the complete wrapper HTML inline
            $isOwner = $exam->is_owner;
            $commentsCount = $newItem->comments_count ?? 0;
            
            $html = '<div class="question-wrapper" data-item-id="' . $newItem->item_id . '">
                <div class="drag-handle" draggable="true" title="Drag to reorder">
                    <i class="bi bi-grip-vertical"></i>
                </div>
                
                ' . $questionCardHtml . '
                
                <div class="floating-action-pane">';
            
            if ($isOwner) {
                $html .= '<button class="floating-btn floating-btn-danger" title="Delete Question" onclick="event.stopPropagation(); deleteQuestion(' . $exam->exam_id . ', ' . $newItem->item_id . ')">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="floating-btn" title="Add Question After" onclick="event.stopPropagation(); addQuestionInstantly(' . $newItem->exam_section_id . ', ' . $newItem->item_id . ')">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    <button class="floating-btn" title="Duplicate" onclick="event.stopPropagation(); duplicateQuestion(' . $exam->exam_id . ', ' . $newItem->item_id . ')">
                        <i class="bi bi-files"></i>
                    </button>
                    <button class="floating-btn" title="Move Up" onclick="event.stopPropagation(); moveQuestion(' . $newItem->item_id . ', \'up\')">
                        <i class="bi bi-arrow-up"></i>
                    </button>
                    <button class="floating-btn" title="Move Down" onclick="event.stopPropagation(); moveQuestion(' . $newItem->item_id . ', \'down\')">
                        <i class="bi bi-arrow-down"></i>
                    </button>';
            }
            
            $html .= '<button class="floating-btn" title="View Comments" onclick="event.stopPropagation(); openCommentsModal(' . $newItem->item_id . ')" style="position: relative;">
                    <i class="bi bi-chat-left-text"></i>
                    <span id="commentBadge_' . $newItem->item_id . '" style="position: absolute; top: -4px; right: -4px; background-color: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.7rem; font-weight: 600; min-width: 18px; text-align: center; ' . ($commentsCount > 0 ? '' : 'display: none;') . '">' . $commentsCount . '</span>
                </button>
            </div>
            
            <div class="floating-comments-box" id="commentsBox_' . $newItem->item_id . '">
                <div class="comments-header">
                    <span>Comments</span>
                    <button class="comments-close-btn" onclick="event.stopPropagation(); closeCommentsBox(' . $newItem->item_id . ')">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="comments-list" id="commentsList_' . $newItem->item_id . '">
                    <p class="text-muted">No comments yet</p>
                </div>
                <div class="comment-input-box">
                    <textarea id="commentInput_' . $newItem->item_id . '" class="comment-input" placeholder="Add a comment..."></textarea>
                    <button class="comment-submit-btn" onclick="submitComment(' . $newItem->item_id . ')">
                        <i class="bi bi-send"></i>
                    </button>
                </div>
            </div>
        </div>';

            return response()->json([
                'success' => true,
                'item' => $newItem,
                'html' => $html,
                'message' => 'Question duplicated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to duplicate question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reorderQuestions(Request $request, $examId)
    {
        \Log::info('Reorder Questions called', [
            'exam_id' => $examId,
            'item_id' => $request->item_id,
            'direction' => $request->direction
        ]);
        
        $exam = Exam::findOrFail($examId);
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $itemId = $request->item_id;
            $direction = $request->direction; // 'up' or 'down'
            
            $item = ExamItem::findOrFail($itemId);
            
            \Log::info('Found item', ['item_id' => $item->item_id, 'exam_section_id' => $item->exam_section_id, 'current_order' => $item->order]);
            
            // Get all items in the same section ordered by 'order'
            $items = ExamItem::where('exam_section_id', $item->exam_section_id)
                ->orderBy('order')
                ->get();
            
            \Log::info('Items in section', ['count' => $items->count(), 'orders' => $items->pluck('order')->toArray()]);
            
            $currentIndex = $items->search(function($i) use ($itemId) {
                return $i->item_id == $itemId;
            });
            
            \Log::info('Current index', ['index' => $currentIndex]);
            
            if ($currentIndex === false) {
                return response()->json(['error' => 'Item not found'], 404);
            }
            
            // Determine the swap index
            if ($direction === 'up' && $currentIndex > 0) {
                $swapIndex = $currentIndex - 1;
            } elseif ($direction === 'down' && $currentIndex < $items->count() - 1) {
                $swapIndex = $currentIndex + 1;
            } else {
                // Already at the boundary
                \Log::info('Item at boundary', ['direction' => $direction, 'index' => $currentIndex]);
                return response()->json([
                    'success' => true,
                    'message' => 'Item is already at the boundary'
                ]);
            }
            
            // Swap the order values
            $currentItem = $items[$currentIndex];
            $swapItem = $items[$swapIndex];
            
            \Log::info('Swapping items', [
                'current' => ['id' => $currentItem->item_id, 'old_order' => $currentItem->order],
                'swap' => ['id' => $swapItem->item_id, 'old_order' => $swapItem->order]
            ]);
            
            $tempOrder = $currentItem->order;
            $currentItem->order = $swapItem->order;
            $swapItem->order = $tempOrder;
            
            $currentItem->save();
            $swapItem->save();
            
            \Log::info('Items swapped successfully');
            
            return response()->json([
                'success' => true,
                'message' => 'Question reordered successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Reorder error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Failed to reorder question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reorderQuestionsByDrag(Request $request, $examId)
    {
        \Log::info('Reorder Questions by Drag called', [
            'exam_id' => $examId,
            'dragged_item_id' => $request->dragged_item_id,
            'target_item_id' => $request->target_item_id,
            'insert_before' => $request->insert_before
        ]);
        
        $exam = Exam::findOrFail($examId);
        
        // Check if exam is archived
        if ($exam->status === 'archived') {
            \Log::warning('Attempt to reorder archived exam', ['exam_id' => $examId]);
            return response()->json(['error' => 'Cannot reorder questions in archived exams'], 403);
        }
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            \Log::warning('Unauthorized reorder attempt', ['exam_id' => $examId, 'teacher_id' => $teacherId]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $draggedItemId = $request->dragged_item_id;
            $targetItemId = $request->target_item_id;
            
            if (!$draggedItemId || !$targetItemId) {
                \Log::error('Missing item IDs', ['dragged' => $draggedItemId, 'target' => $targetItemId]);
                return response()->json(['error' => 'Missing item IDs'], 400);
            }
            
            $draggedItem = ExamItem::findOrFail($draggedItemId);
            $targetItem = ExamItem::findOrFail($targetItemId);
            
            \Log::info('Found items', [
                'dragged' => ['id' => $draggedItem->item_id, 'section' => $draggedItem->exam_section_id, 'order' => $draggedItem->order],
                'target' => ['id' => $targetItem->item_id, 'section' => $targetItem->exam_section_id, 'order' => $targetItem->order]
            ]);
            
            // Check if both items are in the same section
            if ($draggedItem->exam_section_id !== $targetItem->exam_section_id) {
                \Log::warning('Attempt to move across sections', [
                    'dragged_section' => $draggedItem->exam_section_id,
                    'target_section' => $targetItem->exam_section_id
                ]);
                return response()->json([
                    'error' => 'Cannot reorder items across different sections'
                ], 400);
            }
            
            // Get all items in the section ordered by 'order'
            $items = ExamItem::where('exam_section_id', $draggedItem->exam_section_id)
                ->orderBy('order')
                ->get();
            
            \Log::info('Section items', [
                'section_id' => $draggedItem->exam_section_id,
                'total_items' => $items->count(),
                'orders' => $items->pluck('order', 'item_id')->toArray()
            ]);
            
            $draggedIndex = $items->search(function($i) use ($draggedItemId) {
                return $i->item_id == $draggedItemId;
            });
            
            $targetIndex = $items->search(function($i) use ($targetItemId) {
                return $i->item_id == $targetItemId;
            });
            
            if ($draggedIndex === false || $targetIndex === false) {
                \Log::error('Item index not found', [
                    'dragged_index' => $draggedIndex,
                    'target_index' => $targetIndex
                ]);
                return response()->json(['error' => 'Item not found in section'], 404);
            }
            
            \Log::info('Original indices', [
                'dragged_index' => $draggedIndex,
                'target_index' => $targetIndex
            ]);
            
            // Remove dragged item from collection
            $draggedItemObj = $items->pull($draggedIndex);
            
            // Re-index the collection
            $items = $items->values();
            
            // Recalculate target index after removal
            $newTargetIndex = $items->search(function($i) use ($targetItemId) {
                return $i->item_id == $targetItemId;
            });
            
            // Determine insert position based on insert_before flag
            $insertBefore = $request->insert_before ?? true;
            $insertPosition = $insertBefore ? $newTargetIndex : $newTargetIndex + 1;
            
            \Log::info('Insert position calculated', [
                'new_target_index' => $newTargetIndex,
                'insert_before' => $insertBefore,
                'insert_position' => $insertPosition
            ]);
            
            // Insert dragged item at the calculated position
            $items->splice($insertPosition, 0, [$draggedItemObj]);
            
            // Update order for all items
            $updatedOrders = [];
            foreach ($items as $index => $item) {
                $oldOrder = $item->order;
                $item->order = $index + 1;
                $item->save();
                $updatedOrders[] = [
                    'item_id' => $item->item_id,
                    'old_order' => $oldOrder,
                    'new_order' => $item->order
                ];
            }
            
            \Log::info('Items reordered successfully by drag', [
                'updated_orders' => $updatedOrders
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Questions reordered successfully',
                'updated_count' => count($updatedOrders)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Reorder by drag error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Failed to reorder questions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addSection(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent editing archived exams
        if ($exam->status === 'archived') {
            return response()->json(['error' => 'Cannot edit archived exams'], 403);
        }

        try {
            // Get the maximum order number for sections in this exam
            $maxOrder = Section::where('exam_id', $examId)->max('section_order') ?? 0;

            // Create new section
            $section = Section::create([
                'exam_id' => $examId,
                'section_title' => $request->section_title ?? '',
                'section_directions' => $request->section_directions ?? '',
                'section_order' => $maxOrder + 1
            ]);

            return response()->json([
                'success' => true,
                'section' => $section,
                'message' => 'Section created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create section: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSection(Request $request, $examId, $sectionId)
    {
        $exam = Exam::findOrFail($examId);
        $section = Section::where('exam_id', $examId)->where('section_id', $sectionId)->firstOrFail();
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent editing archived exams
        if ($exam->status === 'archived') {
            return response()->json(['error' => 'Cannot edit archived exams'], 403);
        }

        try {
            $section->update($request->all());

            return response()->json([
                'success' => true,
                'section' => $section,
                'message' => 'Section updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update section: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSection($examId, $sectionId)
    {
        $exam = Exam::findOrFail($examId);
        $section = Section::where('exam_id', $examId)->where('section_id', $sectionId)->firstOrFail();
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent editing archived exams
        if ($exam->status === 'archived') {
            return response()->json(['error' => 'Cannot edit archived exams'], 403);
        }

        DB::beginTransaction();
        try {
            // Delete all items in this section and update exam points
            $items = $section->items;
            $totalPoints = $items->sum('points_awarded');
            $totalItems = $items->count();

            // Delete all items
            $section->items()->delete();

            // Update exam totals
            $exam->decrement('no_of_items', $totalItems);
            $exam->decrement('total_points', $totalPoints);

            // Delete the section
            $section->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Section and all its questions deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to delete section: ' . $e->getMessage()
            ], 500);
        }
    }

    public function duplicateSection($examId, $sectionId)
    {
        $exam = Exam::findOrFail($examId);
        $section = Section::where('exam_id', $examId)->where('section_id', $sectionId)->firstOrFail();
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            // Get the highest section_order
            $maxOrder = Section::where('exam_id', $examId)->max('section_order') ?? 0;
            
            // Create duplicate section
            $newSection = Section::create([
                'exam_id' => $examId,
                'section_title' => $section->section_title . ' (Copy)',
                'section_directions' => $section->section_directions,
                'section_order' => $maxOrder + 1
            ]);

            // Duplicate all items in the section
            $items = $section->items;
            $totalPoints = 0;
            $totalItems = 0;

            foreach ($items as $item) {
                ExamItem::create([
                    'exam_id' => $examId,
                    'exam_section_id' => $newSection->section_id,
                    'question' => $item->question,
                    'item_type' => $item->item_type,
                    'options' => $item->options,
                    'answer' => $item->answer,
                    'expected_answer' => $item->expected_answer,
                    'enum_type' => $item->enum_type,
                    'points_awarded' => $item->points_awarded,
                    'order' => $item->order
                ]);
                
                $totalPoints += $item->points_awarded;
                $totalItems++;
            }

            // Update exam totals
            $exam->increment('no_of_items', $totalItems);
            $exam->increment('total_points', $totalPoints);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Section duplicated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to duplicate section: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reorderSections(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $sectionId = $request->input('section_id');
        $direction = $request->input('direction'); // 'up' or 'down'

        DB::beginTransaction();
        try {
            $section = Section::where('exam_id', $examId)
                             ->where('section_id', $sectionId)
                             ->firstOrFail();
            
            $currentOrder = $section->section_order;

            if ($direction === 'up') {
                // Find the section above (with lower order)
                $adjacentSection = Section::where('exam_id', $examId)
                                         ->where('section_order', '<', $currentOrder)
                                         ->orderBy('section_order', 'desc')
                                         ->first();
            } else { // down
                // Find the section below (with higher order)
                $adjacentSection = Section::where('exam_id', $examId)
                                         ->where('section_order', '>', $currentOrder)
                                         ->orderBy('section_order', 'asc')
                                         ->first();
            }

            if ($adjacentSection) {
                // Swap section_order values
                $tempOrder = $section->section_order;
                $section->section_order = $adjacentSection->section_order;
                $adjacentSection->section_order = $tempOrder;
                
                $section->save();
                $adjacentSection->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Section reordered successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to reorder section: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getExamDetails($id)
    {
        $exam = Exam::with(['user', 'subject', 'collaborations.teacher', 'examAssignments'])
            ->findOrFail($id);
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $creatorName = $exam->user ? ($exam->user->first_name . ' ' . $exam->user->last_name) : 'Unknown';
        $creatorInitials = $this->getInitials($creatorName);
        
        // Get assigned class IDs
        $classAssignments = $exam->examAssignments->pluck('class_id')->toArray();
        
        return response()->json([
            'exam' => $exam,
            'creator_name' => $creatorName,
            'creator_initials' => $creatorInitials,
            'subject_name' => $exam->subject ? $exam->subject->subject_name : 'N/A',
            'formatted_created_at' => $exam->created_at->format('F j, Y'),
            'formatted_updated_at' => $exam->updated_at->format('F j, Y'),
            'class_assignments' => $classAssignments,
            'collaborators' => $exam->collaborations->map(function($collab) {
                return [
                    'id' => $collab->teacher_id,
                    'name' => $collab->teacher->first_name . ' ' . $collab->teacher->last_name,
                    'email' => $collab->teacher->email_address,
                    'role' => $collab->role
                ];
            })
        ]);
    }

    public function searchTeachers(Request $request)
    {
        $search = $request->get('search', '');
        $examId = $request->get('exam_id');
        $currentUserId = Auth::id();
        
        $existingCollaborators = [];
        if ($examId) {
            $existingCollaborators = ExamCollaboration::where('exam_id', $examId)
                ->pluck('teacher_id')
                ->toArray();
        }
        
        $teachers = UserTeacher::whereNotIn('user_id', array_merge($existingCollaborators, [$currentUserId]))
            ->where('status', 'Active')
            ->where(function($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email_address', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function($teacher) {
                return [
                    'id' => $teacher->user_id,
                    'name' => $teacher->first_name . ' ' . $teacher->last_name,
                    'email' => $teacher->email_address,
                    'avatar' => null
                ];
            });

        return response()->json($teachers);
    }

    public function addCollaborators(Request $request, $examId)
    {
        $validated = $request->validate([
            'collaborators' => 'required|array',
            'collaborators.*' => 'exists:user_teacher,user_id'
        ]);

        $exam = Exam::findOrFail($examId);
        
        if ($exam->teacher_id != Auth::id()) {
            return response()->json(['error' => 'Only the exam creator can add collaborators'], 403);
        }

        DB::beginTransaction();
        try {
            foreach ($validated['collaborators'] as $teacherId) {
                $exists = ExamCollaboration::where('exam_id', $examId)
                    ->where('teacher_id', $teacherId)
                    ->exists();
                
                if (!$exists) {
                    ExamCollaboration::create([
                        'exam_id' => $examId,
                        'teacher_id' => $teacherId,
                        'role' => 'collaborator'
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Collaborators added successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to add collaborators: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeCollaborator($examId, $teacherId)
    {
        $exam = Exam::findOrFail($examId);
        
        // Check if current user is the owner
        $currentUserId = Auth::id();
        if ($exam->teacher_id != $currentUserId) {
            return response()->json(['error' => 'Only the exam owner can remove collaborators'], 403);
        }

        // Check if exam is draft
        if ($exam->status !== 'draft') {
            return response()->json(['error' => 'Collaborators can only be removed from draft exams'], 403);
        }

        try {
            $deleted = ExamCollaboration::where('exam_id', $examId)
                ->where('teacher_id', $teacherId)
                ->where('role', '!=', 'owner') // Prevent removing the owner
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Collaborator removed successfully'
                ]);
            } else {
                return response()->json([
                    'error' => 'Collaborator not found or cannot be removed'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to remove collaborator: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCollaborators($examId)
    {
        $exam = Exam::with('collaborations.teacher')->findOrFail($examId);
        
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $collaborators = $exam->collaborations->map(function($collab) {
            return [
                'id' => $collab->teacher_id,
                'name' => $collab->teacher->first_name . ' ' . $collab->teacher->last_name,
                'email' => $collab->teacher->email_address,
                'role' => $collab->role
            ];
        });

        return response()->json([
            'success' => true,
            'collaborators' => $collaborators,
            'is_owner' => $exam->teacher_id == $teacherId
        ]);
    }

    public function getClasses(Request $request)
    {
        $subjectId = $request->get('subject_id');
        $teacherId = Auth::id();
        
        \Log::info('getClasses called', [
            'subject_id' => $subjectId,
            'teacher_id' => $teacherId
        ]);
        
        $classes = ClassModel::where('subject_id', $subjectId)
            ->where('status', 'Active')
            ->whereHas('teacherAssignments', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->get()
            ->map(function($class) {
                return [
                    'class_id' => $class->class_id,
                    'title' => $class->title,
                    'section' => $class->section,
                    'year_level' => $class->year_level,
                    'display' => ($class->year_level ?? '') . $class->section . ' - ' . $class->title
                ];
            });

        \Log::info('Classes found', ['count' => $classes->count(), 'classes' => $classes]);

        return response()->json($classes);
    }

    private function getInitials($name)
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    /**
     * Delete an exam and its related data
     */
    public function destroy($examId)
    {
        $exam = Exam::findOrFail($examId);
        
        // Check if user has permission to delete this exam
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json([
                'success' => false, 
                'error' => 'Unauthorized to delete this exam'
            ], 403);
        }

        // Only allow deletion of draft exams
        if ($exam->status !== 'draft') {
            return response()->json([
                'success' => false,
                'error' => 'Only draft exams can be deleted'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Delete related records first
            ExamItem::where('exam_id', $examId)->delete();
            Section::where('exam_id', $examId)->delete();
            ExamCollaboration::where('exam_id', $examId)->delete();
            ExamAssignment::where('exam_id', $examId)->delete();
            
            // Finally delete the exam
            $exam->delete();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Exam deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete exam: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview exam for download
     */
    public function preview($examId)
    {
        $exam = Exam::with(['sections.items', 'subject', 'teacher'])
            ->findOrFail($examId);
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Generate HTML preview
        $html = view('instructor.exam.preview-template', compact('exam'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Download exam as PDF or Word
     */
    public function download($examId, $format)
    {
        $exam = Exam::with(['sections.items', 'subject', 'teacher'])
            ->findOrFail($examId);
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized');
        }

        $filename = \Str::slug($exam->exam_title) . '_' . date('Y-m-d');

        if ($format === 'pdf') {
            return $this->downloadPDF($exam, $filename);
        } elseif ($format === 'word') {
            return $this->downloadWord($exam, $filename);
        }

        abort(400, 'Invalid format');
    }

    /**
     * Generate PDF download
     */
    private function downloadPDF($exam, $filename)
    {
        $pdf = \PDF::loadView('instructor.exam.preview-template', compact('exam'));
        
        // Set paper size and orientation
        $pdf->setPaper('letter', 'portrait');
        
        return $pdf->download($filename . '.pdf');
    }

    /**
     * Generate Word document download
     */
    private function downloadWord($exam, $filename)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        
        // Set document properties
        $properties = $phpWord->getDocInfo();
        $properties->setCreator($exam->teacher->first_name . ' ' . $exam->teacher->last_name);
        $properties->setTitle($exam->exam_title);
        
        // Add a section to the document with 0.5 inch margins (720 twips)
        $section = $phpWord->addSection([
            'marginLeft' => 720,   
            'marginRight' => 720,
            'marginTop' => 720,
            'marginBottom' => 720,
        ]);

        // Add footer with page numbers
        $footer = $section->addFooter();
        $footer->addPreserveText(
            'Page {PAGE} out of {NUMPAGES}',
            ['name' => 'Century Gothic', 'size' => 10],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        // Font styles
        $fontCentury = ['name' => 'Century Gothic', 'size' => 11];
        $fontCenturyBold = ['name' => 'Century Gothic', 'size' => 11, 'bold' => true];
        $fontCenturyItalic = ['name' => 'Century Gothic', 'size' => 11, 'italic' => true];

        // Header Section - Term (Centered)
        $section->addText(
            strtoupper($exam->term ?? 'PRELIM'),
            $fontCentury,
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0]
        );
        
        // Semester and Academic Year (Centered)
        $semester = 'First Semester';
        $academicYear = '';
        if ($exam->schedule_start) {
            $year = $exam->schedule_start->format('Y');
            $nextYear = $exam->schedule_start->copy()->addYear()->format('Y');
            $academicYear = "A.Y. $year-$nextYear";
        }
        $section->addText(
            "$semester, " . ($academicYear ?: 'A.Y.'),
            $fontCentury,
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0]
        );
        
        // Subject (Centered, Bold)
        $section->addText(
            strtoupper($exam->subject->subject_name ?? $exam->exam_title),
            $fontCenturyBold,
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 200]
        );

        // Student Info - Name and Score
        $textRun1 = $section->addTextRun(['spaceAfter' => 0]);
        $textRun1->addText('Name: ______________________________________________________________', $fontCentury);
        $textRun1->addTab();
        $textRun1->addText('Score: ________________', $fontCentury);
        
        $section->addTextBreak();

        // Student Info - Year/Section and Date
        $textRun2 = $section->addTextRun(['spaceAfter' => 0]);
        $textRun2->addText('Year and Section: ___________________________________________________', $fontCentury);
        $textRun2->addTab();
        $textRun2->addText('Date: ________________', $fontCentury);
        
        $section->addTextBreak();

        // Loop through sections and items
        $questionNumber = 1;
        $sectionNumber = 1;
        
        foreach ($exam->sections as $sectionModel) {
            // Section Title (Roman numerals)
            if ($sectionModel->section_title) {
                $romanNumeral = ['I', 'II', 'III', 'IV', 'V'][$sectionNumber - 1] ?? $sectionNumber;
                $section->addText(
                    "$romanNumeral. " . $sectionModel->section_title,
                    $fontCentury,
                    ['spaceAfter' => 0]
                );
            }

            // Section Directions
            if ($sectionModel->section_directions) {
                $section->addText(
                    $sectionModel->section_directions,
                    $fontCentury,
                    ['spaceAfter' => 200]
                );
            } else {
                $section->addTextBreak();
            }

            // Loop through items in this section
            foreach ($sectionModel->items as $item) {
                $questionText = strip_tags($item->question);

                // Multiple Choice Questions - MCQ Format
                if ($item->item_type === 'Multiple Choice' && $item->options) {
                    // Format: _______[Question no.] [Question]
                    $section->addText(
                        "_______$questionNumber. $questionText",
                        $fontCentury,
                        ['spaceAfter' => 50]
                    );

                    // Options A, B, C, D, E (indented with tabs)
                    $options = json_decode($item->options, true);
                    if (is_array($options)) {
                        foreach ($options as $key => $option) {
                            $letter = chr(65 + $key);
                            $optionRun = $section->addTextRun(['spaceAfter' => 50]);
                            $optionRun->addTab();
                            $optionRun->addTab();
                            $optionRun->addText("$letter. " . strip_tags($option), $fontCentury);
                        }
                    }

                // True or False - as MCQ with TRUE/FALSE options
                } elseif ($item->item_type === 'True or False') {
                    $section->addText(
                        "_______$questionNumber. $questionText",
                        $fontCentury,
                        ['spaceAfter' => 50]
                    );
                    
                    $optionRunA = $section->addTextRun(['spaceAfter' => 50]);
                    $optionRunA->addTab();
                    $optionRunA->addTab();
                    $optionRunA->addText('A. TRUE', $fontCentury);
                    
                    $optionRunB = $section->addTextRun(['spaceAfter' => 50]);
                    $optionRunB->addTab();
                    $optionRunB->addTab();
                    $optionRunB->addText('B. FALSE', $fontCentury);

                // Identification / Short Answer Format
                } elseif (in_array($item->item_type, ['Identification', 'Short Answer'])) {
                    $section->addText(
                        "$questionNumber. $questionText",
                        $fontCentury,
                        ['spaceAfter' => 50]
                    );
                    $section->addText(
                        '(Answer in the blank) _______________________________',
                        $fontCenturyItalic,
                        ['spaceAfter' => 100]
                    );

                // Enumeration Questions
                } elseif ($item->item_type === 'Enumeration') {
                    $section->addText(
                        "$questionNumber. $questionText",
                        $fontCentury,
                        ['spaceAfter' => 100]
                    );

                    $enumCount = $item->enum_type ?? 6;
                    $isOrdered = true; // Default to ordered

                    // Create 2-column table
                    $table = $section->addTable([
                        'borderSize' => 6,
                        'borderColor' => '000000',
                        'width' => 50 * 100, // 50% width in percentage
                    ]);

                    $rows = ceil($enumCount / 2);
                    for ($i = 0; $i < $rows; $i++) {
                        $table->addRow();
                        
                        if ($isOrdered) {
                            // Ordered - with numbers
                            $cell1 = $table->addCell(2500);
                            $cell1->addText(($i * 2) + 1 . '.', $fontCentury);
                            
                            $cell2 = $table->addCell(2500);
                            if (($i * 2) + 2 <= $enumCount) {
                                $cell2->addText(($i * 2) + 2 . '.', $fontCentury);
                            }
                        } else {
                            // Unordered - blank cells
                            $table->addCell(2500)->addText('', $fontCentury);
                            $table->addCell(2500)->addText('', $fontCentury);
                        }
                    }

                // Essay - as Identification
                } elseif ($item->item_type === 'Essay') {
                    $section->addText(
                        "$questionNumber. $questionText",
                        $fontCentury,
                        ['spaceAfter' => 50]
                    );
                    $section->addText(
                        '(Answer in the blank) _______________________________',
                        $fontCenturyItalic,
                        ['spaceAfter' => 100]
                    );

                // Default format
                } else {
                    $section->addText(
                        "$questionNumber. $questionText",
                        $fontCentury,
                        ['spaceAfter' => 100]
                    );
                }

                $section->addTextBreak();
                $questionNumber++;
            }

            $sectionNumber++;
        }

        // Add extra spacing before footer section
        $section->addTextBreak();
        $section->addTextBreak();
        $section->addTextBreak();

        // Footer - Prepared By and Checked By
        $footerRun = $section->addTextRun(['spaceAfter' => 0]);
        $footerRun->addText('Prepared By:', $fontCentury);
        for ($i = 0; $i < 9; $i++) {
            $footerRun->addTab();
        }
        $footerRun->addText('Checked by:', $fontCentury);

        $section->addTextBreak();

        // Names
        $namesRun = $section->addTextRun(['spaceAfter' => 0]);
        $authorName = strtoupper(($exam->teacher->first_name ?? '') . ' ' . ($exam->teacher->last_name ?? ''));
        $namesRun->addText($authorName, $fontCenturyBold);
        for ($i = 0; $i < 9; $i++) {
            $namesRun->addTab();
        }
        $namesRun->addText('JULIETA B. BABAS, DIT', $fontCenturyBold);

        // Titles
        $titlesRun = $section->addTextRun(['spaceAfter' => 0]);
        $titlesRun->addText('Faculty', $fontCenturyBold);
        for ($i = 0; $i < 9; $i++) {
            $titlesRun->addTab();
        }
        $titlesRun->addText('College Dean', $fontCenturyBold);

        // Save to temporary file and send download
        $tempFile = tempnam(sys_get_temp_dir(), 'exam_');
        $phpWord->save($tempFile, 'Word2007');
        
        return response()->download($tempFile, $filename . '.docx')->deleteFileAfterSend(true);
    }

    // Get comments for a question
    public function getComments($examId, $itemId)
    {
        $teacherId = Auth::id();
        
        // Verify access to exam
        $exam = Exam::where('exam_id', $examId)
            ->where(function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                      ->orWhereHas('collaborations', function($q) use ($teacherId) {
                          $q->where('teacher_id', $teacherId);
                      });
            })
            ->firstOrFail();
        
        $comments = \App\Models\CollabComment::where('exam_id', $examId)
            ->where('question_id', $itemId)
            ->with('teacher')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($comment) use ($teacherId) {
                $authorName = 'Unknown';
                if ($comment->teacher) {
                    $authorName = $comment->teacher->full_name ?? 
                                 ($comment->teacher->first_name . ' ' . $comment->teacher->last_name);
                }
                
                return [
                    'comment_id' => $comment->comment_id,
                    'comment_text' => $comment->comment_text,
                    'author' => $authorName,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'resolved' => $comment->resolved,
                    'is_own' => $comment->teacher_id == $teacherId
                ];
            });
        
        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }

    // Add a comment to a question
    public function addComment(Request $request, $examId, $itemId)
    {
        $teacherId = Auth::id();
        
        // Verify access to exam
        $exam = Exam::where('exam_id', $examId)
            ->where(function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                      ->orWhereHas('collaborations', function($q) use ($teacherId) {
                          $q->where('teacher_id', $teacherId);
                      });
            })
            ->firstOrFail();
        
        $request->validate([
            'comment_text' => 'required|string|max:1000'
        ]);
        
        $comment = \App\Models\CollabComment::create([
            'exam_id' => $examId,
            'question_id' => $itemId,
            'teacher_id' => $teacherId,
            'comment_text' => $request->comment_text
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully'
        ]);
    }

    // Delete a comment
    public function deleteComment($commentId)
    {
        $comment = \App\Models\CollabComment::findOrFail($commentId);
        $comment->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }

    // Toggle resolve status of a comment
    public function toggleResolveComment($commentId)
    {
        $comment = \App\Models\CollabComment::findOrFail($commentId);
        $comment->resolved = !$comment->resolved;
        $comment->save();
        
        return response()->json([
            'success' => true,
            'resolved' => $comment->resolved,
            'message' => $comment->resolved ? 'Comment marked as resolved' : 'Comment marked as unresolved'
        ]);
    }
    /**
     * Release results for an exam (set release_results to true)
     */
    public function releaseResults($examId)
    {
        $exam = Exam::findOrFail($examId);
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId ||
                     $exam->collaborations->contains('teacher_id', $teacherId);
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Unauthorized to release results for this exam.');
        }
        $exam->release_results = true;
        $exam->save();
        return redirect()->back()->with('success', 'Results have been released for this exam.');
    }
}