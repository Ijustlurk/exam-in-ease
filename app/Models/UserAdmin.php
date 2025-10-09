<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAdmin extends Model
{
    use HasFactory;

    protected $table = 'user_admin';
    protected $primaryKey = 'admin_id';
    public $timestamps = false; // if your table has no created_at/updated_at

    protected $fillable = [
        'user_id',
        'username',
        'password_hash',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
