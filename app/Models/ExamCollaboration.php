<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamCollaboration extends Model
{
    use HasFactory;

    protected $table = 'exam_collaboration';
    protected $primaryKey = 'collaboration_id';
    public $timestamps = false;
    
    protected $fillable = [
        'exam_id',
        'teacher_id',
        'role'
    ];

    /**
     * Relationship with Exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    /**
     * Relationship with Teacher (User)
     */
    public function teacher()
    {
        return $this->belongsTo(UserTeacher::class, 'teacher_id', 'user_id');
    }
}