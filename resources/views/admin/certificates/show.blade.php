@extends('admin.layouts.app')

@section('title', 'Certificate Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Certificate Details</h1>
                        <p class="mt-1 text-sm text-gray-600">View detailed information about certificate {{ $certificate->certificate_number }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.certificates.index') }}" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-900 transition-colors">
                            Back to Certificates
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Certificate Info -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Certificate Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Certificate Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $certificate->certificate_number }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Verification Code</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $certificate->verification_code }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Title</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $certificate->title }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Hours Completed</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $certificate->hours_completed ?? 0 }} hours</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Event Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $certificate->event_date ? $certificate->event_date->format('F d, Y') : 'N/A' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Issued Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $certificate->issued_date ? $certificate->issued_date->format('F d, Y') : 'N/A' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($certificate->is_verified)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Unverified
                                        </span>
                                    @endif
                                </p>
                            </div>
                            
                            @if($certificate->is_verified && $certificate->verified_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Verified At</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $certificate->verified_at->format('F d, Y g:i A') }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Verified By</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $certificate->verifier->name ?? 'Unknown' }}</p>
                                </div>
                            @endif
                        </div>
                        
                        @if($certificate->description)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-500">Description</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $certificate->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- User -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">User</h3>
                    </div>
                    <div class="p-6">
                        @if($certificate->user)
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-700 font-medium">{{ substr($certificate->user->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $certificate->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $certificate->user->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500">No user information available.</p>
                        @endif
                    </div>
                </div>
                
                <!-- Event -->
                <div class="mt-8 bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Event</h3>
                    </div>
                    <div class="p-6">
                        @if($certificate->event)
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $certificate->event->title }}</p>
                                <p class="text-sm text-gray-500">{{ $certificate->event->organization->name ?? 'Unknown Organization' }}</p>
                                @if($certificate->event->start_date)
                                    <p class="text-sm text-gray-500 mt-1">{{ $certificate->event->start_date->format('F d, Y') }}</p>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500">No event information available.</p>
                        @endif
                    </div>
                </div>
                
                <!-- Organization -->
                <div class="mt-8 bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Organization</h3>
                    </div>
                    <div class="p-6">
                        @if($certificate->organization)
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $certificate->organization->name }}</p>
                                <p class="text-sm text-gray-500">{{ $certificate->organization->email }}</p>
                            </div>
                        @else
                            <p class="text-gray-500">No organization information available.</p>
                        @endif
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="mt-8 bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($certificate->is_verified)
                            <form action="{{ route('admin.certificates.revoke', $certificate) }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors" onclick="return confirm('Are you sure you want to revoke this certificate?')">
                                    Revoke Certificate
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('admin.certificates.index') }}" class="block w-full text-center bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700 transition-colors">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection