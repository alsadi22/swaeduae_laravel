<?php

namespace Database\Factories;

use App\Models\BadgeProgress;
use App\Models\User;
use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeProgressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BadgeProgress::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'badge_id' => Badge::factory(),
            'progress' => $this->faker->numberBetween(0, 100),
            'metadata' => [],
        ];
    }
}