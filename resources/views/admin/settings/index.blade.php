@extends('layouts.admin')

@section('title', 'Website Settings')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Website Settings</h1>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    
                    <!-- Settings Tabs -->
                    <div class="mb-6 border-b border-gray-200">
                        <nav class="flex space-x-8" aria-label="Tabs">
                            <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm active" data-tab="general">
                                General
                            </button>
                            <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="email">
                                Email
                            </button>
                            <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="api">
                                API
                            </button>
                            <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="social">
                                Social Media
                            </button>
                            <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="appearance">
                                Appearance
                            </button>
                        </nav>
                    </div>

                    <!-- General Settings Tab -->
                    <div class="tab-content active" id="general-tab">
                        <div class="bg-white p-6 rounded-lg shadow border mb-6">
                            <h2 class="text-xl font-semibold mb-4">General Settings</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                                    <input type="text" name="site_name" id="site_name" value="{{ $generalSettings['site_name'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                                    <textarea name="site_description" id="site_description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $generalSettings['site_description'] ?? '' }}</textarea>
                                </div>
                                
                                <div>
                                    <label for="site_keywords" class="block text-sm font-medium text-gray-700">SEO Keywords</label>
                                    <input type="text" name="site_keywords" id="site_keywords" value="{{ $generalSettings['site_keywords'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="site_logo" class="block text-sm font-medium text-gray-700">Site Logo</label>
                                    <input type="file" name="site_logo" id="site_logo" accept="image/*" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @if(!empty($generalSettings['site_logo']))
                                        <div class="mt-2">
                                            <img src="{{ $generalSettings['site_logo'] }}" alt="Current Logo" class="h-16 w-auto">
                                        </div>
                                    @endif
                                </div>
                                
                                <div>
                                    <label for="site_favicon" class="block text-sm font-medium text-gray-700">Favicon</label>
                                    <input type="file" name="site_favicon" id="site_favicon" accept="image/*,.ico" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @if(!empty($generalSettings['site_favicon']))
                                        <div class="mt-2">
                                            <img src="{{ $generalSettings['site_favicon'] }}" alt="Current Favicon" class="h-8 w-8">
                                        </div>
                                    @endif
                                </div>
                                
                                <div>
                                    <label for="maintenance_mode" class="block text-sm font-medium text-gray-700">Maintenance Mode</label>
                                    <select name="maintenance_mode" id="maintenance_mode" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="0" {{ empty($generalSettings['maintenance_mode']) ? 'selected' : '' }}>Disabled</option>
                                        <option value="1" {{ !empty($generalSettings['maintenance_mode']) ? 'selected' : '' }}>Enabled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings Tab -->
                    <div class="tab-content hidden" id="email-tab">
                        <div class="bg-white p-6 rounded-lg shadow border mb-6">
                            <h2 class="text-xl font-semibold mb-4">Email Settings</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="mail_driver" class="block text-sm font-medium text-gray-700">Mail Driver</label>
                                    <input type="text" name="mail_driver" id="mail_driver" value="{{ $emailSettings['mail_driver'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="mail_host" class="block text-sm font-medium text-gray-700">Mail Host</label>
                                    <input type="text" name="mail_host" id="mail_host" value="{{ $emailSettings['mail_host'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="mail_port" class="block text-sm font-medium text-gray-700">Mail Port</label>
                                    <input type="number" name="mail_port" id="mail_port" value="{{ $emailSettings['mail_port'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="mail_username" class="block text-sm font-medium text-gray-700">Username</label>
                                    <input type="text" name="mail_username" id="mail_username" value="{{ $emailSettings['mail_username'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="mail_password" class="block text-sm font-medium text-gray-700">Password</label>
                                    <input type="password" name="mail_password" id="mail_password" value="" placeholder="Leave blank to keep current password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="mail_encryption" class="block text-sm font-medium text-gray-700">Encryption</label>
                                    <input type="text" name="mail_encryption" id="mail_encryption" value="{{ $emailSettings['mail_encryption'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="mail_from_address" class="block text-sm font-medium text-gray-700">From Address</label>
                                    <input type="email" name="mail_from_address" id="mail_from_address" value="{{ $emailSettings['mail_from_address'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="mail_from_name" class="block text-sm font-medium text-gray-700">From Name</label>
                                    <input type="text" name="mail_from_name" id="mail_from_name" value="{{ $emailSettings['mail_from_name'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- API Settings Tab -->
                    <div class="tab-content hidden" id="api-tab">
                        <div class="bg-white p-6 rounded-lg shadow border mb-6">
                            <h2 class="text-xl font-semibold mb-4">API Settings</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="api_base_url" class="block text-sm font-medium text-gray-700">API Base URL</label>
                                    <input type="url" name="api_base_url" id="api_base_url" value="{{ $apiSettings['api_base_url'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="api_key" class="block text-sm font-medium text-gray-700">API Key</label>
                                    <input type="password" name="api_key" id="api_key" value="" placeholder="Leave blank to keep current key" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="api_secret" class="block text-sm font-medium text-gray-700">API Secret</label>
                                    <input type="password" name="api_secret" id="api_secret" value="" placeholder="Leave blank to keep current secret" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="google_maps_api_key" class="block text-sm font-medium text-gray-700">Google Maps API Key</label>
                                    <input type="password" name="google_maps_api_key" id="google_maps_api_key" value="" placeholder="Leave blank to keep current key" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="firebase_api_key" class="block text-sm font-medium text-gray-700">Firebase API Key</label>
                                    <input type="password" name="firebase_api_key" id="firebase_api_key" value="" placeholder="Leave blank to keep current key" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Settings Tab -->
                    <div class="tab-content hidden" id="social-tab">
                        <div class="bg-white p-6 rounded-lg shadow border mb-6">
                            <h2 class="text-xl font-semibold mb-4">Social Media Settings</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="facebook_url" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                                    <input type="url" name="facebook_url" id="facebook_url" value="{{ $socialSettings['facebook_url'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="twitter_url" class="block text-sm font-medium text-gray-700">Twitter URL</label>
                                    <input type="url" name="twitter_url" id="twitter_url" value="{{ $socialSettings['twitter_url'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="instagram_url" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                                    <input type="url" name="instagram_url" id="instagram_url" value="{{ $socialSettings['instagram_url'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="linkedin_url" class="block text-sm font-medium text-gray-700">LinkedIn URL</label>
                                    <input type="url" name="linkedin_url" id="linkedin_url" value="{{ $socialSettings['linkedin_url'] ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance Settings Tab -->
                    <div class="tab-content hidden" id="appearance-tab">
                        <div class="bg-white p-6 rounded-lg shadow border mb-6">
                            <h2 class="text-xl font-semibold mb-4">Appearance Settings</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="primary_color" class="block text-sm font-medium text-gray-700">Primary Color</label>
                                    <input type="color" name="primary_color" id="primary_color" value="{{ $appearanceSettings['primary_color'] ?? '#dc2626' }}" class="mt-1 block w-full h-10 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="secondary_color" class="block text-sm font-medium text-gray-700">Secondary Color</label>
                                    <input type="color" name="secondary_color" id="secondary_color" value="{{ $appearanceSettings['secondary_color'] ?? '#2563eb' }}" class="mt-1 block w-full h-10 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="site_language" class="block text-sm font-medium text-gray-700">Default Language</label>
                                    <select name="site_language" id="site_language" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="en" {{ ($appearanceSettings['site_language'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="ar" {{ ($appearanceSettings['site_language'] ?? 'en') == 'ar' ? 'selected' : '' }}>Arabic</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active', 'border-blue-500', 'text-blue-600'));
            tabContents.forEach(content => content.classList.add('hidden'));
            
            // Add active class to clicked button
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            
            // Show corresponding content
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId + '-tab').classList.remove('hidden');
        });
    });
});
</script>

<style>
.tab-button.active {
    border-color: #3b82f6;
    color: #3b82f6;
    border-bottom-width: 2px;
}
</style>
@endsection