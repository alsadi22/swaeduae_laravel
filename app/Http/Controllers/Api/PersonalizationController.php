<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use App\Services\PersonalizationService;
use App\Services\BehaviorAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalizationController extends Controller
{
    protected $recommendationService;
    protected $personalizationService;
    protected $behaviorService;

    public function __construct(
        RecommendationService $recommendationService,
        PersonalizationService $personalizationService,
        BehaviorAnalysisService $behaviorService
    ) {
        $this->recommendationService = $recommendationService;
        $this->personalizationService = $personalizationService;
        $this->behaviorService = $behaviorService;
    }

    /**
     * Get personalized recommendations
     */
    public function recommendations(Request $request)
    {
        $type = $request->get('type', 'event');
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getRecommendations(
            Auth::id(),
            $limit,
            $type
        );

        return response()->json($recommendations);
    }

    /**
     * Track behavior
     */
    public function trackBehavior(Request $request)
    {
        $validated = $request->validate([
            'action_type' => 'required|in:view,click,share,apply,complete',
            'entity_type' => 'required|string',
            'entity_id' => 'nullable|numeric',
            'metadata' => 'nullable|array',
        ]);

        $behavior = $this->behaviorService->trackBehavior(
            Auth::id(),
            $validated['action_type'],
            $validated['entity_type'],
            $validated['entity_id'] ?? null,
            $validated['metadata'] ?? []
        );

        return response()->json(['success' => true, 'behavior' => $behavior], 201);
    }

    /**
     * Get user insights
     */
    public function insights()
    {
        $insights = $this->behaviorService->getUserInsights(Auth::id());

        return response()->json($insights);
    }

    /**
     * Track recommendation click
     */
    public function trackRecommendationClick($recommendationId)
    {
        $rec = $this->recommendationService->trackRecommendationClick($recommendationId);

        return response()->json(['success' => true, 'recommendation' => $rec]);
    }

    /**
     * Track recommendation conversion
     */
    public function trackRecommendationConversion($recommendationId)
    {
        $rec = $this->recommendationService->trackRecommendationConversion($recommendationId);

        return response()->json(['success' => true, 'recommendation' => $rec]);
    }

    /**
     * Get A/B test results
     */
    public function getTestResults($testId)
    {
        $results = $this->personalizationService->getTestResults($testId);

        return response()->json($results);
    }

    /**
     * Get content personalization
     */
    public function getPersonalizedContent($contentType, $contentId)
    {
        $personalization = \App\Models\ContentPersonalization::where('user_id', Auth::id())
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->first();

        if (!$personalization) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($personalization);
    }
}
