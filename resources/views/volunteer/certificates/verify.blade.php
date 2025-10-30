@extends('layouts.app')

@section('title', 'Verify Certificate')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Certificate Verification</h1>
            <p class="text-gray-600 mt-2">Verify the authenticity of a SwaedUAE volunteer certificate</p>
        </div>

        <!-- Verification Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form id="verification-form" class="space-y-6">
                <div>
                    <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Verification Code
                    </label>
                    <input type="text" 
                           id="verification_code" 
                           name="verification_code" 
                           maxlength="12"
                           placeholder="Enter 12-character verification code"
                           class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-center text-lg font-mono tracking-wider"
                           required>
                    <p class="mt-2 text-sm text-gray-500">
                        The verification code can be found on the certificate document.
                    </p>
                </div>

                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="verify-text">Verify Certificate</span>
                    <span class="loading-text hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Verifying...
                    </span>
                </button>
            </form>
        </div>

        <!-- Verification Result -->
        <div id="verification-result" class="mt-8 hidden">
            <!-- Success Result -->
            <div id="success-result" class="bg-green-50 border border-green-200 rounded-lg p-6 hidden">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-medium text-green-800 mb-4">Certificate Verified Successfully</h3>
                        <div id="certificate-details" class="bg-white rounded-lg p-4 space-y-3">
                            <!-- Certificate details will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Result -->
            <div id="error-result" class="bg-red-50 border border-red-200 rounded-lg p-6 hidden">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-red-800">Certificate Verification Failed</h3>
                        <p id="error-message" class="mt-2 text-sm text-red-700"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Section -->
        <div class="mt-12 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4">About Certificate Verification</h3>
            <div class="space-y-3 text-sm text-blue-800">
                <p>
                    <strong>What is certificate verification?</strong><br>
                    Certificate verification allows you to confirm that a SwaedUAE volunteer certificate is authentic and has been officially issued by our platform.
                </p>
                <p>
                    <strong>How to find the verification code:</strong><br>
                    The 12-character verification code is printed on every official certificate, usually at the bottom of the document.
                </p>
                <p>
                    <strong>What information is verified?</strong><br>
                    Our verification system confirms the certificate number, recipient name, event details, issuing organization, and completion hours.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('verification-form');
    const verificationCodeInput = document.getElementById('verification_code');
    const submitButton = form.querySelector('button[type="submit"]');
    const verifyText = submitButton.querySelector('.verify-text');
    const loadingText = submitButton.querySelector('.loading-text');
    const resultContainer = document.getElementById('verification-result');
    const successResult = document.getElementById('success-result');
    const errorResult = document.getElementById('error-result');
    const certificateDetails = document.getElementById('certificate-details');
    const errorMessage = document.getElementById('error-message');

    // Format verification code input
    verificationCodeInput.addEventListener('input', function(e) {
        let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 12) {
            value = value.substring(0, 12);
        }
        e.target.value = value;
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const verificationCode = verificationCodeInput.value.trim();
        
        if (verificationCode.length !== 12) {
            showError('Please enter a valid 12-character verification code.');
            return;
        }

        // Show loading state
        submitButton.disabled = true;
        verifyText.classList.add('hidden');
        loadingText.classList.remove('hidden');
        
        // Hide previous results
        resultContainer.classList.add('hidden');
        successResult.classList.add('hidden');
        errorResult.classList.add('hidden');

        // Make API request
        fetch('{{ route("volunteer.certificates.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                verification_code: verificationCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                showSuccess(data.certificate);
            } else {
                showError(data.message || 'Certificate verification failed.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while verifying the certificate. Please try again.');
        })
        .finally(() => {
            // Reset loading state
            submitButton.disabled = false;
            verifyText.classList.remove('hidden');
            loadingText.classList.add('hidden');
        });
    });

    function showSuccess(certificate) {
        certificateDetails.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600">Certificate Number</p>
                    <p class="text-gray-900 font-mono">${certificate.certificate_number}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Recipient</p>
                    <p class="text-gray-900">${certificate.recipient_name}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Event</p>
                    <p class="text-gray-900">${certificate.event_title}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Organization</p>
                    <p class="text-gray-900">${certificate.organization_name}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Hours Completed</p>
                    <p class="text-gray-900">${certificate.hours_completed} hours</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Issued Date</p>
                    <p class="text-gray-900">${certificate.issued_date}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-600">Type</p>
                    <p class="text-gray-900 capitalize">${certificate.type} Certificate</p>
                </div>
                ${certificate.description ? `
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-600">Description</p>
                    <p class="text-gray-900">${certificate.description}</p>
                </div>
                ` : ''}
            </div>
        `;
        
        resultContainer.classList.remove('hidden');
        successResult.classList.remove('hidden');
    }

    function showError(message) {
        errorMessage.textContent = message;
        resultContainer.classList.remove('hidden');
        errorResult.classList.remove('hidden');
    }
});
</script>
@endpush
@endsection