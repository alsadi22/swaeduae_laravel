@extends('layouts.app')

@section('title', 'QR Code Scanner')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
                <h1 class="text-xl font-semibold text-gray-900 mb-2">Event Check-in/out</h1>
                <p class="text-sm text-gray-600">Scan the QR code to check in or out of your volunteer event</p>
            </div>
        </div>

        <!-- Camera Scanner -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="text-center">
                <div id="scanner-container" class="relative">
                    <!-- Camera View -->
                    <div id="camera-view" class="hidden">
                        <video id="scanner-video" class="w-full h-64 object-cover rounded-lg bg-gray-100"></video>
                        <canvas id="scanner-canvas" class="hidden"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-48 h-48 border-2 border-blue-500 rounded-lg"></div>
                        </div>
                    </div>

                    <!-- Start Scanner Button -->
                    <div id="start-scanner" class="py-12">
                        <button onclick="startScanner()" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Start Camera Scanner
                        </button>
                    </div>

                    <!-- Manual Input Option -->
                    <div class="mt-4">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">Or enter manually</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <input type="text" id="manual-qr-input" placeholder="Enter QR code data" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <button onclick="processManualInput()" class="mt-2 w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Process QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="status-messages" class="space-y-4">
            <!-- Success Message -->
            <div id="success-message" class="hidden bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800" id="success-title">Success!</h3>
                        <div class="mt-2 text-sm text-green-700" id="success-text"></div>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div id="error-message" class="hidden bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error</h3>
                        <div class="mt-2 text-sm text-red-700" id="error-text"></div>
                    </div>
                </div>
            </div>

            <!-- Loading Message -->
            <div id="loading-message" class="hidden bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="animate-spin h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Processing...</h3>
                        <div class="mt-2 text-sm text-blue-700" id="loading-text">Please wait while we process your request.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div id="recent-activity" class="space-y-3">
                @if(isset($recentAttendance) && $recentAttendance->count() > 0)
                    @foreach($recentAttendance as $attendance)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $attendance->event->title }}</p>
                            <p class="text-xs text-gray-500">
                                @if($attendance->checked_in_at)
                                    Checked in: {{ $attendance->checked_in_at->format('M j, Y g:i A') }}
                                @endif
                                @if($attendance->checked_out_at)
                                    • Checked out: {{ $attendance->checked_out_at->format('M j, Y g:i A') }}
                                @endif
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($attendance->status === 'completed') bg-green-100 text-green-800
                            @elseif($attendance->status === 'checked_in') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($attendance->status) }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500 text-center py-4">No recent activity</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
let scanner = null;
let isScanning = false;
let videoStream = null;

// Start camera scanner
async function startScanner() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } 
        });
        
        videoStream = stream;
        const video = document.getElementById('scanner-video');
        video.srcObject = stream;
        video.play();
        
        document.getElementById('start-scanner').classList.add('hidden');
        document.getElementById('camera-view').classList.remove('hidden');
        
        isScanning = true;
        scanForQRCode();
        
    } catch (error) {
        showError('Camera access denied or not available. Please use manual input.');
    }
}

// Stop scanner
function stopScanner() {
    isScanning = false;
    
    if (videoStream) {
        const tracks = videoStream.getTracks();
        tracks.forEach(track => track.stop());
        videoStream = null;
    }
    
    document.getElementById('camera-view').classList.add('hidden');
    document.getElementById('start-scanner').classList.remove('hidden');
}

// Scan for QR code using jsQR
function scanForQRCode() {
    if (!isScanning) return;
    
    const video = document.getElementById('scanner-video');
    const canvas = document.getElementById('scanner-canvas');
    const canvasContext = canvas.getContext('2d');
    
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: "dontInvert",
        });
        
        if (code) {
            // Successfully decoded QR code
            processQRCode(code.data);
            stopScanner(); // Stop scanning after successful decode
            return;
        }
    }
    
    // Continue scanning
    setTimeout(() => {
        if (isScanning) {
            scanForQRCode();
        }
    }, 100);
}

// Process manual input
function processManualInput() {
    const input = document.getElementById('manual-qr-input');
    const qrData = input.value.trim();
    
    if (!qrData) {
        showError('Please enter QR code data');
        return;
    }
    
    processQRCode(qrData);
}

// Process QR code data
async function processQRCode(qrData) {
    showLoading('Processing QR code...');
    
    try {
        // Get current location
        const position = await getCurrentPosition();
        
        // Send to server
        const response = await fetch('/api/attendance/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Authorization': 'Bearer ' + document.querySelector('meta[name="api-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                qr_data: qrData,
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                device_info: {
                    userAgent: navigator.userAgent,
                    timestamp: new Date().toISOString()
                }
            })
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            showSuccess(result.message || 'Successfully processed!', result.data);
            updateRecentActivity(result.data);
        } else {
            showError(result.message || 'Failed to process QR code');
        }
        
    } catch (error) {
        showError('Failed to process QR code: ' + error.message);
    }
    
    hideLoading();
}

// Get current position
function getCurrentPosition() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('Geolocation not supported'));
            return;
        }
        
        navigator.geolocation.getCurrentPosition(resolve, reject, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 60000
        });
    });
}

// Show success message
function showSuccess(message, data) {
    const successDiv = document.getElementById('success-message');
    const successText = document.getElementById('success-text');
    
    successText.innerHTML = message;
    if (data && data.event) {
        successText.innerHTML += `<br><strong>Event:</strong> ${data.event.title}`;
        successText.innerHTML += `<br><strong>Time:</strong> ${new Date().toLocaleString()}`;
    }
    
    successDiv.classList.remove('hidden');
    hideError();
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        successDiv.classList.add('hidden');
    }, 5000);
}

// Show error message
function showError(message) {
    const errorDiv = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    
    errorText.textContent = message;
    errorDiv.classList.remove('hidden');
    hideSuccess();
}

// Show loading message
function showLoading(message) {
    const loadingDiv = document.getElementById('loading-message');
    const loadingText = document.getElementById('loading-text');
    
    loadingText.textContent = message;
    loadingDiv.classList.remove('hidden');
    hideSuccess();
    hideError();
}

// Hide messages
function hideSuccess() {
    document.getElementById('success-message').classList.add('hidden');
}

function hideError() {
    document.getElementById('error-message').classList.add('hidden');
}

function hideLoading() {
    document.getElementById('loading-message').classList.add('hidden');
}

// Update recent activity
function updateRecentActivity(data) {
    if (!data || !data.event) return;
    
    const activityDiv = document.getElementById('recent-activity');
    const newActivity = document.createElement('div');
    newActivity.className = 'flex items-center justify-between py-2 border-b border-gray-100';
    newActivity.innerHTML = `
        <div>
            <p class="text-sm font-medium text-gray-900">${data.event.title}</p>
            <p class="text-xs text-gray-500">
                ${data.type === 'checkin' ? 'Checked in' : 'Checked out'}: ${new Date().toLocaleString()}
            </p>
        </div>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            ${data.type === 'checkin' ? 'Checked In' : 'Checked Out'}
        </span>
    `;
    
    activityDiv.insertBefore(newActivity, activityDiv.firstChild);
}
</script>
@endpush