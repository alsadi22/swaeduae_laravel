@extends('layouts.admin')

@section('title', 'Edit Scheduled Report')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Edit Scheduled Report</h1>

                <form action="{{ route('admin.scheduled-reports.update', $scheduledReport) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Report Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $scheduledReport->name) }}" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Report Type</label>
                            <select name="type" id="type" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="user" {{ old('type', $scheduledReport->type) == 'user' ? 'selected' : '' }}>User Statistics</option>
                                <option value="organization" {{ old('type', $scheduledReport->type) == 'organization' ? 'selected' : '' }}>Organization Statistics</option>
                                <option value="event" {{ old('type', $scheduledReport->type) == 'event' ? 'selected' : '' }}>Event Statistics</option>
                                <option value="certificate" {{ old('type', $scheduledReport->type) == 'certificate' ? 'selected' : '' }}>Certificate Statistics</option>
                                <option value="attendance" {{ old('type', $scheduledReport->type) == 'attendance' ? 'selected' : '' }}>Attendance Statistics</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="format" class="block text-sm font-medium text-gray-700">Export Format</label>
                            <select name="format" id="format" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="pdf" {{ old('format', $scheduledReport->format) == 'pdf' ? 'selected' : '' }}>PDF</option>
                                <option value="excel" {{ old('format', $scheduledReport->format) == 'excel' ? 'selected' : '' }}>Excel</option>
                                <option value="csv" {{ old('format', $scheduledReport->format) == 'csv' ? 'selected' : '' }}>CSV</option>
                            </select>
                            @error('format')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="frequency" class="block text-sm font-medium text-gray-700">Frequency</label>
                            <select name="frequency" id="frequency" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="daily" {{ old('frequency', $scheduledReport->frequency) == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ old('frequency', $scheduledReport->frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('frequency', $scheduledReport->frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                            @error('frequency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="time" class="block text-sm font-medium text-gray-700">Time of Day</label>
                            <input type="time" name="time" id="time" value="{{ old('time', $scheduledReport->time) }}" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="recipients" class="block text-sm font-medium text-gray-700">Recipients (comma separated emails)</label>
                            <input type="text" name="recipients" id="recipients" value="{{ old('recipients', implode(',', $scheduledReport->recipients ?? [])) }}" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('recipients')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="is_active" id="is_active"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="1" {{ old('is_active', $scheduledReport->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $scheduledReport->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('admin.scheduled-reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Scheduled Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection