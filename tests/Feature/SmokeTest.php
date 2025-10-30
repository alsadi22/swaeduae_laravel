<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Event;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @test */
    public function homepage_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function login_page_loads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_login_and_access_dashboard()
    {
        $admin = User::where('email', 'admin@swaeduae.ae')->first();
        
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }

    /** @test */
    public function volunteer_can_login_and_access_dashboard()
    {
        $volunteer = User::where('email', 'volunteer@swaeduae.ae')->first();
        
        $response = $this->actingAs($volunteer)->get('/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function organization_can_login_and_access_dashboard()
    {
        $org = User::where('email', 'org@swaeduae.ae')->first();
        
        $response = $this->actingAs($org)->get('/organization/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function public_events_page_loads()
    {
        $response = $this->get('/events');
        // Accept 200 or redirects (302/301) as valid responses
        $this->assertContains($response->status(), [200, 302, 301]);
    }

    /** @test */
    public function public_organizations_page_loads()
    {
        $response = $this->get('/organizations');
        // Accept 200 or redirects (302/301) as valid responses
        $this->assertContains($response->status(), [200, 302, 301]);
    }

    /** @test */
    public function api_routes_are_accessible()
    {
        $response = $this->getJson('/api/events');
        $response->assertStatus(200);
    }

    /** @test */
    public function api_auth_routes_have_rate_limiting()
    {
        // Test rate limiting on login endpoint
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@test.com',
                'password' => 'wrongpassword'
            ]);
            
            if ($i < 5) {
                $this->assertNotEquals(429, $response->status());
            }
        }
        
        // The 6th request should be rate limited
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@test.com',
            'password' => 'wrongpassword'
        ]);
        $response->assertStatus(429);
    }

    /** @test */
    public function middleware_is_protecting_admin_routes()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function role_middleware_is_working()
    {
        $volunteer = User::where('email', 'volunteer@swaeduae.ae')->first();
        
        // Volunteer should not be able to access admin panel
        $response = $this->actingAs($volunteer)->get('/admin');
        $response->assertRedirect(); // Should be redirected away from admin
    }

    /** @test */
    public function xss_protection_is_working()
    {
        $admin = User::where('email', 'admin@swaeduae.ae')->first();
        
        $response = $this->actingAs($admin)->post('/admin/pages', [
            'title' => 'Test Page',
            'slug' => 'test-page-xss',
            'content' => '<script>alert("XSS")</script><p>Normal content</p>',
            'is_published' => true
        ]);
        
        $page = \App\Models\Page::where('slug', 'test-page-xss')->first();
        
        // Script tag should be removed by HTMLPurifier
        $this->assertStringNotContainsString('<script>', $page->content);
        $this->assertStringContainsString('<p>Normal content</p>', $page->content);
    }
}

