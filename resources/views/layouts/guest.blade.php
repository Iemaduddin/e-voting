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
    @flasher_render
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex bg-gray-50">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-blue-600 relative overflow-hidden">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                <img src="{{ asset('assets/image/id_logo.png') }}" alt="Logo"
                    class="w-32 h-32 object-contain mb-8 drop-shadow-2xl">
                <h1 class="text-5xl font-bold mb-4">E-Voting</h1>
                <p class="text-xl text-blue-100 text-center max-w-md">Sistem Pemilihan Elektronik yang Aman dan
                    Terpercaya</p>
                <div class="mt-12 space-y-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-blue-100">Transparan & Akuntabel</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                        <span class="text-blue-100">Keamanan Terjamin</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span class="text-blue-100">Cepat & Efisien</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('assets/image/id_logo.png') }}" alt="Logo"
                        class="w-20 h-20 object-contain mx-auto mb-4">
                    <h1 class="text-3xl font-bold text-gray-800">E-Voting</h1>
                    <p class="text-gray-600 text-sm mt-2">Sistem Pemilihan Elektronik</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} E-Voting System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
