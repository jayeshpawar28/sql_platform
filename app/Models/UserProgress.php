<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic',
        'problems_solved',
        'total_problems',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
