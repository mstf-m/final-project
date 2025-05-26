<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition()
    {
        $faker = \Faker\Factory::create('fa_IR');
        
        return [
            'creator_id' => \App\Models\User::factory(),
            'title' => $faker->randomElement([
                'سفر به شمال',
                'کوهنوردی در دماوند',
                'دورهمی برنامه‌نویسان',
                'کارگاه لاراول'
            ]),
            'description' => $faker->realText(200),
            'start_time' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'end_time' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'max_participants' => $this->faker->numberBetween(2, 10),
            'category_id' => \App\Models\Category::factory(),
            'latitude' => $this->faker->latitude(32, 36),
            'longitude' => $this->faker->longitude(51, 53),
        ];
    }
}