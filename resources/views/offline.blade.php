<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline - SwaedUAE</title>
    <meta name="description" content="You are currently offline. Please check your internet connection.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#dc2626">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full text-center px-4">
        <div class="bg-red-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">You're Offline</h1>
        <p class="text-gray-600 mb-8">
            It seems you've lost your internet connection. Please check your connection and try again.
        </p>
        
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">While you're offline:</h2>
            <ul class="text-left text-gray-600 space-y-2">
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>You can still view previously loaded pages</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Your data is safely stored locally</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Changes will sync when you're back online</span>
                </li>
            </ul>
        </div>
        
        <button onclick="window.location.reload()" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors w-full">
            Try Again
        </button>
        
        <p class="text-gray-500 text-sm mt-6">
            @auth
                @php
                    $dashboardRoute = auth()->user()->hasRole('admin') 
                        ? route('admin.dashboard') 
                        : (auth()->user()->hasRole(['organization-manager', 'organization-staff']) 
                            ? route('organization.dashboard') 
                            : route('volunteer.dashboard'));
                @endphp
                <a href="{{ $dashboardRoute }}" class="text-red-600 hover:text-red-800">Go to Dashboard</a>
            @else
                <a href="{{ route('home') }}" class="text-red-600 hover:text-red-800">Go to Home</a>
            @endauth
        </p>
    </div>
    
    <script>
        // Check if back online
        window.addEventListener('online', () => {
            window.location.reload();
        });
    </script>
</body>
</html>