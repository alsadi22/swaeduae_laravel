<?php

namespace App\Services;

use App\Models\AbTest;
use App\Models\AbTestResult;
use App\Models\ContentPersonalization;

class PersonalizationService
{
    /**
     * Create A/B test
     */
    public function createAbTest($name, $entityType, $variants, $description = null, $entityId = null)
    {
        return AbTest::create([
            'name' => $name,
            'description' => $description,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'variants' => $variants,
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    /**
     * Assign variant to user
     */
    public function assignVariantToUser($testId, $userId)
    {
        $test = AbTest::findOrFail($testId);

        $variants = $test->variants;
        $variant = $variants[array_rand($variants)];

        AbTestResult::create([
            'ab_test_id' => $testId,
            'user_id' => $userId,
            'variant' => $variant,
        ]);

        return $variant;
    }

    /**
     * Track A/B test metrics
     */
    public function trackTestMetric($testId, $userId, $metric, $value = 1)
    {
        $result = AbTestResult::where('ab_test_id', $testId)
            ->where('user_id', $userId)
            ->first();

        if ($result) {
            if ($metric === 'impression') {
                $result->increment('impressions', $value);
            } elseif ($metric === 'conversion') {
                $result->increment('conversions', $value);
            }

            // Calculate conversion rate
            if ($result->impressions > 0) {
                $result->update(['conversion_rate' => ($result->conversions / $result->impressions) * 100]);
            }
        }
    }

    /**
     * Get test results
     */
    public function getTestResults($testId)
    {
        $results = AbTestResult::where('ab_test_id', $testId)->get();

        $grouped = $results->groupBy('variant')->map(function ($group) {
            $impressions = $group->sum('impressions');
            $conversions = $group->sum('conversions');

            return [
                'impressions' => $impressions,
                'conversions' => $conversions,
                'conversion_rate' => $impressions > 0 ? ($conversions / $impressions) * 100 : 0,
                'users' => $group->count(),
            ];
        });

        return $grouped;
    }

    /**
     * Personalize content
     */
    public function personalizeContent($userId, $contentType, $contentId, $variant, $personalizations = [])
    {
        return ContentPersonalization::updateOrCreate(
            ['user_id' => $userId, 'content_type' => $contentType, 'content_id' => $contentId],
            [
                'variant' => $variant,
                'personalized_title' => $personalizations['title'] ?? null,
                'personalized_description' => $personalizations['description'] ?? null,
                'personalized_metadata' => $personalizations['metadata'] ?? null,
            ]
        );
    }

    /**
     * Track content impression
     */
    public function trackContentImpression($personalizationId)
    {
        $content = ContentPersonalization::findOrFail($personalizationId);

        $content->update(['is_shown' => true]);
        $content->increment('impressions');

        return $content;
    }

    /**
     * Track content click
     */
    public function trackContentClick($personalizationId)
    {
        $content = ContentPersonalization::findOrFail($personalizationId);

        $content->increment('clicks');

        return $content;
    }

    /**
     * Track content conversion
     */
    public function trackContentConversion($personalizationId)
    {
        $content = ContentPersonalization::findOrFail($personalizationId);

        $content->increment('conversions');

        return $content;
    }

    /**
     * End A/B test
     */
    public function endAbTest($testId)
    {
        $test = AbTest::findOrFail($testId);

        $test->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);

        return $test;
    }

    /**
     * Recommend winning variant
     */
    public function getWinningVariant($testId)
    {
        $results = $this->getTestResults($testId);

        return $results->sortByDesc('conversion_rate')->keys()->first();
    }
}
