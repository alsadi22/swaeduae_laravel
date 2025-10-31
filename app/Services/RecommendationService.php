<?php

namespace App\Services;

use App\Models\UserBehavior;
use App\Models\UserPreferenceProfile;
use App\Models\PersonalizedRecommendation;
use App\Models\MlModel;

class RecommendationService
{
    /**
     * Get personalized recommendations for user
     */
    public function getRecommendations($userId, $limit = 10, $type = 'event')
    {
        $recommendations = PersonalizedRecommendation::where('user_id', $userId)
            ->where('recommendation_type', $type)
            ->orderBy('score', 'desc')
            ->limit($limit)
            ->get();

        return $recommendations;
    }

    /**
     * Generate recommendations using collaborative filtering
     */
    public function generateCollaborativeFilteringRecommendations($userId)
    {
        // Find similar users based on behavior
        $userBehaviors = UserBehavior::where('user_id', $userId)
            ->where('engagement_score', '>', 2)
            ->pluck('entity_id', 'entity_type')
            ->toArray();

        if (empty($userBehaviors)) {
            return [];
        }

        // Find users with similar interaction patterns
        $similarUsers = $this->findSimilarUsers($userId);

        $recommendations = [];

        foreach ($similarUsers as $similarUser) {
            $similarUserBehaviors = UserBehavior::where('user_id', $similarUser->user_id)
                ->where('engagement_score', '>', 2)
                ->where('entity_type', key($userBehaviors))
                ->get();

            foreach ($similarUserBehaviors as $behavior) {
                if (!in_array($behavior->entity_id, array_values($userBehaviors))) {
                    $score = $this->calculateCollaborativeScore($behavior, $userId);
                    $recommendations[] = [
                        'user_id' => $userId,
                        'item_id' => $behavior->entity_id,
                        'item_type' => $behavior->entity_type,
                        'score' => $score,
                        'reason' => 'collaborative_filtering',
                    ];
                }
            }
        }

        return $recommendations;
    }

    /**
     * Generate content-based recommendations
     */
    public function generateContentBasedRecommendations($userId)
    {
        $preferences = UserPreferenceProfile::where('user_id', $userId)->first();

        if (!$preferences) {
            return [];
        }

        $recommendations = [];

        // Based on preferred types
        if ($preferences->preferred_event_types) {
            foreach ($preferences->preferred_event_types as $type) {
                $score = 7.5;
                $recommendations[] = [
                    'user_id' => $userId,
                    'item_type' => $type,
                    'score' => $score,
                    'reason' => 'preference_based',
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Find similar users
     */
    private function findSimilarUsers($userId, $limit = 5)
    {
        $userBehaviors = UserBehavior::where('user_id', $userId)
            ->select('entity_id', 'entity_type', 'engagement_score')
            ->get()
            ->toArray();

        $otherUsers = UserBehavior::where('user_id', '!=', $userId)
            ->select('user_id', 'entity_id', 'entity_type', 'engagement_score')
            ->get();

        $similarities = [];

        foreach ($otherUsers->groupBy('user_id') as $userId => $behaviors) {
            $similarity = 0;
            
            foreach ($behaviors as $behavior) {
                foreach ($userBehaviors as $userBehavior) {
                    if ($behavior->entity_id === $userBehavior['entity_id'] &&
                        $behavior->entity_type === $userBehavior['entity_type']) {
                        $similarity += ($behavior->engagement_score * $userBehavior['engagement_score']) / 100;
                    }
                }
            }

            if ($similarity > 0) {
                $similarities[$userId] = $similarity;
            }
        }

        arsort($similarities);

        return array_slice($similarities, 0, $limit, true);
    }

    /**
     * Calculate collaborative score
     */
    private function calculateCollaborativeScore($behavior, $userId)
    {
        $baseScore = $behavior->engagement_score ?? 5;
        $popularityBoost = $this->getItemPopularity($behavior->entity_id) / 100;

        return min(10, $baseScore + $popularityBoost);
    }

    /**
     * Get item popularity
     */
    private function getItemPopularity($itemId)
    {
        return UserBehavior::where('entity_id', $itemId)
            ->count() / max(UserBehavior::count() / 100, 1);
    }

    /**
     * Save recommendation
     */
    public function saveRecommendation($userId, $itemId, $itemType, $score, $reason, $modelId = null, $recommendationType = 'event')
    {
        return PersonalizedRecommendation::create([
            'user_id' => $userId,
            'recommendation_type' => $recommendationType,
            'item_id' => $itemId,
            'item_type' => $itemType,
            'score' => $score,
            'reason' => $reason,
            'ml_model_id' => $modelId,
        ]);
    }

    /**
     * Track recommendation click
     */
    public function trackRecommendationClick($recommendationId)
    {
        $rec = PersonalizedRecommendation::findOrFail($recommendationId);
        $rec->update(['clicked' => true, 'clicked_at' => now()]);

        return $rec;
    }

    /**
     * Track recommendation conversion
     */
    public function trackRecommendationConversion($recommendationId)
    {
        $rec = PersonalizedRecommendation::findOrFail($recommendationId);
        $rec->update(['converted' => true, 'converted_at' => now()]);

        return $rec;
    }
}
