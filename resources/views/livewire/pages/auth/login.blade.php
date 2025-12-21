<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;
    public $showNotification = false;
    public $notificationMessage = '';
    public $notificationType = 'success';

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        // Reset notification sebelumnya
        $this->showNotification = false;

        try {
            $this->validate();
            $this->form->authenticate();

            Session::regenerate();

            $this->showNotification = true;
            $this->notificationMessage = 'Login berhasil! Selamat datang kembali.';
            $this->notificationType = 'success';

            // Redirect berdasarkan role
            $redirectUrl = auth()->user()->hasRole('Voter') ? route('vote.index', absolute: false) : route('dashboard', absolute: false);

            // Dispatch browser event untuk redirect dengan delay
            $this->dispatch('login-success', url: $redirectUrl);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->showNotification = true;
            $this->notificationType = 'error';

            // Ambil pesan error dari ValidationException
            $errors = $e->validator->errors();
            if ($errors->has('form.loginRequest')) {
                $this->notificationMessage = $errors->first('form.loginRequest');
            } elseif ($errors->has('form.login')) {
                $this->notificationMessage = $errors->first('form.login');
            } else {
                $this->notificationMessage = 'Login gagal! Periksa kembali email/username dan password Anda.';
            }
        }
    }
}; ?>

<div class="w-full" x-data="{
    redirectUrl: ''
}"
    @login-success.window="
    redirectUrl = $event.detail.url;
    setTimeout(() => {
        Livewire.navigate(redirectUrl);
    }, 1000);
">
    <x-flash-notification :show="$showNotification" :message="$notificationMessage" :type="$notificationType" />

    <div class="mb-8 mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang Kembali</h2>
        <p class="text-gray-600 text-sm">Silakan masuk ke akun Anda</p>
    </div>

    <form wire:submit="login" class="space-y-6">
        <!-- Email or Username -->
        <div>
            <x-input-label for="login" value="Email atau Username" class="text-gray-700 font-semibold" />
            <div class="relative mt-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z" />
                    </svg>
                </div>
                <x-text-input wire:model="form.login" id="login"
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                    type="text" name="login" placeholder="Masukkan email atau username" required autofocus
                    autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('form.login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div x-data="{ showPassword: false }">
            <x-input-label for="password" value="Password" class="text-gray-700 font-semibold" />
            <div class="relative mt-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <x-text-input wire:model="form.password" id="password"
                    class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                    ::type="showPassword ? 'text' : 'password'" name="password" placeholder="Masukkan password" required
                    autocomplete="current-password" />
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition duration-150">
                    <!-- Eye Icon (show password) -->
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <!-- Eye Slash Icon (hide password) -->
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember"
                class="inline-flex items-center cursor-pointer hover:text-blue-600 transition duration-150">
                <input wire:model="form.remember" id="remember" type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 cursor-pointer"
                    name="remember">
                <span class="ms-2 text-sm text-gray-700 font-medium">Ingat Saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-600 hover:text-blue-800 font-medium transition duration-150"
                    href="{{ route('password.request') }}" wire:navigate>
                    Lupa Password?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div>
            <button type="submit" wire:loading.attr="disabled"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 disabled:opacity-70 disabled:cursor-not-allowed">

                <!-- Loading Spinner -->
                <svg wire:loading wire:target="login" class="animate-spin h-5 w-5 mr-2"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>

                <!-- Login Icon (hidden when loading) -->
                <svg wire:loading.remove wire:target="login" class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M3 4.25A2.25 2.25 0 015.25 2h5.5A2.25 2.25 0 0113 4.25v2a.75.75 0 01-1.5 0v-2a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75v-2a.75.75 0 011.5 0v2A2.25 2.25 0 0110.75 18h-5.5A2.25 2.25 0 013 15.75V4.25z"
                        clip-rule="evenodd" />
                    <path fill-rule="evenodd"
                        d="M6 10a.75.75 0 01.75-.75h9.546l-1.048-.943a.75.75 0 111.004-1.114l2.5 2.25a.75.75 0 010 1.114l-2.5 2.25a.75.75 0 11-1.004-1.114l1.048-.943H6.75A.75.75 0 016 10z"
                        clip-rule="evenodd" />
                </svg>

                <!-- Button Text -->
                <span wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading wire:target="login">Memproses...</span>
            </button>
        </div>
    </form>
</div>
