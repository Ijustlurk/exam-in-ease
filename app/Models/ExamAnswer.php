<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $table = 'exam_answers';
    protected $primaryKey = 'answer_id';

    protected $fillable = [
        'attempt_id',
        'item_id',
        'answer_text',
        'is_correct',
        'points_earned',
        'ai_feedback',
        'ai_confidence',
        'requires_manual_review',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'decimal:2',
        'requires_manual_review' => 'boolean',
    ];

    /**
     * Relationship: Answer belongs to an attempt
     */
    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id', 'attempt_id');
    }

    /**
     * Relationship: Answer belongs to an exam item (question)
     */
    public function examItem()
    {
        return $this->belongsTo(ExamItem::class, 'item_id', 'item_id');
    }
}
