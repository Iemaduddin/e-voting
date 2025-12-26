<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/login', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side - Logo & Brand -->
            <div class="flex items-center space-x-8">
                <a href="{{ route('vote.index') }}" wire:navigate class="flex items-center space-x-3 flex-shrink-0">
                    <img src="{{ asset('assets/image/id_logo.png') }}" alt="Logo" class="h-10 w-10 object-contain">
                    <div>
                        <span
                            class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">E-Voting</span>
                        <p class="text-xs text-gray-500 hidden sm:block">Sistem Pemilihan Elektronik</p>
                    </div>
                </a>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden md:flex md:items-center md:space-x-1">
                    <a href="{{ route('vote.index') }}" wire:navigate
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('vote.index') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        Beranda
                    </a>
                    <a href="#"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-indigo-600 rounded-lg transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Panduan
                    </a>
                </div>
            </div>

            <!-- Right Side - User Menu -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <button
                    class="hidden md:block p-2 rounded-full text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none transition duration-150 relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                </button>

                <!-- User Dropdown (Desktop) -->
                <div class="hidden md:block">
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center px-3 py-2 rounded-xl text-sm font-medium text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 focus:outline-none transition-all duration-200 border border-transparent hover:border-indigo-200 shadow-sm hover:shadow-md">
                                <div
                                    class="w-9 h-9 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full flex items-center justify-center mr-2 shadow-md ring-2 ring-white">
                                    <span class="text-white font-bold text-sm">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ auth()->user()->getRoleNames()->first() ?? 'Voter' }}</p>
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
                            <div class="px-4 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-lg">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg">
                                        <span class="text-indigo-600 font-bold text-lg">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}
                                        </p>
                                        <p class="text-xs text-white/90 truncate">{{ auth()->user()->email }}</p>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white text-indigo-600 mt-1">
                                            {{ auth()->user()->getRoleNames()->first() ?? 'Voter' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-2">
                                <x-dropdown-link :href="route('profile')" wire:navigate
                                    class="hover:bg-indigo-50 transition-colors duration-150">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
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
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
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

                <!-- Mobile menu button -->
                <button @click="open = ! open"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none transition duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden md:hidden border-t border-gray-200 bg-white">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('vote.index') }}" wire:navigate
                class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('vote.index') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }} transition duration-150">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Beranda
                </div>
            </a>
            <a href="#"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 transition duration-150">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Panduan
                </div>
            </a>
        </div>

        <!-- Mobile User Menu -->
        <div class="pt-4 pb-3 border-t border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
            <div class="flex items-center px-4 mb-3">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full flex items-center justify-center mr-3 shadow-lg">
                    <span class="text-white font-bold text-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-base font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</div>
                    <div class="text-sm text-gray-600 truncate">{{ auth()->user()->email }}</div>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white text-indigo-600 mt-1">
                        {{ auth()->user()->getRoleNames()->first() ?? 'Voter' }}
                    </span>
                </div>
            </div>
            <div class="space-y-1 px-2">
                <a href="{{ route('profile') }}" wire:navigate
                    class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-white transition duration-150">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Profil Saya</p>
                        <p class="text-xs text-gray-500">Lihat dan edit profil</p>
                    </div>
                </a>
                <button wire:click="logout"
                    class="w-full flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-white transition duration-150">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-medium">Keluar</p>
                        <p class="text-xs text-gray-500">Keluar dari akun Anda</p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</nav>
