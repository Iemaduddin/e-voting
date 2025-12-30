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

@php
    $menuItems = [
        [
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'icon' =>
                'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'roles' => ['Super Admin', 'Organization', 'Voter'],
            'group' => 'main',
        ],
        [
            'name' => 'Anggota Organisasi',
            'route' => 'members.index',
            'icon' =>
                'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            'roles' => ['Organization'],
            'group' => 'main',
        ],
        [
            'name' => 'Kelola Pemilihan',
            'route' => 'elections.index',
            'icon' =>
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'roles' => ['Organization'],
            'group' => 'main',
        ],
        [
            'name' => 'Jurusan',
            'route' => 'jurusan.index',
            'icon' =>
                'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'roles' => ['Super Admin'],
            'group' => 'admin',
        ],
        [
            'name' => 'Prodi',
            'route' => 'prodi.index',
            'icon' =>
                'M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m14 0h2m-2 0h-4m-6 0H3m2 0h4M7 9h1m-1 4h1m4-4h1m-1 4h1m-5 8v-3a1 1 0 011-1h2a1 1 0 011 1v3m-4 0h4',
            'roles' => ['Super Admin'],
            'group' => 'admin',
        ],
        [
            'name' => 'Pengguna',
            'route' => 'users.index',
            'icon' =>
                'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'roles' => ['Super Admin'],
            'group' => 'admin',
        ],
        [
            'name' => 'Profil',
            'route' => 'profile',
            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'roles' => ['Super Admin', 'Organization', 'Voter'],
            'group' => 'settings',
        ],
    ];
@endphp

<!-- Sidebar -->
<aside
    class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 shadow-lg transform transition-all duration-300 ease-in-out"
    :class="{
        '-translate-x-full': !sidebarOpen,
        'w-20': !sidebarOpen,
        'w-72': sidebarOpen,
        'md:translate-x-0': true
    }">

    <!-- Logo -->
    <div class="flex items-center justify-between h-16 border-b border-gray-200"
        :class="sidebarOpen ? 'px-6' : 'px-4 justify-center'">
        <div class="flex items-center mx-auto" :class="!sidebarOpen && 'hidden md:flex'">
            <img src="{{ asset('assets/image/id_logo.png') }}" alt="Logo" class="h-10 w-10 object-contain"
                :class="sidebarOpen && 'mr-3'">
            <span class="text-xl font-bold" x-show="sidebarOpen" x-transition>E-Voting</span>
        </div>
        <!-- Close/Toggle button -->
        <button @click="sidebarOpen = !sidebarOpen" class="block md:hidden  focus:outline-none"
            :class="!sidebarOpen && 'md:block hidden'">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 pb-20 overflow-y-auto h-[calc(100vh-140px)]" :class="sidebarOpen ? 'px-4' : 'px-2'">
        @php
            $currentGroup = null;
            $mainMenus = array_filter($menuItems, fn($item) => $item['group'] === 'main');
            $adminMenus = array_filter($menuItems, fn($item) => $item['group'] === 'admin');
            $settingsMenus = array_filter($menuItems, fn($item) => $item['group'] === 'settings');
        @endphp

        <!-- Main Menu -->
        <div class="space-y-1">
            @foreach ($mainMenus as $menu)
                @if (auth()->user()->hasAnyRole($menu['roles']))
                    <a href="{{ !empty($menu['route']) ? route($menu['route']) : '#' }}"
                        {{ !empty($menu['route']) ? 'wire:navigate' : '' }}
                        class="flex items-center py-3 rounded-lg transition duration-150 group {{ !empty($menu['route']) && request()->routeIs($menu['route']) ? 'bg-blue-50 text-blue-700 border-t-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 border-t-2 border-transparent hover:border-blue-400' }}"
                        :class="sidebarOpen ? 'px-4' : 'px-2 justify-center'"
                        :title="!sidebarOpen ? '{{ $menu['name'] }}' : ''">
                        <svg class="w-5 h-5 {{ !empty($menu['route']) && request()->routeIs($menu['route']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"
                            :class="sidebarOpen && 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $menu['icon'] }}"></path>
                        </svg>
                        <span class="font-medium" x-show="sidebarOpen" x-transition>{{ $menu['name'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>

        <!-- Admin Menu (Super Admin Only) -->
        @if (auth()->user()->hasRole('Super Admin'))
            <!-- Divider -->
            <div class="my-4 border-t border-gray-200"></div>

            <div class="space-y-1">
                @foreach ($adminMenus as $menu)
                    @if (auth()->user()->hasAnyRole($menu['roles']))
                        <a href="{{ !empty($menu['route']) ? route($menu['route']) : '#' }}"
                            {{ !empty($menu['route']) ? 'wire:navigate' : '' }}
                            class="flex items-center py-3 rounded-lg transition duration-150 group {{ !empty($menu['route']) && request()->routeIs($menu['route']) ? 'bg-blue-50 text-blue-700 border-t-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 border-t-2 border-transparent hover:border-blue-400' }}"
                            :class="sidebarOpen ? 'px-4' : 'px-2 justify-center'"
                            :title="!sidebarOpen ? '{{ $menu['name'] }}' : ''">
                            <svg class="w-5 h-5 {{ !empty($menu['route']) && request()->routeIs($menu['route']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"
                                :class="sidebarOpen && 'mr-3'" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $menu['icon'] }}"></path>
                            </svg>
                            <span class="font-medium" x-show="sidebarOpen" x-transition>{{ $menu['name'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Divider -->
        <div class="my-4 border-t border-gray-200"></div>

        <!-- Settings -->
        <div class="space-y-1">
            @foreach ($settingsMenus as $menu)
                @if (auth()->user()->hasAnyRole($menu['roles']))
                    <a href="{{ !empty($menu['route']) ? route($menu['route']) : '#' }}"
                        {{ !empty($menu['route']) ? 'wire:navigate' : '' }}
                        class="flex items-center py-3 rounded-lg transition duration-150 group {{ !empty($menu['route']) && request()->routeIs($menu['route']) ? 'bg-blue-50 text-blue-700 border-t-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 border-t-2 border-transparent hover:border-blue-300' }}"
                        :class="sidebarOpen ? 'px-4' : 'px-2 justify-center'"
                        :title="!sidebarOpen ? '{{ $menu['name'] }}' : ''">
                        <svg class="w-5 h-5 {{ !empty($menu['route']) && request()->routeIs($menu['route']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"
                            :class="sidebarOpen && 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $menu['icon'] }}"></path>
                        </svg>
                        <span class="font-medium" x-show="sidebarOpen" x-transition>{{ $menu['name'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </nav>

    <!-- Logout Button at Bottom -->
    <div class="absolute bottom-0 w-full border-t border-gray-200">
        <button wire:click="logout"
            class="w-full flex items-center transition-all duration-200 shadow-lg hover:shadow-xl"
            :class="sidebarOpen ? 'px-4 py-4 justify-start' : 'px-3 py-3 justify-center'"
            :title="!sidebarOpen ? 'Keluar' : ''">
            <div class="bg-red-600 rounded-full flex items-center justify-center shadow-md"
                :class="sidebarOpen ? 'w-10 h-10 mr-3' : 'w-9 h-9'">
                <svg class="text-white" :class="sidebarOpen ? 'w-5 h-5' : 'w-4 h-4'" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
            </div>
            <div class="flex-1 text-left" x-show="sidebarOpen" x-transition>
                <p class="font-semibold text-red-700">Keluar</p>
                <p class="text-sm text-red-600">Keluar dari akun Anda</p>
            </div>
        </button>
    </div>
</aside>
