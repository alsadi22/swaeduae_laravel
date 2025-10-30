@extends('layouts.app')

@section('title', 'Generate Certificates')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('organization.events.show', $event) }}" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Generate Certificates</h1>
                    <p class="text-gray-600 mt-1">Issue certificates for: <span class="font-semibold">{{ $event->title }}</span></p>
                </div>
            </div>
        </div>

        <!-- Event Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Event Title</p>
                    <p class="font-medium text-gray-900">{{ $event->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Event Date</p>
                    <p class="font-medium text-gray-900">{{ $event->start_date->format('F j, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Location</p>
                    <p class="font-medium text-gray-900">{{ $event->location }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Participants</p>
                    <p class="font-medium text-gray-900">{{ $eligibleUsers->count() }} eligible for certificates</p>
                </div>
            </div>
        </div>

        @if($eligibleUsers->count() > 0)
            <!-- Certificate Generation Form -->
            <form action="{{ route('organization.certificates.generate') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <h2 class="text-lg font-semibold text-gray-900 mb-6">Certificate Details</h2>

                <!-- Certificate Type -->
                <div class="mb-6">
                    <label for="certificate_type" class="block text-sm font-medium text-gray-700 mb-2">Certificate Type</label>
                    <select name="certificate_type" id="certificate_type" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select certificate type</option>
                        <option value="volunteer">Volunteer Service Certificate</option>
                        <option value="completion">Event Completion Certificate</option>
                        <option value="achievement">Achievement Certificate</option>
                    </select>
                    @error('certificate_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hours Completed -->
                <div class="mb-6">
                    <label for="hours_completed" class="block text-sm font-medium text-gray-700 mb-2">Hours Completed</label>
                    <input type="number" name="hours_completed" id="hours_completed" step="0.5" min="0.5" max="999.99" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter volunteer hours">
                    @error('hours_completed')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Certificate Description (Optional)</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Custom description for the certificate (optional)"></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Eligible Volunteers -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Select Volunteers</label>
                    
                    <!-- Select All Checkbox -->
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Select all eligible volunteers</span>
                        </label>
                    </div>

                    <!-- Volunteers List -->
                    <div class="border border-gray-300 rounded-md max-h-64 overflow-y-auto">
                        @foreach($eligibleUsers as $attendance)
                            <div class="flex items-center p-3 border-b border-gray-200 last:border-b-0 hover:bg-gray-50">
                                <input type="checkbox" name="user_ids[]" value="{{ $attendance->user_id }}" 
                                       class="volunteer-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $attendance->user->email }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-600">Check-in: {{ $attendance->checked_in_at->format('M j, g:i A') }}</p>
                                            @if($attendance->checked_out_at)
                                                <p class="text-sm text-gray-600">Check-out: {{ $attendance->checked_out_at->format('M j, g:i A') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('user_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('organization.events.show', $event) }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Generate Certificates
                    </button>
                </div>
            </form>
        @else
            <!-- No Eligible Users -->
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Eligible Volunteers</h3>
                <p class="text-gray-600 mb-4">There are no volunteers who have completed attendance for this event and don't already have certificates.</p>
                <a href="{{ route('organization.events.show', $event) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Back to Event
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const volunteerCheckboxes = document.querySelectorAll('.volunteer-checkbox');
    
    // Handle select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        volunteerCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Handle individual checkbox changes
    volunteerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(volunteerCheckboxes).every(cb => cb.checked);
            const noneChecked = Array.from(volunteerCheckboxes).every(cb => !cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
        });
    });
});
</script>
@endpush
@endsection