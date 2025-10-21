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
            ->with(['user', 'subject'])
            ->orderBy('updated_at', 'desc')
            ->get();

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
                $query->orderBy('order', 'asc');
            }, 'subject'])
            ->findOrFail($examId);
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->teacher_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this exam.');
        }

        // If no sections exist, create a default section
        if ($exam->sections->count() === 0) {
            Section::create([
                'exam_id' => $exam->exam_id,
                'section_title' => 'Part I',
                'section_directions' => '',
                'section_order' => 1
            ]);
            
            // Reload exam with sections
            $exam->load('sections.items');
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
                'status' => 'draft'
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

        $validated = $request->validate([
            'section_id' => 'required|exists:sections,section_id',
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
            $maxOrder = ExamItem::where('exam_id', $examId)->max('order') ?? 0;

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
                'order' => $maxOrder + 1
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

            return response()->json([
                'success' => true,
                'item' => $newItem,
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
}