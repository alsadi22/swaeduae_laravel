<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    protected $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Get user's activity feed
     */
    public function feed()
    {
        $limit = request()->get('limit', 20);
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $limit;

        $activities = $this->activityService->getUserFeed(Auth::id(), $limit, $offset);

        return response()->json([
            'activities' => $activities,
            'current_page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Get user profile feed
     */
    public function userFeed($userId)
    {
        $user = User::findOrFail($userId);

        $limit = request()->get('limit', 15);
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $limit;

        $activities = $this->activityService->getUserFeed($userId, $limit, $offset);

        return response()->json([
            'user' => $user->only(['id', 'name', 'avatar', 'unique_id']),
            'activities' => $activities,
            'current_page' => $page,
            'limit' => $limit,
        ]);
    }
}
