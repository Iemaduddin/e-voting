<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
    <div class="mx-auto px-2 md:px-3">
        <div class="flex justify-between h-16">
            <!-- Left Side -->
            <div class="flex items-center">
                <!-- Menu Button (All Devices) -->
                <button @click="toggleSidebar()"
                    class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none transition duration-150">
                    <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="size-6">
                        <path fill-rule="evenodd"
                            d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z"
                            clip-rule="evenodd" />
                    </svg>

                    <svg x-show="!sidebarOpen"s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        fill="currentColor" class="size-6">
                        <path fill-rule="evenodd"
                            d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                            clip-rule="evenodd" />
                    </svg>

                </button>

                <!-- Page Title with Greeting -->
                <div class="ml-2 md:ml-4 max-w-[140px] sm:max-w-xs md:max-w-sm lg:max-w-md">
                    @php
                        $hour = now()->format('H');
                        $greeting = '';
                        $icon = '';
                        $iconColor = '';

                        if ($hour >= 5 && $hour < 11) {
                            $greeting = 'Pagi';
                            $icon =
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
                            $iconColor = 'text-amber-500';
                        } elseif ($hour >= 11 && $hour < 15) {
                            $greeting = 'Siang';
                            $icon =
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
                            $iconColor = 'text-orange-500';
                        } elseif ($hour >= 15 && $hour < 18) {
                            $greeting = 'Sore';
                            $icon =
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>';
                            $iconColor = 'text-purple-500';
                        } else {
                            $greeting = 'Malam';
                            $icon =
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>';
                            $iconColor = 'text-indigo-500';
                        }
                    @endphp

                    <div class="flex items-center gap-2 md:gap-2.5">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 md:w-6 md:h-6 {{ $iconColor }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                {!! $icon !!}
                            </svg>
                        </div>

                        <!-- Text -->
                        <div class="min-w-0 flex-1">
                            <p class="text-xs md:text-sm text-gray-500 font-medium">{{ $greeting }}</p>
                            <h1 class="text-sm md:text-base font-semibold text-gray-900 truncate"
                                title="{{ auth()->user()->name }}">
                                {{ auth()->user()->name }}
                            </h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <!-- User Dropdown -->
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button
                            class="flex items-center px-3 py-2 rounded-xl text-sm font-medium text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 focus:outline-none transition-all duration-200 border border-transparent hover:border-blue-200 shadow-sm hover:shadow-md">
                            <div
                                class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center mr-2 shadow-md ring-2 ring-white">
                                <span class="text-white font-bold text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->getRoleNames()->first() }}</p>
                            </div>
                            <svg class="ml-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- User Info Header -->
                        <div class="px-4 py-4 bg-blue-600 rounded-t-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-blue-600 font-bold text-lg">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-sm text-white truncate">{{ auth()->user()->email }}</p>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white text-blue-600 mt-1">
                                        {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            <x-dropdown-link :href="route('profile')" wire:navigate
                                class="hover:bg-blue-50 transition-colors duration-150">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Profil Saya</p>
                                        <p class="text-xs text-gray-500">Lihat dan edit profil</p>
                                    </div>
                                </div>
                            </x-dropdown-link>

                            <div class="border-t border-gray-100 my-2"></div>

                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link class="hover:bg-red-50 transition-colors duration-150">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">Keluar</p>
                                            <p class="text-xs text-gray-500">Keluar dari akun Anda</p>
                                        </div>
                                    </div>
                                </x-dropdown-link>
                            </button>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
