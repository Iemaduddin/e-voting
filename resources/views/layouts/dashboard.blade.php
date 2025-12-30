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
@livewireStyles
</head>

<body class="font-sans antialiased overflow-x-hidden">
<div x-data="{
    sidebarOpen: window.innerWidth >= 768 ? (localStorage.getItem('sidebarOpen') === 'false' ? false : true) : false,
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        if (window.innerWidth >= 768) {
            localStorage.setItem('sidebarOpen', this.sidebarOpen);
        }
    }
}" class="min-h-screen bg-gray-100 flex overflow-x-hidden">
    <!-- Sidebar -->
    <livewire:components.dashboard.sidebar />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 overflow-x-hidden"
        :class="{
            'md:ml-20': !sidebarOpen,
            'md:ml-64': sidebarOpen
        }">
        <!-- Top Navbar -->
        <livewire:components.dashboard.navbar :subtitle="$subtitle ?? 'Dashboard'" /> <!-- Page Content -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-100">
            <div class="mx-auto px-4 sm:px-6 lg:px-8 py-8 max-w-full">
                <!-- Page Header -->
                @if (isset($pageTitle) || isset($pageDescription) || isset($headerAction))
                    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                        <div>
                            @if (isset($pageTitle))
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $pageTitle }}</h2>
                            @endif
                            @if (isset($pageDescription))
                                <p class="mt-1 text-sm text-gray-600">{{ $pageDescription }}</p>
                            @endif
                        </div>
                        @if (isset($headerAction))
                            <div class="w-full sm:w-auto">
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

@livewireScripts
</body>

</html>
