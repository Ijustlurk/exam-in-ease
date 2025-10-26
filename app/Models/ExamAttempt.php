<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $table = 'exam_attempts';
    protected $primaryKey = 'attempt_id';

    protected $fillable = [
        'exam_assignment_id',
        'student_id',
        'start_time',
        'end_time',
        'status',
        'score'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];// Dahil may created_at/updated_at ang inyong attempt table

    // Link sa Student (UserStudent table)
    public function studentDetails()
    {
        // student_id sa attempts table -> user_id sa user_student table
        return $this->belongsTo(UserStudent::class, 'student_id', 'user_id');
    }

    // Link sa Main User table (para makuha ang 'name')
    public function user()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    public function examAssignment()
    {
        return $this->belongsTo(ExamAssignment::class, 'exam_assignment_id', 'assignment_id');
    }

    /**
     * Relationship with Student
     */
    public function student()
    {
        return $this->belongsTo(UserStudent::class, 'student_id', 'user_id');
    }

    /**
     * Relationship: Attempt has many answers
     */
    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id', 'attempt_id');
    }
}
