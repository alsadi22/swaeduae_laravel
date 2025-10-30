<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Badge;
use App\Models\BadgeProgress;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get all badges with user's progress
        $badges = Badge::with(['progress' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();
        
        return view('volunteer.badges.index', compact('badges'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Badge $badge)
    {
        $user = auth()->user();
        
        // Get badge with user's progress
        $badge->load(['progress' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }]);
        
        return view('volunteer.badges.show', compact('badge'));
    }
}