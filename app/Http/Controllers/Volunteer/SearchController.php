<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use App\Models\SavedSearch;
use App\Models\SearchHistory;
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
     * Display search page
     */
    public function index()
    {
        $savedSearches = SavedSearch::where('user_id', Auth::id())->get();
        $searchHistory = SearchHistory::forUser(Auth::id())->recent()->limit(10)->get();

        return view('volunteer.search.index', compact('savedSearches', 'searchHistory'));
    }

    /**
     * Perform search
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
            'type' => 'nullable|in:event,organization,volunteer,all',
            'category' => 'nullable|string',
            'location' => 'nullable|string',
            'distance' => 'nullable|numeric|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'sort_by' => 'nullable|in:created_at,title,start_date',
            'sort_order' => 'nullable|in:asc,desc',
            'page' => 'nullable|numeric|min:1',
        ]);

        $page = $validated['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $query = $validated['q'] ?? '';
        $type = $validated['type'] ?? 'all';

        // Build filters
        $filters = [
            'sort_by' => $validated['sort_by'] ?? 'created_at',
            'sort_order' => $validated['sort_order'] ?? 'desc',
        ];

        if (isset($validated['category'])) {
            $filters['categories'] = [$validated['category']];
        }
        if (isset($validated['start_date'])) {
            $filters['start_date'] = $validated['start_date'];
        }
        if (isset($validated['end_date'])) {
            $filters['end_date'] = $validated['end_date'];
        }

        // Perform search based on type
        $results = [];
        if ($type === 'all' || $type === 'event') {
            $results['events'] = $this->searchService->searchEvents($query, $filters, $limit, $offset);
        }
        if ($type === 'all' || $type === 'organization') {
            $results['organizations'] = $this->searchService->searchOrganizations($query, $filters, $limit, $offset);
        }
        if ($type === 'all' || $type === 'volunteer') {
            $results['volunteers'] = $this->searchService->searchVolunteers($query, $filters, $limit, $offset);
        }

        // Log search
        if (Auth::check()) {
            $totalResults = collect($results)->sum(function ($result) {
                return $result['total'] ?? 0;
            });

            $this->searchService->logSearch(
                Auth::id(),
                $query,
                $type,
                $totalResults,
                $request->ip()
            );
        }

        if ($request->wantsJson()) {
            return response()->json($results);
        }

        return view('volunteer.search.results', compact('results', 'query', 'type', 'page'));
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', null);
        $limit = $request->get('limit', 10);

        $suggestions = $this->searchService->getSuggestions($query, $type, $limit);

        return response()->json($suggestions);
    }

    /**
     * Save a search
     */
    public function saveSearch(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'query' => 'nullable|string',
            'type' => 'required|in:event,organization,volunteer,all',
            'notify' => 'nullable|boolean',
        ]);

        SavedSearch::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'query' => $validated['query'] ?? null,
            'search_type' => $validated['type'],
            'notify_on_match' => $validated['notify'] ?? false,
            'filters' => $request->get('filters', []),
        ]);

        return back()->with('success', 'Search saved successfully!');
    }

    /**
     * Get saved searches
     */
    public function savedSearches()
    {
        $searches = SavedSearch::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('volunteer.search.saved', compact('searches'));
    }

    /**
     * Delete saved search
     */
    public function deleteSavedSearch(SavedSearch $search)
    {
        if ($search->user_id !== Auth::id()) {
            abort(403);
        }

        $search->delete();

        return back()->with('success', 'Search deleted');
    }

    /**
     * Clear search history
     */
    public function clearHistory()
    {
        SearchHistory::forUser(Auth::id())->delete();

        return back()->with('success', 'Search history cleared');
    }

    /**
     * Get search history
     */
    public function history()
    {
        $history = SearchHistory::forUser(Auth::id())
            ->recent()
            ->paginate(20);

        return view('volunteer.search.history', compact('history'));
    }
}
