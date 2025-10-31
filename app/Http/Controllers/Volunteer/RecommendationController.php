<?php

namespace App\Http\Controllers\Volunteer;

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
     * Display recommendations
     */
    public function index()
    {
        $recommendations = $this->recommendationService->getRecommendations(Auth::id(), 20);

        return view('volunteer.recommendations.index', compact('recommendations'));
    }

    /**
     * Generate new recommendations
     */
    public function generate()
    {
        $this->recommendationService->generateRecommendations(Auth::id(), 15);

        return back()->with('success', 'Recommendations generated successfully!');
    }

    /**
     * Mark recommendation as clicked
     */
    public function markClicked(Recommendation $recommendation)
    {
        if ($recommendation->user_id !== Auth::id()) {
            abort(403);
        }

        $this->recommendationService->markAsClicked($recommendation->id);

        return response()->json(['success' => true]);
    }

    /**
     * Get recommendations API endpoint
     */
    public function getRecs()
    {
        $recommendations = $this->recommendationService->getRecommendations(Auth::id(), 10);

        return response()->json($recommendations);
    }
}
