<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Event;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Get user's favorites
     */
    public function index()
    {
        $type = request()->get('type', null);

        $query = Favorite::where('user_id', Auth::id());

        if ($type) {
            $query->where('favoritable_type', $type);
        }

        $favorites = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'favorites' => $favorites,
            'count' => count($favorites),
        ]);
    }

    /**
     * Add to favorites
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:Event,User,Organization',
            'id' => 'required|numeric',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if already favorited
        $exists = Favorite::where('user_id', Auth::id())
            ->where('favoritable_type', $validated['type'])
            ->where('favoritable_id', $validated['id'])
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Already in favorites'], 422);
        }

        $favorite = Favorite::create([
            'user_id' => Auth::id(),
            'favoritable_type' => $validated['type'],
            'favoritable_id' => $validated['id'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json($favorite, 201);
    }

    /**
     * Remove from favorites
     */
    public function remove($type, $id)
    {
        $deleted = Favorite::where('user_id', Auth::id())
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $id)
            ->delete();

        if ($deleted === 0) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json(['message' => 'Removed from favorites']);
    }

    /**
     * Check if favorited
     */
    public function check($type, $id)
    {
        $isFavorited = Favorite::where('user_id', Auth::id())
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $id)
            ->exists();

        return response()->json(['is_favorited' => $isFavorited]);
    }

    /**
     * Update notes
     */
    public function updateNotes(Request $request, $type, $id)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $favorite = Favorite::where('user_id', Auth::id())
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $id)
            ->first();

        if (!$favorite) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $favorite->update(['notes' => $validated['notes']]);

        return response()->json($favorite);
    }

    /**
     * Get favorite count by type
     */
    public function countByType()
    {
        $counts = Favorite::where('user_id', Auth::id())
            ->selectRaw('favoritable_type, COUNT(*) as count')
            ->groupBy('favoritable_type')
            ->pluck('count', 'favoritable_type');

        return response()->json($counts);
    }
}
