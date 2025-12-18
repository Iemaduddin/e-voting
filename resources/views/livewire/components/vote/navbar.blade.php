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

<nav x-data="{ open: false }" class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side - Logo & Brand -->
            <div class="flex items-center">
                <a href="{{ route('vote.index') }}" wire:navigate class="flex items-center space-x-3">
                    <img src="{{ asset('assets/image/id_logo.png') }}" alt="Logo" class="h-10 w-10 object-contain">
                    <div>
                        <span class="text-xl font-bold text-gray-800">E-Voting</span>
                        <p class="text-xs text-gray-500 hidden sm:block">Sistem Pemilihan Elektronik</p>
                    </div>
                </a>
            </div>

            <!-- Center - Navigation Links (Desktop) -->
            <div class="hidden md:flex md:items-center md:space-x-6">
                <a href="{{ route('vote.index') }}" wire:navigate
                    class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 {{ request()->routeIs('vote.index') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Beranda
                </a>
                <a href="#"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 transition duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Panduan
                </a>
            </div>

            <!-- Right Side - User Menu -->
            <div class="flex items-center space-x-4">
                <!-- Desktop User Menu -->
                <div class="hidden md:block">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 transition duration-150">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-white font-bold text-sm">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                <span>{{ auth()->user()->name }}</span>
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>

                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profil Saya
                            </x-dropdown-link>

                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    Keluar
                                </x-dropdown-link>
                            </button>
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
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden md:hidden border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('vote.index') }}" wire:navigate
                class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('vote.index') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }} transition duration-150">
                Beranda
            </a>
            <a href="#"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 transition duration-150">
                Panduan
            </a>
        </div>

        <!-- Mobile User Menu -->
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="flex items-center px-4 mb-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center mr-3">
                    <span class="text-white font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                    <div class="text-sm text-gray-500">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <div class="space-y-1 px-2">
                <a href="{{ route('profile') }}" wire:navigate
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 transition duration-150">
                    Profil Saya
                </a>
                <button wire:click="logout"
                    class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 transition duration-150">
                    Keluar
                </button>
            </div>
        </div>
    </div>
</nav>
