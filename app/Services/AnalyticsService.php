<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\EventTracking;
use App\Models\SessionAnalytic;
use App\Models\GoalConversion;
use App\Models\Goal;

class AnalyticsService
{
    /**
     * Track analytics event
     */
    public function trackEvent($userId, $eventType, $eventCategory, $data = [])
    {
        $event = AnalyticsEvent::create([
            'user_id' => $userId,
            'event_type' => $eventType,
            'event_category' => $eventCategory,
            'event_data' => $data,
            'page_url' => request()->url(),
            'referrer' => request()->referrer(),
            'device_type' => $this->getDeviceType(),
            'ip_address' => request()->ip(),
            'country' => geoip(request()->ip())->country ?? null,
            'city' => geoip(request()->ip())->city ?? null,
        ]);

        // Check if goal is met
        $this->checkGoals($userId, $eventType, $data);

        return $event;
    }

    /**
     * Get device type
     */
    private function getDeviceType()
    {
        $userAgent = request()->header('User-Agent');

        if (preg_match('/mobile|android|iphone|ipad/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Start session
     */
    public function startSession($userId, $sessionId = null)
    {
        $sessionId = $sessionId ?? uniqid('session_');

        $session = SessionAnalytic::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'session_start' => now(),
            'entry_page' => request()->url(),
            'device_type' => $this->getDeviceType(),
        ]);

        return $session;
    }

    /**
     * End session
     */
    public function endSession($sessionId)
    {
        $session = SessionAnalytic::where('session_id', $sessionId)->firstOrFail();

        $duration = $session->session_start->diffInSeconds(now());

        $session->update([
            'session_end' => now(),
            'duration_seconds' => $duration,
            'exit_page' => request()->url(),
        ]);

        return $session;
    }

    /**
     * Track custom event
     */
    public function trackCustomEvent($userId, $eventName, $properties = [], $context = [])
    {
        $event = EventTracking::create([
            'user_id' => $userId,
            'event_name' => $eventName,
            'properties' => $properties,
            'context' => $context,
        ]);

        return $event;
    }

    /**
     * Check if goals are met
     */
    private function checkGoals($userId, $eventType, $data)
    {
        $goals = Goal::where('is_active', true)->get();

        foreach ($goals as $goal) {
            if ($goal->goal_type === 'event' && $goal->conditions['event_type'] === $eventType) {
                GoalConversion::create([
                    'goal_id' => $goal->id,
                    'user_id' => $userId,
                    'conversion_data' => $data,
                ]);
            }
        }
    }

    /**
     * Get events for date range
     */
    public function getEventsByDateRange($startDate, $endDate, $filters = [])
    {
        $query = AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate]);

        if (isset($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        if (isset($filters['event_category'])) {
            $query->where('event_category', $filters['event_category']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->get();
    }

    /**
     * Get session analytics for user
     */
    public function getUserSessions($userId, $limit = 20)
    {
        return SessionAnalytic::where('user_id', $userId)
            ->orderBy('session_start', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular pages
     */
    public function getPopularPages($startDate, $endDate, $limit = 10)
    {
        return AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate])
            ->where('event_type', 'page_view')
            ->groupBy('page_url')
            ->selectRaw('page_url, COUNT(*) as views')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    /**
     * Get conversion funnel
     */
    public function getConversionFunnel($startDate, $endDate)
    {
        $goals = Goal::where('is_active', true)->get();
        $funnel = [];

        foreach ($goals as $goal) {
            $conversions = GoalConversion::where('goal_id', $goal->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $funnel[] = [
                'goal' => $goal->name,
                'conversions' => $conversions,
            ];
        }

        return $funnel;
    }

    /**
     * Get geographic distribution
     */
    public function getGeographicDistribution($startDate, $endDate)
    {
        return AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('country')
            ->groupBy('country', 'city')
            ->selectRaw('country, city, COUNT(*) as count')
            ->orderByDesc('count')
            ->get();
    }
}
