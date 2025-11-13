<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTeacher extends Model
{
    use HasFactory;

    protected $table = 'user_teacher';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'email_address',
        'username',
        'password_hash',
        'status'
    ];

    protected $hidden = [
        'password_hash'
    ];

    // Relationship with User (main users table)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relationship with Teacher Assignments
    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'teacher_id', 'user_id');
    }

    // Get full name
    public function getFullNameAttribute()
    {
        $middle = $this->middle_name ? ' ' . substr($this->middle_name, 0, 1) . '.' : '';
        return $this->first_name . $middle . ' ' . $this->last_name;
    }

    // Scope for active teachers
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}