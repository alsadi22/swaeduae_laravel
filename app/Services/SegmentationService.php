<?php

namespace App\Services;

use App\Models\UserCohort;
use App\Models\CohortAssignment;
use App\Models\FeatureFlag;
use App\Models\UserBehavior;

class SegmentationService
{
    /**
     * Create user cohort
     */
    public function createCohort($name, $criteria, $description = null)
    {
        return UserCohort::create([
            'name' => $name,
            'description' => $description,
            'criteria' => $criteria,
            'status' => 'active',
        ]);
    }

    /**
     * Assign user to cohort
     */
    public function assignUserToCohort($userId, $cohortId)
    {
        $cohort = UserCohort::findOrFail($cohortId);

        $assignment = CohortAssignment::firstOrCreate(
            ['user_id' => $userId, 'cohort_id' => $cohortId],
            ['assigned_at' => now()]
        );

        // Update cohort user count
        $cohort->increment('user_count');

        return $assignment;
    }

    /**
     * Get user cohorts
     */
    public function getUserCohorts($userId)
    {
        return CohortAssignment::where('user_id', $userId)
            ->with('cohort')
            ->get()
            ->pluck('cohort');
    }

    /**
     * Segment users by behavior
     */
    public function segmentByBehavior()
    {
        $allUsers = \App\Models\User::pluck('id');

        foreach ($allUsers as $userId) {
            $behaviors = UserBehavior::where('user_id', $userId)->count();

            // Highly engaged
            if ($behaviors > 100) {
                $this->assignUserToCohort($userId, $this->getOrCreateCohort('Highly Engaged'));
            }
            // Moderately engaged
            elseif ($behaviors > 20) {
                $this->assignUserToCohort($userId, $this->getOrCreateCohort('Moderately Engaged'));
            }
            // Low engagement
            else {
                $this->assignUserToCohort($userId, $this->getOrCreateCohort('Low Engagement'));
            }
        }
    }

    /**
     * Get or create cohort
     */
    private function getOrCreateCohort($name)
    {
        $cohort = UserCohort::where('name', $name)->first();

        if (!$cohort) {
            $cohort = $this->createCohort($name, [], "Auto-generated cohort for {$name}");
        }

        return $cohort->id;
    }

    /**
     * Create feature flag
     */
    public function createFeatureFlag($name, $description = null, $targetGroup = null, $rolloutPercentage = 100)
    {
        return FeatureFlag::create([
            'name' => $name,
            'description' => $description,
            'is_enabled' => false,
            'target_group' => $targetGroup,
            'rollout_percentage' => $rolloutPercentage,
        ]);
    }

    /**
     * Enable feature flag
     */
    public function enableFeatureFlag($flagName)
    {
        $flag = FeatureFlag::where('name', $flagName)->firstOrFail();

        $flag->update(['is_enabled' => true]);

        return $flag;
    }

    /**
     * Disable feature flag
     */
    public function disableFeatureFlag($flagName)
    {
        $flag = FeatureFlag::where('name', $flagName)->firstOrFail();

        $flag->update(['is_enabled' => false]);

        return $flag;
    }

    /**
     * Check if feature is enabled for user
     */
    public function isFeatureEnabled($flagName, $userId = null)
    {
        $flag = FeatureFlag::where('name', $flagName)->first();

        if (!$flag || !$flag->is_enabled) {
            return false;
        }

        // Check rollout percentage
        if ($flag->rollout_percentage < 100) {
            $userHash = crc32($userId . $flagName) % 100;
            return $userHash < $flag->rollout_percentage;
        }

        return true;
    }

    /**
     * Set rollout percentage
     */
    public function setRolloutPercentage($flagName, $percentage)
    {
        $flag = FeatureFlag::where('name', $flagName)->firstOrFail();

        $flag->update(['rollout_percentage' => $percentage]);

        return $flag;
    }

    /**
     * Get feature flag
     */
    public function getFeatureFlag($flagName)
    {
        return FeatureFlag::where('name', $flagName)->first();
    }

    /**
     * Get all enabled features
     */
    public function getEnabledFeatures()
    {
        return FeatureFlag::where('is_enabled', true)->pluck('name');
    }
}
