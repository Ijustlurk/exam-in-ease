<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamItem extends Model
{
    use HasFactory;

    protected $table = 'exam_items';
    protected $primaryKey = 'item_id';
    public $timestamps = false;

    protected $fillable = [
        'exam_id',
        'exam_section_id',
        'question',
        'item_type',
        'enum_type',
        'expected_answer',
        'options',
        'answer',
        'points_awarded',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'answer' => 'array',
    ];

    /**
     * Relationship with Exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    /**
     * Relationship with Exam Section
     */
    public function section()
    {
        return $this->belongsTo(ExamSection::class, 'exam_section_id', 'section_id');
    }
}