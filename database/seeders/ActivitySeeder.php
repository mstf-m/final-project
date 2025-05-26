<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        // Create 20 activities with Persian data
        Activity::factory(20)->create();
        
        // Create specific test activity
        Activity::factory()->create([
            'title' => 'سفر به شمال',
            'description' => 'یک سفر گروهی به استان مازندران'
        ]);
    }
}