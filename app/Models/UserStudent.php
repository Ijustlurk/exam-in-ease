<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStudent extends Model
{
    use HasFactory;

    protected $table = 'user_student';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email_address',
        'id_number',
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

    // Relationship with Classes through class_enrolment
    public function classes()
    {
        return $this->belongsToMany(
            ClassModel::class,
            'class_enrolment',
            'student_id',
            'class_id',
            'user_id',
            'class_id'
        )->wherePivot('status', 'Active');
    }

    // Get full name
    public function getFullNameAttribute()
    {
        $middle = $this->middle_name ? ' ' . substr($this->middle_name, 0, 1) . '.' : '';
        return $this->first_name . $middle . ' ' . $this->last_name;
    }

    // Scope for enrolled students
    public function scopeEnrolled($query)
    {
        return $query->where('status', 'Enrolled');
    }
}