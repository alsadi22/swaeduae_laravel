<?php

namespace App\Console\Commands;

use App\Services\BehaviorAnalysisService;
use App\Models\User;
use App\Models\EngagementMetric;
use Illuminate\Console\Command;

class CalculateEngagementMetricsCommand extends Command
{
    protected $signature = 'engagement:calculate-metrics';
    protected $description = 'Calculate and store engagement metrics for all users';

    public function __construct(private BehaviorAnalysisService $behaviorService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Calculating engagement metrics...');

        try {
            $users = User::where('status', 'active')->get();
            $calculated = 0;

            foreach ($users as $user) {
                $insights = $this->behaviorService->getUserInsights($user->id);
                
                EngagementMetric::updateOrCreate(
                    ['user_id' => $user->id, 'date' => today()],
                    [
                        'events_viewed' => $user->userBehaviors()->where('action_type', 'view')->count(),
                        'events_applied' => $user->applications()->count(),
                        'events_completed' => $user->applications()->where('status', 'completed')->count(),
                        'badges_earned' => $user->badges()->count(),
                        'messages_sent' => $user->messagesSent()->whereDate('created_at', today())->count(),
                        'hours_volunteered' => $user->applications()->sum('hours_participated'),
                        'login_count' => \DB::table('activity_log')
                            ->where('user_id', $user->id)
                            ->whereDate('created_at', today())
                            ->count(),
                        'daily_engagement_score' => $insights->engagement_level ?? 0,
                    ]
                );

                $calculated++;
            }

            $this->info("Calculated engagement metrics for {$calculated} users!");
        } catch (\Exception $e) {
            $this->error('Engagement calculation failed: ' . $e->getMessage());
        }
    }
}
