@extends('organization.layouts.app')

@section('title', 'Application Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-4">
                                <li>
                                    <a href="{{ route('organization.volunteers.index') }}" class="text-gray-400 hover:text-gray-500">
                                        Applications
                                    </a>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="ml-4 text-sm font-medium text-gray-500">Application Details</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="mt-2 text-3xl font-bold text-gray-900">Application from {{ $application->user->name }}</h1>
                        <p class="mt-1 text-sm text-gray-600">Applied for {{ $application->event->title }}</p>
                    </div>
                    <div class="flex space-x-3">
                        @if($application->status === 'pending')
                            <form method="POST" action="{{ route('organization.volunteers.approve', $application) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700 transition-colors" onclick="return confirm('Are you sure you want to approve this application?')">
                                    Approve Application
                                </button>
                            </form>
                            <form method="POST" action="{{ route('organization.volunteers.reject', $application) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors" onclick="return confirm('Are you sure you want to reject this application?')">
                                    Reject Application
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('organization.volunteers.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700 transition-colors">
                            Back to Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Application Status -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Application Status</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($application->status === 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Applied {{ $application->created_at->format('M j, Y \a\t g:i A') }}
                            </div>
                        </div>
                        @if($application->status_updated_at && $application->status !== 'pending')
                            <div class="mt-2 text-sm text-gray-500">
                                Status updated {{ $application->status_updated_at->format('M j, Y \a\t g:i A') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Volunteer Information -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Volunteer Information</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $application->user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $application->user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $application->user->phone ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $application->user->date_of_birth ? $application->user->date_of_birth->format('M j, Y') : 'Not provided' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Motivation -->
                @if($application->motivation)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Motivation</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $application->motivation }}</p>
                    </div>
                </div>
                @endif

                <!-- Skills -->
                @if($application->skills && count($application->skills) > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Skills & Expertise</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($application->skills as $skill)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Availability -->
                @if($application->availability)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Availability</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $application->availability }}</p>
                    </div>
                </div>
                @endif

                <!-- Emergency Contact -->
                @if($application->emergency_contact)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Emergency Contact</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $application->emergency_contact['name'] ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $application->emergency_contact['phone'] ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Custom Responses -->
                @if($application->custom_responses && count($application->custom_responses) > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Additional Questions</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        @foreach($application->custom_responses as $question => $answer)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ $question }}</label>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $answer }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Event Information -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Event Details</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Event Title</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $application->event->title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date & Time</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $application->event->start_date->format('M j, Y') }}
                                @if($application->event->start_time)
                                    at {{ $application->event->start_time->format('g:i A') }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Location</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $application->event->location }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $application->event->category->name ?? 'Uncategorized' }}</p>
                        </div>
                        <div>
                            <a href="{{ route('organization.events.show', $application->event) }}" class="inline-flex items-center text-sm text-red-600 hover:text-red-700">
                                View Event Details
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Application Statistics -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Event Statistics</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Applications</span>
                            <span class="text-sm font-medium text-gray-900">{{ $application->event->applications_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Approved</span>
                            <span class="text-sm font-medium text-gray-900">{{ $application->event->approved_applications_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Pending</span>
                            <span class="text-sm font-medium text-gray-900">{{ $application->event->pending_applications_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Available Spots</span>
                            <span class="text-sm font-medium text-gray-900">
                                @if($application->event->max_volunteers)
                                    {{ max(0, $application->event->max_volunteers - ($application->event->approved_applications_count ?? 0)) }}
                                @else
                                    Unlimited
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Volunteer Profile -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Volunteer Profile</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="text-center">
                            <div class="mx-auto h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-xl font-medium text-gray-700">
                                    {{ substr($application->user->name, 0, 1) }}
                                </span>
                            </div>
                            <h4 class="mt-2 text-lg font-medium text-gray-900">{{ $application->user->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $application->user->email }}</p>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Member Since</span>
                            <span class="text-sm font-medium text-gray-900">{{ $application->user->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Applications</span>
                            <span class="text-sm font-medium text-gray-900">{{ $application->user->applications_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Events Completed</span>
                            <span class="text-sm font-medium text-gray-900">{{ $application->user->completed_events_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action History -->
                @if($application->status !== 'pending')
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Action History</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Application submitted</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $application->created_at->format('M j, Y g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @if($application->status_updated_at)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full 
                                                    @if($application->status === 'approved') bg-green-500
                                                    @else bg-red-500 @endif 
                                                    flex items-center justify-center ring-8 ring-white">
                                                    @if($application->status === 'approved')
                                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Application {{ $application->status }}</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $application->status_updated_at->format('M j, Y g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection