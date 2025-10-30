<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    /**
     * Display a listing of badges.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $isActive = $request->get('is_active');
        $type = $request->get('type');

        $badges = Badge::withCount('users')
            ->when($isActive !== null, function ($query) use ($isActive) {
                return $query->where('is_active', $isActive);
            })
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('sort_order')
            ->paginate($perPage);

        return response()->json($badges);
    }

    /**
     * Display the specified badge.
     *
     * @param  \App\Models\Badge  $badge
     * @return \Illuminate\Http\Response
     */
    public function show(Badge $badge)
    {
        $badge->loadCount('users');
        
        return response()->json($badge);
    }

    /**
     * Store a newly created badge in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:badges',
            'description' => 'string|max:1000',
            'icon' => 'string|max:255',
            'color' => 'string|max:50',
            'type' => 'string|max:100',
            'criteria' => 'array',
            'points' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $badge = Badge::create($request->only([
            'name', 'slug', 'description', 'icon', 'color', 'type',
            'criteria', 'points', 'is_active', 'sort_order'
        ]));

        return response()->json($badge, 201);
    }

    /**
     * Update the specified badge in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Badge  $badge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Badge $badge)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'slug' => 'string|max:255|unique:badges,slug,' . $badge->id,
            'description' => 'string|max:1000',
            'icon' => 'string|max:255',
            'color' => 'string|max:50',
            'type' => 'string|max:100',
            'criteria' => 'array',
            'points' => 'integer|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $badge->update($request->only([
            'name', 'slug', 'description', 'icon', 'color', 'type',
            'criteria', 'points', 'is_active', 'sort_order'
        ]));

        return response()->json($badge);
    }

    /**
     * Remove the specified badge from storage.
     *
     * @param  \App\Models\Badge  $badge
     * @return \Illuminate\Http\Response
     */
    public function destroy(Badge $badge)
    {
        // Check if badge is assigned to any users
        if ($badge->users()->count() > 0) {
            return response()->json(['message' => 'Cannot delete badge that is assigned to users'], 400);
        }

        $badge->delete();

        return response()->json(['message' => 'Badge deleted successfully']);
    }

    /**
     * Get users who have earned the specified badge.
     *
     * @param  \App\Models\Badge  $badge
     * @return \Illuminate\Http\Response
     */
    public function users(Badge $badge, Request $request)
    {
        $perPage = $request->get('per_page', 15);
        
        $users = $badge->users()
            ->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Award a badge to a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Badge  $badge
     * @return \Illuminate\Http\Response
     */
    public function awardToUser(Request $request, Badge $badge)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'metadata' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($request->user_id);
        
        // Check if user already has this badge
        if ($badge->isEarnedBy($user)) {
            return response()->json(['message' => 'User already has this badge'], 400);
        }

        $result = $badge->awardTo($user, $request->metadata ?? []);

        if ($result) {
            return response()->json(['message' => 'Badge awarded successfully']);
        }

        return response()->json(['message' => 'Failed to award badge'], 400);
    }

    /**
     * Get badges earned by the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function myBadges(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $type = $request->get('type');

        $badges = Auth::user()->badges()
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->paginate($perPage);

        return response()->json($badges);
    }

    /**
     * Get all available badges that the user hasn't earned yet.
     *
     * @return \Illuminate\Http\Response
     */
    public function availableBadges(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $type = $request->get('type');

        // Get IDs of badges user has already earned
        $earnedBadgeIds = Auth::user()->badges()->pluck('badges.id');

        $badges = Badge::active()
            ->whereNotIn('id', $earnedBadgeIds)
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('sort_order')
            ->paginate($perPage);

        return response()->json($badges);
    }
}