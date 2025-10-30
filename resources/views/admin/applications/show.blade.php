@extends('layouts.admin')

@section('title', 'Application Details')

@section('content')
<div class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-bold text-gray-900">Application Details</h1>
            <p class="mt-2 text-sm text-gray-700">View details for volunteer application.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            @if($application->status === 'pending')
                <form action="{{ route('admin.applications.approve', $application) }}" method="POST" class="inline-block mr-2">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto">
                        Approve
                    </button>
                </form>
                <form action="{{ route('admin.applications.reject', $application) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                        Reject
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Application Information</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($application->status === 'pending')
                                    <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($application->status === 'approved')
                                    <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">
                                        Rejected
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Applied At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->applied_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Event</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->event->title }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Organization</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->event->organization->name }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Motivation</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->motivation }}</dd>
                        </div>
                        @if($application->rejection_reason)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Rejection Reason</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->rejection_reason }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Volunteer Information</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-lg font-bold text-gray-700">{{ substr($application->user->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-bold text-gray-900">{{ $application->user->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $application->user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Events</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->user->applications()->where('status', 'approved')->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Hours</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->user->total_volunteer_hours ?? 0 }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Points</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->user->points ?? 0 }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection