@extends('layouts.organization')

@section('title', 'Event Messages')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Messages</h1>
                <p class="mt-2 text-gray-600">Communicate with participants for "{{ $event->title }}"</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Participants List -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Participants</h3>
                    <p class="mt-1 text-sm text-gray-500">Select participants to send messages</p>
                </div>
                <div class="border-t border-gray-200">
                    <form id="messageForm" method="POST" action="{{ route('organization.messages.store', $event) }}">
                        @csrf
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input id="select-all" type="checkbox" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="select-all" class="ml-2 block text-sm text-gray-900">
                                        Select all participants
                                    </label>
                                </div>
                                
                                <div class="space-y-2 max-h-96 overflow-y-auto">
                                    @foreach($participants as $participant)
                                        <div class="flex items-center">
                                            <input id="participant_{{ $participant->id }}" name="recipient_ids[]" 
                                                   type="checkbox" 
                                                   value="{{ $participant->id }}"
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded participant-checkbox">
                                            <label for="participant_{{ $participant->id }}" class="ml-2 block text-sm text-gray-900">
                                                {{ $participant->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Message Content -->
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700">Message</label>
                                    <textarea id="content" name="content" rows="4" 
                                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                              placeholder="Type your message here..."></textarea>
                                    @error('content')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    @error('recipient_ids')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <button type="submit" 
                                        class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Messages List -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Message History</h3>
                    <p class="mt-1 text-sm text-gray-500">Recent messages sent to participants</p>
                </div>
                <div class="border-t border-gray-200">
                    @if($messages->count() > 0)
                        <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                            @foreach($messages as $message)
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-blue-600 truncate">
                                            {{ $message->sender->name }}
                                        </p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <p class="text-sm text-gray-500">
                                                {{ $message->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-800">
                                                {{ $message->content }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        To: {{ $message->recipient->name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="px-4 py-4 border-t border-gray-200 sm:px-6">
                            {{ $messages->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No messages</h3>
                            <p class="mt-1 text-sm text-gray-500">Messages you send to participants will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.participant-checkbox');
    
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