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
        return $this->hasMany(ExamSection::class, 'exam_id', 'exam_id')
                    ->orderBy('section_order', 'asc');
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
     * Get formatted created date
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('F j, Y');
    }
}