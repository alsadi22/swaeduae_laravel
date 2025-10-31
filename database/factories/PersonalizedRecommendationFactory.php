<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalizedRecommendationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recommendation_type' => $this->faker->randomElement(['event', 'volunteer', 'organization']),
            'item_id' => $this->faker->numberBetween(1, 100),
            'item_type' => $this->faker->randomElement(['Event', 'Opportunity', 'Badge']),
            'reason' => $this->faker->randomElement(['collaborative_filtering', 'preference_based', 'behavior_based']),
            'ml_model_id' => null,
            'score' => $this->faker->randomFloat(2, 5, 10),
            'rank' => $this->faker->numberBetween(1, 10),
            'explanation' => ['reason' => 'User engagement pattern match'],
            'clicked' => false,
            'converted' => false,
        ];
    }
}
