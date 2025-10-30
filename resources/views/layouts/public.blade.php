<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- SEO Meta Tags -->
        <meta name="description" content="@yield('meta-description', 'SwaedUAE Volunteer Management Platform - Connect with meaningful volunteer opportunities across the UAE')">
        <meta name="keywords" content="@yield('meta-keywords', 'volunteer, uae, community, events, organizations')">
        
        <title>@yield('title', config('app.name', 'SwaedUAE'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <!-- Public Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <a href="{{ route('home') }}" class="text-2xl font-bold text-red-600 hover:text-red-700">
                                SwaedUAE
                            </a>
                        </div>
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                            <a href="{{ route('home') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium">Home</a>
                            <a href="{{ route('events.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium">Opportunities</a>
                            <a href="{{ route('organizations.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium">Organizations</a>
                        </div>
                    </div>
                    
                    <div class="hidden md:flex md:items-center md:space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium">Sign In</a>
                            <a href="{{ route('register') }}" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700">Volunteer Sign Up</a>
                            <a href="{{ route('organization.register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">Organization Sign Up</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-xl font-bold text-red-600 mb-4">SwaedUAE</h3>
                        <p class="text-gray-400">
                            Empowering volunteers and organizations to create positive change across the UAE.
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                            <li><a href="{{ route('events.index') }}" class="hover:text-white">Opportunities</a></li>
                            <li><a href="{{ route('organizations.index') }}" class="hover:text-white">Organizations</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Resources</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="{{ route('pages.show', 'volunteer-guide') }}" class="hover:text-white">Volunteer Guide</a></li>
                            <li><a href="{{ route('pages.show', 'organization-resources') }}" class="hover:text-white">Organization Resources</a></li>
                            <li><a href="{{ route('pages.show', 'faq') }}" class="hover:text-white">FAQ</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Legal</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="{{ route('pages.show', 'privacy-policy') }}" class="hover:text-white">Privacy Policy</a></li>
                            <li><a href="{{ route('pages.show', 'terms-of-service') }}" class="hover:text-white">Terms of Service</a></li>
                            <li><a href="{{ route('pages.show', 'cookie-policy') }}" class="hover:text-white">Cookie Policy</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} SwaedUAE. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>