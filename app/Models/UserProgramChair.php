<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgramChair extends Model
{
    use HasFactory;

    protected $table = 'user_program_chair';
    protected $primaryKey = 'chair_id'; // or user_id if you prefer
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'email_address',
        'username',
        'password_hash',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
