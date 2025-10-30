@extends('layouts.app')

@section('title', 'My Applications')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Applications</h1>
                    <p class="text-gray-600 mt-2">Track your volunteer application status and history</p>
                </div>
                <a href="{{ route('volunteer.events.index') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                    Browse Events
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            @php
                $totalApplications = $applications->total();
                $pendingCount = $applications->where('status', 'pending')->count();
                $approvedCount = $applications->where('status', 'approved')->count();
                $rejectedCount = $applications->where('status', 'rejected')->count();
            @endphp
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Applications</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalApplications }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Review</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $approvedCount }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rejected</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $rejectedCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Filter by Status:</label>
                    <select id="statusFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Applications</option>
                        <option value="pending">Pending Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Sort by:</label>
                    <select id="sortFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="event_date">Event Date</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Applications List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($applications->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($applications as $application)
                        <div class="p-6 hover:bg-gray-50 transition duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <!-- Event Info -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                                <a href="{{ route('volunteer.events.show', $application->event) }}" 
                                                   class="hover:text-blue-600 transition duration-200">
                                                    {{ $application->event->title }}
                                                </a>
                                            </h3>
                                            
                                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-3">
                                                <div class="flex items-center">
                                                    <i class="fas fa-building mr-2"></i>
                                                    {{ $application->event->organization->name }}
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar mr-2"></i>
                                                    {{ $application->event->start_date->format('M j, Y') }}
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                                    {{ $application->event->city }}, {{ $application->event->emirate }}
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    {{ $application->event->volunteer_hours }} hours
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Status Badge -->
                                        <div class="ml-4">
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    'completed' => 'bg-blue-100 text-blue-800',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                @switch($application->status)
                                                    @case('pending')
                                                        <i class="fas fa-clock mr-1"></i>
                                                        Pending Review
                                                        @break
                                                    @case('approved')
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Approved
                                                        @break
                                                    @case('rejected')
                                                        <i class="fas fa-times-circle mr-1"></i>
                                                        Rejected
                                                        @break
                                                    @case('completed')
                                                        <i class="fas fa-trophy mr-1"></i>
                                                        Completed
                                                        @break
                                                    @default
                                                        {{ ucfirst($application->status) }}
                                                @endswitch
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Application Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Applied:</span> 
                                                {{ $application->applied_at->format('M j, Y \a\t g:i A') }}
                                            </p>
                                            @if($application->reviewed_at)
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Reviewed:</span> 
                                                    {{ $application->reviewed_at->format('M j, Y \a\t g:i A') }}
                                                </p>
                                            @endif
                                        </div>
                                        
                                        @if($application->skills && count($application->skills) > 0)
                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">
                                                    <span class="font-medium">Skills:</span>
                                                </p>
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach(array_slice($application->skills, 0, 3) as $skill)
                                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                                            {{ $skill }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($application->skills) > 3)
                                                        <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">
                                                            +{{ count($application->skills) - 3 }} more
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Rejection Reason -->
                                    @if($application->status === 'rejected' && $application->rejection_reason)
                                        <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-4">
                                            <p class="text-sm text-red-800">
                                                <span class="font-medium">Rejection Reason:</span> 
                                                {{ $application->rejection_reason }}
                                            </p>
                                        </div>
                                    @endif
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('volunteer.applications.show', $application) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Details
                                        </a>
                                        
                                        @if($application->status === 'pending')
                                            <form action="{{ route('volunteer.applications.destroy', $application) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to withdraw this application?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    Withdraw Application
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($application->status === 'approved' && $application->event->start_date > now())
                                            <a href="{{ route('volunteer.events.show', $application->event) }}" 
                                               class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                View Event Details
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $applications->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-file-alt text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Applications Yet</h3>
                    <p class="text-gray-600 mb-6">You haven't applied to any volunteer events yet.</p>
                    <a href="{{ route('volunteer.events.index') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                        Browse Events
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusFilter = document.getElementById('statusFilter');
        const sortFilter = document.getElementById('sortFilter');
        
        // Handle filter changes
        function handleFilterChange() {
            const status = statusFilter.value;
            const sort = sortFilter.value;
            
            // Build query parameters
            const params = new URLSearchParams(window.location.search);
            
            if (status !== 'all') {
                params.set('status', status);
            } else {
                params.delete('status');
            }
            
            if (sort !== 'newest') {
                params.set('sort', sort);
            } else {
                params.delete('sort');
            }
            
            // Redirect with new parameters
            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.location.href = newUrl;
        }
        
        statusFilter.addEventListener('change', handleFilterChange);
        sortFilter.addEventListener('change', handleFilterChange);
        
        // Set current filter values from URL
        const urlParams = new URLSearchParams(window.location.search);
        const currentStatus = urlParams.get('status') || 'all';
        const currentSort = urlParams.get('sort') || 'newest';
        
        statusFilter.value = currentStatus;
        sortFilter.value = currentSort;
    });
</script>
@endpush