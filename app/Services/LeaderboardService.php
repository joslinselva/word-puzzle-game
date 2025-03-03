<?php

namespace App\Services;

use App\Repositories\LeaderboardRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class LeaderboardService
{
    protected $leaderboardRepository;

    public function __construct(LeaderboardRepository $leaderboardRepository)
    {
        $this->leaderboardRepository = $leaderboardRepository;
    }

    public function getTopTenLeaderboard(): Collection
    {
        return $this->leaderboardRepository->getTopTen();
    }

    public function addSubmissionToLeaderboard(string $word, int $score): void
    {
        $existingLeaderboardEntry = $this->leaderboardRepository->findByWord($word);

        if ($existingLeaderboardEntry) {
            if ($score > $existingLeaderboardEntry->score) {
                $existingLeaderboardEntry->score = $score;
                $existingLeaderboardEntry->save();
            }
        } else {
            $this->leaderboardRepository->create([
                'user_id' => Auth::id(),
                'word' => $word,
                'score' => $score,
            ]);
        }
    }

    public function updateLeaderboard(): void
    {
        $this->leaderboardRepository->delete();

        $topSubmissions = $this->leaderboardRepository->getTopSubmissions();

        foreach ($topSubmissions as $submission) {
            $this->leaderboardRepository->create([
                'user_id' => $submission->user_id,
                'word' => $submission->word,
                'score' => $submission->score,
            ]);
        }
    }
}