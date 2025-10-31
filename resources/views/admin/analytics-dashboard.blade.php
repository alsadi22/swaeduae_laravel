@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Analytics Dashboard') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Real-time metrics and performance insights') }}</p>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Start Date') }}</label>
                    <input type="date" id="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('End Date') }}</label>
                    <input type="date" id="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    {{ __('Filter') }}
                </button>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">{{ __('Total Page Views') }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['page_views'] ?? 0 }}</p>
                <p class="text-xs text-green-600 mt-2">{{ __('↑ 12% from last period') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">{{ __('Unique Users') }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['unique_users'] ?? 0 }}</p>
                <p class="text-xs text-green-600 mt-2">{{ __('↑ 8% from last period') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">{{ __('Conversion Rate') }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['conversion_rate'] ?? '0%' }}</p>
                <p class="text-xs text-green-600 mt-2">{{ __('↑ 2% from last period') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">{{ __('Avg Session Duration') }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['avg_session_duration'] ?? '0m' }}</p>
                <p class="text-xs text-green-600 mt-2">{{ __('↑ 15s from last period') }}</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- User Growth Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('User Growth') }}</h2>
                <div class="h-64 bg-gradient-to-r from-blue-50 to-blue-100 rounded flex items-center justify-center text-gray-500">
                    {{ __('Chart placeholder - User growth over time') }}
                </div>
            </div>

            <!-- Engagement Metrics Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('Engagement Metrics') }}</h2>
                <div class="h-64 bg-gradient-to-r from-green-50 to-green-100 rounded flex items-center justify-center text-gray-500">
                    {{ __('Chart placeholder - Engagement trends') }}
                </div>
            </div>
        </div>

        <!-- Event Tracking -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('Top Events') }}</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">{{ __('Event Name') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">{{ __('Count') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">{{ __('Percentage') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">{{ __('Trend') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($events ?? [] as $event)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $event->name ?? 'Event' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $event->count ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $event->percentage ?? '0%' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center text-green-600">
                                        ↑ {{ $event->trend ?? '0%' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-sm text-gray-600">{{ __('No events recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Funnel Analysis -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('Conversion Funnel') }}</h2>
            <div class="space-y-4">
                @forelse($funnel ?? [] as $step)
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900">{{ $step->name ?? 'Step' }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $step->percentage ?? '0%' }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $step->percentage ?? '0' }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">{{ $step->count ?? 0 }} users</p>
                    </div>
                @empty
                    <p class="text-gray-600">{{ __('No funnel data available') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
