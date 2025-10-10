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
        'schedule_date',
        'duration',
        'total_points',
        'no_of_items',
        'user_id',
        'status',
        'approved_by',
        'approved_date',
        'revision_notes'
    ];

    protected $casts = [
        'schedule_date' => 'datetime',
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
     * Relationship with User (Creator)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
    public function isCreator($userId)
    {
        return $this->user_id == $userId;
    }

    /**
     * Check if a user is a collaborator on this exam
     */
    public function isCollaborator($userId)
    {
        return $this->collaborations()->where('teacher_id', $userId)->exists();
    }

    /**
     * Check if a user has access to this exam (creator or collaborator)
     */
    public function hasAccess($userId)
    {
        return $this->isCreator($userId) || $this->isCollaborator($userId);
    }
}