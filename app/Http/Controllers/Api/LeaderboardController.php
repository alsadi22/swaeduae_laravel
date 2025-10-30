<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Get the leaderboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $timeframe = $request->get('timeframe', 'all_time'); // all_time, monthly, weekly
        
        $leaderboard = $this->gamificationService->getLeaderboard($limit);
        
        // Add rank to each user
        $rankedLeaderboard = $leaderboard->map(function ($user, $index) {
            $user->rank = $index + 1;
            return $user;
        });
        
        // Get current user's position
        $currentUser = Auth::user();
        $userPosition = $this->gamificationService->getLeaderboardPosition($currentUser);
        
        return response()->json([
            'leaderboard' => $rankedLeaderboard,
            'user_position' => $userPosition,
            'user_points' => $currentUser->points,
            'user_hours' => $currentUser->total_volunteer_hours,
        ]);
    }

    /**
     * Get user's badge progress.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function progress(Request $request)
    {
        $user = Auth::user();
        $progress = $this->gamificationService->getAllProgress($user);
        
        return response()->json($progress);
    }

    /**
     * Get specific badge progress.
     *
     * @param  int  $badgeId
     * @return \Illuminate\Http\Response
     */
    public function badgeProgress($badgeId)
    {
        $user = Auth::user();
        $badge = \App\Models\Badge::findOrFail($badgeId);
        $progress = $this->gamificationService->getProgress($user, $badge);
        
        return response()->json($progress);
    }
}