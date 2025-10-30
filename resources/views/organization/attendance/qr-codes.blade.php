@extends('layouts.organization')

@section('title', 'QR Codes - ' . $event->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('organization.attendance.index') }}" class="text-gray-400 hover:text-gray-500">
                                Attendance
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('organization.attendance.show', $event) }}" class="ml-4 text-gray-400 hover:text-gray-500">
                                    {{ $event->title }}
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-500">QR Codes</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="mt-2 text-3xl font-bold text-gray-900">QR Codes</h1>
                <p class="mt-2 text-gray-600">Generate and manage QR codes for volunteer attendance</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print All
                </button>
                <a href="{{ route('organization.attendance.show', $event) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Attendance
                </a>
            </div>
        </div>
    </div>

    <!-- Event Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Event Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500">Event Title</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $event->title }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $event->start_date->format('M d, Y') }} at {{ $event->start_time }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Location</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $event->location_name }}</dd>
            </div>
        </div>
    </div>

    <!-- QR Codes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 print:grid-cols-2">
        @foreach($qrCodes as $qrData)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 print:break-inside-avoid print:mb-4">
                <!-- Volunteer Info -->
                <div class="text-center mb-4">
                    <div class="flex items-center justify-center mb-2">
                        <img class="h-12 w-12 rounded-full object-cover" 
                             src="{{ $qrData['volunteer']->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($qrData['volunteer']->name) . '&color=7c3aed&background=ede9fe' }}" 
                             alt="{{ $qrData['volunteer']->name }}">
                    </div>
                    <h4 class="text-lg font-medium text-gray-900">{{ $qrData['volunteer']->name }}</h4>
                    <p class="text-sm text-gray-500">{{ $qrData['volunteer']->email }}</p>
                </div>

                <!-- QR Code -->
                <div class="flex justify-center mb-4">
                    <div class="p-4 bg-white border-2 border-gray-200 rounded-lg">
                        {!! $qrData['qr_code'] !!}
                    </div>
                </div>

                <!-- Instructions -->
                <div class="text-center">
                    <p class="text-xs text-gray-600 mb-2">
                        <strong>Check-in Instructions:</strong><br>
                        1. Scan this QR code with your phone<br>
                        2. Allow location access when prompted<br>
                        3. Confirm your check-in at the event location
                    </p>
                    <div class="text-xs text-gray-500 bg-gray-50 rounded p-2">
                        <strong>Event:</strong> {{ $event->title }}<br>
                        <strong>Date:</strong> {{ $event->start_date->format('M d, Y') }}<br>
                        <strong>Time:</strong> {{ $event->start_time }}
                    </div>
                </div>

                <!-- Action Buttons (hidden in print) -->
                <div class="mt-4 flex justify-center space-x-2 print:hidden">
                    <button data-volunteer-id="{{ $qrData['volunteer']->id }}" onclick="printQR(this.dataset.volunteerId)" 
                            class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                    <button data-volunteer-id="{{ $qrData['volunteer']->id }}" onclick="downloadQR(this.dataset.volunteerId)" 
                            class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    @if(empty($qrCodes))
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-4.01M12 12v4m6-4h.01M12 8h.01M12 8h4.01M12 8H7.99M12 8V4m0 0H8m4 0h4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No approved volunteers</h3>
            <p class="mt-1 text-sm text-gray-500">
                QR codes can only be generated for volunteers with approved applications.
            </p>
            <div class="mt-6">
                <a href="{{ route('organization.volunteers.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Manage Volunteers
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Print Styles -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    .print-area, .print-area * {
        visibility: visible;
    }
    
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    .print:hidden {
        display: none !important;
    }
    
    .print:break-inside-avoid {
        break-inside: avoid;
        page-break-inside: avoid;
    }
    
    .print:mb-4 {
        margin-bottom: 1rem;
    }
    
    .print:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>

<script>
function printQR(index) {
    const qrCard = document.querySelectorAll('.grid > div')[index];
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>QR Code - {{ $event->title }}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .qr-card { max-width: 400px; margin: 0 auto; text-align: center; }
                    .qr-code { margin: 20px 0; }
                    .instructions { font-size: 12px; color: #666; margin-top: 20px; }
                    .event-info { background: #f5f5f5; padding: 10px; border-radius: 5px; margin-top: 15px; }
                </style>
            </head>
            <body>
                <div class="qr-card">
                    ${qrCard.innerHTML}
                </div>
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

function downloadQR(index) {
    // This would require additional implementation to convert QR code to downloadable image
    alert('Download functionality would be implemented with additional JavaScript to convert SVG to PNG/PDF');
}
</script>
@endsection