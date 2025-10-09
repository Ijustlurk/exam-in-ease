<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\UserTeacher;
use App\Models\TeacherAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ManageClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ClassModel::with(['subject', 'teacher', 'students']);

        // Filter by status
        if ($request->has('show_archived') && $request->show_archived == 'true') {
            // Show all including archived
        } else {
            $query->active();
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%")
                  ->orWhereHas('subject', function($subQuery) use ($search) {
                      $subQuery->where('subject_name', 'like', "%{$search}%");
                  });
            });
        }

        $classes = $query->orderBy('created_at', 'desc')->get();

        // Get subjects and teachers for modal
        $subjects = Subject::orderBy('subject_name')->get();
        $teachers = UserTeacher::active()->orderBy('first_name')->get();

        return view('admin.manage-classes.index', compact('classes', 'subjects', 'teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'subject_id' => 'required|exists:subjects,subject_id',
            'year_level' => 'required|integer|between:1,4',
            'section' => 'required|string|max:10',
            'semester' => 'required|in:1,2',
            'school_year' => 'required|string|max:20',
            'teacher_id' => 'required|exists:user_teacher,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create the class
            $class = ClassModel::create([
                'title' => $request->title,
                'subject_id' => $request->subject_id,
                'year_level' => $request->year_level,
                'section' => $request->section,
                'semester' => $request->semester,
                'school_year' => $request->school_year,
                'status' => 'Active'
            ]);

            // Assign teacher to class
            TeacherAssignment::create([
                'class_id' => $class->class_id,
                'teacher_id' => $request->teacher_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Class created successfully',
                'class_id' => $class->class_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create class: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $class = ClassModel::with(['subject', 'teacher', 'students'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'class' => $class
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'subject_id' => 'required|exists:subjects,subject_id',
            'year_level' => 'required|integer|between:1,4',
            'section' => 'required|string|max:10',
            'semester' => 'required|in:1,2',
            'school_year' => 'required|string|max:20',
            'teacher_id' => 'nullable|exists:user_teacher,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $class = ClassModel::findOrFail($id);
            
            $class->update([
                'title' => $request->title,
                'subject_id' => $request->subject_id,
                'year_level' => $request->year_level,
                'section' => $request->section,
                'semester' => $request->semester,
                'school_year' => $request->school_year
            ]);

            // Update teacher assignment if provided
            if ($request->has('teacher_id')) {
                TeacherAssignment::updateOrCreate(
                    ['class_id' => $class->class_id],
                    ['teacher_id' => $request->teacher_id]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Class updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update class: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $class = ClassModel::findOrFail($id);
            $class->delete();

            return response()->json([
                'success' => true,
                'message' => 'Class deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete class: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive a class
     */
    public function archive(string $id)
    {
        try {
            $class = ClassModel::findOrFail($id);
            $class->update(['status' => 'Archived']);

            return response()->json([
                'success' => true,
                'message' => 'Class archived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive class: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unarchive a class
     */
    public function unarchive(string $id)
    {
        try {
            $class = ClassModel::findOrFail($id);
            $class->update(['status' => 'Active']);

            return response()->json([
                'success' => true,
                'message' => 'Class unarchived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unarchive class: ' . $e->getMessage()
            ], 500);
        }
    }
}