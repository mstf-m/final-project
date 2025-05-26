<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $faker = \Faker\Factory::create('fa_IR');
        
        return [
            'firstname' => $faker->firstName(),
            'lastname' => $faker->lastName(),
            'phone_number' => '09' . $this->faker->unique()->numerify('#########'),
            'password_hash' => bcrypt('12345678'), // Common simple password
            'avatar_url' => $this->faker->optional()->imageUrl(200, 200, 'people'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'birthday' => $this->faker->dateTimeBetween('-50 years', '-18 years'),
            'bio' => $faker->realText(60),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now')
        ];
    }
}