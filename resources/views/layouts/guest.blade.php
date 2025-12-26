<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('assets/image/id_logo.png') }}" type="image/png">

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
        <div class="hidden lg:flex lg:w-1/2 bg-blue-600 relative rounded-r-3xl overflow-hidden">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                <img src="{{ asset('assets/image/id_logo.png') }}" alt="Logo"
                    class="w-40 h-40 object-contain mb-8 drop-shadow-2xl bg-white p-4 rounded-full hover:scale-105 transition-all duration-300">
                <h1 class="text-5xl font-bold mb-4">E-Voting</h1>
                <p class="text-xl text-blue-100 text-center max-w-md">Sistem Pemilihan Elektronik yang Aman dan
                    Terpercaya</p>
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
