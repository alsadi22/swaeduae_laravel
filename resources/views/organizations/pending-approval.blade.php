<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Organization Pending Approval') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-clock text-yellow-600 text-4xl"></i>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Pending Approval</h3>
                        
                        <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                            Thank you for registering your organization. Your application is currently under review by our team. 
                            We will notify you via email once your organization has been approved.
                        </p>
                        
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 max-w-2xl mx-auto">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>What happens next?</strong> Our team will review your organization's information and verify your license. 
                                        This process typically takes 1-3 business days. You will receive an email notification once approved.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            @php
                                $dashboardRoute = auth()->user()->hasRole('admin') 
                                    ? route('admin.dashboard') 
                                    : (auth()->user()->hasRole(['organization-manager', 'organization-staff']) 
                                        ? route('organization.dashboard') 
                                        : route('volunteer.dashboard'));
                            @endphp
                            <a href="{{ $dashboardRoute }}" class="bg-gray-500 text-white px-6 py-3 rounded-md font-medium hover:bg-gray-600">
                                Go to Dashboard
                            </a>
                            <a href="{{ route('profile.edit') }}" class="bg-blue-500 text-white px-6 py-3 rounded-md font-medium hover:bg-blue-600">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>