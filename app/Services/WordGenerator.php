<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WordGenerator
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function generateShuffledWord(): array
    {
        $words = $this->getRandomWords();
        if (empty($words)) {
            Log::error('Could not retrieve random words from API.');
            throw new \Exception('Failed to retrieve random words.');
        }

        $combinedWord = implode('', $words);
        $shuffledWord = $this->shuffleLetters($combinedWord);
        return ['shuffled_letters' => $shuffledWord, 'possible_words' => $words];
    }

    protected function getRandomWords(): array
    {
        try {
            $filePath = storage_path('app/words.json');
            $words = json_decode(file_get_contents($filePath), true);

            if (empty($words)) {
                Log::error('words.json file is empty or could not be read.');
                return [];
            }

            $numberOfWords = random_int(1, 3);
            $randomWords = array_rand(array_flip($words), $numberOfWords);

            if (!is_array($randomWords)) {
                $randomWords = [$randomWords];
            }

            return $randomWords;

        } catch (\Exception $e) {
            Log::error('Error fetching random words from JSON: ' . $e->getMessage());
            return [];
        }
    }

    public function checkValidWord($word): array
    {
        try {
            $response = $this->client->get("https://api.dictionaryapi.dev/api/v2/entries/en/" . $word);
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                // Word not found
                return json_decode($e->getResponse()->getBody()->getContents(), true);
            } else {
                // Other client errors
                Log::error('API Client Error: ' . $e->getMessage());
                return [];
            }
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return [];
        }
    }

    protected function shuffleLetters(string $word): string
    {
        $letters = str_split($word);
        shuffle($letters);
        return implode('', $letters);
    }
}