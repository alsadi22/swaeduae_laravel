@extends('layouts.organization')

@section('title', 'Emergency Communications')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Emergency Communications</h1>
                <p class="mt-2 text-gray-600">Manage emergency communications for "{{ $event->title }}"</p>
            </div>
            <a href="{{ route('organization.emergency-communications.create', $event) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Send Emergency Message
            </a>
        </div>
    </div>

    <!-- Emergency Communications List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($emergencyCommunications->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($emergencyCommunications as $communication)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-red-600 truncate">
                                    {{ $communication->title }}
                                </div>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $communication->priority === 'critical' ? 'red' : ($communication->priority === 'high' ? 'orange' : ($communication->priority === 'normal' ? 'yellow' : 'gray')) }}-100 text-{{ $communication->priority === 'critical' ? 'red' : ($communication->priority === 'high' ? 'orange' : ($communication->priority === 'normal' ? 'yellow' : 'gray')) }}-800">
                                        {{ ucfirst($communication->priority) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        {{ Str::limit($communication->content, 100) }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>
                                        Sent {{ $communication->sent_at->diffForHumans() }} by {{ $communication->creator->name }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-4 flex space-x-3">
                                <a href="{{ route('organization.emergency-communications.show', [$event, $communication]) }}" 
                                   class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                    View Details
                                </a>
                                
                                <form method="POST" action="{{ route('organization.emergency-communications.destroy', [$event, $communication]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-900" 
                                            onclick="return confirm('Are you sure you want to delete this emergency communication?')">
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
                {{ $emergencyCommunications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2zM9 9l3 3-3 3"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No emergency communications</h3>
                <p class="mt-1 text-sm text-gray-500">Emergency communications sent during the event will appear here.</p>
                <div class="mt-6">
                    <a href="{{ route('organization.emergency-communications.create', $event) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Send Emergency Message
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection