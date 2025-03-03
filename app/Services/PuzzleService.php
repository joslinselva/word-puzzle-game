<?php

namespace App\Services;

use App\Repositories\PuzzleRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PuzzleService
{
    protected $puzzleRepository;
    protected $wordGenerator;
    protected $submissionService;

    public function __construct(PuzzleRepository $puzzleRepository, WordGenerator $wordGenerator, SubmissionService $submissionService)
    {
        $this->puzzleRepository = $puzzleRepository;
        $this->wordGenerator = $wordGenerator;
        $this->submissionService = $submissionService;
    }

    public function generatePuzzle()
    {
        try {
            $wordData = $this->wordGenerator->generateShuffledWord();
            return $this->puzzleRepository->createPuzzle($wordData['shuffled_letters'], $wordData['possible_words']);
        } catch (\Exception $e) {
            Log::error('Error generating puzzle: ' . $e->getMessage());
            throw new \Exception('Failed to generate puzzle.');
        }
    }

    public function getPuzzleById(int $id)
    {
        return $this->puzzleRepository->findPuzzleById($id);
    }

    public function submitWord(int $puzzleId, string $word)
    {
        $puzzle = $this->puzzleRepository->findPuzzleById($puzzleId);

        if (!$puzzle) {
            return ['error' => 'Puzzle not found.'];
        }

        // $validData = $this->wordGenerator->checkValidWord($word);

        // if (empty($validData)) {
        //     return ['error' => 'Error checking word validity. Please try again.'];
        // }

        // if (isset($validData['title']) && $validData['title'] === 'No Definitions Found') {
        //     return ['error' => 'Invalid word, not a valid English word.'];
        // }

        // Checking if the submitted word is in the possible_words array
        $possibleWords = $puzzle->possible_words;

        if (!in_array($word, $possibleWords)) {
            return ['error' => 'Invalid word, not in possible words.'];
        }

        // Check if the word has already been submitted
        $alreadySubmitted = $this->submissionService->checkWordAlreadySubmitted($puzzleId, $word);

        if ($alreadySubmitted) {
            return ['error' => 'Word already submitted for this puzzle.'];
        }

        // Calculating the score based on string length
        $score = strlen($word);

        // Create the submission
        $this->submissionService->createSubmission($puzzleId, $word, $score);

        // Remove the letters of the submitted word from shuffled_letters
        $shuffledLetters = $puzzle->shuffled_letters;
        for ($i = 0; $i < strlen($word); $i++) {
            $char = $word[$i];
            $pos = strpos($shuffledLetters, $char);
            if ($pos !== false) {
                $shuffledLetters = substr_replace($shuffledLetters, '', $pos, 1);
            }
        }

        // Update the puzzle with the reduced shuffled_letters
        $puzzle->shuffled_letters = $shuffledLetters;
        $puzzle->save();

        // Get the updated score and leaderboard
        $score = $this->submissionService->getScore($puzzleId);
        //$leaderboard = $this->submissionService->getLeaderboard($puzzleId);

        return [
            'shuffled_letters' => $puzzle->shuffled_letters,
            'score' => $score,
            //'leaderboard' => $leaderboard,
        ];
    }
}