@extends('layouts.organization')

@section('title', 'Create Announcement')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Announcement</h1>
                <p class="mt-2 text-gray-600">Create a new announcement for "{{ $event->title }}"</p>
            </div>
            <a href="{{ route('organization.announcements.index', $event) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Back to Announcements
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('organization.announcements.store', $event) }}">
                @csrf
                
                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" id="title" 
                               value="{{ old('title') }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                        <textarea id="content" name="content" rows="6" 
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <fieldset class="mt-2">
                            <legend class="sr-only">Announcement type</legend>
                            <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                                <div class="flex items-center">
                                    <input id="type_general" name="type" type="radio" value="general" 
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" 
                                           {{ old('type', 'general') === 'general' ? 'checked' : '' }}>
                                    <label for="type_general" class="ml-3 block text-sm font-medium text-gray-700">
                                        General
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="type_update" name="type" type="radio" value="update" 
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                           {{ old('type') === 'update' ? 'checked' : '' }}>
                                    <label for="type_update" class="ml-3 block text-sm font-medium text-gray-700">
                                        Update
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="type_urgent" name="type" type="radio" value="urgent" 
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                           {{ old('type') === 'urgent' ? 'checked' : '' }}>
                                    <label for="type_urgent" class="ml-3 block text-sm font-medium text-gray-700">
                                        Urgent
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Publish Option -->
                    <div class="flex items-center">
                        <input id="is_published" name="is_published" type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                               {{ old('is_published') ? 'checked' : '' }}>
                        <label for="is_published" class="ml-2 block text-sm text-gray-900">
                            Publish immediately
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('organization.announcements.index', $event) }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection