<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MoodEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_mood_entries(): void
    {
        $this->getJson('/api/mood-entries')
            ->assertUnauthorized();
    }

    public function test_it_returns_a_maximum_of_ten_entries_per_page(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        for ($i = 0; $i < 12; $i++) {
            $createdAt = now()->subMinutes($i);

            $user->moodEntries()->create([
                'mood' => 'happy',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $response = $this->getJson('/api/mood-entries');

        $response
            ->assertOk()
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 12);

        $this->assertCount(10, $response->json('data'));

        $secondPage = $this->getJson('/api/mood-entries?page=2');

        $secondPage
            ->assertOk()
            ->assertJsonPath('meta.current_page', 2);

        $this->assertCount(2, $secondPage->json('data'));
    }



    public function test_statistics_include_all_entries_not_only_the_current_page(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        for ($i = 0; $i < 12; $i++) {
            $createdAt = Carbon::now()
                ->subDays(intdiv($i, 4))
                ->setTime(12, $i, 0);

            $entry = $user->moodEntries()->create([
                'mood' => 'calm',
            ]);

            $entry->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->save();
        }

        $this->getJson('/api/mood-entries')
            ->assertOk()
            ->assertJsonPath('stats.total_entries', 12)
            ->assertJsonPath('stats.days_tracked', 3);
    }


    public function test_user_can_only_see_their_own_mood_entries(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $firstEntry = $firstUser->moodEntries()->create([
            'mood' => 'happy',
        ]);

        $secondUser->moodEntries()->create([
            'mood' => 'sad',
        ]);

        Sanctum::actingAs($firstUser);

        $response = $this->getJson('/api/mood-entries');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $firstEntry->id)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_user_can_update_their_own_mood_entry(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $entry = $user->moodEntries()->create([
            'mood' => 'happy',
            'note' => 'Feeling good',
        ]);

        $this->patchJson("/api/mood-entries/{$entry->id}", [
            'mood' => 'calm',
            'note' => 'Feeling more relaxed',
        ])
            ->assertOk()
            ->assertJsonPath('data.mood', 'calm')
            ->assertJsonPath('data.note', 'Feeling more relaxed');

        $this->assertDatabaseHas('mood_entries', [
            'id' => $entry->id,
            'mood' => 'calm',
            'note' => 'Feeling more relaxed',
        ]);
    }

    public function test_user_can_delete_their_own_mood_entry(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $entry = $user->moodEntries()->create([
            'mood' => 'happy',
        ]);

        $this->deleteJson("/api/mood-entries/{$entry->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('mood_entries', [
            'id' => $entry->id,
        ]);
    }

    public function test_user_cannot_update_another_users_mood_entry(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $entry = $secondUser->moodEntries()->create([
            'mood' => 'happy',
        ]);

        Sanctum::actingAs($firstUser);

        $this->patchJson("/api/mood-entries/{$entry->id}", [
            'mood' => 'sad',
        ])->assertNotFound();
    }

    public function test_user_cannot_delete_another_users_mood_entry(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $entry = $secondUser->moodEntries()->create([
            'mood' => 'happy',
        ]);

        Sanctum::actingAs($firstUser);

        $this->deleteJson("/api/mood-entries/{$entry->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('mood_entries', [
            'id' => $entry->id,
        ]);
    }
}
