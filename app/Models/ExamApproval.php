<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamApproval extends Model
{
    use HasFactory;

    protected $table = 'exam_approvals';
    protected $primaryKey = 'approval_id';
    public $timestamps = false;
    
    protected $fillable = [
        'exam_id',
        'approver_id',
        'status',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    /**
     * Relationship with Exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    /**
     * Relationship with Approver (Admin)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id', 'id');
    }
}