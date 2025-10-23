<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\UserTeacher;
use App\Models\UserStudent;
use App\Models\TeacherAssignment;
use App\Models\ClassEnrolment;
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
            $query->where('status', 'Active');
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('section', 'like', "%{$search}%")
                    ->orWhereHas('subject', function ($subQuery) use ($search) {
                        $subQuery->where('subject_name', 'like', "%{$search}%");
                    });
            });
        }

        $classes = $query->orderBy('created_at', 'desc')->get();

        // Get subjects and teachers for modal
        $subjects = Subject::orderBy('subject_name')->get();
        $teachers = UserTeacher::where('status', 'Active')->orderBy('first_name')->get();

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
            'class' => [
                'class_id' => $class->class_id,
                'title' => $class->title,
                'subject' => $class->subject,
                'teacher' => $class->teacher,
                'students_count' => $class->students->count()
            ]
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

    /**
     * Manage students page (optional, if you want a dedicated page)
     */
    public function manageStudents(string $id)
    {
        $class = ClassModel::with(['subject', 'teacher', 'students'])
            ->findOrFail($id);

        return view('admin.manage-classes.students', compact('class'));
    }


    /**
     * Add students to class
     */
    public function addStudents(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:user_student,user_id'
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

            foreach ($request->student_ids as $studentId) {
                // Check if already enrolled
                $existing = ClassEnrolment::where('class_id', $id)
                    ->where('student_id', $studentId)
                    ->first();

                if ($existing) {
                    // Update status if archived
                    $existing->update(['status' => 'Active']);
                } else {
                    // Create new enrollment
                    ClassEnrolment::create([
                        'class_id' => $id,
                        'student_id' => $studentId,
                        'status' => 'Active'
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Students added successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove student from class
     */
    public function removeStudent($classId, $studentId)
    {
        try {
            $enrolment = ClassEnrolment::where('class_id', $classId)
                ->where('student_id', $studentId)
                ->firstOrFail();

            $enrolment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Student removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove student: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Copy students from another class
     */
    public function copyStudentsFromClass($id, $sourceClassId)
    {
        try {
            \Log::info("==========================================");
            \Log::info("COPY STUDENTS FROM CLASS - START");
            \Log::info("Target Class ID: {$id}");
            \Log::info("Source Class ID: {$sourceClassId}");

            DB::beginTransaction();

            // Verify both classes exist
            $targetClass = ClassModel::findOrFail($id);
            $sourceClass = ClassModel::findOrFail($sourceClassId);

            \Log::info("Target Class: {$targetClass->title}");
            \Log::info("Source Class: {$sourceClass->title}");

            // Try multiple methods to get students

            // Method 1: Direct DB query (most reliable)
            $sourceStudentsRaw = DB::select(
                "SELECT student_id FROM class_enrolment WHERE class_id = ? AND status = 'Active'",
                [$sourceClassId]
            );

            \Log::info("Method 1 (Raw SQL) - Found: " . count($sourceStudentsRaw) . " students");
            \Log::info("Raw data: " . json_encode($sourceStudentsRaw));

            // Method 2: Using Query Builder
            $sourceStudentsBuilder = DB::table('class_enrolment')
                ->where('class_id', $sourceClassId)
                ->where('status', 'Active')
                ->pluck('student_id')
                ->toArray();

            \Log::info("Method 2 (Query Builder) - Found: " . count($sourceStudentsBuilder) . " students");
            \Log::info("Builder data: " . json_encode($sourceStudentsBuilder));

            // Method 3: Using Eloquent Model
            $sourceStudentsEloquent = ClassEnrolment::where('class_id', $sourceClassId)
                ->where('status', 'Active')
                ->pluck('student_id')
                ->toArray();

            \Log::info("Method 3 (Eloquent) - Found: " . count($sourceStudentsEloquent) . " students");
            \Log::info("Eloquent data: " . json_encode($sourceStudentsEloquent));

            // Use the method that returned results
            $sourceStudents = !empty($sourceStudentsRaw)
                ? array_map(function ($obj) {
                    return $obj->student_id;
                }, $sourceStudentsRaw)
                : (!empty($sourceStudentsBuilder) ? $sourceStudentsBuilder : $sourceStudentsEloquent);

            \Log::info("Final source students array: " . json_encode($sourceStudents));
            \Log::info("Total students to process: " . count($sourceStudents));

            if (empty($sourceStudents)) {
                \Log::warning("No students found in source class");

                // Check if there are ANY records in class_enrolment for this class
                $anyRecords = DB::table('class_enrolment')
                    ->where('class_id', $sourceClassId)
                    ->count();

                \Log::info("Total records in class_enrolment for source class (any status): " . $anyRecords);

                // Check what statuses exist
                $statuses = DB::table('class_enrolment')
                    ->where('class_id', $sourceClassId)
                    ->select('status', DB::raw('COUNT(*) as count'))
                    ->groupBy('status')
                    ->get();

                \Log::info("Status breakdown: " . json_encode($statuses));

                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No students found in the source class',
                    'debug_info' => [
                        'total_records' => $anyRecords,
                        'status_breakdown' => $statuses
                    ]
                ]);
            }

            // Get current class members to filter out duplicates
            $currentMembers = DB::table('class_enrolment')
                ->where('class_id', $id)
                ->where('status', 'Active')
                ->pluck('student_id')
                ->toArray();

            // Filter out students who are already enrolled
            $studentsToAdd = array_diff($sourceStudents, $currentMembers);

            if (empty($studentsToAdd)) {
                DB::rollBack();
                \Log::info("No new students to copy - all are already enrolled");
                
                return response()->json([
                    'success' => false,
                    'message' => 'All students from the source class are already enrolled in this class',
                    'students' => []
                ]);
            }

            // Get full student details for the frontend
            $studentDetails = UserStudent::whereIn('user_id', $studentsToAdd)
                ->get()
                ->map(function ($student) {
                    return [
                        'user_id' => $student->user_id,
                        'name' => $student->first_name . ' ' . $student->last_name,
                        'id_number' => $student->id_number
                    ];
                });

            DB::commit();

            \Log::info("COPY PREPARATION COMPLETED");
            \Log::info("Students ready to copy: " . count($studentDetails));
            \Log::info("==========================================");

            return response()->json([
                'success' => true,
                'message' => count($studentDetails) . ' student(s) ready to be added',
                'students' => $studentDetails
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("COPY FAILED: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to copy students: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }


    /**
     * Get available students (not enrolled in the class)
     */
    public function getAvailableStudents($id)
    {
        try {
            $class = ClassModel::findOrFail($id);

            // Get enrolled student IDs
            $enrolledStudentIds = ClassEnrolment::where('class_id', $id)
                ->where('status', 'Active')
                ->pluck('student_id')
                ->toArray();

            // Get available students
            $query = UserStudent::where('status', 'Enrolled');

            if (!empty($enrolledStudentIds)) {
                $query->whereNotIn('user_id', $enrolledStudentIds);
            }

            $availableStudents = $query
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
                ->map(function ($student) {
                    return [
                        'user_id' => $student->user_id,
                        'name' => $student->last_name . ', ' . $student->first_name,
                        'id_number' => $student->id_number
                    ];
                });

            return response()->json([
                'success' => true,
                'students' => $availableStudents
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getAvailableStudents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get class members (enrolled students)
     */
    public function getClassMembers($id)
    {
        try {
            \Log::info("=== Getting Class Members for Class ID: {$id} ===");

            $class = ClassModel::findOrFail($id);
            \Log::info("Class found: " . $class->title);

            // Check raw enrolments
            $rawEnrolments = ClassEnrolment::where('class_id', $id)
                ->where('status', 'Active')
                ->get();

            \Log::info("Raw enrolments count: " . $rawEnrolments->count());
            \Log::info("Raw enrolments: " . json_encode($rawEnrolments->toArray()));

            // FIX: Specify which table the status column belongs to
            $members = ClassEnrolment::where('class_enrolment.class_id', $id)
                ->where('class_enrolment.status', 'Active')  // ← Specify table name
                ->join('user_student', 'class_enrolment.student_id', '=', 'user_student.user_id')
                ->where('user_student.status', 'Enrolled')  // ← Also filter enrolled students
                ->select(
                    'user_student.user_id',
                    'user_student.first_name',
                    'user_student.last_name',
                    'user_student.id_number'
                )
                ->orderBy('user_student.last_name')
                ->orderBy('user_student.first_name')
                ->get();

            \Log::info("Members found: " . $members->count());

            $membersData = $members->map(function ($student) {
                return [
                    'user_id' => $student->user_id,
                    'name' => $student->last_name . ', ' . $student->first_name,
                    'id_number' => $student->id_number
                ];
            });

            \Log::info("Formatted members data: " . json_encode($membersData));

            return response()->json([
                'success' => true,
                'members' => $membersData,
                'count' => $membersData->count()
            ]);

        } catch (\Exception $e) {
            \Log::error("Error in getClassMembers: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching class members: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
    /**
     * Get other classes for copying students
     */
    public function getOtherClasses($id)
    {
        try {
            $currentClass = ClassModel::findOrFail($id);

            $otherClasses = ClassModel::where('class_id', '!=', $id)
                ->where('status', 'Active')
                ->with(['subject'])
                ->withCount('students')
                ->orderBy('title')
                ->get()
                ->map(function ($class) {
                    // Get teacher name
                    $teacherAssignment = TeacherAssignment::where('class_id', $class->class_id)->first();
                    $teacherName = 'No Teacher';

                    if ($teacherAssignment) {
                        $teacher = UserTeacher::find($teacherAssignment->teacher_id);
                        if ($teacher) {
                            $teacherName = $teacher->first_name . ' ' . $teacher->last_name;
                        }
                    }

                    return [
                        'class_id' => $class->class_id,
                        'name' => $class->title,
                        'teacher' => $teacherName,
                        'subject' => $class->subject->subject_name,
                        'student_count' => $class->students_count
                    ];
                });

            return response()->json([
                'success' => true,
                'classes' => $otherClasses
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getOtherClasses: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching classes: ' . $e->getMessage()
            ], 500);
        }
    }
}