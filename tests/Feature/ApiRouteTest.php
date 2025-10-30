<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiRouteTest extends TestCase
{
    /**
     * Test that API routes are properly registered
     *
     * @return void
     */
    public function test_api_routes_are_registered()
    {
        // Test that the basic API route exists
        $this->get('/api/test')
            ->assertStatus(200)
            ->assertJson(['message' => 'API is working']);
            
        // Test that the users API route exists
        $this->get('/api/users')
            ->assertStatus(401); // Should return unauthorized if not logged in
            
        // Test that the applications API route exists
        $this->get('/api/applications')
            ->assertStatus(401); // Should return unauthorized if not logged in
            
        // Test that the certificates API route exists
        $this->get('/api/certificates')
            ->assertStatus(401); // Should return unauthorized if not logged in
            
        // Test that the badges API route exists
        $this->get('/api/badges')
            ->assertStatus(401); // Should return unauthorized if not logged in
    }
}