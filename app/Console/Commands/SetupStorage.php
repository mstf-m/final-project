<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SetupStorage extends Command
{
    protected $signature = 'storage:setup';
    protected $description = 'Set up storage directories and permissions';

    public function handle()
    {
        $this->info('Setting up storage directories...');

        // Create the activity-images directory
        if (!Storage::disk('public')->exists('activity-images')) {
            Storage::disk('public')->makeDirectory('activity-images');
            $this->info('Created activity-images directory');
        }

        // Create symbolic link if it doesn't exist
        if (!file_exists(public_path('storage'))) {
            $this->call('storage:link');
            $this->info('Created storage symbolic link');
        }

        $this->info('Storage setup completed successfully!');
    }
} 