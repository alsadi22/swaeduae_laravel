<?php

namespace App\Http\Controllers\Volunteer;

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
     * Display activity feed
     */
    public function feed()
    {
        $page = request()->get('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $activities = $this->activityService->getUserFeed(Auth::id(), $limit, $offset);

        return view('volunteer.activity.feed', compact('activities'));
    }

    /**
     * Get user profile feed (public view)
     */
    public function userFeed($userId)
    {
        $user = User::findOrFail($userId);
        
        $page = request()->get('page', 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $activities = $this->activityService->getUserFeed($userId, $limit, $offset);

        return view('volunteer.activity.user-feed', compact('user', 'activities'));
    }
}
