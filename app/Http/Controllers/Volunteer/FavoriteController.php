<?php

namespace App\Http\Controllers\Volunteer;

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
        $favorites = Favorite::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('volunteer.favorites.index', compact('favorites'));
    }

    /**
     * Add item to favorites
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:Event,User,Organization',
            'id' => 'required|numeric',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify item exists
        $model = $this->getModel($validated['type']);
        $item = $model::findOrFail($validated['id']);

        // Check if already favorited
        $exists = Favorite::where('user_id', Auth::id())
            ->where('favoritable_type', $validated['type'])
            ->where('favoritable_id', $validated['id'])
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Already in favorites'], 422);
        }

        // Add to favorites
        Favorite::create([
            'user_id' => Auth::id(),
            'favoritable_type' => $validated['type'],
            'favoritable_id' => $validated['id'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json(['message' => 'Added to favorites']);
    }

    /**
     * Remove from favorites
     */
    public function remove($type, $id)
    {
        Favorite::where('user_id', Auth::id())
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $id)
            ->delete();

        return back()->with('success', 'Removed from favorites');
    }

    /**
     * Check if item is favorited
     */
    public function isFavorited($type, $id)
    {
        $isFavorited = Favorite::where('user_id', Auth::id())
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $id)
            ->exists();

        return response()->json(['is_favorited' => $isFavorited]);
    }

    /**
     * Update notes for favorite
     */
    public function updateNotes(Request $request, $type, $id)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        Favorite::where('user_id', Auth::id())
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $id)
            ->update(['notes' => $validated['notes']]);

        return response()->json(['message' => 'Notes updated']);
    }

    /**
     * Get model class by type
     */
    private function getModel($type)
    {
        $models = [
            'Event' => Event::class,
            'User' => User::class,
            'Organization' => Organization::class,
        ];

        return $models[$type] ?? null;
    }
}
