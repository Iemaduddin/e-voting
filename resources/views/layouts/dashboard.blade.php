<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}@isset($subtitle)
        - {{ $subtitle }}
    @endisset
</title>
<link rel="icon" href="{{ asset('assets/image/id_logo.png') }}" type="image/png">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

<!-- Scripts -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
@flasher_render
</head>

<body class="font-sans antialiased">
<div x-data="{
    sidebarOpen: localStorage.getItem('sidebarOpen') === 'false' ? false : true,
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        localStorage.setItem('sidebarOpen', this.sidebarOpen);
    }
}" class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <livewire:components.dashboard.sidebar />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
        :class="{
            'md:ml-20': !sidebarOpen,
            'md:ml-72': sidebarOpen
        }">
        <!-- Top Navbar -->
        <livewire:components.dashboard.navbar :subtitle="$subtitle ?? 'Dashboard'" /> <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-gray-100">
            <div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Page Header -->
                @if (isset($pageTitle) || isset($pageDescription) || isset($headerAction))
                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            @if (isset($pageTitle))
                                <h2 class="text-2xl font-bold text-gray-900">{{ $pageTitle }}</h2>
                            @endif
                            @if (isset($pageDescription))
                                <p class="mt-1 text-sm text-gray-600">{{ $pageDescription }}</p>
                            @endif
                        </div>
                        @if (isset($headerAction))
                            <div>
                                {{ $headerAction }}
                            </div>
                        @endif
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <livewire:components.dashboard.footer />
    </div>
</div>
</body>

</html>
