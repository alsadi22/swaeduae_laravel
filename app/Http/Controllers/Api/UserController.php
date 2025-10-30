<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $role = $request->get('role');

        $users = User::with(['roles', 'organizations'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($role, function ($query, $role) {
                return $query->role($role);
            })
            ->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load(['roles', 'organizations', 'badges']);
        
        return response()->json($user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'phone' => 'string|max:20',
            'date_of_birth' => 'date',
            'gender' => 'in:male,female,other',
            'nationality' => 'string|max:100',
            'emirates_id' => 'string|max:50',
            'address' => 'string|max:255',
            'city' => 'string|max:100',
            'emirate' => 'string|max:100',
            'postal_code' => 'string|max:20',
            'emergency_contact_name' => 'string|max:255',
            'emergency_contact_phone' => 'string|max:20',
            'emergency_contact_relationship' => 'string|max:100',
            'skills' => 'array',
            'interests' => 'array',
            'bio' => 'string|max:1000',
            'languages' => 'array',
            'education_level' => 'string|max:100',
            'occupation' => 'string|max:100',
            'has_transportation' => 'boolean',
            'availability' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only([
            'name', 'email', 'phone', 'date_of_birth', 'gender', 'nationality',
            'emirates_id', 'address', 'city', 'emirate', 'postal_code',
            'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
            'skills', 'interests', 'bio', 'languages', 'education_level',
            'occupation', 'has_transportation', 'availability'
        ]));

        return response()->json($user);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Check if user can be deleted (e.g., no critical dependencies)
        if ($user->applications()->count() > 0 || $user->attendances()->count() > 0) {
            return response()->json(['message' => 'Cannot delete user with existing applications or attendance records'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Get user's applications.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function applications(User $user)
    {
        $applications = $user->applications()->with(['event.organization'])->paginate(15);
        
        return response()->json($applications);
    }

    /**
     * Get user's attendance records.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function attendance(User $user)
    {
        $attendance = $user->attendances()->with(['event'])->paginate(15);
        
        return response()->json($attendance);
    }

    /**
     * Get user's certificates.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function certificates(User $user)
    {
        $certificates = $user->certificates()->with(['event', 'organization'])->paginate(15);
        
        return response()->json($certificates);
    }

    /**
     * Get user's badges.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function badges(User $user)
    {
        $badges = $user->badges()->paginate(15);
        
        return response()->json($badges);
    }

    /**
     * Get user's statistics.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function statistics(User $user)
    {
        $stats = [
            'total_applications' => $user->applications()->count(),
            'approved_applications' => $user->applications()->where('status', 'approved')->count(),
            'total_attendance' => $user->attendances()->count(),
            'completed_attendance' => $user->attendances()->where('status', 'checked_out')->count(),
            'total_certificates' => $user->certificates()->count(),
            'total_badges' => $user->badges()->count(),
            'total_volunteer_hours' => $user->total_volunteer_hours ?? 0,
            'total_points' => $user->points ?? 0,
        ];

        return response()->json($stats);
    }

    /**
     * Update user's notification preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateNotificationPreferences(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'notification_preferences' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update(['notification_preferences' => $request->notification_preferences]);

        return response()->json($user);
    }

    /**
     * Update user's privacy settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updatePrivacySettings(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'privacy_settings' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update(['privacy_settings' => $request->privacy_settings]);

        return response()->json($user);
    }
}