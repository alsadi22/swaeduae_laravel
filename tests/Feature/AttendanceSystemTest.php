<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Event;
use App\Models\Application;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $volunteer;
    protected $organization;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        // Create volunteer
        $this->volunteer = User::where('email', 'volunteer@swaeduae.ae')->first();
        
        // Create organization
        $this->organization = Organization::factory()->create([
            'name' => 'Test Organization',
            'status' => 'approved',
            'is_verified' => true,
        ]);

        // Create event
        $this->event = Event::factory()->create([
            'organization_id' => $this->organization->id,
            'title' => 'Test Event',
            'status' => 'published',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2),
            'latitude' => 25.2048,
            'longitude' => 55.2708,
        ]);

        // Create approved application
        Application::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'approved',
        ]);
    }

    public function test_volunteer_can_check_in_to_event()
    {
        $response = $this->actingAs($this->volunteer, 'sanctum')
            ->postJson('/api/attendance/checkin', [
                'event_id' => $this->event->id,
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'attendance' => [
                    'id',
                    'event_id',
                    'user_id',
                    'check_in_time',
                ]
            ]);

        $this->assertDatabaseHas('attendances', [
            'event_id' => $this->event->id,
            'user_id' => $this->volunteer->id,
            'status' => 'checked_in',
        ]);
    }

    public function test_volunteer_can_check_out_from_event()
    {
        // First check in
        Attendance::create([
            'event_id' => $this->event->id,
            'user_id' => $this->volunteer->id,
            'check_in_time' => now(),
            'check_in_latitude' => 25.2048,
            'check_in_longitude' => 55.2708,
            'status' => 'checked_in',
        ]);

        // Now check out
        $response = $this->actingAs($this->volunteer, 'sanctum')
            ->postJson('/api/attendance/checkout', [
                'event_id' => $this->event->id,
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('attendances', [
            'event_id' => $this->event->id,
            'user_id' => $this->volunteer->id,
            'status' => 'checked_out',
        ]);
    }

    public function test_attendance_location_validation_works()
    {
        // Try to check in from too far away
        $response = $this->actingAs($this->volunteer, 'sanctum')
            ->postJson('/api/attendance/checkin', [
                'event_id' => $this->event->id,
                'latitude' => 26.0,  // Too far from event location
                'longitude' => 56.0,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'You are not within the event location'
            ]);
    }

    public function test_qr_code_scan_for_attendance()
    {
        $qrData = json_encode([
            'event_id' => $this->event->id,
            'type' => 'check_in'
        ]);

        $response = $this->actingAs($this->volunteer, 'sanctum')
            ->postJson('/api/attendance/scan', [
                'qr_data' => $qrData,
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ]);

        $response->assertStatus(200);
    }

    public function test_volunteer_can_view_attendance_history()
    {
        // Create some attendance records
        Attendance::create([
            'event_id' => $this->event->id,
            'user_id' => $this->volunteer->id,
            'check_in_time' => now()->subDays(1),
            'check_out_time' => now()->subDays(1)->addHours(3),
            'status' => 'checked_out',
        ]);

        $response = $this->actingAs($this->volunteer, 'sanctum')
            ->getJson('/api/attendance/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'event',
                        'check_in_time',
                        'check_out_time',
                        'status',
                    ]
                ]
            ]);
    }

    public function test_cannot_check_in_without_approved_application()
    {
        // Create another volunteer without application
        $volunteer2 = User::factory()->create([
            'unique_id' => 'VOL' . strtoupper(uniqid()),
        ]);
        $volunteer2->assignRole('volunteer');

        $response = $this->actingAs($volunteer2, 'sanctum')
            ->postJson('/api/attendance/checkin', [
                'event_id' => $this->event->id,
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ]);

        $response->assertStatus(403);
    }

    public function test_cannot_check_in_twice()
    {
        // First check in
        $this->actingAs($this->volunteer, 'sanctum')
            ->postJson('/api/attendance/checkin', [
                'event_id' => $this->event->id,
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ]);

        // Try to check in again
        $response = $this->actingAs($this->volunteer, 'sanctum')
            ->postJson('/api/attendance/checkin', [
                'event_id' => $this->event->id,
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ]);

        $response->assertStatus(400);
    }
}

