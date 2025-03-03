<?php

namespace Database\Factories;

use App\Models\Puzzle;
use Illuminate\Database\Eloquent\Factories\Factory;

class PuzzleFactory extends Factory
{
    protected $model = Puzzle::class;

    public function definition()
    {
        return [
            'shuffled_letters' => $this->faker->words(5, true),
            'possible_words' => $this->faker->words(3),
        ];
    }
}