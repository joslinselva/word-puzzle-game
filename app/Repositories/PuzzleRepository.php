<?php

namespace App\Repositories;

use App\Models\Puzzle;
use Illuminate\Support\Facades\Auth;

class PuzzleRepository
{
    public function createPuzzle(string $shuffled_letters, array $possible_words)
    {
        $puzzle = new Puzzle();
        $puzzle->shuffled_letters = $shuffled_letters;
        $puzzle->possible_words = $possible_words;
        $puzzle->user_id = Auth::id();
        $puzzle->save();
        return $puzzle;
    }

    public function findPuzzleById(int $id)
    {
        return Puzzle::find($id);
    }
}