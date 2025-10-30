@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Event Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-blue-600">
                                    <i class="fas fa-calendar-alt mr-2"></i>Events
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                    <span class="text-gray-500">{{ Str::limit($event->title, 50) }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
                
                @auth
                    @if(auth()->user()?->hasRole(['admin', 'super-admin']) || 
                        (auth()->user()?->hasRole(['organization-manager', 'organization-staff']) && 
                         auth()->user()?->organizations->contains($event->organization_id)))
                        <div class="flex space-x-2">
                            <a href="{{ route('organization.events.edit', $event) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                                <i class="fas fa-edit mr-2"></i>Edit Event
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Event Image -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="h-64 md:h-96 relative">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-purple-500">
                                <i class="fas fa-hands-helping text-white text-6xl"></i>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4">
                            @if($event->status === 'published')
                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">Published</span>
                            @elseif($event->status === 'pending')
                                <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-medium">Pending Approval</span>
                            @elseif($event->status === 'completed')
                                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">Completed</span>
                            @endif
                        </div>

                        <!-- Featured Badge -->
                        @if($event->is_featured)
                            <div class="absolute top-4 left-4">
                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Event Details -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">{{ $event->category }}</span>
                    </div>

                    <!-- Event Meta -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar text-blue-500 mr-3"></i>
                            <div>
                                <div class="font-medium">{{ $event->start_date->format('l, F j, Y') }}</div>
                                <div class="text-sm">{{ $event->start_time }} - {{ $event->end_time }}</div>
                            </div>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt text-red-500 mr-3"></i>
                            <div>
                                <div class="font-medium">{{ $event->location }}</div>
                                <div class="text-sm">{{ $event->city }}, {{ $event->emirate }}</div>
                            </div>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-building text-green-500 mr-3"></i>
                            <div>
                                <div class="font-medium">{{ $event->organization->name }}</div>
                                <div class="text-sm">Verified Organization</div>
                            </div>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-clock text-purple-500 mr-3"></i>
                            <div>
                                <div class="font-medium">{{ $event->volunteer_hours }} Hours</div>
                                <div class="text-sm">Volunteer Time</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($event->tags && count($event->tags) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($event->tags as $tag)
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">About This Event</h3>
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>

                    <!-- Requirements -->
                    @if($event->requirements)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Requirements</h3>
                            <div class="prose max-w-none text-gray-700">
                                {!! nl2br(e($event->requirements)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Skills Required -->
                    @if($event->skills_required && count($event->skills_required) > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Skills Required</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($event->skills_required as $skill)
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Gallery -->
                    @if($event->gallery && count($event->gallery) > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Gallery</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($event->gallery as $image)
                                    <img src="{{ Storage::url($image) }}" alt="Event gallery" 
                                         class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition duration-200"
                                         onclick="openImageModal('{{ Storage::url($image) }}')">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Application Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Information</h3>
                    
                    <!-- Volunteer Spots -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Available Spots</span>
                            <span class="font-semibold text-gray-900">{{ $event->available_spots }} / {{ $event->max_volunteers }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 60%"></div>
                        </div>
                    </div>

                    <!-- Age Requirements -->
                    @if($event->min_age || $event->max_age)
                        <div class="mb-4">
                            <span class="text-gray-600">Age Requirement</span>
                            <div class="font-semibold text-gray-900">
                                @if($event->min_age && $event->max_age)
                                    {{ $event->min_age }} - {{ $event->max_age }} years
                                @elseif($event->min_age)
                                    {{ $event->min_age }}+ years
                                @else
                                    Up to {{ $event->max_age }} years
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Application Deadline -->
                    @if($event->application_deadline)
                        <div class="mb-4">
                            <span class="text-gray-600">Application Deadline</span>
                            <div class="font-semibold text-gray-900">{{ $event->application_deadline->format('M j, Y') }}</div>
                        </div>
                    @endif

                    <!-- Application Button -->
                    @auth
                        @if(auth()->user()?->hasRole('volunteer'))
                            @php
                                $userApplication = $event->applications()->where('user_id', auth()->id())->first();
                            @endphp
                            
                            @if($userApplication)
                                <div class="text-center">
                                    @if($userApplication->status === 'pending')
                                        <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded-lg mb-3">
                                            <i class="fas fa-clock mr-2"></i>Application Pending
                                        </div>
                                    @elseif($userApplication->status === 'approved')
                                        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-3">
                                            <i class="fas fa-check-circle mr-2"></i>Application Approved
                                        </div>
                                    @elseif($userApplication->status === 'rejected')
                                        <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-3">
                                            <i class="fas fa-times-circle mr-2"></i>Application Rejected
                                        </div>
                                    @endif
                                    <a href="{{ route('volunteer.applications.show', $userApplication) }}" 
                                       class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                                        View Application
                                    </a>
                                </div>
                            @elseif($event->applications_open)
                                <a href="{{ route('volunteer.events.apply', $event) }}" 
                                   class="block w-full bg-green-600 text-white text-center py-3 rounded-lg hover:bg-green-700 transition duration-200">
                                    <i class="fas fa-hand-paper mr-2"></i>Apply to Volunteer
                                </a>
                            @elseif($event->is_full)
                                <div class="bg-gray-100 text-gray-600 text-center py-3 rounded-lg">
                                    <i class="fas fa-users mr-2"></i>Event is Full
                                </div>
                            @elseif($event->application_deadline && $event->application_deadline < now())
                                <div class="bg-gray-100 text-gray-600 text-center py-3 rounded-lg">
                                    <i class="fas fa-calendar-times mr-2"></i>Applications Closed
                                </div>
                            @else
                                <div class="bg-gray-100 text-gray-600 text-center py-3 rounded-lg">
                                    <i class="fas fa-info-circle mr-2"></i>Applications Not Open
                                </div>
                            @endif
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                            Login to Apply
                        </a>
                    @endauth
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-user text-gray-400 mr-3"></i>
                            <span class="text-gray-700">{{ $event->contact_person }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-gray-400 mr-3"></i>
                            <a href="mailto:{{ $event->contact_email }}" class="text-blue-600 hover:text-blue-800">{{ $event->contact_email }}</a>
                        </div>
                        @if($event->contact_phone)
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-3"></i>
                                <a href="tel:{{ $event->contact_phone }}" class="text-blue-600 hover:text-blue-800">{{ $event->contact_phone }}</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Location Map -->
                @if($event->latitude && $event->longitude)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Location</h3>
                        <div class="mb-3">
                            <div class="font-medium text-gray-900">{{ $event->location }}</div>
                            <div class="text-gray-600 text-sm">{{ $event->address }}</div>
                        </div>
                        <div class="h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <i class="fas fa-map-marked-alt text-2xl mb-2"></i>
                                <div class="text-sm">Map integration coming soon</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <img id="modalImage" src="" alt="Gallery image" class="max-w-full max-h-full object-contain">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Close modal when clicking outside the image
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
</script>
@endpush