<?php

namespace Database\Factories;

use App\Models\ActivityRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityRequestFactory extends Factory
{
    protected $model = ActivityRequest::class;

    public function definition()
    {
        return [
            'activity_id' => \App\Models\Activity::factory(),
            'user_id' => \App\Models\User::factory(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'canceled', 'expired']),
            'latitude' => $this->faker->latitude(32, 36),
            'longitude' => $this->faker->longitude(51, 53),
        ];
    }
}