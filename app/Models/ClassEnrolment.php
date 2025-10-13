<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassEnrolment extends Model
{
    use HasFactory;

    protected $table = 'class_enrolment';
    
    public $timestamps = false;
    
    // No auto-incrementing primary key for pivot tables
    public $incrementing = false;
    
    protected $fillable = [
        'class_id',
        'student_id',
        'status'
    ];

    /**
     * Relationship with Class
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    /**
     * Relationship with Student
     */
    public function student()
    {
        return $this->belongsTo(UserStudent::class, 'student_id', 'user_id');
    }

    /**
     * Scope for active enrolments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}