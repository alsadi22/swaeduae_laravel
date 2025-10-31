@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header with Search and Filter -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('Volunteer Events') }}</h1>
                    <p class="text-gray-600 mt-2">{{ __('Discover opportunities to make a difference') }}</p>
                </div>
                @auth
                    @if(auth()->user()->hasRole('organization'))
                        <a href="{{ route('events.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                            {{ __('Create Event') }}
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Search and Filter Bar -->
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('events.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Search') }}</label>
                        <input type="text" name="search" placeholder="Event name or description..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               value="{{ request('search') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Category') }}</label>
                        <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('All Categories') }}</option>
                            <option value="education">{{ __('Education') }}</option>
                            <option value="environment">{{ __('Environment') }}</option>
                            <option value="health">{{ __('Health') }}</option>
                            <option value="community">{{ __('Community') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Location') }}</label>
                        <input type="text" name="location" placeholder="City or region..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               value="{{ request('location') }}">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                            {{ __('Search') }}
                        </button>
                        <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-400 transition">
                            {{ __('Clear') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                    <!-- Event Image -->
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>

                    <!-- Event Details -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $event->title ?? 'Event' }}</h3>
                        
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($event->description ?? '', 100) }}</p>

                        <!-- Event Meta -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-gray-600 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                {{ $event->location ?? 'TBA' }}
                            </div>

                            <div class="flex items-center text-gray-600 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $event->start_date ? $event->start_date->format('M d, Y') : 'Date TBA' }}
                            </div>

                            <div class="flex items-center text-gray-600 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292m0-5.292H6.462m5.538 0H18m0 5.338A6 6 0 1012 4.354"></path>
                                </svg>
                                {{ $event->volunteers_needed ?? 0 }} {{ __('volunteers needed') }}
                            </div>
                        </div>

                        <!-- Category Badge -->
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $event->category ?? 'General' }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <a href="{{ route('events.show', $event->id ?? '#') }}" class="flex-1 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-center">
                                {{ __('View Details') }}
                            </a>
                            @auth
                                <button class="px-4 py-2 border border-blue-600 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition">
                                    {{ __('Save') }}
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-600 text-lg">{{ __('No events found. Try adjusting your filters.') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($events instanceof \Illuminate\Pagination\Paginator)
            <div class="mt-8">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
