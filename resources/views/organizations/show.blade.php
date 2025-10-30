@extends('layouts.app')

@section('title', $organization->name . ' - SwaedUAE')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Organization Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
                <!-- Organization Logo -->
                <div class="flex-shrink-0 mb-4 md:mb-0">
                    <div class="h-24 w-24 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center overflow-hidden">
                        @if($organization->logo)
                            <img src="{{ Storage::url($organization->logo) }}" alt="{{ $organization->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="text-white text-2xl font-bold">
                                {{ substr($organization->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Organization Info -->
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $organization->name }}</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Verified ✓
                        </span>
                    </div>
                    <p class="text-lg text-gray-600 mb-4">{{ $organization->description }}</p>
                    
                    <!-- Contact Info -->
                    <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                        @if($organization->website)
                            <a href="{{ $organization->website }}" target="_blank" class="flex items-center hover:text-blue-600">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Website
                            </a>
                        @endif
                        @if($organization->phone)
                            <span class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                {{ $organization->phone }}
                            </span>
                        @endif
                        @if($organization->user->email)
                            <span class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                {{ $organization->user->email }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">About {{ $organization->name }}</h2>
                    <div class="prose max-w-none text-gray-600">
                        <p>{{ $organization->description }}</p>
                        @if($organization->mission)
                            <h3 class="text-lg font-medium text-gray-900 mt-6 mb-2">Our Mission</h3>
                            <p>{{ $organization->mission }}</p>
                        @endif
                    </div>
                </div>

                <!-- Focus Areas -->
                @if($organization->focus_areas)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Focus Areas</h2>
                        @php
                            $focusAreas = is_string($organization->focus_areas) ? explode(',', $organization->focus_areas) : $organization->focus_areas;
                        @endphp
                        <div class="flex flex-wrap gap-2">
                            @foreach($focusAreas as $area)
                                <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                    {{ trim($area) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Upcoming Events -->
                @if($upcomingEvents->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Events</h2>
                        <div class="space-y-4">
                            @foreach($upcomingEvents->take(5) as $event)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900 mb-1">{{ $event->title }}</h3>
                                            <p class="text-sm text-gray-600 mb-2">{{ Str::limit($event->description, 100) }}</p>
                                            <div class="flex items-center text-sm text-gray-500 space-x-4">
                                                <span class="flex items-center">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $event->start_date->format('M j, Y') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    {{ $event->location }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('events.show', $event) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                                View Event
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($upcomingEvents->count() > 5)
                            <div class="mt-4 text-center">
                                <a href="{{ route('events.index', ['organization' => $organization->id]) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    View All {{ $upcomingEvents->count() }} Events →
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Past Events -->
                @if($pastEvents->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Past Events</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($pastEvents->take(4) as $event)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="font-medium text-gray-900 mb-1">{{ $event->title }}</h3>
                                    <p class="text-sm text-gray-500 mb-2">{{ $event->start_date->format('M j, Y') }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">
                                            {{ $event->applications()->where('status', 'approved')->count() }} volunteers
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statistics -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Organization Stats</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Events</span>
                            <span class="font-semibold text-gray-900">{{ $stats['total_events'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Upcoming Events</span>
                            <span class="font-semibold text-blue-600">{{ $stats['upcoming_events'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Completed Events</span>
                            <span class="font-semibold text-green-600">{{ $stats['completed_events'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Volunteers</span>
                            <span class="font-semibold text-purple-600">{{ $stats['total_volunteers'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                    <div class="space-y-3">
                        @if($organization->user->email)
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-600">{{ $organization->user->email }}</span>
                            </div>
                        @endif
                        @if($organization->phone)
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-gray-600">{{ $organization->phone }}</span>
                            </div>
                        @endif
                        @if($organization->website)
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                <a href="{{ $organization->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    Visit Website
                                </a>
                            </div>
                        @endif
                        @if($organization->address)
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-gray-600">{{ $organization->address }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Join Organization CTA -->
                <div class="bg-blue-50 rounded-lg p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Interested in Volunteering?</h3>
                    <p class="text-gray-600 mb-4">Browse their upcoming events and apply to make a difference!</p>
                    <a href="{{ route('events.index', ['organization' => $organization->id]) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        View All Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection