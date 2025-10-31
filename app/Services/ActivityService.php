<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;

class ActivityService
{
    /**
     * Log an activity
     */
    public function log($userId, $action, $subjectType = null, $subjectId = null, $data = [], $visibility = 'public')
    {
        return Activity::create([
            'user_id' => $userId,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'data' => $data,
            'visibility' => $visibility,
            'description' => $this->generateDescription($action, $subjectType),
        ]);
    }

    /**
     * Get user's activity feed
     */
    public function getUserFeed($userId, $limit = 20, $offset = 0)
    {
        return Activity::where('visibility', 'public')
            ->where('user_id', $userId)
            ->recent()
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    /**
     * Get follower feed (for future followers feature)
     */
    public function getFollowingFeed($userId, $limit = 20, $offset = 0)
    {
        // Placeholder for when followers feature is implemented
        return Activity::public()
            ->recent()
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    /**
     * Generate description for activity
     */
    private function generateDescription($action, $subjectType)
    {
        $descriptions = [
            'joined_event' => 'Joined a volunteer event',
            'completed_event' => 'Completed a volunteer event',
            'earned_badge' => 'Earned a new badge',
            'milestone_reached' => 'Reached a volunteer milestone',
            'certificate_earned' => 'Earned a volunteer certificate',
            'volunteered_hours' => 'Logged volunteer hours',
        ];

        return $descriptions[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }
}
