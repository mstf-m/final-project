<?php

namespace Database\Seeders;

use App\Models\ActivityRequest;
use Illuminate\Database\Seeder;

class ActivityRequestSeeder extends Seeder
{
    public function run()
    {
        // Create 5 activity requests
        ActivityRequest::factory(5)->create();
    }
}