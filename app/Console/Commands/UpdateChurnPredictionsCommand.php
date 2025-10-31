<?php

namespace App\Console\Commands;

use App\Services\PredictionService;
use App\Models\User;
use App\Models\ChurnPrediction;
use Illuminate\Console\Command;

class UpdateChurnPredictionsCommand extends Command
{
    protected $signature = 'predictions:update-churn';
    protected $description = 'Update churn risk predictions for all active users';

    public function __construct(private PredictionService $predictionService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Updating churn predictions...');

        try {
            $users = User::where('status', 'active')->get();
            $updated = 0;

            foreach ($users as $user) {
                $riskProbability = $this->predictionService->predictChurnRisk($user->id);
                $riskLevel = $this->predictionService->calculateRiskLevel($riskProbability);

                ChurnPrediction::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'churn_probability' => $riskProbability,
                        'risk_level' => $riskLevel,
                        'last_calculated_at' => now(),
                    ]
                );

                $updated++;
            }

            $this->info("Updated {$updated} churn predictions!");
        } catch (\Exception $e) {
            $this->error('Churn prediction update failed: ' . $e->getMessage());
        }
    }
}
