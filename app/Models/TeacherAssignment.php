<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAssignment extends Model
{
    use HasFactory;

    protected $table = 'teacher_assignments';
    protected $primaryKey = 'teacher_assignment_id';

    protected $fillable = [
        'class_id',
        'teacher_id'
    ];

    // Relationship with Class
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    // Relationship with Teacher
    public function teacher()
    {
        return $this->belongsTo(UserTeacher::class, 'teacher_id', 'user_id');
    }
}