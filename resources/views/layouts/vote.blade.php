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

<body class="font-sans antialiased bg-gray-50">
<div class="min-h-screen flex flex-col">
    <!-- Navbar -->
    <livewire:components.vote.navbar />

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <livewire:components.vote.footer />
</div>
</body>

</html>
