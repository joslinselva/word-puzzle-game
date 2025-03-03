<?php

namespace App\Services;

use App\Repositories\SubmissionRepository;

class SubmissionService
{
    protected $submissionRepository;

    public function __construct(SubmissionRepository $submissionRepository)
    {
        $this->submissionRepository = $submissionRepository;
    }

    public function createSubmission(int $puzzleId, string $word, int $score)
    {
        return $this->submissionRepository->create($puzzleId, $word, $score);
    }

    public function checkWordAlreadySubmitted(int $puzzleId, string $word): bool
    {
        return $this->submissionRepository->checkWordAlreadySubmitted($puzzleId, $word);
    }

    public function getScore(int $puzzleId)
    {
        return $this->submissionRepository->getScoreByPuzzleAndUser($puzzleId);
    }

    public function getLeaderboard(int $puzzleId)
    {
        return $this->submissionRepository->getLeaderboardByPuzzle($puzzleId);
    }

    public function getSubmittedWords(int $puzzleId)
    {
        return $this->submissionRepository->getSubmittedWordsByUserAndPuzzle($puzzleId);
    }
}