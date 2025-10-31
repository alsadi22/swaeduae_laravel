<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();
    }

    /** @test */
    public function api_authentication_works()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function can_list_events()
    {
        Event::factory(5)->create(['organization_id' => $this->organization->id]);

        $response = $this->getJson('/api/events');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function can_search_events()
    {
        Event::factory(10)->create(['organization_id' => $this->organization->id]);

        $response = $this->getJson('/api/search/events?query=volunteer&limit=5');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_get_recommendations()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/personalization/recommendations');

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_track_behavior()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/personalization/track-behavior', [
                'action_type' => 'view',
                'entity_type' => 'Event',
                'entity_id' => 1,
            ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function authenticated_user_can_access_analytics()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/analytics/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function can_get_user_badges()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/badges');

        $response->assertStatus(200);
    }

    /** @test */
    public function can_list_applications()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/applications');

        $response->assertStatus(200);
    }

    /** @test */
    public function can_access_wallet()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/wallet');

        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_endpoints()
    {
        $response = $this->getJson('/api/wallet');
        
        $response->assertStatus(401);
    }

    /** @test */
    public function invalid_routes_return_404()
    {
        $response = $this->getJson('/api/nonexistent-endpoint');
        
        $response->assertStatus(404);
    }

    /** @test */
    public function api_validates_required_fields()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/personalization/track-behavior', []);

        $response->assertStatus(422);
    }

    /** @test */
    public function pagination_works_correctly()
    {
        Event::factory(25)->create(['organization_id' => $this->organization->id]);

        $response = $this->getJson('/api/events?per_page=10&page=1');
        
        $response->assertStatus(200);
        $this->assertLessThanOrEqual(10, count($response->json('data')));
    }
}
