@props([
    'name' => '',
])

<div x-show="activeTab === '{{ $name }}'" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak style="display: none;" class="p-6"
    {{ $attributes }}>
    {{ $slot }}
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
