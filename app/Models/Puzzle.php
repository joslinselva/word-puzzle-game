<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    use HasFactory;

    protected $fillable = [
        'shuffled_letters',
        'possible_words',
        'user_id',
    ];

    protected $casts = [
        'possible_words' => 'array',
    ];
}