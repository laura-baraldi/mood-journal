<?php

namespace App\Http\Controllers;

use App\Models\MoodEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MoodEntryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $entries = $user->moodEntries()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        $daysTracked = $user->moodEntries()
            ->get()
            ->pluck('created_at')
            ->map(fn($date) => $date->toDateString())
            ->unique()
            ->count();

        return response()->json([
            'data' => $entries->items(),
            'meta' => [
                'current_page' => $entries->currentPage(),
                'last_page' => $entries->lastPage(),
                'per_page' => $entries->perPage(),
                'total' => $entries->total(),
            ],
            'stats' => [
                'total_entries' => $entries->total(),
                'days_tracked' => $daysTracked,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mood' => [
                'required',
                'string',
                Rule::in([
                    'sad',
                    'lonely',
                    'anxious',
                    'frustrated',
                    'angry',
                    'tired',
                    'neutral',
                    'calm',
                    'happy',
                    'excited',
                ]),
            ],
            'note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $entry = $request->user()
            ->moodEntries()
            ->create($validated);

        return response()->json([
            'data' => $entry,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $entry = $request->user()
            ->moodEntries()
            ->findOrFail($id);

        $validated = $request->validate([
            'mood' => [
                'sometimes',
                'required',
                'string',
                Rule::in([
                    'sad',
                    'lonely',
                    'anxious',
                    'frustrated',
                    'angry',
                    'tired',
                    'neutral',
                    'calm',
                    'happy',
                    'excited',
                ]),
            ],
            'note' => [
                'sometimes',
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $entry->update($validated);

        return response()->json([
            'data' => $entry->fresh(),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $entry = $request->user()
            ->moodEntries()
            ->findOrFail($id);

        $entry->delete();

        return response()->json(null, 204);
    }
}
