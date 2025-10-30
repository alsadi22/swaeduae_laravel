<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Event;
use App\Models\Application;
use App\Models\Attendance;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $organization;
    protected $event;
    protected $application;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'total_volunteer_hours' => 10.5,
            'total_events_attended' => 2,
            'points' => 100,
        ]);

        // Create organization
        $this->organization = Organization::factory()->create([
            'name' => 'Test Organization',
            'email' => 'org@example.com',
            'is_verified' => true,
        ]);

        // Create event
        $this->event = Event::factory()->create([
            'organization_id' => $this->organization->id,
            'title' => 'Test Event',
            'description' => 'Test event description',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(7)->addHours(3),
            'city' => 'Dubai',
            'emirate' => 'Dubai',
            'status' => 'published',
        ]);

        // Create application
        $this->application = Application::factory()->create([
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function it_can_get_mobile_dashboard()
    {
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/mobile/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['name', 'email'],
                'stats' => ['total_volunteer_hours', 'total_events_attended', 'total_certificates', 'total_badges', 'points'],
                'upcoming_events',
                'recent_certificates',
                'recent_badges',
            ]);
    }

    /** @test */
    public function it_can_get_events_for_mobile()
    {
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/mobile/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'start_date', 'end_date', 'location', 'city', 'emirate', 'is_applied', 'application_status']
                ],
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_filter_events_by_search()
    {
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/mobile/events?search=Test');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Test Event'
            ]);
    }

    /** @test */
    public function it_can_get_user_applications()
    {
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/mobile/applications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'event_id', 'status', 'applied_at']
                ],
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_get_user_attendance()
    {
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/mobile/attendance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_checkin_to_event()
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/mobile/checkin', [
                'event_id' => $this->event->id,
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully checked in'
            ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
            'status' => 'checked_in',
        ]);
    }

    /** @test */
    public function it_can_checkout_from_event()
    {
        // First check in
        $attendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
            'application_id' => $this->application->id,
            'checked_in_at' => now(),
            'status' => 'checked_in',
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/mobile/checkout', [
                'attendance_id' => $attendance->id,
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully checked out'
            ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'checked_out_at' => now(),
            'status' => 'checked_out',
        ]);
    }

    /** @test */
    public function it_requires_authentication_for_mobile_routes()
    {
        $response = $this->getJson('/api/mobile/dashboard');
        
        $response->assertStatus(401);
    }
}