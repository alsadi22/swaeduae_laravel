@extends('layouts.admin')

@section('title', 'Scheduled Report Details')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Scheduled Report Details</h1>
                    <div class="space-x-2">
                        <a href="{{ route('admin.scheduled-reports.edit', $scheduledReport) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        <form action="{{ route('admin.scheduled-reports.destroy', $scheduledReport) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" 
                                onclick="return confirm('Are you sure you want to delete this scheduled report?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="border rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                            <dl class="mt-4 space-y-4">
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $scheduledReport->name }}</dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($scheduledReport->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Created At</dt>
                                    <dd class="text-sm text-gray-900">{{ $scheduledReport->created_at->format('M j, Y g:i A') }}</dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $scheduledReport->updated_at->format('M j, Y g:i A') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Schedule Details</h3>
                            <dl class="mt-4 space-y-4">
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Report Type</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($scheduledReport->type) }}</dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Format</dt>
                                    <dd class="text-sm text-gray-900">{{ strtoupper($scheduledReport->format) }}</dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Frequency</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($scheduledReport->frequency) }}</dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Time</dt>
                                    <dd class="text-sm text-gray-900">{{ $scheduledReport->time }}</dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Next Run</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($scheduledReport->next_run_at)
                                            {{ $scheduledReport->next_run_at->format('M j, Y g:i A') }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Last Run</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($scheduledReport->last_run_at)
                                            {{ $scheduledReport->last_run_at->format('M j, Y g:i A') }}
                                        @else
                                            Never
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="border rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recipients</h3>
                    <div class="bg-gray-50 p-4 rounded">
                        @if(!empty($scheduledReport->recipients))
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($scheduledReport->recipients as $recipient)
                                    <li class="text-sm text-gray-900">{{ $recipient }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">No recipients specified</p>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('admin.scheduled-reports.index') }}" class="text-blue-600 hover:text-blue-900">
                        ← Back to Scheduled Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection