<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Search events
     */
    public function events(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string',
            'category' => 'nullable|array',
            'location' => 'nullable|string',
            'distance' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'sort_by' => 'nullable|string',
            'sort_order' => 'nullable|in:asc,desc',
            'limit' => 'nullable|numeric|min:1|max:100',
            'offset' => 'nullable|numeric|min:0',
        ]);

        $limit = $validated['limit'] ?? 20;
        $offset = $validated['offset'] ?? 0;
        $filters = array_filter($validated, function ($key) {
            return in_array($key, ['category', 'location', 'distance', 'start_date', 'end_date', 'sort_by', 'sort_order']);
        }, ARRAY_FILTER_USE_KEY);

        $results = $this->searchService->searchEvents($validated['q'] ?? '', $filters, $limit, $offset);

        return response()->json($results);
    }

    /**
     * Search organizations
     */
    public function organizations(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string',
            'limit' => 'nullable|numeric|min:1|max:100',
            'offset' => 'nullable|numeric|min:0',
        ]);

        $limit = $validated['limit'] ?? 20;
        $offset = $validated['offset'] ?? 0;

        $results = $this->searchService->searchOrganizations($validated['q'] ?? '', [], $limit, $offset);

        return response()->json($results);
    }

    /**
     * Search volunteers
     */
    public function volunteers(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string',
            'limit' => 'nullable|numeric|min:1|max:100',
            'offset' => 'nullable|numeric|min:0',
        ]);

        $limit = $validated['limit'] ?? 20;
        $offset = $validated['offset'] ?? 0;

        $results = $this->searchService->searchVolunteers($validated['q'] ?? '', [], $limit, $offset);

        return response()->json($results);
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:1',
            'type' => 'nullable|in:event,organization,volunteer',
            'limit' => 'nullable|numeric|min:1|max:50',
        ]);

        $suggestions = $this->searchService->getSuggestions(
            $validated['q'],
            $validated['type'] ?? null,
            $validated['limit'] ?? 10
        );

        return response()->json($suggestions);
    }

    /**
     * Get search history
     */
    public function history(Request $request)
    {
        $limit = $request->get('limit', 20);
        $history = $this->searchService->getSearchHistory(Auth::id(), $limit);

        return response()->json($history);
    }

    /**
     * Clear search history
     */
    public function clearHistory()
    {
        $this->searchService->clearSearchHistory(Auth::id());

        return response()->json(['message' => 'History cleared']);
    }

    /**
     * Save search
     */
    public function saveSearch(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'query' => 'nullable|string',
            'type' => 'required|in:event,organization,volunteer,all',
            'filters' => 'nullable|array',
            'notify_on_match' => 'nullable|boolean',
        ]);

        $search = $this->searchService->saveSearch(
            Auth::id(),
            $validated['name'],
            $validated['type'],
            $validated['filters'] ?? [],
            $validated['query'] ?? null,
            $validated['notify_on_match'] ?? false
        );

        return response()->json($search, 201);
    }

    /**
     * Get saved searches
     */
    public function savedSearches()
    {
        $searches = $this->searchService->getSavedSearches(Auth::id());

        return response()->json($searches);
    }

    /**
     * Delete saved search
     */
    public function deleteSavedSearch($searchId)
    {
        $this->searchService->deleteSavedSearch($searchId, Auth::id());

        return response()->json(['message' => 'Search deleted']);
    }
}
