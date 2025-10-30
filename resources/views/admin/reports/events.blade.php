@extends('admin.layouts.app')

@section('title', 'Event Reports')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Event Reports</h1>
                        <p class="mt-1 text-sm text-gray-600">Event performance metrics and statistics</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.reports.index') }}" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-900 transition-colors">
                            Back to Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Events by Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Events by Status</h3>
                </div>
                <div class="p-6">
                    @if(isset($eventsByStatus) && count($eventsByStatus) > 0)
                        <div class="space-y-4">
                            @foreach($eventsByStatus as $status => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ ucfirst($status) }}</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-red-600 h-2 rounded-full status-bar" style="width: {{ $count > 0 ? ($count / $maxEventsByStatus * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No data available.</p>
                    @endif
                </div>
            </div>

            <!-- Events by Category -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Events by Category</h3>
                </div>
                <div class="p-6">
                    @if(isset($eventsByCategory) && count($eventsByCategory) > 0)
                        <div class="space-y-4">
                            @foreach($eventsByCategory as $category => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ $category }}</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full category-bar" style="width: {{ $count > 0 ? ($count / $maxEventsByCategory * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No data available.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Event Trends -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Event Creation Trends (Last 12 Months)</h3>
            </div>
            <div class="p-6">
                @if(isset($eventTrends) && count($eventTrends) > 0)
                    <div class="space-y-4">
                        @foreach($eventTrends as $trend)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ $trend->month }}</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full trend-bar" style="width: {{ $trend->count > 0 ? ($trend->count / $maxEventTrend * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $trend->count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No data available.</p>
                @endif
            </div>
        </div>

        <!-- Popular Events -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Popular Events by Applications</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applications</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($popularEvents ?? [] as $event)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $event->organization->name ?? 'Unknown Organization' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $event->start_date ? $event->start_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $event->applications_count ?? 0 }} applications
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No events found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection