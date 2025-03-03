<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-semibold mb-4">Admin Dashboard</h1>

                    <h2 class="text-lg font-semibold mb-2">Top 10 Leaderboard</h2>
                    @if ($leaderboard->count() > 0)
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
                                @foreach ($leaderboard as $index => $entry)
                                    <tr class="border-b">
                                        <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $entry->user->name ?? 'Unknown' }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $entry->word }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $entry->score }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No leaderboard data available.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>