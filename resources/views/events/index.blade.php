@extends('layouts.app')

@section('title', 'Volunteer Events')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Volunteer Opportunities</h1>
                <p class="text-xl opacity-90">Make a difference in your community</p>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" action="{{ route('events.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Events</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Search by title or description..."
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" id="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            <option value="Education" {{ request('category') == 'Education' ? 'selected' : '' }}>Education</option>
                            <option value="Environment" {{ request('category') == 'Environment' ? 'selected' : '' }}>Environment</option>
                            <option value="Health" {{ request('category') == 'Health' ? 'selected' : '' }}>Health</option>
                            <option value="Community" {{ request('category') == 'Community' ? 'selected' : '' }}>Community</option>
                            <option value="Sports" {{ request('category') == 'Sports' ? 'selected' : '' }}>Sports</option>
                            <option value="Arts & Culture" {{ request('category') == 'Arts & Culture' ? 'selected' : '' }}>Arts & Culture</option>
                            <option value="Technology" {{ request('category') == 'Technology' ? 'selected' : '' }}>Technology</option>
                            <option value="Elderly Care" {{ request('category') == 'Elderly Care' ? 'selected' : '' }}>Elderly Care</option>
                        </select>
                    </div>

                    <!-- Emirate Filter -->
                    <div>
                        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-1">Emirate</label>
                        <select name="emirate" id="emirate" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Emirates</option>
                            <option value="Abu Dhabi" {{ request('emirate') == 'Abu Dhabi' ? 'selected' : '' }}>Abu Dhabi</option>
                            <option value="Dubai" {{ request('emirate') == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                            <option value="Sharjah" {{ request('emirate') == 'Sharjah' ? 'selected' : '' }}>Sharjah</option>
                            <option value="Ajman" {{ request('emirate') == 'Ajman' ? 'selected' : '' }}>Ajman</option>
                            <option value="Umm Al Quwain" {{ request('emirate') == 'Umm Al Quwain' ? 'selected' : '' }}>Umm Al Quwain</option>
                            <option value="Ras Al Khaimah" {{ request('emirate') == 'Ras Al Khaimah' ? 'selected' : '' }}>Ras Al Khaimah</option>
                            <option value="Fujairah" {{ request('emirate') == 'Fujairah' ? 'selected' : '' }}>Fujairah</option>
                        </select>
                    </div>

                    <!-- Status Filter (for authenticated users) -->
                    @auth
                        @if(auth()->user()?->hasRole(['admin', 'super-admin', 'organization-manager']))
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Status</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        @endif
                    @endauth
                </div>

                <div class="flex justify-between items-center">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-search mr-2"></i>Search Events
                    </button>
                    <a href="{{ route('events.index') }}" class="text-gray-600 hover:text-gray-800">Clear Filters</a>
                </div>
            </form>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                    <!-- Event Image -->
                    <div class="h-48 bg-gray-200 relative">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-purple-500">
                                <i class="fas fa-hands-helping text-white text-4xl"></i>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2">
                            @if($event->status === 'published')
                                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">Published</span>
                            @elseif($event->status === 'pending')
                                <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs">Pending</span>
                            @elseif($event->status === 'completed')
                                <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs">Completed</span>
                            @endif
                        </div>

                        <!-- Featured Badge -->
                        @if($event->is_featured)
                            <div class="absolute top-2 left-2">
                                <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Event Content -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $event->category }}</span>
                            <span class="text-gray-500 text-sm">{{ $event->volunteer_hours }}h</span>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $event->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($event->description, 120) }}</p>

                        <!-- Event Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar mr-2 text-blue-500"></i>
                                {{ $event->start_date->format('M d, Y') }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                                {{ $event->city }}, {{ $event->emirate }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-building mr-2 text-green-500"></i>
                                {{ $event->organization->name }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-users mr-2 text-purple-500"></i>
                                {{ $event->available_spots }} spots available
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <a href="{{ route('events.show', $event) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                                View Details
                            </a>
                            @auth
                                @if(auth()->user()?->hasRole('volunteer') && $event->applications_open)
                                    <a href="{{ route('volunteer.events.apply', $event) }}" 
                                       class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-200">
                                        <i class="fas fa-hand-paper"></i>
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Events Found</h3>
                    <p class="text-gray-500">Try adjusting your search criteria or check back later for new opportunities.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
            <div class="mt-8">
                {{ $events->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush