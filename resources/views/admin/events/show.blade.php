@extends('admin.layouts.app')

@section('title', 'Event Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Event Details</h1>
                        <p class="mt-1 text-sm text-gray-600">View detailed information about "{{ $event->title }}"</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.events.index') }}" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-900 transition-colors">
                            Back to Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Event Info -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Event Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Title</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->title }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Organization</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->organization->name ?? 'Unknown Organization' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($event->status === 'published')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Published
                                        </span>
                                    @elseif($event->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Category</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->category ?? 'Not specified' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Start Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->start_date ? $event->start_date->format('F d, Y') : 'N/A' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">End Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->end_date ? $event->end_date->format('F d, Y') : 'N/A' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Location</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->location ?? 'Not specified' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">City & Emirate</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->city }}, {{ $event->emirate }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Max Volunteers</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->max_volunteers ?? 'Unlimited' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Volunteer Hours</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->volunteer_hours ?? 0 }} hours</p>
                            </div>
                        </div>
                        
                        @if($event->description)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-500">Description</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->description }}</p>
                            </div>
                        @endif
                        
                        @if($event->requirements)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-500">Requirements</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $event->requirements }}</p>
                            </div>
                        @endif
                        
                        @if($event->rejection_reason)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-500">Rejection Reason</label>
                                <p class="mt-1 text-sm text-gray-900 text-red-600">{{ $event->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Applications -->
                <div class="mt-8 bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Applications</h3>
                    </div>
                    <div class="p-6">
                        @if($event->applications->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volunteer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($event->applications as $application)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $application->user->name ?? 'Unknown User' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $application->user->email ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($application->status === 'approved') bg-green-100 text-green-800
                                                        @elseif($application->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($application->status === 'rejected') bg-red-100 text-red-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ ucfirst($application->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $application->applied_at ? $application->applied_at->format('M d, Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">No applications found.</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- Actions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($event->status === 'pending')
                            <form action="{{ route('admin.events.approve', $event) }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700 transition-colors">
                                    Approve Event
                                </button>
                            </form>
                            
                            <button type="button" class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors" onclick="showRejectModal()">
                                Reject Event
                            </button>
                        @elseif($event->status === 'published')
                            <span class="block w-full text-center bg-green-100 text-green-800 px-4 py-2 rounded-md text-sm font-medium">
                                Event is published
                            </span>
                        @elseif($event->status === 'rejected')
                            <span class="block w-full text-center bg-red-100 text-red-800 px-4 py-2 rounded-md text-sm font-medium">
                                Event is rejected
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Statistics -->
                <div class="mt-8 bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statistics</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Total Applications</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $event->applications->count() }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-500">Approved Applications</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $event->applications->where('status', 'approved')->count() }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-500">Certificates Issued</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $event->certificates->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Event Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Reject Event</h3>
        </div>
        <form action="{{ route('admin.events.reject', $event) }}" method="POST">
            @csrf
            <div class="p-6">
                <div>
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                    Reject Event
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showRejectModal() {
        document.getElementById('rejectModal').style.display = 'flex';
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }
</script>
@endsection