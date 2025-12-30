@props([
    'active' => 'preview',
])

<div x-data="{ activeTab: '{{ $active }}' }" class="w-full" x-cloak>
    <!-- Tab Navigation -->
    <div class="bg-white rounded-t-lg shadow-sm border-b border-gray-200">
        <nav class="flex space-x-2 sm:space-x-4 px-2 sm:px-6 overflow-x-auto whitespace-nowrap scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100"
            aria-label="Tabs" style="scrollbar-width: thin;">
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
