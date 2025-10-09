<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAssignment extends Model
{
    use HasFactory;

    // app/Models/ExamAssignment.php
    // ...
    protected $primaryKey = 'assignment_id';
    public $timestamps = false; // Based on your schema

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    // Relationship para kunin ang lahat ng students na kumukuha ng exam na ito
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_assignment_id', 'assignment_id');
    }

}
