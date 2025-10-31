@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Admin Dashboard') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('System Overview & Management') }}</p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">{{ __('Total Users') }}</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                    </div>
                    <div class="text-blue-600 bg-blue-100 p-3 rounded-lg">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292m0-5.292H6.462m5.538 0H18m0 5.338A6 6 0 1012 4.354"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-green-600 mt-2">{{ __('↑ Active this month') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">{{ __('Total Events') }}</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_events'] ?? 0 }}</p>
                    </div>
                    <div class="text-green-600 bg-green-100 p-3 rounded-lg">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-green-600 mt-2">{{ __('↑ Growing') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">{{ __('Active Organizations') }}</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_organizations'] ?? 0 }}</p>
                    </div>
                    <div class="text-purple-600 bg-purple-100 p-3 rounded-lg">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m0 0h5.581m0 0a2.121 2.121 0 01-3.758 0M9 6.5a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0zm12 6h-4m-2 5h-4"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-green-600 mt-2">{{ __('Verified') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">{{ __('Pending Approvals') }}</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_approvals'] ?? 0 }}</p>
                    </div>
                    <div class="text-yellow-600 bg-yellow-100 p-3 rounded-lg">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-yellow-600 mt-2">{{ __('Action Required') }}</p>
            </div>
        </div>

        <!-- Management Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Recent Users -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Recent Users') }}</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($recent_users ?? [] as $user)
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-blue-600">{{ substr($user->first_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->first_name }}</p>
                                    <p class="text-xs text-gray-600">{{ $user->email }}</p>
                                </div>
                            </div>
                            <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                {{ __('View') }}
                            </a>
                        </div>
                    @empty
                        <p class="p-4 text-gray-600 text-sm">{{ __('No recent users') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('System Status') }}</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-sm">{{ __('Database') }}</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ __('Healthy') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-sm">{{ __('Cache') }}</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ __('Active') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-sm">{{ __('Email Service') }}</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ __('Connected') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-sm">{{ __('API Health') }}</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ __('Normal') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Quick Actions') }}</h2>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('admin.users.index') }}" class="block p-3 rounded hover:bg-gray-50 transition border border-transparent hover:border-gray-200">
                        <p class="font-medium text-gray-900 text-sm">{{ __('Manage Users') }}</p>
                    </a>
                    <a href="{{ route('admin.personalization.featureFlags') }}" class="block p-3 rounded hover:bg-gray-50 transition border border-transparent hover:border-gray-200">
                        <p class="font-medium text-gray-900 text-sm">{{ __('Feature Flags') }}</p>
                    </a>
                    <a href="{{ route('admin.personalization.abTests') }}" class="block p-3 rounded hover:bg-gray-50 transition border border-transparent hover:border-gray-200">
                        <p class="font-medium text-gray-900 text-sm">{{ __('A/B Tests') }}</p>
                    </a>
                    <a href="{{ route('admin.analyticsReports.kpiManagement') }}" class="block p-3 rounded hover:bg-gray-50 transition border border-transparent hover:border-gray-200">
                        <p class="font-medium text-gray-900 text-sm">{{ __('KPI Management') }}</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Analytics Preview -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Analytics Overview') }}</h2>
                    <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        {{ __('View Full Analytics') }}
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="border border-gray-200 rounded p-4">
                        <p class="text-gray-600 text-xs uppercase font-semibold">{{ __('Users This Month') }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['users_this_month'] ?? 0 }}</p>
                    </div>
                    <div class="border border-gray-200 rounded p-4">
                        <p class="text-gray-600 text-xs uppercase font-semibold">{{ __('Events Completed') }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['events_completed'] ?? 0 }}</p>
                    </div>
                    <div class="border border-gray-200 rounded p-4">
                        <p class="text-gray-600 text-xs uppercase font-semibold">{{ __('Volunteer Hours') }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['volunteer_hours'] ?? 0 }}</p>
                    </div>
                    <div class="border border-gray-200 rounded p-4">
                        <p class="text-gray-600 text-xs uppercase font-semibold">{{ __('Engagement Rate') }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['engagement_rate'] ?? '0%' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
