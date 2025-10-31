<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnalyticsApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_get_analytics_dashboard()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/analytics/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function it_can_get_kpi_metrics()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/analytics/kpi?start_date=2025-01-01&end_date=2025-12-31');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_get_metric_trends()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/analytics/metric-trend/users?days=30');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_get_conversion_funnel()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/analytics/conversion-funnel?start_date=2025-01-01&end_date=2025-12-31');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_requires_authentication_for_analytics()
    {
        $response = $this->getJson('/api/analytics/dashboard');

        $response->assertStatus(401);
    }
}
