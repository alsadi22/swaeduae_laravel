<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use App\Models\NotificationPreference;
use Illuminate\Console\Command;

class SendDigestNotificationsCommand extends Command
{
    protected $signature = 'notifications:send-digests';
    protected $description = 'Send digest notifications to users based on preferences';

    public function __construct(private NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Sending digest notifications...');

        try {
            $preferences = NotificationPreference::where('digest_notifications', true)
                ->where('digest_frequency', 'daily')
                ->get();

            foreach ($preferences as $pref) {
                $user = $pref->user;
                $summary = $this->buildDigestSummary($user);

                $this->notificationService->sendEmail(
                    $user->id,
                    $user->email,
                    'daily_digest',
                    [
                        'user_name' => $user->first_name,
                        'summary' => $summary,
                    ]
                );

                $this->info("Digest sent to: {$user->email}");
            }

            $this->info('Digest notifications sent successfully!');
        } catch (\Exception $e) {
            $this->error('Digest notification failed: ' . $e->getMessage());
        }
    }

    private function buildDigestSummary($user)
    {
        return [
            'event_applications' => $user->applications()->count(),
            'new_recommendations' => $user->personalizedRecommendations()->whereDate('created_at', today())->count(),
            'messages_count' => $user->messages()->whereDate('created_at', today())->count(),
            'badges_earned' => $user->badges()->whereDate('created_at', today())->count(),
        ];
    }
}
