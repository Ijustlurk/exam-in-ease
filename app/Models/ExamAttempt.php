<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

     protected $primaryKey = 'attempt_id';
    public $timestamps = true; // Dahil may created_at/updated_at ang inyong attempt table

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
}
