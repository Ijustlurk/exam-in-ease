<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ManageSubjectController extends Controller
{
    /**
     * Display a listing of subjects
     */
    public function index(Request $request)
    {
        $query = Subject::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject_name', 'like', "%{$search}%")
                  ->orWhere('subject_code', 'like', "%{$search}%");
            });
        }

        $subjects = $query->orderBy('subject_name', 'asc')->get();

        // Get count of classes using each subject
        foreach ($subjects as $subject) {
            $subject->classes_count = $subject->classes()->count();
        }

        return view('admin.manage-subject.index', compact('subjects'));
    }

    /**
     * Store a newly created subject
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_code' => 'required|string|max:50|unique:subjects,subject_code',
            'subject_name' => 'required|string|max:150',
            'semester' => 'required|in:1st Semester,2nd Semester'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subject = Subject::create([
                'subject_code' => strtoupper($request->subject_code),
                'subject_name' => $request->subject_name,
                'semester' => $request->semester
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subject created successfully',
                'subject_id' => $subject->subject_id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subject: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified subject
     */
    public function show(string $id)
    {
        try {
            $subject = Subject::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'subject' => $subject
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found'
            ], 404);
        }
    }

    /**
     * Update the specified subject
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'subject_code' => 'required|string|max:50|unique:subjects,subject_code,' . $id . ',subject_id',
            'subject_name' => 'required|string|max:150',
            'semester' => 'required|in:1st Semester,2nd Semester'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subject = Subject::findOrFail($id);
            
            $subject->update([
                'subject_code' => strtoupper($request->subject_code),
                'subject_name' => $request->subject_name,
                'semester' => $request->semester
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subject updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subject: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified subject
     */
    public function destroy(string $id)
    {
        try {
            $subject = Subject::findOrFail($id);
            
            // Check if subject is being used by any classes
            $classCount = $subject->classes()->count();
            
            if ($classCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete subject. It is being used by {$classCount} class(es)."
                ], 400);
            }
            
            $subject->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subject deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subject: ' . $e->getMessage()
            ], 500);
        }
    }
}