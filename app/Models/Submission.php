<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'puzzle_id',
        'user_id',
        'word',
        'score',
    ];

    public function puzzle()
    {
        return $this->belongsTo(Puzzle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}