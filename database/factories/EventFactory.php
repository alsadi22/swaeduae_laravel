<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'title' => $this->faker->sentence(4),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(),
            'requirements' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement(['education', 'health', 'environment', 'social', 'sports', 'culture']),
            'tags' => json_encode([$this->faker->word(), $this->faker->word()]),
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'location' => $this->faker->streetAddress(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'emirate' => $this->faker->randomElement(['abu-dhabi', 'dubai', 'sharjah', 'ajman', 'umm-al-quwain', 'ras-al-khaimah', 'fujairah']),
            'latitude' => 25.2048,
            'longitude' => 55.2708,
            'max_volunteers' => $this->faker->numberBetween(10, 100),
            'min_age' => $this->faker->numberBetween(16, 21),
            'max_age' => null,
            'skills_required' => null,
            'volunteer_hours' => $this->faker->randomFloat(2, 1, 8),
            'image' => null,
            'gallery' => null,
            'status' => 'published',
            'rejection_reason' => null,
            'is_featured' => false,
            'requires_application' => true,
            'application_deadline' => now()->addDays(1),
            'contact_person' => $this->faker->name(),
            'contact_email' => $this->faker->email(),
            'contact_phone' => $this->faker->phoneNumber(),
            'custom_fields' => null,
        ];
    }
}

