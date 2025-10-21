<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';
    protected $primaryKey = 'section_id';
    
    protected $fillable = [
        'exam_id',
        'section_title',
        'section_directions',
        'section_order'
    ];

    /**
     * Relationship with Exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    /**
     * Relationship with Exam Items
     */
    public function items()
    {
        return $this->hasMany(ExamItem::class, 'exam_section_id', 'section_id')
                    ->orderBy('order', 'asc');
    }

     public function examItems()
    {
        return $this->hasMany(ExamItem::class, 'exam_section_id', 'section_id');
    }
}