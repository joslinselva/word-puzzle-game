<?php

namespace App\Repositories;

use App\Models\Leaderboard;
use App\Models\Submission;
use Illuminate\Support\Collection;

class LeaderboardRepository
{
    public function getTopTen(): Collection
    {
        $leaderboard = Leaderboard::with('user')->orderBy('score', 'desc')->take(10)->get();

        return $leaderboard;
    }

    public function create(array $data): Leaderboard
    {
        $leaderboard = Leaderboard::create($data);
        $this->maintainTopTen();
        return $leaderboard;
    }

    public function findByWord(string $word): ?Leaderboard
    {
        return Leaderboard::where('word', $word)
            ->first();
    }

    public function delete(): void
    {
        Leaderboard::truncate();
    }

    public function getTopSubmissions(): Collection
    {
        return Submission::select('user_id', 'word', 'score')
            ->orderByDesc('score')
            ->groupBy('word')
            ->limit(10)
            ->get();
    }

    private function maintainTopTen(): void
    {
        $topTenIds = Leaderboard::orderBy('score', 'desc')->take(10)->pluck('id')->toArray();
        Leaderboard::whereNotIn('id', $topTenIds)->delete();
    }
}