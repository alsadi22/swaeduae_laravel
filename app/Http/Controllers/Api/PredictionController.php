<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PredictionController extends Controller
{
    protected $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * Get churn prediction
     */
    public function churnRisk()
    {
        $prediction = $this->predictionService->predictChurnRisk(Auth::id());

        return response()->json($prediction);
    }

    /**
     * Get user predictions
     */
    public function predictions(Request $request)
    {
        $type = $request->get('type');

        $predictions = $this->predictionService->getUserPredictions(Auth::id(), $type);

        return response()->json($predictions);
    }

    /**
     * Predict conversion
     */
    public function predictConversion(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|numeric',
        ]);

        $prediction = $this->predictionService->predictConversionProbability(
            Auth::id(),
            $validated['event_id']
        );

        return response()->json($prediction, 201);
    }

    /**
     * Get retention score
     */
    public function retentionScore()
    {
        $prediction = $this->predictionService->predictRetentionScore(Auth::id());

        return response()->json($prediction);
    }
}
