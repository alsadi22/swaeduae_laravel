<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SegmentationService;
use App\Services\PersonalizationService;
use App\Models\FeatureFlag;
use App\Models\AbTest;
use Illuminate\Http\Request;

class PersonalizationAdminController extends Controller
{
    protected $segmentationService;
    protected $personalizationService;

    public function __construct(
        SegmentationService $segmentationService,
        PersonalizationService $personalizationService
    ) {
        $this->segmentationService = $segmentationService;
        $this->personalizationService = $personalizationService;
    }

    /**
     * List feature flags
     */
    public function featureFlags()
    {
        $flags = FeatureFlag::paginate(20);

        return view('admin.personalization.feature-flags', compact('flags'));
    }

    /**
     * Create feature flag
     */
    public function createFeatureFlag(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:feature_flags',
            'description' => 'nullable|string',
            'target_group' => 'nullable|string',
            'rollout_percentage' => 'numeric|min:0|max:100',
        ]);

        $flag = $this->segmentationService->createFeatureFlag(
            $validated['name'],
            $validated['description'] ?? null,
            $validated['target_group'] ?? null,
            $validated['rollout_percentage'] ?? 100
        );

        return back()->with('success', 'Feature flag created');
    }

    /**
     * Toggle feature flag
     */
    public function toggleFeatureFlag(Request $request, $flagId)
    {
        $flag = FeatureFlag::findOrFail($flagId);

        if ($flag->is_enabled) {
            $this->segmentationService->disableFeatureFlag($flag->name);
        } else {
            $this->segmentationService->enableFeatureFlag($flag->name);
        }

        return back()->with('success', 'Feature flag updated');
    }

    /**
     * Update rollout
     */
    public function updateRollout(Request $request, $flagId)
    {
        $validated = $request->validate([
            'rollout_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $flag = FeatureFlag::findOrFail($flagId);

        $this->segmentationService->setRolloutPercentage($flag->name, $validated['rollout_percentage']);

        return back()->with('success', 'Rollout percentage updated');
    }

    /**
     * List A/B tests
     */
    public function abTests()
    {
        $tests = AbTest::paginate(20);

        return view('admin.personalization.ab-tests', compact('tests'));
    }

    /**
     * Create A/B test
     */
    public function createAbTest(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:ab_tests',
            'description' => 'nullable|string',
            'entity_type' => 'required|string',
            'variants' => 'required|array|min:2',
        ]);

        $test = $this->personalizationService->createAbTest(
            $validated['name'],
            $validated['entity_type'],
            $validated['variants'],
            $validated['description'] ?? null
        );

        return back()->with('success', 'A/B test created');
    }

    /**
     * View test details
     */
    public function testDetails($testId)
    {
        $test = AbTest::findOrFail($testId);
        $results = $this->personalizationService->getTestResults($testId);

        return view('admin.personalization.test-details', compact('test', 'results'));
    }

    /**
     * End A/B test
     */
    public function endTest(Request $request, $testId)
    {
        $this->personalizationService->endAbTest($testId);

        return back()->with('success', 'A/B test ended');
    }

    /**
     * Get winning variant
     */
    public function getWinner($testId)
    {
        $winner = $this->personalizationService->getWinningVariant($testId);

        return response()->json(['winner' => $winner]);
    }
}
