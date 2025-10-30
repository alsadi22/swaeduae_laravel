@extends('layouts.app')

@section('title', 'Browse Volunteer Events')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Browse Volunteer Events</h1>
                    <p class="text-gray-600 mt-2">Find meaningful volunteer opportunities in the UAE</p>
                </div>
                <a href="{{ route('volunteer.applications.index') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                    My Applications
                </a>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" action="{{ route('volunteer.events.index') }}" class="space-y-4">
                <!-- Search Bar -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search events by title, description, or location..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" 
                               name="location" 
                               value="{{ request('location') }}"
                               placeholder="City or Emirate"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" 
                               name="date_from" 
                               value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" 
                               name="date_to" 
                               value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Clear Filters -->
                @if(request()->hasAny(['search', 'category', 'location', 'date_from', 'date_to']))
                    <div class="flex justify-end">
                        <a href="{{ route('volunteer.events.index') }}" 
                           class="text-gray-600 hover:text-gray-800 text-sm">
                            Clear all filters
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Events Grid -->
        @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($events as $event)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                        <!-- Event Image -->
                        <div class="h-48 bg-gray-200 relative">
                            @if($event->image)
                                <img src="{{ Storage::url($event->image) }}" 
                                     alt="{{ $event->title }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-blue-600">
                                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Featured Badge -->
                            @if($event->featured)
                                <div class="absolute top-3 left-3">
                                    <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                        Featured
                                    </span>
                                </div>
                            @endif

                            <!-- Application Status Badge -->
                            @if($event->user_applied > 0)
                                <div class="absolute top-3 right-3">
                                    <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                        Applied
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Event Content -->
                        <div class="p-6">
                            <!-- Event Title -->
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('volunteer.events.show', $event) }}" 
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $event->title }}
                                </a>
                            </h3>

                            <!-- Organization -->
                            <p class="text-sm text-gray-600 mb-3">
                                <i class="fas fa-building mr-1"></i>
                                {{ $event->organization->name }}
                            </p>

                            <!-- Event Details -->
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2 w-4"></i>
                                    {{ $event->start_date->format('M j, Y') }}
                                    @if($event->end_date && !$event->start_date->isSameDay($event->end_date))
                                        - {{ $event->end_date->format('M j, Y') }}
                                    @endif
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2 w-4"></i>
                                    {{ $event->start_time }} - {{ $event->end_time }}
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 w-4"></i>
                                    {{ $event->city }}, {{ $event->emirate }}
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-2 w-4"></i>
                                    {{ $event->volunteer_hours }} volunteer hours
                                </div>
                            </div>

                            <!-- Category -->
                            @if($event->category)
                                <div class="mb-4">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ ucfirst($event->category) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ Str::limit($event->description, 120) }}
                            </p>

                            <!-- Available Spots -->
                            @if($event->max_volunteers)
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Available Spots</span>
                                        <span>{{ $event->max_volunteers - $event->applications_count }}/{{ $event->max_volunteers }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ ($event->applications_count / $event->max_volunteers) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <a href="{{ route('volunteer.events.show', $event) }}" 
                                   class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-center text-sm font-medium hover:bg-gray-200 transition-colors">
                                    View Details
                                </a>
                                @if($event->user_applied == 0)
                                    @if($event->application_deadline && $event->application_deadline->isPast())
                                        <span class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-center text-sm font-medium cursor-not-allowed">
                                            Deadline Passed
                                        </span>
                                    @elseif($event->max_volunteers && $event->applications_count >= $event->max_volunteers)
                                        <span class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-center text-sm font-medium cursor-not-allowed">
                                            Full
                                        </span>
                                    @else
                                        <a href="{{ route('volunteer.events.apply', $event) }}" 
                                           class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-center text-sm font-medium hover:bg-blue-700 transition-colors">
                                            Apply Now
                                        </a>
                                    @endif
                                @else
                                    <span class="flex-1 bg-green-100 text-green-700 px-4 py-2 rounded-lg text-center text-sm font-medium">
                                        Applied
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $events->appends(request()->query())->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-alt text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Events Found</h3>
                <p class="text-gray-600 mb-6">
                    @if(request()->hasAny(['search', 'category', 'location', 'date_from', 'date_to']))
                        No events match your current filters. Try adjusting your search criteria.
                    @else
                        There are no volunteer events available at the moment. Check back soon!
                    @endif
                </p>
                @if(request()->hasAny(['search', 'category', 'location', 'date_from', 'date_to']))
                    <a href="{{ route('volunteer.events.index') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                        Clear Filters
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush