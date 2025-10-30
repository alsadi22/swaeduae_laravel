<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use App\Models\BadgeProgress;

class GamificationService
{
    /**
     * Award a badge to a user.
     */
    public function awardBadge(User $user, Badge $badge, array $metadata = []): bool
    {
        return $badge->awardTo($user, $metadata);
    }

    /**
     * Update user progress toward a badge.
     */
    public function updateProgress(User $user, Badge $badge, int $increment, array $metadata = []): bool
    {
        $progress = BadgeProgress::firstOrCreate(
            ['user_id' => $user->id, 'badge_id' => $badge->id],
            ['progress' => 0, 'metadata' => []]
        );

        $progress->metadata = array_merge($progress->metadata ?? [], $metadata);
        $progress->save();

        return $progress->updateProgress($increment);
    }

    /**
     * Get user's progress toward a specific badge.
     */
    public function getProgress(User $user, Badge $badge): ?BadgeProgress
    {
        return BadgeProgress::where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->first();
    }

    /**
     * Get all of user's badge progress.
     */
    public function getAllProgress(User $user)
    {
        return BadgeProgress::where('user_id', $user->id)
            ->with(['badge'])
            ->get();
    }

    /**
     * Check if user has earned a specific badge.
     */
    public function hasBadge(User $user, Badge $badge): bool
    {
        return $badge->isEarnedBy($user);
    }

    /**
     * Get user's leaderboard position.
     */
    public function getLeaderboardPosition(User $user): int
    {
        $users = User::orderBy('points', 'desc')
            ->orderBy('total_volunteer_hours', 'desc')
            ->get();

        $position = 1;
        foreach ($users as $u) {
            if ($u->id === $user->id) {
                return $position;
            }
            $position++;
        }

        return $position;
    }

    /**
     * Get top users for leaderboard.
     */
    public function getLeaderboard(int $limit = 10)
    {
        return User::orderBy('points', 'desc')
            ->orderBy('total_volunteer_hours', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Award badges based on user activity.
     */
    public function awardActivityBadges(User $user): void
    {
        // First event badge
        if ($user->applications()->where('status', 'approved')->count() >= 1) {
            $badge = Badge::where('slug', 'first-event')->first();
            if ($badge && !$this->hasBadge($user, $badge)) {
                $this->awardBadge($user, $badge, ['reason' => 'First approved event']);
            }
        }

        // Five events badge
        if ($user->applications()->where('status', 'approved')->count() >= 5) {
            $badge = Badge::where('slug', 'five-events')->first();
            if ($badge && !$this->hasBadge($user, $badge)) {
                $this->awardBadge($user, $badge, ['reason' => 'Five approved events']);
            }
        }

        // Ten hours badge
        if ($user->total_volunteer_hours >= 10) {
            $badge = Badge::where('slug', 'ten-hours')->first();
            if ($badge && !$this->hasBadge($user, $badge)) {
                $this->awardBadge($user, $badge, ['reason' => 'Ten volunteer hours']);
            }
        }

        // Fifty hours badge
        if ($user->total_volunteer_hours >= 50) {
            $badge = Badge::where('slug', 'fifty-hours')->first();
            if ($badge && !$this->hasBadge($user, $badge)) {
                $this->awardBadge($user, $badge, ['reason' => 'Fifty volunteer hours']);
            }
        }

        // Certificate badge
        if ($user->certificates()->count() >= 1) {
            $badge = Badge::where('slug', 'first-certificate')->first();
            if ($badge && !$this->hasBadge($user, $badge)) {
                $this->awardBadge($user, $badge, ['reason' => 'First certificate earned']);
            }
        }
    }
}