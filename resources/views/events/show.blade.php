@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="{{ route('events.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            {{ __('Back to Events') }}
        </a>

        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="h-80 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                <svg class="w-32 h-32 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>

            <div class="p-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $event->title ?? 'Event Title' }}</h1>
                
                <div class="flex items-center gap-6 mb-6">
                    <div>
                        <p class="text-gray-600 text-sm">{{ __('Organized by') }}</p>
                        <p class="text-xl font-semibold text-gray-900">{{ $event->organization->name ?? 'Organization' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">{{ __('Category') }}</p>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $event->category ?? 'General' }}
                        </span>
                    </div>
                </div>

                <!-- Event Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 py-6 border-t border-gray-200">
                    <div>
                        <p class="text-gray-600 text-sm">{{ __('Date & Time') }}</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $event->start_date ? $event->start_date->format('M d, Y h:i A') : 'TBA' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">{{ __('Duration') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $event->duration ?? '0' }} {{ __('hours') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">{{ __('Volunteers Needed') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $event->volunteers_needed ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">{{ __('Volunteers Signed Up') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $event->applications_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('About This Event') }}</h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {{ $event->description ?? 'No description available.' }}
                    </div>
                </div>

                <!-- Requirements -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Requirements') }}</h2>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">{{ $event->requirements ?? 'No specific requirements' }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Location -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Location') }}</h2>
                    <div class="flex items-start gap-4">
                        <svg class="w-6 h-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        <div>
                            <p class="text-lg font-semibold text-gray-900">{{ $event->location ?? 'Location TBA' }}</p>
                            <p class="text-gray-600">{{ $event->address ?? 'Full address not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Application Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('Ready to Volunteer?') }}</h3>
                    
                    @auth
                        @if(auth()->user()->hasApplied($event->id ?? null))
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                                <p class="text-green-800 font-semibold">{{ __('✓ You Applied') }}</p>
                                <p class="text-sm text-green-700 mt-1">{{ __('Waiting for approval') }}</p>
                            </div>
                        @else
                            <button class="w-full px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                                {{ __('Apply Now') }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition text-center">
                            {{ __('Sign In to Apply') }}
                        </a>
                    @endauth

                    <button class="w-full mt-3 px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                        {{ __('Save Event') }}
                    </button>
                </div>

                <!-- Share Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Share This Event') }}</h3>
                    <div class="space-y-2">
                        <button class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium">
                            {{ __('Share on Facebook') }}
                        </button>
                        <button class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium">
                            {{ __('Share on Twitter') }}
                        </button>
                        <button class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium">
                            {{ __('Share on WhatsApp') }}
                        </button>
                    </div>
                </div>

                <!-- Contact Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Questions?') }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ __('Contact the organizer directly.') }}</p>
                    <a href="mailto:{{ $event->organization->email ?? '#' }}" class="block w-full px-4 py-2 bg-gray-100 text-gray-900 text-center font-medium rounded-lg hover:bg-gray-200 transition">
                        {{ __('Send Email') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
