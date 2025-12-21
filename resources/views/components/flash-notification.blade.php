@props([
    'show' => false,
    'message' => '',
    'type' => 'success', // success, error, warning, info
])

@php
    $typeClasses = [
        'success' => [
            'border' => 'border-green-500',
            'bg' => 'bg-green-50',
            'text' => 'text-green-800',
            'icon' => 'text-green-500',
            'path' => 'M5 13l4 4L19 7',
        ],
        'error' => [
            'border' => 'border-red-500',
            'bg' => 'bg-red-50',
            'text' => 'text-red-800',
            'icon' => 'text-red-500',
            'path' => 'M6 18L18 6M6 6l12 12',
        ],
        'warning' => [
            'border' => 'border-yellow-500',
            'bg' => 'bg-yellow-50',
            'text' => 'text-yellow-800',
            'icon' => 'text-yellow-500',
            'path' =>
                'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
        'info' => [
            'border' => 'border-blue-500',
            'bg' => 'bg-blue-50',
            'text' => 'text-blue-800',
            'icon' => 'text-blue-500',
            'path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];

    $classes = $typeClasses[$type] ?? $typeClasses['success'];
@endphp

@if ($show)
    <div x-data="{ show: true }" x-show="show"
        @if ($type !== 'error') x-init="setTimeout(() => show = false, 3000)" @endif
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90"
        x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90"
        class="{{ $classes['bg'] }} border-l-4 {{ $classes['border'] }} p-4 rounded-lg shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 {{ $classes['icon'] }} mr-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $classes['path'] }}" />
                </svg>
                <p class="text-sm font-medium {{ $classes['text'] }}">{{ $message }}</p>
            </div>
            @if ($type === 'error')
                <button @click="show = false" class="ml-4 {{ $classes['icon'] }} hover:opacity-75 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
@endif
