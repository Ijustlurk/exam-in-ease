<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';
    protected $primaryKey = 'section_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'exam_id',
    ];

    public $timestamps = true; // change to false if you don’t want timestamps

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id'); 
        // use 'exam_id' if Exam PK = exam_id
    }
}
