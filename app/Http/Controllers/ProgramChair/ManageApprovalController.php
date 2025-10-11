<?php

namespace App\Http\Controllers\ProgramChair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamApproval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->whereIn('status', ['draft', 'approved'])
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

        // Add approval status for each exam
        $exams->each(function($exam) {
            $latestApproval = $exam->approvals->first();
            
            $exam->approval_status = $latestApproval ? $latestApproval->status : 'pending';
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

        // Get latest approval status
        $latestApproval = $exam->approvals->first();
        $exam->approval_status = $latestApproval ? $latestApproval->status : 'pending';
        $exam->approval_notes = $latestApproval ? $latestApproval->notes : null;

        return view('program-chair.manage-approval.show', compact('exam'));
    }

    /**
     * Approve an exam
     * FIXED: Using route model binding with Exam $exam
     */
    public function approve(Request $request, Exam $exam)
    {
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

            // Update exam status
            $exam->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_date' => now(),
                'revision_notes' => null  // Clear any previous revision notes
            ]);

            DB::commit();

            return view('programchair.manage-approval.index')
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