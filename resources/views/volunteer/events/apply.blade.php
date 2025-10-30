@extends('layouts.app')

@section('title', 'Apply to Volunteer - ' . $event->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('volunteer.events.index') }}" class="text-gray-700 hover:text-blue-600">
                            Events
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('volunteer.events.show', $event) }}" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">
                                {{ Str::limit($event->title, 30) }}
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-gray-500 md:ml-2">Apply</span>
                        </div>
                    </li>
                </ol>
            </nav>
            
            <h1 class="text-3xl font-bold text-gray-900 mt-4">Apply to Volunteer</h1>
            <p class="text-gray-600 mt-2">Submit your application for this volunteer opportunity</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Application Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <form action="{{ route('volunteer.events.apply.submit', $event) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Motivation -->
                        <div class="mb-6">
                            <label for="motivation" class="block text-sm font-medium text-gray-700 mb-2">
                                Why do you want to volunteer for this event? <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="motivation" 
                                name="motivation" 
                                rows="4" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('motivation') border-red-500 @enderror"
                                placeholder="Tell us about your motivation to volunteer for this event..."
                                required
                                maxlength="1000">{{ old('motivation') }}</textarea>
                            @error('motivation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Maximum 1000 characters</p>
                        </div>

                        <!-- Skills -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Relevant Skills & Experience
                            </label>
                            <div class="space-y-2">
                                @php
                                    $commonSkills = [
                                        'Communication', 'Leadership', 'Teamwork', 'Organization', 
                                        'First Aid', 'Teaching', 'Event Management', 'Social Media',
                                        'Photography', 'Translation', 'Computer Skills', 'Customer Service'
                                    ];
                                @endphp
                                
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($commonSkills as $skill)
                                        <label class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="skills[]" 
                                                value="{{ $skill }}"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                {{ in_array($skill, old('skills', [])) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">{{ $skill }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                
                                <div class="mt-3">
                                    <input 
                                        type="text" 
                                        name="other_skills" 
                                        placeholder="Other skills (comma separated)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('other_skills') }}">
                                </div>
                            </div>
                            @error('skills')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Availability -->
                        <div class="mb-6">
                            <label for="availability" class="block text-sm font-medium text-gray-700 mb-2">
                                Availability & Schedule <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="availability" 
                                name="availability" 
                                rows="3" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('availability') border-red-500 @enderror"
                                placeholder="Please describe your availability for this event..."
                                required
                                maxlength="500">{{ old('availability') }}</textarea>
                            @error('availability')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Maximum 500 characters</p>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Emergency Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="emergency_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Contact Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="emergency_name" 
                                        name="emergency_name" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('emergency_name') border-red-500 @enderror"
                                        value="{{ old('emergency_name') }}"
                                        required>
                                    @error('emergency_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="emergency_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Contact Phone <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="tel" 
                                        id="emergency_phone" 
                                        name="emergency_phone" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('emergency_phone') border-red-500 @enderror"
                                        value="{{ old('emergency_phone') }}"
                                        required>
                                    @error('emergency_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Questions -->
                        @if($event->custom_fields && count($event->custom_fields) > 0)
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Additional Questions</h3>
                                @foreach($event->custom_fields as $index => $field)
                                    <div class="mb-4">
                                        <label for="custom_{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ $field['question'] }}
                                            @if($field['required'] ?? false)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        
                                        @if($field['type'] === 'textarea')
                                            <textarea 
                                                id="custom_{{ $index }}" 
                                                name="custom_responses[{{ $index }}]" 
                                                rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                {{ ($field['required'] ?? false) ? 'required' : '' }}>{{ old("custom_responses.{$index}") }}</textarea>
                                        @else
                                            <input 
                                                type="text" 
                                                id="custom_{{ $index }}" 
                                                name="custom_responses[{{ $index }}]" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                value="{{ old("custom_responses.{$index}") }}"
                                                {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Terms and Conditions -->
                        <div class="mb-6">
                            <label class="flex items-start">
                                <input 
                                    type="checkbox" 
                                    name="terms_accepted" 
                                    class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    required>
                                <span class="ml-2 text-sm text-gray-700">
                                    I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">terms and conditions</a> 
                                    and understand that I am committing to volunteer for the full duration of this event. <span class="text-red-500">*</span>
                                </span>
                            </label>
                            @error('terms_accepted')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between">
                            <a href="{{ route('volunteer.events.show', $event) }}" 
                               class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition duration-200">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition duration-200 font-medium">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Event Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Summary</h3>
                    
                    <!-- Event Image -->
                    @if($event->image)
                        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" 
                             class="w-full h-32 object-cover rounded-lg mb-4">
                    @endif
                    
                    <!-- Event Title -->
                    <h4 class="font-semibold text-gray-900 mb-2">{{ $event->title }}</h4>
                    
                    <!-- Organization -->
                    <div class="flex items-center mb-3">
                        <i class="fas fa-building text-gray-400 mr-2"></i>
                        <span class="text-sm text-gray-600">{{ $event->organization->name }}</span>
                    </div>
                    
                    <!-- Date & Time -->
                    <div class="flex items-center mb-3">
                        <i class="fas fa-calendar text-gray-400 mr-2"></i>
                        <div class="text-sm text-gray-600">
                            <div>{{ $event->start_date->format('M j, Y') }}</div>
                            <div>{{ $event->start_time }} - {{ $event->end_time }}</div>
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div class="flex items-center mb-3">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                        <div class="text-sm text-gray-600">
                            <div>{{ $event->location }}</div>
                            <div>{{ $event->city }}, {{ $event->emirate }}</div>
                        </div>
                    </div>
                    
                    <!-- Duration -->
                    <div class="flex items-center mb-4">
                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                        <span class="text-sm text-gray-600">{{ $event->volunteer_hours }} hours</span>
                    </div>
                    
                    <!-- Available Spots -->
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Available Spots</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $event->available_spots }} / {{ $event->max_volunteers }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = $event->max_volunteers > 0 ? (($event->max_volunteers - $event->available_spots) / $event->max_volunteers) * 100 : 0;
                            @endphp
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, max(0, $percentage)) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Character count for textareas
    document.addEventListener('DOMContentLoaded', function() {
        const textareas = document.querySelectorAll('textarea[maxlength]');
        
        textareas.forEach(textarea => {
            const maxLength = textarea.getAttribute('maxlength');
            const helpText = textarea.nextElementSibling?.nextElementSibling;
            
            if (helpText && helpText.classList.contains('text-gray-500')) {
                const updateCount = () => {
                    const remaining = maxLength - textarea.value.length;
                    helpText.textContent = `${remaining} characters remaining`;
                    
                    if (remaining < 50) {
                        helpText.classList.add('text-orange-500');
                        helpText.classList.remove('text-gray-500');
                    } else {
                        helpText.classList.add('text-gray-500');
                        helpText.classList.remove('text-orange-500');
                    }
                };
                
                textarea.addEventListener('input', updateCount);
                updateCount(); // Initial count
            }
        });
    });
</script>
@endpush