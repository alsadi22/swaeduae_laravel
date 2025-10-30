<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SwaedUAE') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js for interactive elements -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        
        <!-- Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="h-full bg-white">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ mobileMenuOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <a href="{{ route('home') }}" class="text-2xl font-bold text-red-600 hover:text-red-700">
                                <h1 class="text-2xl font-bold text-red-600">SwaedUAE</h1>
                            </a>
                        </div>
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                            <a href="#features" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium">Features</a>
                            <a href="#how-it-works" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium">How It Works</a>
                            <a href="{{ route('events.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium">Opportunities</a>
                            <a href="#organizations" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium">Organizations</a>
                            <a href="#contact" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium">Contact</a>
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
                    
                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-900 hover:text-red-600 p-2">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" x-transition class="md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t border-gray-200">
                    <a href="#features" class="block text-gray-900 hover:text-red-600 px-3 py-2 text-base font-medium">Features</a>
                    <a href="#how-it-works" class="block text-gray-900 hover:text-red-600 px-3 py-2 text-base font-medium">How It Works</a>
                    <a href="{{ route('events.index') }}" class="block text-gray-900 hover:text-red-600 px-3 py-2 text-base font-medium">Opportunities</a>
                    <a href="#organizations" class="block text-gray-900 hover:text-red-600 px-3 py-2 text-base font-medium">Organizations</a>
                    <a href="#contact" class="block text-gray-900 hover:text-red-600 px-3 py-2 text-base font-medium">Contact</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="block text-gray-900 hover:text-red-600 px-3 py-2 text-base font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block text-gray-900 hover:text-red-600 px-3 py-2 text-base font-medium">Sign In</a>
                        <a href="{{ route('register') }}" class="block text-gray-900 hover:text-red-600 px-3 py-2 text-base font-medium">Volunteer Sign Up</a>
                        <a href="{{ route('organization.register') }}" class="block text-gray-900 hover:text-red-600 px-3 py-2 text-base font-medium">Organization Sign Up</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-red-600 to-red-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">
                        Volunteer for a Better 
                        <span class="text-red-600">UAE</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto">
                        Connect with meaningful volunteer opportunities, track your impact, and build a stronger community across the United Arab Emirates.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            <a href="{{ route('dashboard') }}" class="bg-red-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-red-700 transition-colors">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="bg-red-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-red-700 transition-colors">
                                Start Volunteering
                            </a>
                            <a href="{{ route('organization.register') }}" class="border-2 border-red-600 text-red-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-red-50 transition-colors">
                                Organizations Join Here
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Impact</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Join thousands of volunteers making a difference across the UAE</p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-red-600 mb-2">1000+</div>
                        <div class="text-gray-600">Active Volunteers</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-red-600 mb-2">250+</div>
                        <div class="text-gray-600">Partner Organizations</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-red-600 mb-2">5000+</div>
                        <div class="text-gray-600">Volunteer Hours</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-red-600 mb-2">150+</div>
                        <div class="text-gray-600">Events Completed</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Photo Gallery Section -->
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Our Impact in Action
                    </h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        See the amazing work our volunteers and organizations are doing across the UAE
                    </p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="relative h-64 rounded-lg overflow-hidden shadow-lg group">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10"></div>
                        <div class="absolute inset-0 bg-red-600 flex items-center justify-center">
                            <i class="fas fa-image text-white text-4xl opacity-20"></i>
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 z-20 text-white">
                            <p class="text-sm font-semibold">Beach Cleanup</p>
                            <p class="text-xs opacity-80">Dubai Marina</p>
                        </div>
                    </div>
                    
                    <div class="relative h-64 rounded-lg overflow-hidden shadow-lg group">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10"></div>
                        <div class="absolute inset-0 bg-blue-600 flex items-center justify-center">
                            <i class="fas fa-image text-white text-4xl opacity-20"></i>
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 z-20 text-white">
                            <p class="text-sm font-semibold">Food Distribution</p>
                            <p class="text-xs opacity-80">Community Center</p>
                        </div>
                    </div>
                    
                    <div class="relative h-64 rounded-lg overflow-hidden shadow-lg group">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10"></div>
                        <div class="absolute inset-0 bg-green-600 flex items-center justify-center">
                            <i class="fas fa-image text-white text-4xl opacity-20"></i>
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 z-20 text-white">
                            <p class="text-sm font-semibold">Tree Planting</p>
                            <p class="text-xs opacity-80">Al Ain Oasis</p>
                        </div>
                    </div>
                    
                    <div class="relative h-64 rounded-lg overflow-hidden shadow-lg group">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10"></div>
                        <div class="absolute inset-0 bg-purple-600 flex items-center justify-center">
                            <i class="fas fa-image text-white text-4xl opacity-20"></i>
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 z-20 text-white">
                            <p class="text-sm font-semibold">Education Program</p>
                            <p class="text-xs opacity-80">Sharjah Schools</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Events Browser Section -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Upcoming Volunteer Opportunities
                    </h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Browse and join exciting volunteer opportunities happening across the UAE
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <!-- Sample Event Card 1 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center">
                            <i class="fas fa-hands-helping text-white text-5xl"></i>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>Coming Soon</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Community Support Event</h3>
                            <p class="text-gray-600 mb-4 text-sm">Help support local communities through various activities including food distribution and assistance programs.</p>
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span>Dubai</span>
                            </div>
                            <a href="{{ route('events.index') }}" class="block w-full text-center bg-red-600 text-white py-2 rounded-md hover:bg-red-700 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                    
                    <!-- Sample Event Card 2 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center">
                            <i class="fas fa-leaf text-white text-5xl"></i>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>Coming Soon</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Environmental Clean-up</h3>
                            <p class="text-gray-600 mb-4 text-sm">Join us in keeping UAE beaches and parks clean and beautiful for everyone to enjoy.</p>
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span>Abu Dhabi</span>
                            </div>
                            <a href="{{ route('events.index') }}" class="block w-full text-center bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                    
                    <!-- Sample Event Card 3 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-5xl"></i>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>Coming Soon</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Education & Tutoring</h3>
                            <p class="text-gray-600 mb-4 text-sm">Share your knowledge by tutoring students and helping them achieve their educational goals.</p>
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span>Sharjah</span>
                            </div>
                            <a href="{{ route('events.index') }}" class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <a href="{{ route('events.index') }}" class="inline-flex items-center bg-red-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-red-700 transition-colors">
                        Browse All Opportunities
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Everything You Need to Volunteer
                    </h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Our platform makes it easy to find, join, and track your volunteer journey
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-lg shadow-sm border text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-search text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Find Opportunities</h3>
                        <p class="text-gray-600">
                            Browse hundreds of volunteer opportunities across the UAE that match your interests and skills
                        </p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-lg shadow-sm border text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-calendar-check text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Easy Registration</h3>
                        <p class="text-gray-600">
                            Apply to events with a simple click and manage all your volunteer activities in one place
                        </p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-lg shadow-sm border text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-certificate text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Track Your Impact</h3>
                        <p class="text-gray-600">
                            Earn certificates, badges, and track your volunteer hours to showcase your contributions
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        How It Works
                    </h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Getting started with SwaedUAE is simple and straightforward
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center text-white text-xl font-bold mx-auto mb-4">
                            1
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Sign Up</h3>
                        <p class="text-gray-600">
                            Create your volunteer profile or register your organization
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center text-white text-xl font-bold mx-auto mb-4">
                            2
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Browse</h3>
                        <p class="text-gray-600">
                            Find volunteer opportunities that match your interests
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center text-white text-xl font-bold mx-auto mb-4">
                            3
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Apply</h3>
                        <p class="text-gray-600">
                            Apply to events and get confirmed by organizations
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center text-white text-xl font-bold mx-auto mb-4">
                            4
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Volunteer</h3>
                        <p class="text-gray-600">
                            Attend events and make a positive impact in your community
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Organizations Section -->
        <section id="organizations" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Partner Organizations
                    </h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Join leading organizations making a difference across the UAE
                    </p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-sm border text-center">
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mx-auto mb-4"></div>
                        <h3 class="font-semibold text-gray-900">Red Crescent</h3>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border text-center">
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mx-auto mb-4"></div>
                        <h3 class="font-semibold text-gray-900">UAE Hope</h3>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border text-center">
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mx-auto mb-4"></div>
                        <h3 class="font-semibold text-gray-900">Green Earth</h3>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border text-center">
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mx-auto mb-4"></div>
                        <h3 class="font-semibold text-gray-900">Youth Empower</h3>
                    </div>
                </div>
                
                <div class="text-center mt-12">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-red-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-red-700 transition-colors">
                            View All Organizations
                        </a>
                    @else
                        <a href="{{ route('organization.register') }}" class="bg-red-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-red-700 transition-colors">
                            Register Your Organization
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Get In Touch
                    </h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Have questions? We're here to help you make a difference
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-envelope text-red-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Us</h3>
                        <a href="mailto:support@swaeduae.ae" class="text-gray-600 hover:text-red-600">support@swaeduae.ae</a>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-phone text-red-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Call Us</h3>
                        <a href="tel:+97141234567" class="text-gray-600 hover:text-red-600">+971 4 123 4567</a>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-whatsapp text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">WhatsApp</h3>
                        <a href="https://wa.me/971501234567" target="_blank" class="text-gray-600 hover:text-green-600">+971 50 123 4567</a>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-map-marker-alt text-red-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Visit Us</h3>
                        <p class="text-gray-600">Dubai, UAE</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
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
                            <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                            <li><a href="#how-it-works" class="hover:text-white transition-colors">How It Works</a></li>
                            <li><a href="{{ route('events.index') }}" class="hover:text-white transition-colors">Opportunities</a></li>
                            <li><a href="{{ route('organizations.index') }}" class="hover:text-white transition-colors">Organizations</a></li>
                            <li><a href="#contact" class="hover:text-white transition-colors">Contact</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Resources</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="{{ route('pages.show', 'volunteer-guide') }}" class="hover:text-white transition-colors">Volunteer Guide</a></li>
                            <li><a href="{{ route('pages.show', 'organization-resources') }}" class="hover:text-white transition-colors">Organization Resources</a></li>
                            <li><a href="{{ route('pages.show', 'faq') }}" class="hover:text-white transition-colors">FAQ</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Legal</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="{{ route('pages.show', 'privacy-policy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                            <li><a href="{{ route('pages.show', 'terms-of-service') }}" class="hover:text-white transition-colors">Terms of Service</a></li>
                            <li><a href="{{ route('pages.show', 'cookie-policy') }}" class="hover:text-white transition-colors">Cookie Policy</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; 2025 SwaedUAE. All rights reserved.</p>
                </div>
            </div>
        </footer>
        
        <!-- WhatsApp Floating Button -->
        <a href="https://wa.me/971501234567?text=Hello!%20I%20have%20a%20question%20about%20SwaedUAE" 
           target="_blank" 
           class="fixed bottom-6 right-6 bg-green-500 text-white rounded-full p-4 shadow-lg hover:bg-green-600 transition-all hover:scale-110 z-50"
           aria-label="Contact us on WhatsApp">
            <i class="fab fa-whatsapp text-3xl"></i>
        </a>
    </body>
</html>