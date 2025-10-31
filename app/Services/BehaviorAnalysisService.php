<?php

namespace App\Services;

use App\Models\UserBehavior;
use App\Models\UserPreferenceProfile;
use App\Models\UserInsight;
use App\Models\EngagementMetric;
use App\Models\ChurnPrediction;

class BehaviorAnalysisService
{
    /**
     * Track user behavior
     */
    public function trackBehavior($userId, $actionType, $entityType, $entityId = null, $metadata = [])
    {
        $behavior = UserBehavior::create([
            'user_id' => $userId,
            'action_type' => $actionType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata,
            'engagement_score' => $this->calculateEngagementScore($actionType),
            'device_type' => request()->header('User-Agent'),
        ]);

        // Update preference profile
        $this->updatePreferenceProfile($userId);

        return $behavior;
    }

    /**
     * Calculate engagement score
     */
    private function calculateEngagementScore($actionType)
    {
        $scores = [
            'view' => 1,
            'click' => 2,
            'share' => 4,
            'apply' => 5,
            'complete' => 10,
        ];

        return $scores[$actionType] ?? 1;
    }

    /**
     * Update user preference profile
     */
    public function updatePreferenceProfile($userId)
    {
        $behaviors = UserBehavior::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $preferredTypes = [];
        $totalScore = 0;

        foreach ($behaviors as $behavior) {
            if ($behavior->entity_type === 'Event') {
                $preferredTypes[$behavior->entity_type] = ($preferredTypes[$behavior->entity_type] ?? 0) + $behavior->engagement_score;
                $totalScore += $behavior->engagement_score;
            }
        }

        arsort($preferredTypes);
        $preferredTypes = array_slice(array_keys($preferredTypes), 0, 5);

        UserPreferenceProfile::updateOrCreate(
            ['user_id' => $userId],
            [
                'preferred_event_types' => $preferredTypes,
                'average_engagement_score' => $totalScore > 0 ? $totalScore / count($behaviors) : 0,
                'total_interactions' => $behaviors->count(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Get user insights
     */
    public function getUserInsights($userId)
    {
        $behaviors = UserBehavior::where('user_id', $userId)->get();
        $engagementMetrics = EngagementMetric::where('user_id', $userId)->latest('date')->first();

        $engagementLevel = $this->calculateEngagementLevel($behaviors, $engagementMetrics);
        $volunteerType = $this->detectVolunteerType($behaviors);
        $ltv = $this->estimateLifetimeValue($userId);

        $insights = UserInsight::updateOrCreate(
            ['user_id' => $userId],
            [
                'engagement_level' => $engagementLevel,
                'activity_frequency' => $this->calculateActivityFrequency($behaviors),
                'volunteer_type' => $volunteerType,
                'estimated_lifetime_value' => $ltv,
                'behavior_patterns' => $this->extractBehaviorPatterns($behaviors),
                'risk_indicators' => $this->identifyRisks($userId),
                'opportunities' => $this->identifyOpportunities($userId),
                'last_analyzed_at' => now(),
            ]
        );

        return $insights;
    }

    /**
     * Calculate engagement level
     */
    private function calculateEngagementLevel($behaviors, $metrics)
    {
        $score = 0;

        // Based on behavior count
        if ($behaviors->count() > 100) $score += 2;
        elseif ($behaviors->count() > 50) $score += 1;

        // Based on daily engagement
        if ($metrics && $metrics->daily_engagement_score > 7) $score += 2;
        elseif ($metrics && $metrics->daily_engagement_score > 4) $score += 1;

        return min(5, $score);
    }

    /**
     * Calculate activity frequency
     */
    private function calculateActivityFrequency($behaviors)
    {
        $weekAgo = now()->subWeek();
        $recentBehaviors = $behaviors->where('created_at', '>=', $weekAgo)->count();

        return $recentBehaviors;
    }

    /**
     * Detect volunteer type
     */
    private function detectVolunteerType($behaviors)
    {
        $types = [];

        foreach ($behaviors as $behavior) {
            if ($behavior->action_type === 'apply') {
                $types['applicant'] = ($types['applicant'] ?? 0) + 1;
            } elseif ($behavior->action_type === 'complete') {
                $types['active_volunteer'] = ($types['active_volunteer'] ?? 0) + 1;
            } elseif ($behavior->action_type === 'share') {
                $types['advocate'] = ($types['advocate'] ?? 0) + 1;
            }
        }

        if (empty($types)) return 'observer';

        return array_key_first($types);
    }

    /**
     * Estimate lifetime value
     */
    private function estimateLifetimeValue($userId)
    {
        $behaviors = UserBehavior::where('user_id', $userId)
            ->where('engagement_score', '>', 2)
            ->sum('engagement_score');

        return (int) ($behaviors * 10);
    }

    /**
     * Extract behavior patterns
     */
    private function extractBehaviorPatterns($behaviors)
    {
        $patterns = [
            'most_active_day' => $this->getMostActiveDay($behaviors),
            'preferred_entity_type' => $this->getPreferredEntityType($behaviors),
            'action_distribution' => $this->getActionDistribution($behaviors),
        ];

        return $patterns;
    }

    /**
     * Get most active day
     */
    private function getMostActiveDay($behaviors)
    {
        $days = $behaviors->groupBy(function ($behavior) {
            return $behavior->created_at->format('N');
        })->map->count();

        return array_key_first($days->sortDesc()->toArray());
    }

    /**
     * Get preferred entity type
     */
    private function getPreferredEntityType($behaviors)
    {
        return $behaviors->groupBy('entity_type')->map->count()->sortDesc()->keys()->first();
    }

    /**
     * Get action distribution
     */
    private function getActionDistribution($behaviors)
    {
        return $behaviors->groupBy('action_type')->map->count()->toArray();
    }

    /**
     * Identify risks
     */
    private function identifyRisks($userId)
    {
        $risks = [];
        $behaviors = UserBehavior::where('user_id', $userId)->get();

        if ($behaviors->isEmpty()) {
            $risks[] = 'no_activity';
        }

        $weekAgo = now()->subWeek();
        $recentBehaviors = $behaviors->where('created_at', '>=', $weekAgo);

        if ($recentBehaviors->isEmpty()) {
            $risks[] = 'inactive';
        }

        return $risks;
    }

    /**
     * Identify opportunities
     */
    private function identifyOpportunities($userId)
    {
        $opportunities = [];

        $behaviors = UserBehavior::where('user_id', $userId)
            ->where('action_type', '!=', 'complete')
            ->get();

        if ($behaviors->count() > 5) {
            $opportunities[] = 'high_interest_low_conversion';
        }

        return $opportunities;
    }

    /**
     * Log daily engagement
     */
    public function logDailyEngagement($userId, $date)
    {
        $metrics = EngagementMetric::where('user_id', $userId)
            ->where('date', $date)
            ->first();

        if (!$metrics) {
            EngagementMetric::create([
                'user_id' => $userId,
                'date' => $date,
                'daily_engagement_score' => 0,
            ]);
        }
    }
}
