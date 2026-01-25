<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Estimo') }} - Professional Estimation Software</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-white">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-md border-b border-gray-200 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <x-application-logo class="h-8 w-8 text-indigo-600" />
                        <span class="text-xl font-bold text-gray-900">Estimo</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-gray-900 transition">Features</a>
                    <a href="#pricing" class="text-gray-600 hover:text-gray-900 transition">Pricing</a>
                    <a href="#about" class="text-gray-600 hover:text-gray-900 transition">About</a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 transition">Sign in</a>
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Get
                            Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="pt-24 pb-16 sm:pt-32 sm:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl sm:text-6xl font-bold text-gray-900 tracking-tight">
                    Professional Estimation
                    <span class="block text-indigo-600">Made Simple</span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    Create accurate estimates, manage clients, and grow your business with our powerful estimation
                    software.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}"
                        class="px-8 py-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold text-lg shadow-lg hover:shadow-xl">
                        Start Free Trial
                    </a>
                    <a href="#features"
                        class="px-8 py-4 bg-white text-gray-900 border-2 border-gray-300 rounded-lg hover:border-gray-400 transition font-semibold text-lg">
                        Learn More
                    </a>
                </div>
                <p class="mt-4 text-sm text-gray-500">14-day free trial • No credit card required</p>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-16 sm:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Everything you need</h2>
                <p class="mt-4 text-lg text-gray-600">Powerful features to streamline your estimation process</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Smart Estimates</h3>
                    <p class="text-gray-600">Create professional estimates in minutes with our intuitive interface and
                        templates.</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Client Management</h3>
                    <p class="text-gray-600">Keep all your client information organized and accessible in one place.</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Analytics & Reports</h3>
                    <p class="text-gray-600">Track your performance and make data-driven decisions with detailed
                        analytics.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-indigo-600 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-12 sm:px-12 sm:py-16 text-center">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                        Ready to get started?
                    </h2>
                    <p class="text-lg text-indigo-100 mb-8 max-w-2xl mx-auto">
                        Join thousands of professionals who trust Estimo for their estimation needs.
                    </p>
                    <a href="{{ route('register') }}"
                        class="inline-block px-8 py-4 bg-white text-indigo-600 rounded-lg hover:bg-gray-50 transition font-semibold text-lg shadow-lg">
                        Start Your Free Trial
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center text-gray-600">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Estimo') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>
