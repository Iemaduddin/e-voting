@props([
    'icon' => null,
    'title' => 'Tidak ada data',
    'description' => '',
    'action' => null,
    'actionLabel' => '',
])

<div class="text-center py-12 px-4">
    <!-- Icon -->
    @if ($icon)
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
            {!! $icon !!}
        </div>
    @else
        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
    @endif

    <!-- Title -->
    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $title }}</h3>

    <!-- Description -->
    @if ($description)
        <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">{{ $description }}</p>
    @endif

    <!-- Action Button -->
    @if ($action)
        <div class="mt-6">
            {{ $action }}
        </div>
    @elseif ($actionLabel)
        <button type="button"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ $actionLabel }}
        </button>
    @endif

    <!-- Additional Content -->
    {{ $slot }}
</div>
