@extends('admin.layouts.app')

@section('title', 'User Reports')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">User Reports</h1>
                        <p class="mt-1 text-sm text-gray-600">Detailed user analytics and statistics</p>
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
            <!-- Users by Role -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Users by Role</h3>
                </div>
                <div class="p-6">
                    @if(isset($usersByRole) && count($usersByRole) > 0)
                        <div class="space-y-4">
                            @foreach($usersByRole as $role => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ ucfirst($role) }}</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full role-bar" data-count="{{ $count }}" data-total="{{ $totalUsersByRole }}"></div>
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

            <!-- Registration Trends -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Registration Trends (Last 12 Months)</h3>
                </div>
                <div class="p-6">
                    @if(isset($registrationTrends) && count($registrationTrends) > 0)
                        <div class="space-y-4">
                            @foreach($registrationTrends as $trend)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ $trend->month }}</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full registration-bar" data-count="{{ $trend->count }}" data-max="{{ $maxRegistrationTrend }}"></div>
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
        </div>

        <!-- Top Volunteers -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Top Volunteers by Hours</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volunteer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Events Attended</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topVolunteers ?? [] as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-700 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $user->total_volunteer_hours ?? 0 }} hours
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->total_events_attended ?? 0 }} events
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No volunteers found.
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
        // Set width for role bars
        var roleBars = document.querySelectorAll('.role-bar');
        roleBars.forEach(function(bar) {
            var count = parseInt(bar.getAttribute('data-count'));
            var total = parseInt(bar.getAttribute('data-total'));
            var width = count > 0 ? (count / total * 100) : 0;
            bar.style.width = width + '%';
        });
        
        // Set width for registration bars
        var registrationBars = document.querySelectorAll('.registration-bar');
        registrationBars.forEach(function(bar) {
            var count = parseInt(bar.getAttribute('data-count'));
            var max = parseInt(bar.getAttribute('data-max'));
            var width = count > 0 ? (count / max * 100) : 0;
            bar.style.width = width + '%';
        });
    });
</script>
@endsection