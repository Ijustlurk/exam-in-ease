<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollabComment extends Model
{
    protected $table = 'collab_comments';
    protected $primaryKey = 'comment_id';
    public $timestamps = false; // Disable updated_at, we only use created_at
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null; // Disable updated_at

    protected $fillable = [
        'exam_id',
        'question_id',
        'teacher_id',
        'comment_text',
        'resolved'
    ];

    protected $casts = [
        'resolved' => 'boolean',
        'created_at' => 'datetime'
    ];

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function question()
    {
        return $this->belongsTo(ExamItem::class, 'question_id', 'item_id');
    }

    public function teacher()
    {
        return $this->belongsTo(UserTeacher::class, 'teacher_id', 'user_id');
    }
}
