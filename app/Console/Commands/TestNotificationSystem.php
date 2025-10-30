<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Application;
use App\Models\Event;

class TestNotificationSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get a user to test with
        $user = User::first();
        
        if (!$user) {
            $this->error('No users found in the database');
            return 1;
        }
        
        // Create a fake application for testing
        $application = new Application([
            'event_id' => 1,
            'user_id' => $user->id,
            'status' => 'approved'
        ]);
        
        // Create a fake event for the application
        $application->setRelation('event', new Event(['title' => 'Test Event']));
        
        // Send notification
        $user->sendApplicationStatusNotification($application, 'approved');
        
        $this->info('Test notification sent to user: ' . $user->name);
        
        return 0;
    }
}