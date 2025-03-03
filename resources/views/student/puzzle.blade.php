<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200"
                     x-data="{
                        shuffledLetters: '{{ $shuffled_letters }}',
                        word: '',
                        message: '',
                        remainingWords: @json($remainingWords ?? []),
                        score: {{ $score ?? 0 }},
                        leaderboard: @json($leaderboard ?? []),
                        testEnded: false,

                        submitWord() {
                            fetch('{{ route('puzzle.submit') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    puzzleId: '{{ $puzzleId }}',
                                    word: this.word
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                this.$nextTick(() => {
                                    if (data.error) {
                                        this.message = `<div class='alert alert-danger'>${data.error}</div>`;
                                    } else {
                                        this.message = `<div class='alert alert-success'>${data.message}</div>`;
                                        this.shuffledLetters = data.shuffled_letters;
                                        this.word = '';
                                        this.remainingWords = data.remainingWords;
                                        this.score = data.score;
                                        this.leaderboard = data.leaderboard;
                                        this.testEnded = data.testEnded || false;

                                        if (this.testEnded) {
                                            this.shuffledLetters = '';
                                            this.message = `<div class='alert alert-info'>${data.message} Your Score: ${data.score}</div>`;
                                        }
                                    }
                                });
                            })
                            .catch(error => {
                                this.message = `<div class='alert alert-danger'>An error occurred.</div>`;
                            });
                        },

                        endTest() {
                            fetch('{{ route('puzzle.end') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    puzzleId: '{{ $puzzleId }}'
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                this.$nextTick(() => {
                                    this.remainingWords = data.remainingWords;
                                    this.score = data.score;
                                    this.leaderboard = data.leaderboard;
                                    this.shuffledLetters = '';
                                    this.message = `<div class='alert alert-info'>Test Ended. Your Total Score: ${data.score}</div>`;
                                    this.testEnded = true;
                                });
                            })
                            .catch(error => {
                                this.message = `<div class='alert alert-danger'>An error occurred.</div>`;
                            });
                        },
                        playAgain() {
                            window.location.reload();
                        }
                    }">

                    <h1 class="text-2xl font-semibold mb-4">Word Puzzle Game</h1>
                    <p class="ml-2 mb-2">Letters: <span x-text="shuffledLetters"></span></p>

                    <div class="flex items-center space-x-4 mb-4">
                        <form @submit.prevent="submitWord()" class="flex items-center space-x-2">
                            <input type="hidden" name="puzzleId" value="{{ $puzzleId }}">
                            <input type="text" x-model="word" placeholder="Enter word" required class="border rounded p-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" :disabled="testEnded">Add Words</button>
                        </form>
                        <button @click="endTest()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" :disabled="testEnded">End Test</button>
                        <template x-if="testEnded">
                            <button @click="playAgain()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Play Again</button>
                        </template>
                    </div>

                    <div x-html="message"></div>

                    <div class="hidden">
                        <p x-text="JSON.stringify(remainingWords)"></p>
                        <p x-text="JSON.stringify(leaderboard)"></p>
                    </div>

                    <template x-if="remainingWords.length > 0">
                        <div>
                            <h3 class="mt-4">Remaining Words:</h3>
                            <ul class="list-disc list-inside">
                                <template x-for="(word, index) in remainingWords" :key="index">
                                    <li class="p-1"><span x-text="word"></span></li>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <template x-if="score > 0">
                        <h3 class="mt-4">Your Score: <span x-text="score"></span></h3>
                    </template>

                    <template x-if="leaderboard.length > 0">
                        <div>
                            <h1 class="text-2xl font-semibold mb-4">Leaderboard</h1>
                            <table class="min-w-full table-auto">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-4 py-2">Rank</th>
                                        <th class="border px-4 py-2">Student Name</th>
                                        <th class="border px-4 py-2">Word</th>
                                        <th class="border px-4 py-2">Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(entry, index) in leaderboard" :key="index">
                                        <tr class="border-b">
                                            <td class="border px-4 py-2 text-center" x-text="index + 1"></td>
                                            <td class="border px-4 py-2 text-center" x-text="entry.user.name"></td>
                                            <td class="border px-4 py-2 text-center" x-text="entry.word"></td>
                                            <td class="border px-4 py-2 text-center" x-text="entry.score"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</x-app-layout>