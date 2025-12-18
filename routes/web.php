<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::redirect('/', '/login', 301);



// Dashboard page using Volt
Volt::route('/dashboard', 'pages.dashboard.dashboard')
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
