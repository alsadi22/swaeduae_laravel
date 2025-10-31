@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Volunteer Dashboard') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Welcome back, ') }}{{ Auth::user()->first_name }}</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">{{ __('Active Opportunities') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_opportunities'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">{{ __('Hours Volunteered') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['hours_volunteered'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">{{ __('Pending Applications') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_applications'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">{{ __('Total Points') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_points'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Opportunities -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Recommended for You') }}</h2>
                @if($opportunities->isEmpty())
                    <p class="text-gray-600">{{ __('No opportunities found. Check back soon!') }}</p>
                @else
                    <div class="space-y-4">
                        @foreach($opportunities as $opportunity)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $opportunity->title ?? 'Opportunity' }}</h3>
                                        <p class="text-gray-600 text-sm mt-1">{{ $opportunity->organization->name ?? 'Organization' }}</p>
                                        <div class="flex items-center gap-4 mt-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                {{ $opportunity->category ?? 'General' }}
                                            </span>
                                            <span class="text-gray-600 text-sm">{{ $opportunity->location ?? 'Location TBA' }}</span>
                                        </div>
                                    </div>
                                    <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                        {{ __('View Details') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Your Applications') }}</h2>
                    @if($applications->isEmpty())
                        <p class="text-gray-600">{{ __('No applications yet') }}</p>
                    @else
                        <div class="space-y-3">
                            @foreach($applications->take(5) as $app)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-900 font-medium">{{ $app->event->title ?? 'Event' }}</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Quick Actions') }}</h2>
                    <div class="space-y-3">
                        <a href="{{ route('volunteer.applications.index') }}" class="block p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <p class="font-medium text-gray-900">{{ __('View All Applications') }}</p>
                            <p class="text-sm text-gray-600">{{ __('Manage your volunteer applications') }}</p>
                        </a>
                        <a href="{{ route('volunteer.groups.index') }}" class="block p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <p class="font-medium text-gray-900">{{ __('Join a Group') }}</p>
                            <p class="text-sm text-gray-600">{{ __('Connect with other volunteers') }}</p>
                        </a>
                        <a href="{{ route('volunteer.profile.index') }}" class="block p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <p class="font-medium text-gray-900">{{ __('Update Profile') }}</p>
                            <p class="text-sm text-gray-600">{{ __('Keep your information current') }}</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
