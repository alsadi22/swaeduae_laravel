<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'emirate' => $this->faker->randomElement(['abu-dhabi', 'dubai', 'sharjah', 'ajman', 'umm-al-quwain', 'ras-al-khaimah', 'fujairah']),
            'postal_code' => $this->faker->postcode(),
            'website' => $this->faker->url(),
            'logo' => null,
            'documents' => null,
            'status' => 'approved',
            'rejection_reason' => null,
            'is_verified' => true,
            'mission_statement' => $this->faker->sentence(),
            'founded_year' => $this->faker->year(),
            'organization_type' => $this->faker->randomElement(['ngo', 'charity', 'government', 'educational', 'corporate', 'community']),
            'focus_areas' => json_encode([$this->faker->randomElement(['education', 'health', 'environment', 'social'])]),
        ];
    }
}

