@extends('layouts.organization')

@section('title', 'Real-time Attendance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Real-time Attendance</h1>
                <p class="mt-2 text-gray-600">Live tracking of volunteer attendance for your event</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-sm text-gray-600">Live</span>
                </div>
                <button onclick="refreshData()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Event Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ $event->title }}</h2>
                <p class="text-gray-600">{{ $event->start_date->format('M d, Y') }} • {{ $event->location }}</p>
            </div>
            <div class="flex space-x-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_registered'] }}</div>
                    <div class="text-sm text-gray-500">Registered</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['checked_in'] }}</div>
                    <div class="text-sm text-gray-500">Checked In</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['checked_out'] }}</div>
                    <div class="text-sm text-gray-500">Completed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Participants List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Participants</h3>
                <div class="flex space-x-2">
                    <input type="text" id="searchInput" placeholder="Search participants..." 
                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="checked_in">Checked In</option>
                        <option value="checked_out">Checked Out</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volunteer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="participantsTable" class="bg-white divide-y divide-gray-200">
                    @foreach($attendances as $attendance)
                        <tr class="hover:bg-gray-50" data-status="{{ $attendance->status }}" data-name="{{ strtolower($attendance->user->name) }}" data-email="{{ strtolower($attendance->user->email) }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" 
                                             src="{{ $attendance->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($attendance->user->name) . '&color=7c3aed&background=ede9fe' }}" 
                                             alt="{{ $attendance->user->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $attendance->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($attendance->checked_in_at)
                                    <div>{{ $attendance->checked_in_at->format('M d, Y H:i:s') }}</div>
                                    @if($attendance->checkin_latitude && $attendance->checkin_longitude)
                                        <div class="text-xs text-gray-500 mt-1">
                                            <a href="https://www.google.com/maps?q={{ $attendance->checkin_latitude }},{{ $attendance->checkin_longitude }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                View Location
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($attendance->checked_out_at)
                                    {{ $attendance->checked_out_at->format('M d, Y H:i:s') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($attendance->hours_worked)
                                    {{ number_format($attendance->hours_worked, 1) }}h
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg-gray-100', 'text-gray-800', 'Pending'],
                                        'checked_in' => ['bg-blue-100', 'text-blue-800', 'Checked In'],
                                        'checked_out' => ['bg-yellow-100', 'text-yellow-800', 'Checked Out'],
                                        'completed' => ['bg-green-100', 'text-green-800', 'Completed']
                                    ];
                                    $config = $statusConfig[$attendance->status] ?? ['bg-gray-100', 'text-gray-800', 'Unknown'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config[0] }} {{ $config[1] }}">
                                    {{ $config[2] }}
                                    @if($attendance->status === 'checked_in')
                                        <span class="ml-1 w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($attendance->status === 'checked_in')
                                        <button onclick="processCheckout('{{ $attendance->id }}')" 
                                                class="text-green-600 hover:text-green-900">
                                            Check Out
                                        </button>
                                    @elseif($attendance->status === 'pending')
                                        <button onclick="processCheckin('{{ $attendance->id }}')" 
                                                class="text-blue-600 hover:text-blue-900">
                                            Check In
                                        </button>
                                    @endif
                                    
                                    @if($attendance->status === 'checked_out' && !$attendance->organizer_verified)
                                        <form method="POST" action="{{ route('organization.attendance.verify', $attendance) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-purple-600 hover:text-purple-900">Verify</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($attendances->count() === 0)
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No participants yet</h3>
                <p class="mt-1 text-sm text-gray-500">Participants will appear here once they start checking in to your event.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Refresh data function
function refreshData() {
    location.reload();
}

// Filter participants based on search and status
function filterParticipants() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const statusValue = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('#participantsTable tr');
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        const email = row.getAttribute('data-email');
        const status = row.getAttribute('data-status');
        
        const matchesSearch = searchValue === '' || 
                             name.includes(searchValue) || 
                             email.includes(searchValue);
        
        const matchesStatus = statusValue === '' || status === statusValue;
        
        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Process manual check-in
function processCheckin(attendanceId) {
    if (confirm('Are you sure you want to manually check in this participant?')) {
        // In a real implementation, this would make an API call to check in the participant
        alert('Manual check-in would be processed here. In a real implementation, this would make an API call.');
    }
}

// Process manual check-out
function processCheckout(attendanceId) {
    if (confirm('Are you sure you want to manually check out this participant?')) {
        // In a real implementation, this would make an API call to check out the participant
        alert('Manual check-out would be processed here. In a real implementation, this would make an API call.');
    }
}

// Set up event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').addEventListener('input', filterParticipants);
    document.getElementById('statusFilter').addEventListener('change', filterParticipants);
});

// Auto-refresh every 30 seconds
setInterval(function() {
    // Only auto-refresh if the page is visible
    if (!document.hidden) {
        refreshData();
    }
}, 30000);
</script>
@endpush