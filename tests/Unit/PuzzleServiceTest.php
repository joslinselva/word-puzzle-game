<?php

namespace Tests\Unit\Services;

use App\Models\Puzzle;
use App\Services\PuzzleService;
use App\Repositories\PuzzleRepository;
use App\Services\SubmissionService;
use App\Services\WordGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class PuzzleServiceTest extends TestCase
{
    //use RefreshDatabase;

    protected $puzzleService;
    protected $puzzleRepository;
    protected $wordGenerator;
    protected $submissionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->puzzleRepository = Mockery::mock(PuzzleRepository::class);
        $this->wordGenerator = Mockery::mock(WordGenerator::class);
        $this->submissionService = Mockery::mock(SubmissionService::class);

        $this->puzzleService = new PuzzleService(
            $this->puzzleRepository,
            $this->wordGenerator,
            $this->submissionService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_generate_puzzle()
    {
        $puzzle = Puzzle::factory()->make();
        $this->wordGenerator->shouldReceive('generateShuffledLetters')->once()->andReturn($puzzle->shuffled_letters);
        $this->wordGenerator->shouldReceive('generatePossibleWords')->once()->andReturn($puzzle->possible_words);
        $this->puzzleRepository->shouldReceive('create')->once()->andReturn($puzzle);

        $result = $this->puzzleService->generatePuzzle();

        $this->assertInstanceOf(Puzzle::class, $result);
        $this->assertEquals($puzzle->shuffled_letters, $result->shuffled_letters);
        $this->assertEquals($puzzle->possible_words, $result->possible_words);
    }

}