<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\PuzzleController;
use App\Models\Puzzle;
use App\Services\LeaderboardService;
use App\Services\PuzzleService;
use App\Services\SubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Mockery;
use App\Models\User;

class PuzzleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $puzzleService;
    protected $submissionService;
    protected $leaderboardService;
    protected $puzzleController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->puzzleService = Mockery::mock(PuzzleService::class);
        $this->submissionService = Mockery::mock(SubmissionService::class);
        $this->leaderboardService = Mockery::mock(LeaderboardService::class);

        $this->puzzleController = new PuzzleController(
            $this->puzzleService,
            $this->submissionService,
            $this->leaderboardService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_show_generates_new_puzzle_if_no_session_puzzle_id()
    {
        $puzzle = Puzzle::factory()->make();
        $this->puzzleService->shouldReceive('generatePuzzle')->once()->andReturn($puzzle);

        // Create a user with the 'student' role
        $user = User::factory()->create(['role' => 'student']);

        // Confirm initial session state
        $this->assertNull(Session::get('puzzleId'));

        // Simulate an HTTP GET request as the authenticated user
        $response = $this->actingAs($user)->get(route('puzzle.show'));

        $this->assertEquals($puzzle->id, Session::get('puzzleId'));
        $response->assertViewIs('student.puzzle');
        $response->assertViewHas('shuffled_letters', $puzzle->shuffled_letters);
        $response->assertViewHas('puzzleId', $puzzle->id);
    }
    
}