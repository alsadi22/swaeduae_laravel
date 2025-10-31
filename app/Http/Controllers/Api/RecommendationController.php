<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use App\Models\Recommendation;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get recommendations
     */
    public function index()
    {
        $limit = request()->get('limit', 10);
        $recommendations = $this->recommendationService->getRecommendations(Auth::id(), $limit);

        return response()->json([
            'recommendations' => $recommendations,
            'count' => count($recommendations),
        ]);
    }

    /**
     * Generate recommendations
     */
    public function generate()
    {
        $limit = request()->get('limit', 15);
        $recommendations = $this->recommendationService->generateRecommendations(Auth::id(), $limit);

        return response()->json([
            'message' => 'Recommendations generated',
            'recommendations' => $recommendations,
            'count' => count($recommendations),
        ]);
    }

    /**
     * Mark as clicked
     */
    public function click($recommendationId)
    {
        $recommendation = Recommendation::findOrFail($recommendationId);

        if ($recommendation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->recommendationService->markAsClicked($recommendationId);

        return response()->json(['message' => 'Recommendation clicked']);
    }
}
