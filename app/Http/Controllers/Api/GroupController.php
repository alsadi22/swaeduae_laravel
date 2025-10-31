<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VolunteerGroup;
use App\Models\GroupInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display all volunteer groups
     */
    public function index()
    {
        $groups = VolunteerGroup::where('status', 'active')
            ->paginate(12);

        return response()->json($groups);
    }

    /**
     * Show group details
     */
    public function show(VolunteerGroup $group)
    {
        $group->load(['members', 'creator']);

        return response()->json([
            'group' => $group,
            'is_member' => $group->members()->where('user_id', Auth::id())->exists(),
            'is_admin' => $group->created_by === Auth::id(),
        ]);
    }

    /**
     * Store new group
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:volunteer_groups',
            'description' => 'required|string|max:1000',
        ]);

        $group = VolunteerGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'created_by' => Auth::id(),
        ]);

        $group->members()->attach(Auth::id(), [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        $group->increment('member_count');

        return response()->json([
            'message' => 'Group created successfully',
            'group' => $group,
        ], 201);
    }

    /**
     * Join a group
     */
    public function join(VolunteerGroup $group)
    {
        if ($group->members()->where('user_id', Auth::id())->exists()) {
            return response()->json(['error' => 'Already a member'], 422);
        }

        $group->members()->attach(Auth::id(), [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $group->increment('member_count');

        return response()->json(['message' => 'Joined group successfully']);
    }

    /**
     * Leave group
     */
    public function leave(VolunteerGroup $group)
    {
        if ($group->created_by === Auth::id()) {
            return response()->json(['error' => 'Group creator cannot leave'], 422);
        }

        $group->members()->detach(Auth::id());
        $group->decrement('member_count');

        return response()->json(['message' => 'Left group successfully']);
    }

    /**
     * Invite user to group
     */
    public function invite(VolunteerGroup $group, Request $request)
    {
        if ($group->created_by !== Auth::id()) {
            return response()->json(['error' => 'Only group admin can invite'], 403);
        }

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->first();

        GroupInvitation::create([
            'group_id' => $group->id,
            'invited_by' => Auth::id(),
            'invited_user' => $user?->id,
            'email' => $validated['email'],
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json(['message' => 'Invitation sent successfully']);
    }
}
