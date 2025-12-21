<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string', message: 'Email atau Username wajib diisi')]
    public string $login = '';

    #[Validate('required|string', message: 'Password wajib diisi')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Determine if login is email or username
        $fieldType = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $this->login,
            'password' => $this->password,
        ];

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.loginRequest' => 'Email/Username atau Password yang Anda masukkan salah.',
            ]);
        }
        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();

            $statusMhs = optional($user->mahasiswa)->status;
            $statusOrg = optional($user->organizationMember)->is_active;

            $messages = [
                'Lulus'    => 'Akses ditolak. Status akademik Anda telah dinyatakan lulus.',
                'Drop Out' => 'Akses ditolak. Status akademik Anda tercatat sebagai Drop Out.',
                'Cuti'    => 'Akses ditolak. Saat ini Anda sedang berada dalam status Cuti akademik.',
            ];
            if ($statusMhs && isset($messages[$statusMhs])) {
                $message = $messages[$statusMhs];
            } elseif ($statusOrg === false) {
                $message = 'Keanggotaan organisasi Anda tidak aktif. Silakan hubungi administrator.';
            } else {
                $message = 'Akun Anda tidak aktif. Silakan hubungi administrator.';
            }

            throw ValidationException::withMessages([
                'form.loginRequest' => $message,
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.login' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->login) . '|' . request()->ip());
    }
}
