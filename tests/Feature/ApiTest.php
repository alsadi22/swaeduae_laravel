<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Event;
use App\Models\Application;
use App\Models\Certificate;
use App\Models\Badge;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $volunteerUser;
    protected $organization;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->volunteerUser = User::factory()->create([
            'name' => 'Volunteer User',
            'email' => 'volunteer@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create organization
        $this->organization = Organization::factory()->create([
            'name' => 'Test Organization',
            'email' => 'org@example.com',
            'is_verified' => true,
        ]);

        // Attach user to organization
        $this->organization->users()->attach($this->adminUser->id, [
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create event
        $this->event = Event::factory()->create([
            'organization_id' => $this->organization->id,
            'title' => 'Test Event',
            'requires_application' => true,
        ]);
    }

    /** @test */
    public function it_can_get_all_users()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_get_a_user()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->getJson('/api/users/' . $this->volunteerUser->id);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $this->volunteerUser->id,
                'name' => $this->volunteerUser->name,
                'email' => $this->volunteerUser->email,
            ]);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->putJson('/api/users/' . $this->volunteerUser->id, [
                'name' => 'Updated Name',
                'phone' => '1234567890',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Name',
                'phone' => '1234567890',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->volunteerUser->id,
            'name' => 'Updated Name',
            'phone' => '1234567890',
        ]);
    }

    /** @test */
    public function it_can_create_an_application()
    {
        $response = $this->actingAs($this->volunteerUser, 'api')
            ->postJson('/api/applications', [
                'event_id' => $this->event->id,
                'motivation' => 'I want to volunteer for this event',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'user_id' => $this->volunteerUser->id,
                'event_id' => $this->event->id,
                'motivation' => 'I want to volunteer for this event',
                'status' => 'pending',
            ]);
    }

    /** @test */
    public function it_can_get_applications()
    {
        // Create an application
        $application = Application::factory()->create([
            'user_id' => $this->volunteerUser->id,
            'event_id' => $this->event->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'api')
            ->getJson('/api/applications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_approve_an_application()
    {
        // Create an application
        $application = Application::factory()->create([
            'user_id' => $this->volunteerUser->id,
            'event_id' => $this->event->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser, 'api')
            ->postJson('/api/applications/' . $application->id . '/approve');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'approved',
            ]);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function it_can_create_a_certificate()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->postJson('/api/certificates', [
                'user_id' => $this->volunteerUser->id,
                'event_id' => $this->event->id,
                'organization_id' => $this->organization->id,
                'type' => 'participation',
                'title' => 'Participation Certificate',
                'hours_completed' => 5.5,
                'event_date' => '2023-01-01',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'certificate_number',
                'verification_code',
                'user_id',
                'event_id',
                'organization_id',
                'type',
                'title',
                'hours_completed',
                'event_date',
            ]);
    }

    /** @test */
    public function it_can_get_certificates()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->getJson('/api/certificates');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_verify_a_certificate()
    {
        // Create a certificate
        $certificate = Certificate::factory()->create([
            'user_id' => $this->volunteerUser->id,
            'event_id' => $this->event->id,
            'organization_id' => $this->organization->id,
            'is_verified' => true,
        ]);

        $response = $this->postJson('/api/certificates/verify', [
            'verification_code' => $certificate->verification_code,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'certificate' => [
                    'id' => $certificate->id,
                    'certificate_number' => $certificate->certificate_number,
                ],
            ]);
    }

    /** @test */
    public function it_can_create_a_badge()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->postJson('/api/badges', [
                'name' => 'Test Badge',
                'slug' => 'test-badge',
                'description' => 'A test badge',
                'points' => 100,
                'is_active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Test Badge',
                'slug' => 'test-badge',
                'description' => 'A test badge',
                'points' => 100,
                'is_active' => true,
            ]);
    }

    /** @test */
    public function it_can_get_badges()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->getJson('/api/badges');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_award_a_badge_to_user()
    {
        // Create a badge
        $badge = Badge::factory()->create([
            'name' => 'Test Badge',
            'points' => 100,
        ]);

        $initialPoints = $this->volunteerUser->points ?? 0;

        $response = $this->actingAs($this->adminUser, 'api')
            ->postJson('/api/badges/' . $badge->id . '/award', [
                'user_id' => $this->volunteerUser->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Badge awarded successfully',
            ]);

        $this->assertDatabaseHas('user_badges', [
            'user_id' => $this->volunteerUser->id,
            'badge_id' => $badge->id,
        ]);

        // Check that points were added
        $this->volunteerUser->refresh();
        $this->assertEquals($initialPoints + 100, $this->volunteerUser->points);
    }

    /** @test */
    public function it_can_get_my_applications()
    {
        // Create an application
        $application = Application::factory()->create([
            'user_id' => $this->volunteerUser->id,
            'event_id' => $this->event->id,
        ]);

        $response = $this->actingAs($this->volunteerUser, 'api')
            ->getJson('/api/my-applications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ])
            ->assertJsonFragment([
                'user_id' => $this->volunteerUser->id,
                'event_id' => $this->event->id,
            ]);
    }

    /** @test */
    public function it_can_get_my_certificates()
    {
        // Create a certificate
        $certificate = Certificate::factory()->create([
            'user_id' => $this->volunteerUser->id,
            'event_id' => $this->event->id,
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($this->volunteerUser, 'api')
            ->getJson('/api/my-certificates');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ])
            ->assertJsonFragment([
                'user_id' => $this->volunteerUser->id,
                'event_id' => $this->event->id,
            ]);
    }

    /** @test */
    public function it_can_get_my_badges()
    {
        // Create a badge and award it to user
        $badge = Badge::factory()->create();
        $badge->awardTo($this->volunteerUser);

        $response = $this->actingAs($this->volunteerUser, 'api')
            ->getJson('/api/my-badges');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ])
            ->assertJsonFragment([
                'id' => $badge->id,
                'name' => $badge->name,
            ]);
    }
}