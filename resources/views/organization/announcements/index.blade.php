@extends('layouts.organization')

@section('title', 'Event Announcements')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Announcements</h1>
                <p class="mt-2 text-gray-600">Manage announcements for "{{ $event->title }}"</p>
            </div>
            <a href="{{ route('organization.announcements.create', $event) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Announcement
            </a>
        </div>
    </div>

    <!-- Announcements List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($announcements->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($announcements as $announcement)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-blue-600 truncate">
                                    {{ $announcement->title }}
                                </div>
                                <div class="ml-2 flex-shrink-0 flex">
                                    @if($announcement->is_published)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Published
                                        </span>
                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $announcement->type === 'urgent' ? 'red' : ($announcement->type === 'update' ? 'yellow' : 'blue') }}-100 text-{{ $announcement->type === 'urgent' ? 'red' : ($announcement->type === 'update' ? 'yellow' : 'blue') }}-800">
                                            {{ ucfirst($announcement->type) }}
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Draft
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        {{ Str::limit($announcement->content, 100) }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>
                                        {{ $announcement->created_at->diffForHumans() }}
                                        @if($announcement->published_at)
                                            • Published {{ $announcement->published_at->diffForHumans() }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="mt-4 flex space-x-3">
                                <a href="{{ route('organization.announcements.edit', [$event, $announcement]) }}" 
                                   class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                    Edit
                                </a>
                                
                                @if($announcement->is_published)
                                    <form method="POST" action="{{ route('organization.announcements.unpublish', [$event, $announcement]) }}">
                                        @csrf
                                        <button type="submit" class="text-sm font-medium text-yellow-600 hover:text-yellow-900">
                                            Unpublish
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('organization.announcements.publish', [$event, $announcement]) }}">
                                        @csrf
                                        <button type="submit" class="text-sm font-medium text-green-600 hover:text-green-900">
                                            Publish
                                        </button>
                                    </form>
                                @endif
                                
                                <form method="POST" action="{{ route('organization.announcements.destroy', [$event, $announcement]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-900" 
                                            onclick="return confirm('Are you sure you want to delete this announcement?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            
            <!-- Pagination -->
            <div class="px-4 py-4 border-t border-gray-200 sm:px-6">
                {{ $announcements->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2zM9 9l3 3-3 3"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No announcements</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new announcement.</p>
                <div class="mt-6">
                    <a href="{{ route('organization.announcements.create', $event) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Announcement
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection