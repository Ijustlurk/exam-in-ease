<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $table = 'exams';
    protected $primaryKey = 'exam_id';

    protected $fillable = [
        'exam_title',
        'exam_desc',
        'subject_id',
        'teacher_id',
        'approved_by',
        'schedule_start',
        'schedule_end',
        'duration',
        'term',
        'total_points',
        'no_of_items',
        'status',
        'approved_date',
        'revision_notes',
        'exam_password'
    ];

    protected $casts = [
        'schedule_start' => 'datetime',
        'schedule_end' => 'datetime',
        'approved_date' => 'datetime',
    ];

    /**
     * Relationship with Teacher (Creator)
     */
    public function user()
    {
        return $this->belongsTo(UserTeacher::class, 'teacher_id', 'user_id');
    }

    /**
     * Relationship with Teacher (alternative accessor)
     */
    public function teacher()
    {
        return $this->belongsTo(UserTeacher::class, 'teacher_id', 'user_id');
    }

    /**
     * Relationship with Exam Sections
     */
    public function sections()
    {
        return $this->hasMany(Section::class, 'exam_id', 'exam_id')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Relationship with Exam Items
     */
    public function items()
    {
        return $this->hasMany(ExamItem::class, 'exam_id', 'exam_id')
            ->orderBy('order', 'asc');
    }

    /**
     * Alternative name for items (for controller compatibility)
     */
    public function examItems()
    {
        return $this->items();
    }

    /**
     * Get formatted created date
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('F j, Y');
    }

    /**
     * Check if a user is the creator of this exam
     */
    public function isCreator($teacherId)
    {
        return $this->teacher_id == $teacherId;
    }

    /**
     * Check if a user is a collaborator on this exam
     */
    public function isCollaborator($teacherId)
    {
        return $this->collaborations()->where('teacher_id', $teacherId)->exists();
    }

    /**
     * Check if a user has access to this exam (creator or collaborator)
     */
    public function hasAccess($teacherId)
    {
        return $this->isCreator($teacherId) || $this->isCollaborator($teacherId);
    }


    /**
     * Get the subject of this exam
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    /**
     * Get the admin who approved this exam
     */
    public function approvedBy()
    {
        return $this->belongsTo(UserAdmin::class, 'approved_by', 'user_id');
    }

    /**
     * Get all approval records for this exam
     */
    public function approvals()
    {
        return $this->hasMany(ExamApproval::class, 'exam_id', 'exam_id');
    }



    /**
     * Get all collaborators for this exam
     */
    public function collaborations()
    {
        return $this->hasMany(ExamCollaboration::class, 'exam_id', 'exam_id');
    }

    /**
     * Get exam assignments (classes this exam is assigned to)
     */
    public function assignments()
    {
        return $this->hasMany(ExamAssignment::class, 'exam_id', 'exam_id');
    }

    /**
     * Get the latest approval status
     */
    public function getLatestApprovalAttribute()
    {
        return $this->approvals()->latest('created_at')->first();
    }

    /**
     * Get approval status
     */
    public function getApprovalStatusAttribute()
    {
        $latestApproval = $this->latest_approval;
        return $latestApproval ? $latestApproval->status : 'pending';
    }

    /**
     * Check if the exam schedule has ended
     */
    public function hasScheduleEnded()
    {
        if (!$this->schedule_end) {
            return false;
        }
        return now()->isAfter($this->schedule_end);
    }

    /**
     * Check if the exam should be archived
     */
    public function shouldBeArchived()
    {
        return in_array($this->status, ['ongoing', 'approved']) 
            && $this->hasScheduleEnded();
    }

    /**
     * Archive this exam
     */
    public function archive()
    {
        if ($this->shouldBeArchived()) {
            return $this->update(['status' => 'archived']);
        }
        return false;
    }

    public function examAssignments()
    {
        return $this->hasMany(ExamAssignment::class, 'exam_id', 'exam_id');
    }

    public function classes()
    {
        return $this->belongsToMany(
            ClassModel::class,
            'exam_assignments',
            'exam_id',
            'class_id',
            'exam_id',
            'class_id'
        );
    }

    /**
     * Get all exam attempts for this exam through exam assignments
     */
    public function examAttempts()
    {
        return $this->hasManyThrough(
            ExamAttempt::class,      // Final model we want to access
            ExamAssignment::class,   // Intermediate model
            'exam_id',              // Foreign key on exam_assignments table
            'exam_assignment_id',   // Foreign key on exam_attempts table
            'exam_id',              // Local key on exams table
            'assignment_id'         // Local key on exam_assignments table
        );
    }
}