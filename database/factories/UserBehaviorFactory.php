<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserBehaviorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action_type' => $this->faker->randomElement(['view', 'click', 'share', 'apply', 'complete']),
            'entity_type' => $this->faker->randomElement(['Event', 'Badge', 'Certificate', 'User']),
            'entity_id' => $this->faker->numberBetween(1, 100),
            'metadata' => ['duration' => $this->faker->numberBetween(10, 300)],
            'engagement_score' => $this->faker->randomFloat(2, 1, 10),
            'device_type' => $this->faker->randomElement(['desktop', 'mobile', 'tablet']),
            'duration_seconds' => $this->faker->numberBetween(30, 600),
            'referrer' => $this->faker->url(),
        ];
    }
}
