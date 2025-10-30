@extends('layouts.app')

@section('title', 'Organizations - SwaedUAE')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Approved Organizations</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Discover trusted organizations making a difference in the UAE. Join their volunteer programs and contribute to meaningful causes.
                </p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('organizations.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
                <!-- Search Input -->
                <div class="flex-1">
                    <label for="search" class="sr-only">Search organizations</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Search organizations...">
                    </div>
                </div>

                <!-- Focus Area Filter -->
                <div class="md:w-48">
                    <select name="focus_area" class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Focus Areas</option>
                        <option value="Education" {{ request('focus_area') == 'Education' ? 'selected' : '' }}>Education</option>
                        <option value="Environment" {{ request('focus_area') == 'Environment' ? 'selected' : '' }}>Environment</option>
                        <option value="Health" {{ request('focus_area') == 'Health' ? 'selected' : '' }}>Health</option>
                        <option value="Community" {{ request('focus_area') == 'Community' ? 'selected' : '' }}>Community</option>
                        <option value="Youth" {{ request('focus_area') == 'Youth' ? 'selected' : '' }}>Youth</option>
                        <option value="Elderly" {{ request('focus_area') == 'Elderly' ? 'selected' : '' }}>Elderly</option>
                    </select>
                </div>

                <!-- Sort Options -->
                <div class="md:w-48">
                    <select name="sort" class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="most_active" {{ request('sort') == 'most_active' ? 'selected' : '' }}>Most Active</option>
                    </select>
                </div>

                <!-- Search Button -->
                <div>
                    <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Count -->
        <div class="mb-6">
            <p class="text-gray-600">
                Showing {{ $organizations->firstItem() ?? 0 }} to {{ $organizations->lastItem() ?? 0 }} of {{ $organizations->total() }} organizations
            </p>
        </div>

        <!-- Organizations Grid -->
        @if($organizations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($organizations as $organization)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                        <!-- Organization Logo/Image -->
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            @if($organization->logo)
                                <img src="{{ Storage::url($organization->logo) }}" alt="{{ $organization->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="text-white text-4xl font-bold">
                                    {{ substr($organization->name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Organization Info -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $organization->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($organization->description, 120) }}</p>
                            
                            <!-- Focus Areas -->
                            @if($organization->focus_areas)
                                <div class="mb-4">
                                    @php
                                        $focusAreas = is_string($organization->focus_areas) ? explode(',', $organization->focus_areas) : $organization->focus_areas;
                                    @endphp
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(array_slice($focusAreas, 0, 3) as $area)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                                {{ trim($area) }}
                                            </span>
                                        @endforeach
                                        @if(count($focusAreas) > 3)
                                            <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                                                +{{ count($focusAreas) - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Statistics -->
                            <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                                <span>{{ $organization->active_events_count }} Active Events</span>
                                <span>Verified ✓</span>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('organizations.show', $organization) }}" 
                               class="block w-full text-center bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                                View Organization
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $organizations->appends(request()->query())->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No organizations found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request()->hasAny(['search', 'focus_area']))
                        Try adjusting your search criteria or filters.
                    @else
                        There are no verified organizations available at the moment.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'focus_area']))
                    <div class="mt-6">
                        <a href="{{ route('organizations.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection