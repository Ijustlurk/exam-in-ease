<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';
    protected $primaryKey = 'subject_id';

    protected $fillable = [
        'subject_code',
        'subject_name',
        'semester'
    ];

    // Relationship with Classes
    public function classes()
    {
        return $this->hasMany(ClassModel::class, 'subject_id', 'subject_id');
    }
}