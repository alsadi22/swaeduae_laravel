<x-guest-layout>
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-white p-8 rounded-lg shadow-sm border">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Success Message -->
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Application Submitted Successfully!') }}</h2>
            
            <p class="text-gray-600 mb-6">
                {{ __('Thank you for registering your organization with SwaedUAE. Your application has been submitted and is currently under review by our verification team.') }}
            </p>

            <!-- What's Next -->
            <div class="bg-blue-50 p-6 rounded-lg mb-6 text-left">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">{{ __('What happens next?') }}</h3>
                <ul class="space-y-2 text-blue-800">
                    <li class="flex items-start">
                        <span class="flex-shrink-0 h-5 w-5 text-blue-600 mt-0.5">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <span class="ml-2">{{ __('Our team will review your documents and organization information') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 h-5 w-5 text-blue-600 mt-0.5">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <span class="ml-2">{{ __('You will receive an email notification about the verification status') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 h-5 w-5 text-blue-600 mt-0.5">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <span class="ml-2">{{ __('Once approved, you can start creating volunteer events') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Important Information -->
            <div class="bg-yellow-50 p-4 rounded-lg mb-6 text-left">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">{{ __('Important') }}</h3>
                        <p class="text-sm text-yellow-700 mt-1">
                            {{ __('The verification process typically takes 2-5 business days. Please ensure all uploaded documents are clear and valid.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="text-sm text-gray-600 mb-6">
                <p>{{ __('If you have any questions about your application, please contact us at:') }}</p>
                <p class="font-semibold text-gray-900 mt-1">support@swaeduae.ae</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('organization.dashboard') }}" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Go to Dashboard') }}
                </a>
                
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Back to Home') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>