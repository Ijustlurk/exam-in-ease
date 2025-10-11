<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAssignment extends Model
{
    use HasFactory;

    protected $table = 'exam_assignments';
    protected $primaryKey = 'assignment_id';
    public $timestamps = false;
    
    // ✅ ADD THIS - This was missing!
    protected $fillable = [
        'class_id',
        'exam_id'
    ];

    /**
     * Relationship with Exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    /**
     * Relationship with Class
     */
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    /**
     * Relationship to get all students taking this exam
     */
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_assignment_id', 'assignment_id');
    }
}