<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex">
            <!-- Left Side - Brand/Image -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 to-purple-700 p-12 flex-col justify-between">
                <div>
                    <a href="/" wire:navigate class="flex items-center space-x-2">
                        <x-application-logo class="w-10 h-10 text-white" />
                        <span class="text-2xl font-bold text-white">Estimo</span>
                    </a>
                </div>
                
                <div class="text-white">
                    <h2 class="text-4xl font-bold mb-4">Professional Estimation Made Simple</h2>
                    <p class="text-lg text-indigo-100">Create accurate estimates, manage clients, and grow your business with our powerful software.</p>
                    
                    <div class="mt-12 space-y-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-indigo-200 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <h3 class="font-semibold">14-day free trial</h3>
                                <p class="text-indigo-100 text-sm">No credit card required</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-indigo-200 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <h3 class="font-semibold">Cancel anytime</h3>
                                <p class="text-indigo-100 text-sm">No long-term contracts</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-indigo-200 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <h3 class="font-semibold">Premium support</h3>
                                <p class="text-indigo-100 text-sm">Get help when you need it</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-indigo-200 text-sm">
                    <p>"Estimo has transformed how we create estimates. The time savings alone have been incredible."</p>
                    <p class="mt-2 font-semibold text-white">- Sarah Johnson, CEO</p>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
                <div class="mx-auto w-full max-w-sm lg:w-96">
                    <div class="lg:hidden mb-8">
                        <a href="/" wire:navigate class="flex items-center space-x-2">
                            <x-application-logo class="w-10 h-10 text-indigo-600" />
                            <span class="text-2xl font-bold text-gray-900">Estimo</span>
                        </a>
                    </div>

                    {{ $slot }}
                    
                    <div class="mt-8">
                        <a href="/" wire:navigate class="text-sm text-gray-600 hover:text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
