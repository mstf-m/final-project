<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Create 100 notifications with Persian messages
        Notification::factory(10)->create();
    }
}