<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }} - Professional Quoting & Cost Estimation Platform</title>
        <meta name="description" content="Create professional quotes and cost estimations for construction, manufacturing, and professional services. Multi-industry SaaS platform built with Laravel.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-white dark:bg-gray-900 font-sans">
        <!-- Navigation -->
        <nav class="border-b border-gray-200 dark:border-gray-800 sticky top-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-xl">E</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">{{ config('app.name') }}</span>
                        </a>
                    </div>
                    @if (Route::has('login'))
                        <div class="flex items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white font-medium">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white font-medium">
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition">
                                        Get Started
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 py-20 sm:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white leading-tight mb-6">
                        Professional Quotes &<br>
                        <span class="text-indigo-600 dark:text-indigo-400">Cost Estimations</span> Made Simple
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-8">
                        Create, manage, and deliver professional quotes across multiple industries. Built for contractors, manufacturers, and service providers.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 border border-transparent rounded-lg font-semibold text-base text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition shadow-lg hover:shadow-xl">
                                Start Free Trial
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        @endif
                        <a href="#features" class="inline-flex items-center justify-center px-8 py-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg font-semibold text-base text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Everything you need to quote faster
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Powerful features designed for modern businesses
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Visual Quote Builder</h3>
                        <p class="text-gray-600 dark:text-gray-400">Create professional quotes with drag-and-drop ease. Real-time calculations and auto-save functionality.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Advanced Pricing Engine</h3>
                        <p class="text-gray-600 dark:text-gray-400">Track costs vs selling prices, apply markups, volume discounts, and client-specific pricing rules.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Client Portal</h3>
                        <p class="text-gray-600 dark:text-gray-400">Secure client access to view quotes, accept/reject, add comments, and sign digitally.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Professional PDFs</h3>
                        <p class="text-gray-600 dark:text-gray-400">Generate branded PDF quotes with your logo, colors, and custom templates.</p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Product Catalog</h3>
                        <p class="text-gray-600 dark:text-gray-400">Manage your products and services with categories, variants, and bulk import/export.</p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Analytics & Reporting</h3>
                        <p class="text-gray-600 dark:text-gray-400">Track quote conversion rates, revenue trends, and team performance with detailed reports.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Industries Section -->
        <section class="py-20 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Built for Multiple Industries
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Customizable for your specific business needs
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="bg-white dark:bg-gray-900 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Construction & Trades</h3>
                        <p class="text-gray-600 dark:text-gray-400">Electrical, plumbing, HVAC, general contractors</p>
                    </div>

                    <div class="text-center">
                        <div class="bg-white dark:bg-gray-900 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Manufacturing</h3>
                        <p class="text-gray-600 dark:text-gray-400">Custom fabrication, production, assembly</p>
                    </div>

                    <div class="text-center">
                        <div class="bg-white dark:bg-gray-900 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Professional Services</h3>
                        <p class="text-gray-600 dark:text-gray-400">Consulting, IT services, creative agencies</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-indigo-600 dark:bg-indigo-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                    Ready to streamline your quoting process?
                </h2>
                <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                    Join hundreds of businesses creating professional quotes faster
                </p>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-white border border-transparent rounded-lg font-semibold text-base text-indigo-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600 transition shadow-lg hover:shadow-xl">
                        Start Your Free Trial
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-xl">E</span>
                            </div>
                            <span class="text-xl font-bold text-white">{{ config('app.name') }}</span>
                        </div>
                        <p class="text-sm max-w-md">
                            Professional quoting and cost estimation platform for modern businesses.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold mb-4">Product</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#features" class="hover:text-white transition">Features</a></li>
                            <li><a href="#" class="hover:text-white transition">Pricing</a></li>
                            <li><a href="#" class="hover:text-white transition">Industries</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold mb-4">Company</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white transition">About</a></li>
                            <li><a href="#" class="hover:text-white transition">Contact</a></li>
                            <li><a href="#" class="hover:text-white transition">Support</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-8 text-sm text-center">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
