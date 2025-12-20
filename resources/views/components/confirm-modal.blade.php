@props([
    'show' => false,
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'confirmText' => 'Konfirmasi',
    'cancelText' => 'Batal',
    'confirmAction' => '',
    'cancelAction' => '',
    'type' => 'danger', // danger, warning, info, success
])

@php
    $typeClasses = [
        'danger' => [
            'icon' => 'bg-red-100',
            'iconColor' => 'text-red-600',
            'button' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
        ],
        'warning' => [
            'icon' => 'bg-yellow-100',
            'iconColor' => 'text-yellow-600',
            'button' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
        ],
        'info' => [
            'icon' => 'bg-blue-100',
            'iconColor' => 'text-blue-600',
            'button' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        ],
        'success' => [
            'icon' => 'bg-green-100',
            'iconColor' => 'text-green-600',
            'button' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
        ],
    ];

    $classes = $typeClasses[$type] ?? $typeClasses['danger'];
@endphp

@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                wire:click="{{ $cancelAction }}"></div>

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full {{ $classes['icon'] }} sm:mx-0 sm:h-10 sm:w-10">
                        @if ($type === 'danger')
                            <svg class="h-6 w-6 {{ $classes['iconColor'] }}" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        @elseif ($type === 'warning')
                            <svg class="h-6 w-6 {{ $classes['iconColor'] }}" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        @elseif ($type === 'info')
                            <svg class="h-6 w-6 {{ $classes['iconColor'] }}" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                        @else
                            <svg class="h-6 w-6 {{ $classes['iconColor'] }}" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $title }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                {!! $message !!}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="{{ $confirmAction }}"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 {{ $classes['button'] }} text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ $confirmText }}
                    </button>
                    <button type="button" wire:click="{{ $cancelAction }}"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        {{ $cancelText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
