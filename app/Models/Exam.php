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
        'total_points',
        'no_of_items',
        'status',
        'approved_date',
        'revision_notes'
    ];

    protected $casts = [
        'schedule_start' => 'datetime',
        'schedule_end' => 'datetime',
        'approved_date' => 'datetime',
    ];

    /**
     * Relationship with Subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

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
     * Relationship with Exam Collaborations
     */
    public function collaborations()
    {
        return $this->hasMany(ExamCollaboration::class, 'exam_id', 'exam_id');
    }

    /**
     * Relationship with Exam Assignments (Classes)
     */
    public function assignments()
    {
        return $this->hasMany(ExamAssignment::class, 'exam_id', 'exam_id');
    }

    /**
     * Relationship with Exam Approvals
     */
    public function approvals()
    {
        return $this->hasMany(ExamApproval::class, 'exam_id', 'exam_id');
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
}