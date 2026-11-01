<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MoodEntryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $entries = $request->user()
            ->moodEntries()
            ->latest()
            ->get();

        return response()->json([
            'data' => $entries,
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
}
