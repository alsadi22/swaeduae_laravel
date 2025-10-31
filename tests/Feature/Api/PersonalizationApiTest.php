<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonalizationApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_track_user_behavior()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/personalization/track-behavior', [
                'action_type' => 'view',
                'entity_type' => 'Event',
                'entity_id' => 1,
                'metadata' => ['duration' => 30],
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'behavior']);
    }

    /** @test */
    public function it_can_get_user_insights()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/personalization/insights');

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'user_id', 'engagement_level']);
    }

    /** @test */
    public function it_can_get_recommendations()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/personalization/recommendations');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->postJson('/api/personalization/track-behavior', [
            'action_type' => 'view',
            'entity_type' => 'Event',
        ]);

        $response->assertStatus(401);
    }
}
