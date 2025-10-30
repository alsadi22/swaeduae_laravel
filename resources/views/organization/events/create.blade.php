@extends('layouts.organization')

@section('title', 'Create New Event')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Event</h1>
        <p class="text-gray-600">Follow the steps below to create your volunteer event</p>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            @for($i = 1; $i <= 4; $i++)
                <div class="flex items-center {{ $i < 4 ? 'flex-1' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 
                        {{ $step >= $i ? 'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-500' }}">
                        @if($step > $i)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            {{ $i }}
                        @endif
                    </div>
                    <div class="ml-3 {{ $i < 4 ? 'flex-1' : '' }}">
                        <p class="text-sm font-medium {{ $step >= $i ? 'text-blue-600' : 'text-gray-500' }}">
                            Step {{ $i }}
                        </p>
                        <p class="text-xs text-gray-500">
                            @switch($i)
                                @case(1) Basic Information @break
                                @case(2) Date & Location @break
                                @case(3) Requirements @break
                                @case(4) Contact & Media @break
                            @endswitch
                        </p>
                    </div>
                    @if($i < 4)
                        <div class="flex-1 h-0.5 mx-4 {{ $step > $i ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('organization.events.store-step') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @csrf
        <input type="hidden" name="step" value="{{ $step }}">

        @if($step == 1)
            <!-- Step 1: Basic Information -->
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>

                <!-- Event Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $eventData['title'] ?? '') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter a compelling event title">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select id="category" 
                            name="category" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('category', $eventData['category'] ?? '') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Event Description *</label>
                    <textarea id="description" 
                              name="description" 
                              rows="6" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Describe your event in detail. What will volunteers be doing? What impact will they make?">{{ old('description', $eventData['description'] ?? '') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Minimum 50 characters. Be descriptive to attract more volunteers.</p>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Requirements -->
                <div>
                    <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Special Requirements</label>
                    <textarea id="requirements" 
                              name="requirements" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Any special requirements, equipment needed, or preparation instructions for volunteers">{{ old('requirements', $eventData['requirements'] ?? '') }}</textarea>
                    @error('requirements')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

        @elseif($step == 2)
            <!-- Step 2: Date & Location -->
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Date & Location</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                        <input type="date" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ old('start_date', $eventData['start_date'] ?? '') }}"
                               required
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                        <input type="date" 
                               id="end_date" 
                               name="end_date" 
                               value="{{ old('end_date', $eventData['end_date'] ?? '') }}"
                               required
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                        <input type="time" 
                               id="start_time" 
                               name="start_time" 
                               value="{{ old('start_time', $eventData['start_time'] ?? '') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Time -->
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                        <input type="time" 
                               id="end_time" 
                               name="end_time" 
                               value="{{ old('end_time', $eventData['end_time'] ?? '') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location Name *</label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           value="{{ old('location', $eventData['location'] ?? '') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., Dubai Marina Beach, Community Center">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Full Address *</label>
                    <textarea id="address" 
                              name="address" 
                              rows="2"
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Enter the complete address with landmarks">{{ old('address', $eventData['address'] ?? '') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                        <input type="text" 
                               id="city" 
                               name="city" 
                               value="{{ old('city', $eventData['city'] ?? '') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., Dubai, Abu Dhabi">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Emirate -->
                    <div>
                        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">Emirate *</label>
                        <select id="emirate" 
                                name="emirate" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Emirate</option>
                            @foreach($emirates as $emirate)
                                <option value="{{ $emirate }}" {{ old('emirate', $eventData['emirate'] ?? '') === $emirate ? 'selected' : '' }}>
                                    {{ $emirate }}
                                </option>
                            @endforeach
                        </select>
                        @error('emirate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

        @elseif($step == 3)
            <!-- Step 3: Requirements -->
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Volunteer Requirements</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Max Volunteers -->
                    <div>
                        <label for="max_volunteers" class="block text-sm font-medium text-gray-700 mb-2">Maximum Volunteers *</label>
                        <input type="number" 
                               id="max_volunteers" 
                               name="max_volunteers" 
                               value="{{ old('max_volunteers', $eventData['max_volunteers'] ?? '') }}"
                               required
                               min="1"
                               max="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('max_volunteers')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Volunteer Hours -->
                    <div>
                        <label for="volunteer_hours" class="block text-sm font-medium text-gray-700 mb-2">Volunteer Hours *</label>
                        <input type="number" 
                               id="volunteer_hours" 
                               name="volunteer_hours" 
                               value="{{ old('volunteer_hours', $eventData['volunteer_hours'] ?? '') }}"
                               required
                               min="0.5"
                               max="24"
                               step="0.5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('volunteer_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Min Age -->
                    <div>
                        <label for="min_age" class="block text-sm font-medium text-gray-700 mb-2">Minimum Age</label>
                        <input type="number" 
                               id="min_age" 
                               name="min_age" 
                               value="{{ old('min_age', $eventData['min_age'] ?? '') }}"
                               min="13"
                               max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('min_age')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Age -->
                    <div>
                        <label for="max_age" class="block text-sm font-medium text-gray-700 mb-2">Maximum Age</label>
                        <input type="number" 
                               id="max_age" 
                               name="max_age" 
                               value="{{ old('max_age', $eventData['max_age'] ?? '') }}"
                               min="13"
                               max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('max_age')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Skills Required -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Skills Required</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($skillsOptions as $skill)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="skills_required[]" 
                                       value="{{ $skill }}"
                                       {{ in_array($skill, old('skills_required', $eventData['skills_required'] ?? [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">{{ $skill }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('skills_required')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Application Settings -->
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="requires_application" 
                               name="requires_application" 
                               value="1"
                               {{ old('requires_application', $eventData['requires_application'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="requires_application" class="ml-2 text-sm text-gray-700">
                            Require application approval (recommended)
                        </label>
                    </div>

                    <div id="application_deadline_container" class="{{ old('requires_application', $eventData['requires_application'] ?? true) ? '' : 'hidden' }}">
                        <label for="application_deadline" class="block text-sm font-medium text-gray-700 mb-2">Application Deadline</label>
                        <input type="date" 
                               id="application_deadline" 
                               name="application_deadline" 
                               value="{{ old('application_deadline', $eventData['application_deadline'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to accept applications until event starts</p>
                        @error('application_deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

        @elseif($step == 4)
            <!-- Step 4: Contact & Media -->
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Information & Media</h2>

                <!-- Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person *</label>
                        <input type="text" 
                               id="contact_person" 
                               name="contact_person" 
                               value="{{ old('contact_person', $eventData['contact_person'] ?? '') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('contact_person')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone *</label>
                        <input type="tel" 
                               id="contact_phone" 
                               name="contact_phone" 
                               value="{{ old('contact_phone', $eventData['contact_phone'] ?? '') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Contact Email *</label>
                    <input type="email" 
                           id="contact_email" 
                           name="contact_email" 
                           value="{{ old('contact_email', $eventData['contact_email'] ?? '') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Event Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Event Image</label>
                    <input type="file" 
                           id="image" 
                           name="image" 
                           accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Upload a compelling image for your event (JPEG, PNG, max 2MB)</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gallery Images -->
                <div>
                    <label for="gallery" class="block text-sm font-medium text-gray-700 mb-2">Additional Images</label>
                    <input type="file" 
                           id="gallery" 
                           name="gallery[]" 
                           accept="image/jpeg,image/png,image/jpg"
                           multiple
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Upload additional images (optional, max 5 images, 2MB each)</p>
                    @error('gallery.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags -->
                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                    <input type="text" 
                           id="tags" 
                           name="tags" 
                           value="{{ old('tags', $eventData['tags'] ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., beach cleanup, environment, community">
                    <p class="mt-1 text-sm text-gray-500">Separate tags with commas to help volunteers find your event</p>
                    @error('tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex justify-between pt-6 border-t border-gray-200 mt-8">
            @if($step > 1)
                <a href="{{ route('organization.events.create', ['step' => $step - 1]) }}" 
                   class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors">
                    Previous
                </a>
            @else
                <div></div>
            @endif

            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                @if($step == 4)
                    Create Event
                @else
                    Next Step
                @endif
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle application requirement toggle
    const requiresApplication = document.getElementById('requires_application');
    const deadlineContainer = document.getElementById('application_deadline_container');
    
    if (requiresApplication && deadlineContainer) {
        requiresApplication.addEventListener('change', function() {
            if (this.checked) {
                deadlineContainer.classList.remove('hidden');
            } else {
                deadlineContainer.classList.add('hidden');
            }
        });
    }

    // Set minimum date for application deadline based on start date
    const startDate = document.getElementById('start_date');
    const applicationDeadline = document.getElementById('application_deadline');
    
    if (startDate && applicationDeadline) {
        startDate.addEventListener('change', function() {
            applicationDeadline.max = this.value;
        });
    }

    // Set minimum end date based on start date
    const endDate = document.getElementById('end_date');
    
    if (startDate && endDate) {
        startDate.addEventListener('change', function() {
            endDate.min = this.value;
        });
    }
});
</script>
@endsection