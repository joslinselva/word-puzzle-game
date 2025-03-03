<?php

namespace App\Http\Controllers;

use App\Services\PuzzleService;
use App\Services\SubmissionService;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use App\Models\Puzzle;
use App\Models\Submission;

class PuzzleController extends Controller
{
    protected $puzzleService;
    protected $submissionService;
    protected $leaderboardService;

    public function __construct(PuzzleService $puzzleService, SubmissionService $submissionService, LeaderboardService $leaderboardService)
    {
        $this->puzzleService = $puzzleService;
        $this->submissionService = $submissionService;
        $this->leaderboardService = $leaderboardService; 
    }

    public function show()
    {
        $puzzleId = Session::get('puzzleId');

        if ($puzzleId) {
            $puzzle = $this->puzzleService->getPuzzleById($puzzleId);

            if (!$puzzle) {
                $puzzle = $this->puzzleService->generatePuzzle();
                Session::put('puzzleId', $puzzle->id);
            }
        } else {
            $puzzle = $this->puzzleService->generatePuzzle();
            Session::put('puzzleId', $puzzle->id);
        }

        return view('student.puzzle', ['shuffled_letters' => $puzzle->shuffled_letters, 'puzzleId' => $puzzle->id]);
    }

    public function submit(Request $request)
    {
        try {
            $request->validate([
                'word' => ['required', 'string', 'regex:/^[a-zA-Z]+$/'],
                'puzzleId' => 'required|exists:puzzles,id',
            ]);

            $word = $request->input('word');
            $puzzleId = $request->input('puzzleId');

            $result = $this->puzzleService->submitWord($puzzleId, $word);

            if (isset($result['error'])) {
                return response()->json(['error' => $result['error']], 400);
            }

            $puzzle = $this->puzzleService->getPuzzleById($puzzleId);

            $wordScore = strlen($word);

            // Add submission to leaderboard
            $this->leaderboardService->addSubmissionToLeaderboard($word, $wordScore);

            $leaderboard = $this->leaderboardService->getTopTenLeaderboard();

            // Checking if shuffled_letters is empty
            if (empty($result['shuffled_letters'])) {
                Session::forget('puzzleId');
                return response()->json([
                    'message' => 'Test Ended. No characters left!',
                    'shuffled_letters' => $result['shuffled_letters'],
                    'score' => $result['score'],
                    'remainingWords' => [],
                    'testEnded' => true,
                    'leaderboard' => $leaderboard,
                ]);
            }

            // Checking if all possible words have been submitted
            $submittedWords = $this->submissionService->getSubmittedWords($puzzleId);

            $remainingWords = array_diff($puzzle->possible_words, $submittedWords);
            if (empty($remainingWords)) {
                Session::forget('puzzleId');
                return response()->json([
                    'message' => 'Test Ended. All possible words found!',
                    'shuffled_letters' => $result['shuffled_letters'],
                    'score' => $result['score'],
                    'remainingWords' => [],
                    'testEnded' => true,
                    'leaderboard' => $leaderboard,
                ]);
            }


            return response()->json([
                'message' => 'Word submitted!',
                'shuffled_letters' => $result['shuffled_letters'],
                'score' => $result['score'],
                'remainingWords' => [],
            ]);

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()['word'][0]], 422);
        }
    }

    public function end(Request $request)
    {
        $puzzleId = $request->input('puzzleId');
        $puzzle = $this->puzzleService->getPuzzleById($puzzleId);

        if (!$puzzle) {
            return response()->json(['error' => 'Puzzle not found.'], 404);
        }

        $submittedWords = $this->submissionService->getSubmittedWords($puzzleId);

        $remainingWords = array_diff($puzzle->possible_words, $submittedWords);

        $score = $this->submissionService->getScore($puzzleId);

        $leaderboard = $this->leaderboardService->getTopTenLeaderboard();

        Session::forget('puzzleId');

        return response()->json([
            'remainingWords' => array_values($remainingWords),
            'score' => $score,
            'leaderboard' => $leaderboard,
            'testEnded' => true,
        ]);
    }
}