@extends('layouts.organization')

@section('title', 'Send Emergency Message')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Send Emergency Message</h1>
                <p class="mt-2 text-gray-600">Send an urgent message for "{{ $event->title }}"</p>
            </div>
            <a href="{{ route('organization.emergency-communications.index', $event) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Back to Emergency Communications
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('organization.emergency-communications.store', $event) }}">
                @csrf
                
                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="title" id="title" 
                               value="{{ old('title') }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea id="content" name="content" rows="6" 
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                  placeholder="Enter your emergency message here..."
                                  required>{{ old('content') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Be clear and concise. This message will be sent as an urgent communication.</p>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Priority Level</label>
                        <fieldset class="mt-2">
                            <legend class="sr-only">Priority level</legend>
                            <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                                <div class="flex items-center">
                                    <input id="priority_low" name="priority" type="radio" value="low" 
                                           class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300" 
                                           {{ old('priority', 'normal') === 'low' ? 'checked' : '' }}>
                                    <label for="priority_low" class="ml-3 block text-sm font-medium text-gray-700">
                                        Low
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="priority_normal" name="priority" type="radio" value="normal" 
                                           class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                                           {{ old('priority', 'normal') === 'normal' ? 'checked' : '' }}>
                                    <label for="priority_normal" class="ml-3 block text-sm font-medium text-gray-700">
                                        Normal
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="priority_high" name="priority" type="radio" value="high" 
                                           class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                                           {{ old('priority') === 'high' ? 'checked' : '' }}>
                                    <label for="priority_high" class="ml-3 block text-sm font-medium text-gray-700">
                                        High
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="priority_critical" name="priority" type="radio" value="critical" 
                                           class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                                           {{ old('priority') === 'critical' ? 'checked' : '' }}>
                                    <label for="priority_critical" class="ml-3 block text-sm font-medium text-gray-700">
                                        Critical
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Communication Channels -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Send Via</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center">
                                <input id="send_sms" name="send_sms" type="checkbox" 
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                       {{ old('send_sms') ? 'checked' : '' }}>
                                <label for="send_sms" class="ml-2 block text-sm text-gray-900">
                                    SMS
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="send_email" name="send_email" type="checkbox" 
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                       {{ old('send_email', true) ? 'checked' : '' }}>
                                <label for="send_email" class="ml-2 block text-sm text-gray-900">
                                    Email
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="send_push" name="send_push" type="checkbox" 
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                       {{ old('send_push') ? 'checked' : '' }}>
                                <label for="send_push" class="ml-2 block text-sm text-gray-900">
                                    Push Notification
                                </label>
                            </div>
                        </div>
                        @error('send_sms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('send_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('send_push')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Recipients -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Recipients</label>
                        <fieldset class="mt-2">
                            <legend class="sr-only">Recipients</legend>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input id="recipient_type_all" name="recipient_type" type="radio" value="all" 
                                           class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300" 
                                           {{ old('recipient_type', 'all') === 'all' ? 'checked' : '' }}>
                                    <label for="recipient_type_all" class="ml-3 block text-sm font-medium text-gray-700">
                                        All Event Participants ({{ $event->approvedApplications()->count() }} volunteers)
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="recipient_type_selected" name="recipient_type" type="radio" value="selected" 
                                           class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                                           {{ old('recipient_type') === 'selected' ? 'checked' : '' }}>
                                    <label for="recipient_type_selected" class="ml-3 block text-sm font-medium text-gray-700">
                                        Selected Participants
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        
                        <!-- Selected Recipients -->
                        <div id="selected-recipients" class="mt-4 {{ old('recipient_type') === 'selected' ? '' : 'hidden' }}">
                            <div class="border border-gray-300 rounded-md p-4">
                                <div class="flex items-center mb-3">
                                    <input id="select-all-recipients" type="checkbox" 
                                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                    <label for="select-all-recipients" class="ml-2 block text-sm text-gray-900">
                                        Select all participants
                                    </label>
                                </div>
                                
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    @foreach($participants as $participant)
                                        <div class="flex items-center">
                                            <input id="recipient_{{ $participant->id }}" name="recipient_ids[]" 
                                                   type="checkbox" 
                                                   value="{{ $participant->id }}"
                                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded recipient-checkbox">
                                            <label for="recipient_{{ $participant->id }}" class="ml-2 block text-sm text-gray-900">
                                                {{ $participant->name }} ({{ $participant->phone }})
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        @error('recipient_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('recipient_ids')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('organization.emergency-communications.index', $event) }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Send Emergency Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const recipientTypeAll = document.getElementById('recipient_type_all');
    const recipientTypeSelected = document.getElementById('recipient_type_selected');
    const selectedRecipients = document.getElementById('selected-recipients');
    const selectAll = document.getElementById('select-all-recipients');
    const checkboxes = document.querySelectorAll('.recipient-checkbox');
    
    // Toggle selected recipients section
    recipientTypeSelected.addEventListener('change', function() {
        if (this.checked) {
            selectedRecipients.classList.remove('hidden');
        }
    });
    
    recipientTypeAll.addEventListener('change', function() {
        if (this.checked) {
            selectedRecipients.classList.add('hidden');
        }
    });
    
    // Select all recipients functionality
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                selectAll.checked = false;
            } else {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                selectAll.checked = allChecked;
            }
        });
    });
});
</script>
@endpush