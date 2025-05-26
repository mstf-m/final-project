<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create 10 Persian users
        User::factory(10)->create();
        
        // Create specific test user
        User::factory()->create([
            'phone_number' => '09123456789',
            'firstname' => 'امیر',
            'lastname' => 'محمدی'
        ]);
    }
}