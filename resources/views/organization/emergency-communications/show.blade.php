@extends('layouts.organization')

@section('title', 'Emergency Communication Details')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Emergency Communication</h1>
                <p class="mt-2 text-gray-600">Details for "{{ $emergencyCommunication->title }}"</p>
            </div>
            <a href="{{ route('organization.emergency-communications.index', $event) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Back to Emergency Communications
            </a>
        </div>
    </div>

    <!-- Communication Details -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $emergencyCommunication->title }}
                </h3>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $emergencyCommunication->priority === 'critical' ? 'red' : ($emergencyCommunication->priority === 'high' ? 'orange' : ($emergencyCommunication->priority === 'normal' ? 'yellow' : 'gray')) }}-100 text-{{ $emergencyCommunication->priority === 'critical' ? 'red' : ($emergencyCommunication->priority === 'high' ? 'orange' : ($emergencyCommunication->priority === 'normal' ? 'yellow' : 'gray')) }}-800">
                    {{ ucfirst($emergencyCommunication->priority) }} Priority
                </span>
            </div>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Sent {{ $emergencyCommunication->sent_at->diffForHumans() }} by {{ $emergencyCommunication->creator->name }}
            </p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Event
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $emergencyCommunication->event->title }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Sent At
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $emergencyCommunication->sent_at->format('F j, Y \a\t g:i A') }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Communication Channels
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex space-x-4">
                            @if($emergencyCommunication->send_sms)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    SMS
                                </span>
                            @endif
                            @if($emergencyCommunication->send_email)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Email
                                </span>
                            @endif
                            @if($emergencyCommunication->send_push)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Push Notification
                                </span>
                            @endif
                            @if(!$emergencyCommunication->send_sms && !$emergencyCommunication->send_email && !$emergencyCommunication->send_push)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    None
                                </span>
                            @endif
                        </div>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Message Content
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="whitespace-pre-wrap">{{ $emergencyCommunication->content }}</p>
                        </div>
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Recipients
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($emergencyCommunication->recipient_filters['type'] === 'all')
                            <p>All event participants ({{ $event->approvedApplications()->count() }} volunteers)</p>
                        @else
                            <p>Selected participants ({{ count($emergencyCommunication->recipient_filters['recipients'] ?? []) }} volunteers)</p>
                            @if(count($emergencyCommunication->recipient_filters['recipients'] ?? []) > 0)
                                <div class="mt-2 max-h-40 overflow-y-auto">
                                    <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                        @foreach($emergencyCommunication->recipient_filters['recipients'] ?? [] as $recipientId)
                                            @php
                                                $recipient = \App\Models\User::find($recipientId);
                                            @endphp
                                            @if($recipient)
                                                <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                                    <div class="w-0 flex-1 flex items-center">
                                                        <span class="ml-2 flex-1 w-0 truncate">
                                                            {{ $recipient->name }} ({{ $recipient->phone }})
                                                        </span>
                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex justify-end">
        <form method="POST" action="{{ route('organization.emergency-communications.destroy', [$event, $emergencyCommunication]) }}">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    onclick="return confirm('Are you sure you want to delete this emergency communication?')">
                Delete Communication
            </button>
        </form>
    </div>
</div>
@endsection