<?php

namespace App\Http\Controllers\ProgramChair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamApproval;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManageApprovalController extends Controller
{
    /**
     * Display list of exams for approval
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        // Get exams that need approval or are already approved
        // FIXED: Changed 'user' to 'teacher' to match database structure
        $exams = Exam::with([
                'teacher',  // Changed from 'user'
                'subject', 
                'collaborations.teacher',
                'approvals' => function($query) {
                    $query->latest('created_at');
                }
            ])
            ->whereIn('status', ['for approval', 'approved'])
            ->when($search, function($query, $search) {
                $query->where('exam_title', 'like', "%{$search}%")
                      ->orWhereHas('subject', function($q) use ($search) {
                          $q->where('subject_name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('teacher', function($q) use ($search) {
                          $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                      });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Add approval status for each exam based on the exam's actual status
        // The exam.status field is the source of truth
        $exams->each(function($exam) {
            // Use the exam's status field as the primary source
            if ($exam->status == 'approved') {
                $exam->approval_status = 'approved';
            } elseif ($exam->status == 'for approval') {
                // Check if there's a rejection/revision in approvals table
                $latestApproval = $exam->approvals->first();
                if ($latestApproval && $latestApproval->status == 'rejected') {
                    $exam->approval_status = 'rejected';
                } else {
                    $exam->approval_status = 'pending';
                }
            } else {
                $exam->approval_status = 'pending';
            }
            
            $latestApproval = $exam->approvals->first();
            $exam->approval_notes = $latestApproval ? $latestApproval->notes : null;
        });

        return view('program-chair.manage-approval.index', compact('exams'));
    }

    /**
     * Show exam details for approval
     * FIXED: Using route model binding with Exam $exam
     */
    public function show(Exam $exam)
    {
        // Load relationships
        $exam->load([
            'teacher',
            'subject',
            'sections.items' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'collaborations.teacher',
            'approvals' => function($query) {
                $query->latest('created_at');
            }
        ]);

        // Get approval status based on exam's actual status (source of truth)
        if ($exam->status == 'approved') {
            $exam->approval_status = 'approved';
        } elseif ($exam->status == 'for approval') {
            // Check if there's a rejection/revision in approvals table
            $latestApproval = $exam->approvals->first();
            if ($latestApproval && $latestApproval->status == 'rejected') {
                $exam->approval_status = 'rejected';
            } else {
                $exam->approval_status = 'pending';
            }
        } else {
            $exam->approval_status = 'pending';
        }
        
        $latestApproval = $exam->approvals->first();
        $exam->approval_notes = $latestApproval ? $latestApproval->notes : null;

        return view('program-chair.manage-approval.show', compact('exam'));
    }

    /**
     * Get exam details for approval modal (AJAX)
     */
    public function getDetails(Exam $exam)
    {
        try {
            // Load relationships
            $exam->load(['teacher', 'subject', 'examAssignments.class']);
            
            // Get assigned classes - using the display_name attribute or building the name
            $classes = $exam->examAssignments->map(function($assignment) {
                if ($assignment->class) {
                    // Use the display_name accessor or build manually
                    return $assignment->class->display_name ?? 
                           (($assignment->class->year_level ?? '') . 
                            ($assignment->class->section ?? '') . ' - ' . 
                            ($assignment->class->title ?? 'Unknown'));
                }
                return null;
            })->filter()->join(', ');
            
            // Get author name
            $author = 'Unknown Author';
            if ($exam->teacher) {
                $author = $exam->teacher->first_name . ' ' . $exam->teacher->last_name;
            }
            
            // Format datetime for input fields
            $scheduleStart = $exam->schedule_start ? $exam->schedule_start->format('Y-m-d\TH:i') : '';
            $scheduleEnd = $exam->schedule_end ? $exam->schedule_end->format('Y-m-d\TH:i') : '';
            
            return response()->json([
                'exam_title' => $exam->exam_title,
                'author' => $author,
                'exam_desc' => $exam->exam_desc,
                'subject' => $exam->subject->subject_name ?? null,
                'classes' => $classes ?: 'Not assigned to any class yet',
                'duration' => $exam->duration,
                'schedule_start' => $scheduleStart,
                'schedule_end' => $scheduleEnd,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching exam details: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Approve an exam
     * FIXED: Using route model binding with Exam $exam
     */
    public function approve(Request $request, Exam $exam)
    {
        // Validate the request
        $validated = $request->validate([
            'duration' => 'required|integer|min:1',
            'schedule_start' => 'required|date',
            'schedule_end' => 'required|date|after:schedule_start',
            'exam_password' => 'nullable|string|max:255',
        ]);

        // Check if already approved
        $latestApproval = $exam->approvals()->latest('created_at')->first();
        if ($latestApproval && $latestApproval->status === 'approved') {
            return redirect()
                ->route('programchair.manage-approval.index')
                ->with('error', 'This exam is already approved!');
        }

        DB::beginTransaction();
        try {
            // Create approval record
            ExamApproval::create([
                'exam_id' => $exam->exam_id,
                'approver_id' => Auth::id(),
                'status' => 'approved',
                'notes' => $request->input('notes'),
            ]);

            // Update exam with new configuration
            $exam->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_date' => now(),
                'duration' => $validated['duration'],
                'schedule_start' => $validated['schedule_start'],
                'schedule_end' => $validated['schedule_end'],
                'exam_password' => $validated['exam_password'],
                'revision_notes' => null  // Clear any previous revision notes
            ]);

            // Create notification for the exam creator
            if ($exam->teacher_id) {
                $scheduleStart = Carbon::parse($validated['schedule_start'])->format('F d, Y \a\t h:i A');
                $scheduleEnd = Carbon::parse($validated['schedule_end'])->format('F d, Y \a\t h:i A');
                $passwordText = $validated['exam_password'] ? "The exam password is: {$validated['exam_password']}" : "No exam password is required.";
                
                Notification::create([
                    'user_id' => $exam->teacher_id,
                    'type' => 'exam_approved',
                    'title' => 'Exam Approved',
                    'message' => "Your \"{$exam->exam_title}\" exam has been approved. The exam is scheduled on {$scheduleStart} to {$scheduleEnd}. {$passwordText}",
                    'data' => json_encode([
                        'exam_id' => $exam->exam_id,
                        'exam_title' => $exam->exam_title,
                        'schedule_start' => $validated['schedule_start'],
                        'schedule_end' => $validated['schedule_end'],
                        'exam_password' => $validated['exam_password'],
                        'url' => route('instructor.exams.show', $exam->exam_id)
                    ]),
                    'is_read' => false
                ]);
            }

            DB::commit();

            return redirect()
                ->route('programchair.manage-approval.index')
                ->with('success', 'Exam "' . $exam->exam_title . '" has been approved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve exam: ' . $e->getMessage());
        }
    }

    /**
     * Send exam back for revision
     * FIXED: Using route model binding with Exam $exam
     */
    public function revise(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'notes' => 'required|string|min:10'
        ], [
            'notes.required' => 'Revision notes are required.',
            'notes.min' => 'Revision notes must be at least 10 characters.'
        ]);

        DB::beginTransaction();
        try {
            // Create rejection/revision record
            ExamApproval::create([
                'exam_id' => $exam->exam_id,
                'approver_id' => Auth::id(),
                'status' => 'rejected',
                'notes' => $validated['notes'],
            ]);

            // Update exam status back to draft
            $exam->update([
                'status' => 'draft',
                'revision_notes' => $validated['notes'],
                'approved_by' => null,
                'approved_date' => null
            ]);

            DB::commit();

            return redirect()
                ->route('programchair.manage-approval.index')
                ->with('success', 'Exam "' . $exam->exam_title . '" has been sent back for revision.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to send exam for revision: ' . $e->getMessage());
        }
    }

    /**
     * Rescind approval (withdraw approval)
     * FIXED: Using route model binding with Exam $exam
     */
    public function rescind(Request $request, Exam $exam)
    {
        // Check if exam is actually approved
        if ($exam->status !== 'approved') {
            return redirect()
                ->route('programchair.manage-approval.index')
                ->with('error', 'This exam is not currently approved!');
        }

        DB::beginTransaction();
        try {
            // Create rescind record
            ExamApproval::create([
                'exam_id' => $exam->exam_id,
                'approver_id' => Auth::id(),
                'status' => 'rejected',
                'notes' => $request->input('notes', 'Approval rescinded by Program Chair'),
            ]);

            // Update exam status back to draft
            $exam->update([
                'status' => 'draft',
                'approved_by' => null,
                'approved_date' => null,
                'revision_notes' => $request->input('notes', 'Approval has been rescinded. Please review and resubmit.')
            ]);

            DB::commit();

            return redirect()
                ->route('programchair.manage-approval.index')
                ->with('success', 'Approval for "' . $exam->exam_title . '" has been rescinded.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to rescind approval: ' . $e->getMessage());
        }
    }
}