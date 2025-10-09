<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'class';
    protected $primaryKey = 'class_id';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'subject_id',
        'year_level',
        'section',
        'semester',
        'school_year',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationship with Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    // Relationship with Teacher Assignments
    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'class_id', 'class_id');
    }

    // Get primary teacher (first assigned teacher)
    public function teacher()
    {
        return $this->hasOneThrough(
            UserTeacher::class,
            TeacherAssignment::class,
            'class_id',
            'user_id',
            'class_id',
            'teacher_id'
        );
    }

    // Relationship with Students through class_enrolment
    public function students()
    {
        return $this->belongsToMany(
            UserStudent::class,
            'class_enrolment',
            'class_id',
            'student_id',
            'class_id',
            'user_id'
        )->wherePivot('status', 'Active');
    }

    // Get student count
    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }

    // Scope for active classes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // Scope for archived classes
    public function scopeArchived($query)
    {
        return $query->where('status', 'Archived');
    }
}