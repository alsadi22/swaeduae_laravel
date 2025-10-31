<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use App\Models\Organization;
use App\Models\SearchHistory;
use App\Models\SavedSearch;
use App\Models\SearchSuggestion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Perform full-text search across events
     */
    public function searchEvents($query, $filters = [], $limit = 20, $offset = 0)
    {
        $eventQuery = Event::query();

        // Full-text search
        if ($query) {
            $eventQuery->where(function (Builder $q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('organization', function (Builder $org) use ($query) {
                      $org->where('name', 'like', "%{$query}%");
                  });
            });
        }

        // Apply filters
        $eventQuery = $this->applyEventFilters($eventQuery, $filters);

        $total = $eventQuery->count();
        $events = $eventQuery->limit($limit)->offset($offset)->get();

        // Track suggestion
        if ($query) {
            SearchSuggestion::incrementPopularity($query, 'event');
        }

        return [
            'data' => $events,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    /**
     * Search organizations
     */
    public function searchOrganizations($query, $filters = [], $limit = 20, $offset = 0)
    {
        $orgQuery = Organization::query();

        if ($query) {
            $orgQuery->where(function (Builder $q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }

        // Only approved organizations
        $orgQuery->where('status', 'approved');

        $total = $orgQuery->count();
        $orgs = $orgQuery->limit($limit)->offset($offset)->get();

        if ($query) {
            SearchSuggestion::incrementPopularity($query, 'organization');
        }

        return [
            'data' => $orgs,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    /**
     * Search volunteers
     */
    public function searchVolunteers($query, $filters = [], $limit = 20, $offset = 0)
    {
        $userQuery = User::role('volunteer');

        if ($query) {
            $userQuery->where(function (Builder $q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('bio', 'like', "%{$query}%")
                  ->orWhere('unique_id', 'like', "%{$query}%");
            });
        }

        // Only verified users
        $userQuery->whereNotNull('email_verified_at');

        $total = $userQuery->count();
        $volunteers = $userQuery->limit($limit)->offset($offset)->get();

        if ($query) {
            SearchSuggestion::incrementPopularity($query, 'volunteer');
        }

        return [
            'data' => $volunteers,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    /**
     * Apply event filters to query
     */
    private function applyEventFilters(Builder $query, $filters = [])
    {
        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', 'published');
        }

        // Category filter
        if (isset($filters['categories']) && !empty($filters['categories'])) {
            $query->whereIn('category_id', $filters['categories']);
        }

        // Location/distance filter
        if (isset($filters['latitude']) && isset($filters['longitude']) && isset($filters['max_distance'])) {
            $query = $this->filterByDistance($query, $filters['latitude'], $filters['longitude'], $filters['max_distance']);
        }

        // Date range filter
        if (isset($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        // Organization filter
        if (isset($filters['organization_id'])) {
            $query->where('organization_id', $filters['organization_id']);
        }

        // Difficulty level
        if (isset($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }

    /**
     * Filter events by distance using Haversine formula
     */
    private function filterByDistance(Builder $query, $userLat, $userLon, $maxDistance)
    {
        $earthRadiusKm = 6371;

        return $query->selectRaw(
            "*, (
                {$earthRadiusKm} * acos(
                    cos(radians(?)) * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance",
            [$userLat, $userLon, $userLat]
        )
        ->having('distance', '<=', $maxDistance)
        ->orderBy('distance');
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function getSuggestions($query, $type = null, $limit = 10)
    {
        $suggestions = SearchSuggestion::byQuery($query);

        if ($type) {
            $suggestions->byType($type);
        }

        return $suggestions->popular()->limit($limit)->get();
    }

    /**
     * Log search history
     */
    public function logSearch($userId, $query, $searchType, $resultsCount, $ipAddress = null)
    {
        return SearchHistory::create([
            'user_id' => $userId,
            'query' => $query,
            'search_type' => $searchType,
            'results_count' => $resultsCount,
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Save a search
     */
    public function saveSearch($userId, $name, $searchType, $filters, $query = null, $notifyOnMatch = false)
    {
        return SavedSearch::create([
            'user_id' => $userId,
            'name' => $name,
            'search_type' => $searchType,
            'filters' => $filters,
            'query' => $query,
            'notify_on_match' => $notifyOnMatch,
        ]);
    }

    /**
     * Get saved searches for user
     */
    public function getSavedSearches($userId)
    {
        return SavedSearch::where('user_id', $userId)
            ->orderBy('last_used_at', 'desc')
            ->get();
    }

    /**
     * Delete saved search
     */
    public function deleteSavedSearch($searchId, $userId)
    {
        return SavedSearch::where('id', $searchId)
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Update last used timestamp
     */
    public function updateLastUsed($searchId)
    {
        return SavedSearch::findOrFail($searchId)
            ->update(['last_used_at' => now()]);
    }

    /**
     * Get search history for user
     */
    public function getSearchHistory($userId, $limit = 20)
    {
        return SearchHistory::forUser($userId)
            ->recent()
            ->limit($limit)
            ->get();
    }

    /**
     * Clear search history for user
     */
    public function clearSearchHistory($userId)
    {
        return SearchHistory::forUser($userId)->delete();
    }
}
