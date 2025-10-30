@extends('layouts.app')

@section('title', 'Application Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
                        <a href="{{ route('volunteer.applications.index') }}" class="hover:text-blue-600">My Applications</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-gray-900">Application Details</span>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900">Application Details</h1>
                </div>
                
                <!-- Status Badge -->
                @php
                    $statusClasses = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'completed' => 'bg-blue-100 text-blue-800',
                    ];
                @endphp
                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium {{ $statusClasses[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                    @switch($application->status)
                        @case('pending')
                            <i class="fas fa-clock mr-2"></i>
                            Pending Review
                            @break
                        @case('approved')
                            <i class="fas fa-check-circle mr-2"></i>
                            Approved
                            @break
                        @case('rejected')
                            <i class="fas fa-times-circle mr-2"></i>
                            Rejected
                            @break
                        @case('completed')
                            <i class="fas fa-trophy mr-2"></i>
                            Completed
                            @break
                        @default
                            {{ ucfirst($application->status) }}
                    @endswitch
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Event Information -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Event Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            @if($application->event->featured_image)
                                <img src="{{ Storage::url($application->event->featured_image) }}" 
                                     alt="{{ $application->event->title }}"
                                     class="w-20 h-20 object-cover rounded-lg">
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-2xl text-gray-400"></i>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('volunteer.events.show', $application->event) }}" 
                                       class="hover:text-blue-600 transition duration-200">
                                        {{ $application->event->title }}
                                    </a>
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <i class="fas fa-building mr-2 text-gray-400"></i>
                                        {{ $application->event->organization->name }}
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                        {{ $application->event->start_date->format('M j, Y') }}
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-clock mr-2 text-gray-400"></i>
                                        {{ $application->event->start_time }} - {{ $application->event->end_time }}
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                        {{ $application->event->city }}, {{ $application->event->emirate }}
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-users mr-2 text-gray-400"></i>
                                        {{ $application->event->volunteer_hours }} volunteer hours
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-tag mr-2 text-gray-400"></i>
                                        {{ $application->event->category->name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Details -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Application Details</h2>
                            @if($application->status === 'pending')
                                <button onclick="toggleEditMode()" 
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit Application
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- View Mode -->
                        <div id="viewMode">
                            <!-- Motivation -->
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-900 mb-2">Motivation</h3>
                                <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $application->motivation }}</p>
                            </div>

                            <!-- Skills -->
                            @if($application->skills && count($application->skills) > 0)
                                <div class="mb-6">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">Skills & Expertise</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($application->skills as $skill)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Availability -->
                            @if($application->availability)
                                <div class="mb-6">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">Availability</h3>
                                    <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $application->availability }}</p>
                                </div>
                            @endif

                            <!-- Experience -->
                            @if($application->experience)
                                <div class="mb-6">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">Previous Experience</h3>
                                    <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $application->experience }}</p>
                                </div>
                            @endif

                            <!-- Emergency Contact -->
                            @if($application->emergency_contact)
                                <div class="mb-6">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">Emergency Contact</h3>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-gray-700">
                                            <span class="font-medium">Name:</span> {{ $application->emergency_contact['name'] ?? 'N/A' }}
                                        </p>
                                        <p class="text-gray-700">
                                            <span class="font-medium">Phone:</span> {{ $application->emergency_contact['phone'] ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Custom Responses -->
                            @if($application->custom_responses && count($application->custom_responses) > 0)
                                <div class="mb-6">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">Additional Information</h3>
                                    <div class="space-y-4">
                                        @foreach($application->custom_responses as $question => $answer)
                                            <div class="bg-gray-50 p-4 rounded-lg">
                                                <p class="font-medium text-gray-900 mb-2">{{ $question }}</p>
                                                <p class="text-gray-700">{{ $answer }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Edit Mode (Hidden by default) -->
                        @if($application->status === 'pending')
                            <form id="editMode" style="display: none;" 
                                  action="{{ route('volunteer.applications.update', $application) }}" 
                                  method="POST">
                                @csrf
                                @method('PUT')
                                
                                <!-- Motivation -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-900 mb-2">
                                        Motivation <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="motivation" 
                                              rows="4" 
                                              required
                                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Why do you want to volunteer for this event?">{{ old('motivation', $application->motivation) }}</textarea>
                                </div>

                                <!-- Skills -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-900 mb-2">Skills & Expertise</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
                                        @php
                                            $availableSkills = [
                                                'Communication', 'Leadership', 'Teamwork', 'Problem Solving',
                                                'Event Planning', 'Social Media', 'Photography', 'First Aid',
                                                'Teaching', 'Translation', 'Computer Skills', 'Driving'
                                            ];
                                        @endphp
                                        @foreach($availableSkills as $skill)
                                            <label class="flex items-center">
                                                <input type="checkbox" 
                                                       name="skills[]" 
                                                       value="{{ $skill }}"
                                                       {{ in_array($skill, $application->skills ?? []) ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">{{ $skill }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <input type="text" 
                                           name="other_skills" 
                                           placeholder="Other skills (comma-separated)"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Availability -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-900 mb-2">Availability</label>
                                    <textarea name="availability" 
                                              rows="3"
                                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Please describe your availability for this event">{{ old('availability', $application->availability) }}</textarea>
                                </div>

                                <!-- Emergency Contact -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-900 mb-2">Emergency Contact</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <input type="text" 
                                               name="emergency_name" 
                                               placeholder="Emergency contact name"
                                               value="{{ old('emergency_name', $application->emergency_contact['name'] ?? '') }}"
                                               class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <input type="tel" 
                                               name="emergency_phone" 
                                               placeholder="Emergency contact phone"
                                               value="{{ old('emergency_phone', $application->emergency_contact['phone'] ?? '') }}"
                                               class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-4">
                                    <button type="submit" 
                                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                                        Update Application
                                    </button>
                                    <button type="button" 
                                            onclick="toggleEditMode()"
                                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition duration-200">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Rejection Reason -->
                @if($application->status === 'rejected' && $application->rejection_reason)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-3"></i>
                            <div>
                                <h3 class="text-lg font-medium text-red-900 mb-2">Application Rejected</h3>
                                <p class="text-red-800">{{ $application->rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Application Timeline -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Timeline</h3>
                    <div class="space-y-4">
                        <!-- Applied -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-paper-plane text-blue-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Application Submitted</p>
                                <p class="text-xs text-gray-600">{{ $application->applied_at->format('M j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>

                        <!-- Reviewed -->
                        @if($application->reviewed_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-{{ $application->status === 'approved' ? 'green' : 'red' }}-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-{{ $application->status === 'approved' ? 'check' : 'times' }} text-{{ $application->status === 'approved' ? 'green' : 'red' }}-600 text-sm"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        Application {{ ucfirst($application->status) }}
                                    </p>
                                    <p class="text-xs text-gray-600">{{ $application->reviewed_at->format('M j, Y \a\t g:i A') }}</p>
                                    @if($application->reviewedBy)
                                        <p class="text-xs text-gray-600">by {{ $application->reviewedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Event Date -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-gray-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Event Date</p>
                                <p class="text-xs text-gray-600">{{ $application->event->start_date->format('M j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('volunteer.events.show', $application->event) }}" 
                           class="block w-full text-center bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                            View Event Details
                        </a>
                        
                        @if($application->status === 'pending')
                            <form action="{{ route('volunteer.applications.destroy', $application) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to withdraw this application?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="block w-full text-center bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200">
                                    Withdraw Application
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('volunteer.applications.index') }}" 
                           class="block w-full text-center bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition duration-200">
                            Back to Applications
                        </a>
                    </div>
                </div>

                <!-- Contact Organization -->
                @if($application->status === 'approved')
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Organization</h3>
                        <div class="space-y-3">
                            @if($application->event->contact_email)
                                <a href="mailto:{{ $application->event->contact_email }}" 
                                   class="flex items-center text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-envelope mr-2"></i>
                                    {{ $application->event->contact_email }}
                                </a>
                            @endif
                            @if($application->event->contact_phone)
                                <a href="tel:{{ $application->event->contact_phone }}" 
                                   class="flex items-center text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-phone mr-2"></i>
                                    {{ $application->event->contact_phone }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleEditMode() {
        const viewMode = document.getElementById('viewMode');
        const editMode = document.getElementById('editMode');
        
        if (viewMode.style.display === 'none') {
            viewMode.style.display = 'block';
            editMode.style.display = 'none';
        } else {
            viewMode.style.display = 'none';
            editMode.style.display = 'block';
        }
    }
</script>
@endpush