<?php

namespace App\Services;

use App\Models\ChurnPrediction;
use App\Models\Prediction;
use App\Models\UserBehavior;
use App\Models\EngagementMetric;

class PredictionService
{
    /**
     * Predict churn risk
     */
    public function predictChurnRisk($userId)
    {
        $weekAgo = now()->subWeek();
        $recentBehaviors = UserBehavior::where('user_id', $userId)
            ->where('created_at', '>=', $weekAgo)
            ->count();

        $lastMonthBehaviors = UserBehavior::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonth())
            ->count();

        $riskFactors = [];
        $churnProbability = 0;

        // Low recent activity
        if ($recentBehaviors === 0) {
            $riskFactors[] = 'no_recent_activity';
            $churnProbability += 30;
        } elseif ($recentBehaviors < 3) {
            $riskFactors[] = 'low_recent_activity';
            $churnProbability += 15;
        }

        // Declining trend
        if ($lastMonthBehaviors > 0 && $recentBehaviors < $lastMonthBehaviors / 4) {
            $riskFactors[] = 'declining_activity';
            $churnProbability += 20;
        }

        // No completed events
        $completions = UserBehavior::where('user_id', $userId)
            ->where('action_type', 'complete')
            ->count();

        if ($completions === 0) {
            $riskFactors[] = 'no_completions';
            $churnProbability += 10;
        }

        $riskLevel = $this->calculateRiskLevel($churnProbability);

        $prediction = ChurnPrediction::updateOrCreate(
            ['user_id' => $userId],
            [
                'churn_probability' => min(100, $churnProbability),
                'risk_factors' => $riskFactors,
                'risk_level' => $riskLevel,
                'recommended_action' => $this->getRecommendedAction($riskLevel),
            ]
        );

        return $prediction;
    }

    /**
     * Calculate risk level
     */
    private function calculateRiskLevel($probability)
    {
        if ($probability >= 60) return 'high';
        if ($probability >= 30) return 'medium';
        return 'low';
    }

    /**
     * Get recommended action
     */
    private function getRecommendedAction($riskLevel)
    {
        $actions = [
            'high' => 'Send re-engagement email and recommend events',
            'medium' => 'Send periodic reminders about new events',
            'low' => 'Continue normal engagement',
        ];

        return $actions[$riskLevel] ?? 'Monitor user';
    }

    /**
     * Predict conversion probability
     */
    public function predictConversionProbability($userId, $eventId)
    {
        $behaviors = UserBehavior::where('user_id', $userId)->get();
        $eventApplications = $behaviors->where('action_type', 'apply')->count();

        $probability = min(100, (($eventApplications + 1) / 5) * 100);

        return Prediction::create([
            'user_id' => $userId,
            'prediction_type' => 'conversion_probability',
            'predicted_value' => $probability,
            'confidence' => 0.75,
            'factors' => [
                'past_applications' => $eventApplications,
                'engagement_history' => $behaviors->count(),
            ],
            'prediction_date' => now(),
        ]);
    }

    /**
     * Predict retention score
     */
    public function predictRetentionScore($userId)
    {
        $behaviors = UserBehavior::where('user_id', $userId)->get();
        $completions = $behaviors->where('action_type', 'complete')->count();
        $recentActivity = $behaviors->where('created_at', '>=', now()->subMonth())->count();

        $baseScore = 50;
        $completionBoost = $completions * 5;
        $activityBoost = min($recentActivity * 2, 30);

        $retentionScore = min(100, $baseScore + $completionBoost + $activityBoost);

        return Prediction::create([
            'user_id' => $userId,
            'prediction_type' => 'retention_score',
            'predicted_value' => $retentionScore,
            'confidence' => 0.80,
            'factors' => [
                'completions' => $completions,
                'recent_activity' => $recentActivity,
            ],
            'prediction_date' => now(),
        ]);
    }

    /**
     * Get user predictions
     */
    public function getUserPredictions($userId, $type = null)
    {
        $query = Prediction::where('user_id', $userId);

        if ($type) {
            $query->where('prediction_type', $type);
        }

        return $query->orderBy('prediction_date', 'desc')->get();
    }

    /**
     * Record actual outcome
     */
    public function recordOutcome($predictionId, $actualResult)
    {
        $prediction = Prediction::findOrFail($predictionId);

        $prediction->update([
            'actual_result' => $actualResult,
            'result_date' => now(),
        ]);

        return $prediction;
    }
}
