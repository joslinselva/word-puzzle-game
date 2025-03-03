<?php

namespace App\Repositories;

use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

class SubmissionRepository
{
    public function create(int $puzzleId, string $word, int $score)
    {
        return Submission::create([
            'puzzle_id' => $puzzleId,
            'user_id' => Auth::id(),
            'word' => $word,
            'score' => $score,
        ]);
    }

    public function checkWordAlreadySubmitted(int $puzzleId, string $word): bool
    {
        return Submission::where('puzzle_id', $puzzleId)
            ->where('word', $word)
            ->where('user_id', Auth::id())
            ->exists();
    }

    public function getScoreByPuzzleAndUser(int $puzzleId)
    {
        return Submission::where('puzzle_id', $puzzleId)
            ->where('user_id', Auth::id())
            ->sum('score');
    }

    public function getLeaderboardByPuzzle(int $puzzleId)
    {
        return Submission::select('word', 'score')
            ->where('puzzle_id', $puzzleId)
            ->orderBy('score', 'desc')
            ->groupBy('word')
            ->take(10)
            ->get();
    }

    public function getSubmittedWordsByUserAndPuzzle(int $puzzleId)
    {
        return Submission::where('puzzle_id', $puzzleId)
            ->where('user_id', Auth::id())
            ->pluck('word')
            ->toArray();
    }
}