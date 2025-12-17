<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::redirect('/', '/login', 301);

// Dashboard with role-based redirect
Route::get('dashboard', function () {
    $user = auth()->user();

    // Redirect voter role to /vote
    if ($user->hasRole('voter')) {
        return redirect()->route('vote.index');
    }

    // Super admin and organization go to dashboard
    return view('dashboard');
})
    ->middleware(['auth'])
    ->name('dashboard');

// Vote routes for voters
Route::middleware(['auth'])->prefix('vote')->name('vote.')->group(function () {
    Volt::route('/', 'pages.vote.index')->name('index');
    Volt::route('/{id}', 'pages.vote.show')->name('show');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
