@extends('layouts.app')

@section('title', 'My Certificates')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Certificates</h1>
        <p class="text-gray-600 mt-2">View and manage your volunteer service certificates</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Certificates</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $certificates->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Hours</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $certificates->sum('hours_completed') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">This Year</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $certificates->where('issued_date', '>=', now()->startOfYear())->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Shared</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $certificates->where('is_public', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Verification -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-blue-800">Certificate Verification</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Need to verify a certificate? <a href="{{ route('volunteer.certificates.verify-form') }}" class="font-medium underline hover:text-blue-600">Use our verification tool</a> to check certificate authenticity.</p>
                </div>
            </div>
        </div>
    </div>

    @if($certificates->count() > 0)
        <!-- Certificates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($certificates as $certificate)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <!-- Certificate Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="text-white">
                                <h3 class="font-semibold text-lg">{{ $certificate->title }}</h3>
                                <p class="text-blue-100 text-sm">{{ $certificate->certificate_number }}</p>
                            </div>
                            <div class="text-white">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($certificate->type === 'volunteer') bg-green-500
                                    @elseif($certificate->type === 'completion') bg-blue-500
                                    @else bg-purple-500 @endif">
                                    {{ ucfirst($certificate->type) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate Body -->
                    <div class="p-6">
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Event</p>
                                <p class="font-medium text-gray-900">{{ $certificate->event->title }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Organization</p>
                                <p class="font-medium text-gray-900">{{ $certificate->organization->name }}</p>
                            </div>
                            
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Hours</p>
                                    <p class="font-medium text-gray-900">{{ $certificate->hours_completed }} hrs</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Issued</p>
                                    <p class="font-medium text-gray-900">{{ $certificate->issued_date->format('M j, Y') }}</p>
                                </div>
                            </div>

                            @if($certificate->description)
                                <div>
                                    <p class="text-sm text-gray-600">Description</p>
                                    <p class="text-sm text-gray-900">{{ Str::limit($certificate->description, 100) }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Status Badges -->
                        <div class="flex items-center space-x-2 mt-4">
                            @if($certificate->is_verified)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Verified
                                </span>
                            @endif
                            
                            @if($certificate->is_public)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                    </svg>
                                    Public
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Certificate Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('volunteer.certificates.show', $certificate) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Details
                                </a>
                                
                                @if($certificate->file_path)
                                    <a href="{{ route('volunteer.certificates.download', $certificate) }}" 
                                       class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Download PDF
                                    </a>
                                @endif
                            </div>
                            
                            <form action="{{ route('volunteer.certificates.share', $certificate) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-gray-600 hover:text-gray-800 text-sm font-medium"
                                        title="{{ $certificate->is_public ? 'Make Private' : 'Make Public' }}">
                                    @if($certificate->is_public)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $certificates->links() }}
        </div>
    @else
        <!-- No Certificates -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Certificates Yet</h3>
            <p class="text-gray-600 mb-6">Complete volunteer events to earn certificates that recognize your service.</p>
            <a href="{{ route('events.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Browse Events
            </a>
        </div>
    @endif
</div>
@endsection