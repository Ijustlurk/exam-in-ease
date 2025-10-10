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
        
        // Get exams created by or collaborated on by the current teacher
        $teacherId = Auth::id();
        
        $exams = Exam::where(function($query) use ($teacherId) {
                $query->where('user_id', $teacherId)
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

        // Get the first exam for the details panel
        $selectedExam = $exams->first();
        
        if ($selectedExam) {
            $selectedExam->formatted_created_at = $selectedExam->created_at->format('F j, Y');
        }

        // Get subjects for the teacher
        $subjects = Subject::all();
        
        // Get classes assigned to this teacher
        $classes = ClassModel::whereHas('teacherAssignments', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->where('status', 'Active')
            ->with('subject')
            ->get();

        return view('instructor.exams.dashboard', compact('exams', 'selectedExam', 'subjects', 'classes'));
    }

    public function show($id)
    {
        $exam = Exam::with(['user', 'subject', 'collaborations.teacher', 'examItems'])
            ->findOrFail($id);
        
        // Check if user has access to this exam
        $teacherId = Auth::id();
        $hasAccess = $exam->user_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this exam.');
        }

        return view('instructor.exams.show', compact('exam'));
    }

    public function create($examId = null)
    {
        $exam = null;
        if ($examId) {
            $exam = Exam::with(['examItems', 'subject'])->findOrFail($examId);
            
            // Check access
            $teacherId = Auth::id();
            $hasAccess = $exam->user_id == $teacherId || 
                         $exam->collaborations->contains('teacher_id', $teacherId);
            
            if (!$hasAccess) {
                abort(403, 'Unauthorized access to this exam.');
            }
        }

        $subjects = Subject::all();
        
        $classes = ClassModel::whereHas('teacherAssignments', function($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->where('status', 'Active')
            ->with('subject')
            ->get();

        return view('instructor.exams.create', compact('exam', 'subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_title' => 'required|string|max:200',
            'exam_desc' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,subject_id',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:class,class_id',
            'duration' => 'required|integer|min:0',
            'schedule_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Create the exam
            $exam = Exam::create([
                'exam_title' => $validated['exam_title'],
                'exam_desc' => $validated['exam_desc'],
                'subject_id' => $validated['subject_id'],
                'schedule_date' => $validated['schedule_date'],
                'duration' => $validated['duration'],
                'total_points' => 0,
                'no_of_items' => 0,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            // Assign exam to classes if selected
            if (!empty($validated['class_ids'])) {
                foreach ($validated['class_ids'] as $classId) {
                    ExamAssignment::create([
                        'class_id' => $classId,
                        'exam_id' => $exam->exam_id
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('instructor.exams.create', $exam->exam_id)
                ->with('success', 'Exam created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create exam: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        
        // Check if user has access
        $teacherId = Auth::id();
        $hasAccess = $exam->user_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return back()->with('error', 'Unauthorized to edit this exam.');
        }

        $validated = $request->validate([
            'exam_title' => 'required|string|max:200',
            'exam_desc' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,subject_id',
            'duration' => 'required|integer|min:0',
            'schedule_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $exam->update($validated);

            DB::commit();

            return back()->with('success', 'Exam updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update exam: ' . $e->getMessage());
        }
    }

    public function duplicate($id)
    {
        $originalExam = Exam::with('examItems')->findOrFail($id);
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $originalExam->user_id == $teacherId || 
                     $originalExam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return back()->with('error', 'Unauthorized to duplicate this exam.');
        }

        DB::beginTransaction();
        try {
            $newExam = $originalExam->replicate();
            $newExam->exam_title = $originalExam->exam_title . ' (Copy)';
            $newExam->user_id = Auth::id();
            $newExam->status = 'draft';
            $newExam->approved_by = null;
            $newExam->approved_date = null;
            $newExam->save();

            // Copy exam items
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

    // Question Management Methods
    public function addQuestion(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->user_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'question' => 'required|string',
            'item_type' => 'required|in:mcq,torf,enum,iden,essay',
            'points_awarded' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'answer' => 'nullable|string',
            'expected_answer' => 'nullable|string',
            'exam_section_id' => 'nullable|exists:sections,section_id'
        ]);

        DB::beginTransaction();
        try {
            // Get the next order number
            $maxOrder = ExamItem::where('exam_id', $examId)->max('order') ?? 0;

            $item = ExamItem::create([
                'exam_id' => $examId,
                'exam_section_id' => $validated['exam_section_id'] ?? null,
                'question' => $validated['question'],
                'item_type' => $validated['item_type'],
                'points_awarded' => $validated['points_awarded'],
                'options' => isset($validated['options']) ? json_encode($validated['options']) : null,
                'answer' => $validated['answer'] ?? null,
                'expected_answer' => $validated['expected_answer'] ?? null,
                'order' => $maxOrder + 1
            ]);

            // Update exam totals
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

    public function updateQuestion(Request $request, $examId, $itemId)
    {
        $exam = Exam::findOrFail($examId);
        $item = ExamItem::where('exam_id', $examId)->where('item_id', $itemId)->firstOrFail();
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->user_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'question' => 'required|string',
            'item_type' => 'required|in:mcq,torf,enum,iden,essay',
            'points_awarded' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'answer' => 'nullable|string',
            'expected_answer' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $oldPoints = $item->points_awarded;
            
            $item->update([
                'question' => $validated['question'],
                'item_type' => $validated['item_type'],
                'points_awarded' => $validated['points_awarded'],
                'options' => isset($validated['options']) ? json_encode($validated['options']) : null,
                'answer' => $validated['answer'] ?? null,
                'expected_answer' => $validated['expected_answer'] ?? null
            ]);

            // Update exam total points
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
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->user_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $points = $item->points_awarded;
            $item->delete();

            // Update exam totals
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
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->user_id == $teacherId || 
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

            // Update exam totals
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
        $exam = Exam::findOrFail($examId);
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->user_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.item_id' => 'required|exists:exam_items,item_id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['orders'] as $orderData) {
                ExamItem::where('item_id', $orderData['item_id'])
                    ->where('exam_id', $examId)
                    ->update(['order' => $orderData['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Questions reordered successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to reorder questions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSection(Request $request, $examId, $sectionId)
    {
        $exam = Exam::findOrFail($examId);
        $section = Section::where('exam_id', $examId)->where('section_id', $sectionId)->firstOrFail();
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->user_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255'
        ]);

        try {
            $section->update($validated);

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

    // NEW METHODS FOR DASHBOARD

    public function getExamDetails($id)
    {
        $exam = Exam::with(['user', 'subject', 'collaborations.teacher'])
            ->findOrFail($id);
        
        // Check access
        $teacherId = Auth::id();
        $hasAccess = $exam->user_id == $teacherId || 
                     $exam->collaborations->contains('teacher_id', $teacherId);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $creatorName = $exam->user ? $exam->user->name : 'Unknown';
        $creatorInitials = $this->getInitials($creatorName);
        
        return response()->json([
            'exam' => $exam,
            'creator_name' => $creatorName,
            'creator_initials' => $creatorInitials,
            'subject_name' => $exam->subject ? $exam->subject->subject_name : 'N/A',
            'formatted_created_at' => $exam->created_at->format('F j, Y'),
            'formatted_updated_at' => $exam->updated_at->format('F j, Y'),
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
        
        // Get teachers who are not already collaborators and not the exam creator
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
                    'avatar' => null // Add profile picture logic if available
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
        
        // Check if user is the exam creator
        if ($exam->user_id != Auth::id()) {
            return response()->json(['error' => 'Only the exam creator can add collaborators'], 403);
        }

        DB::beginTransaction();
        try {
            foreach ($validated['collaborators'] as $teacherId) {
                // Check if already a collaborator
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

    public function getClasses(Request $request)
    {
        $subjectId = $request->get('subject_id');
        $teacherId = Auth::id();
        
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
}