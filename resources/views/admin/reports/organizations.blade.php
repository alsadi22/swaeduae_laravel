@extends('admin.layouts.app')

@section('title', 'Organization Reports')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Organization Reports</h1>
                        <p class="mt-1 text-sm text-gray-600">Organization analytics and statistics</p>
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
            <!-- Organizations by Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Organizations by Status</h3>
                </div>
                <div class="p-6">
                    @if(isset($orgsByStatus) && count($orgsByStatus) > 0)
                        <div class="space-y-4">
                            @foreach($orgsByStatus as $status => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ ucfirst($status) }}</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full status-bar" data-width="{{ $count > 0 ? ($count / $maxOrgsByStatus * 100) : 0 }}"></div>
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

            <!-- Organizations by Verification -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Organizations by Verification</h3>
                </div>
                <div class="p-6">
                    @if(isset($orgsByVerification) && count($orgsByVerification) > 0)
                        <div class="space-y-4">
                            @foreach($orgsByVerification as $verification => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ $verification }}</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full verification-bar" data-width="{{ $count > 0 ? ($count / $maxOrgsByVerification * 100) : 0 }}"></div>
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

        <!-- Organization Trends -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Organization Registration Trends (Last 12 Months)</h3>
            </div>
            <div class="p-6">
                @if(isset($orgTrends) && count($orgTrends) > 0)
                    <div class="space-y-4">
                        @foreach($orgTrends as $trend)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ $trend->month }}</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-600 h-2 rounded-full trend-bar" data-width="{{ $trend->count > 0 ? ($trend->count / $maxOrgTrend * 100) : 0 }}"></div>
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

        <!-- Top Organizations -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Top Organizations by Events</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Events Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topOrganizations ?? [] as $organization)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $organization->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $organization->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $organization->events_count ?? 0 }} events
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $organization->users->count() }} members
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No organizations found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set width for status bars
        var statusBars = document.querySelectorAll('.status-bar');
        statusBars.forEach(function(bar) {
            var width = parseFloat(bar.getAttribute('data-width'));
            bar.style.width = width + '%';
        });
        
        // Set width for verification bars
        var verificationBars = document.querySelectorAll('.verification-bar');
        verificationBars.forEach(function(bar) {
            var width = parseFloat(bar.getAttribute('data-width'));
            bar.style.width = width + '%';
        });
        
        // Set width for trend bars
        var trendBars = document.querySelectorAll('.trend-bar');
        trendBars.forEach(function(bar) {
            var width = parseFloat(bar.getAttribute('data-width'));
            bar.style.width = width + '%';
        });
    });
</script>
@endsection