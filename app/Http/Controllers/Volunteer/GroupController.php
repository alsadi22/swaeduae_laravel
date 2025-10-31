<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\VolunteerGroup;
use App\Models\GroupInvitation;
use App\Models\User;
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

        $userGroups = Auth::user()->groups ?? collect();

        return view('volunteer.groups.index', compact('groups', 'userGroups'));
    }

    /**
     * Show group details
     */
    public function show(VolunteerGroup $group)
    {
        $members = $group->members()->paginate(10);
        $isAdmin = Auth::user()->id === $group->created_by;
        $isMember = $group->members()->where('user_id', Auth::id())->exists();

        return view('volunteer.groups.show', compact('group', 'members', 'isAdmin', 'isMember'));
    }

    /**
     * Create a new group
     */
    public function create()
    {
        return view('volunteer.groups.create');
    }

    /**
     * Store new group
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:volunteer_groups',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
        ]);

        $group = VolunteerGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'created_by' => Auth::id(),
            'image' => $request->file('image')?->store('groups', 'public'),
        ]);

        // Add creator as admin member
        $group->members()->attach(Auth::id(), [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        $group->increment('member_count');

        return redirect()->route('groups.show', $group)->with('success', 'Group created successfully!');
    }

    /**
     * Join a group
     */
    public function join(VolunteerGroup $group)
    {
        if ($group->members()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You are already a member of this group');
        }

        $group->members()->attach(Auth::id(), [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $group->increment('member_count');

        return back()->with('success', 'You joined the group!');
    }

    /**
     * Leave group
     */
    public function leave(VolunteerGroup $group)
    {
        if ($group->created_by === Auth::id()) {
            return back()->with('error', 'Group creator cannot leave');
        }

        $group->members()->detach(Auth::id());
        $group->decrement('member_count');

        return back()->with('success', 'You left the group');
    }

    /**
     * Invite user to group
     */
    public function invite(VolunteerGroup $group, Request $request)
    {
        $this->authorizeAdmin($group);

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        GroupInvitation::create([
            'group_id' => $group->id,
            'invited_by' => Auth::id(),
            'invited_user' => $user?->id,
            'email' => $validated['email'],
            'expires_at' => now()->addDays(7),
        ]);

        return back()->with('success', 'Invitation sent!');
    }

    /**
     * Accept invitation
     */
    public function acceptInvitation(GroupInvitation $invitation)
    {
        if (!is_null($invitation->invited_user) && $invitation->invited_user !== Auth::id()) {
            abort(403);
        }

        $group = $invitation->group;

        if (!$group->members()->where('user_id', Auth::id())->exists()) {
            $group->members()->attach(Auth::id(), [
                'role' => 'member',
                'joined_at' => now(),
            ]);
            $group->increment('member_count');
        }

        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return redirect()->route('groups.show', $group)->with('success', 'Invitation accepted!');
    }

    /**
     * Authorize admin
     */
    private function authorizeAdmin(VolunteerGroup $group)
    {
        if ($group->created_by !== Auth::id()) {
            abort(403, 'Only group admin can perform this action');
        }
    }
}
