<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * Display a listing of badges.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $badges = Badge::withCount('users')->paginate(15);
        
        return view('admin.badges.index', compact('badges'));
    }

    /**
     * Show the form for creating a new badge.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Store a newly created badge in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
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

        $badge = Badge::create($request->only([
            'name', 'slug', 'description', 'icon', 'color', 'type',
            'criteria', 'points', 'is_active', 'sort_order'
        ]));

        return redirect()->route('admin.badges.index')->with('success', 'Badge created successfully!');
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
        
        return view('admin.badges.show', compact('badge'));
    }

    /**
     * Show the form for editing the specified badge.
     *
     * @param  \App\Models\Badge  $badge
     * @return \Illuminate\Http\Response
     */
    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
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
        $request->validate([
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

        $badge->update($request->only([
            'name', 'slug', 'description', 'icon', 'color', 'type',
            'criteria', 'points', 'is_active', 'sort_order'
        ]));

        return redirect()->route('admin.badges.index')->with('success', 'Badge updated successfully!');
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
            return redirect()->back()->with('error', 'Cannot delete badge that is assigned to users');
        }

        $badge->delete();

        return redirect()->route('admin.badges.index')->with('success', 'Badge deleted successfully!');
    }

    /**
     * Award a badge to a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Badge  $badge
     * @return \Illuminate\Http\Response
     */
    public function award(Request $request, Badge $badge)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Check if user already has this badge
        if ($badge->isEarnedBy($user)) {
            return redirect()->back()->with('error', 'User already has this badge');
        }

        $result = $badge->awardTo($user);

        if ($result) {
            return redirect()->back()->with('success', 'Badge awarded successfully!');
        }

        return redirect()->back()->with('error', 'Failed to award badge');
    }
}