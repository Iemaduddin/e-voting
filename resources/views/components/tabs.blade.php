@props([
    'active' => 'preview',
])

<div x-data="{ activeTab: '{{ $active }}' }" class="w-full" x-cloak>
    <!-- Tab Navigation -->
    <div class="bg-white rounded-t-lg shadow-sm border-b border-gray-200">
        <nav class="flex space-x-2 px-6" aria-label="Tabs">
            {{ $navigation ?? '' }}
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content bg-white rounded-b-lg shadow-sm min-h-[400px] relative">
        {{ $slot }}
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
