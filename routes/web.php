<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


// Dashboard page using Volt
Volt::route('/dashboard', 'pages.dashboard.dashboard')
    ->middleware(['auth'])
    ->name('dashboard');


// User management page
Route::middleware(['auth', 'role:Super Admin'])->prefix('users')->name('users.')->group(function () {
    Volt::route('/', 'pages.dashboard.users.index')->name('index');
    Volt::route('/create', 'pages.dashboard.users.create')->name('create');
    Volt::route('/{id}/edit', 'pages.dashboard.users.edit')->name('edit');
});
// Jurusan management page
Route::middleware(['auth', 'role:Super Admin'])->prefix('jurusan')->name('jurusan.')->group(function () {
    Volt::route('/', 'pages.dashboard.jurusan.index')->name(name: 'index');
    Volt::route('/create', 'pages.dashboard.jurusan.create')->name('create');
    Volt::route('/{id}/edit', 'pages.dashboard.jurusan.edit')->name('edit');
});
// Program Studi management page
Route::middleware(['auth', 'role:Super Admin'])->prefix('prodi')->name('prodi.')->group(function () {
    Volt::route('/', 'pages.dashboard.prodi.index')->name(name: 'index');
    Volt::route('/create', 'pages.dashboard.prodi.create')->name('create');
    Volt::route('/{id}/edit', 'pages.dashboard.prodi.edit')->name('edit');
});

// Organization member management page
Route::middleware(['auth', 'role:Organization'])->prefix('members')->name('members.')->group(function () {
    Volt::route('/', 'pages.dashboard.members.index')->name('index');
    Volt::route('/create', 'pages.dashboard.members.create')->name('create');
    Volt::route('/{id}/edit', 'pages.dashboard.members.edit')->name('edit');
});

// Election 
Route::middleware(['auth', 'role:Organization'])->prefix('elections')->name('elections.')->group(function () {
    Volt::route('/', 'pages.dashboard.elections.index')->name('index');
    Volt::route('/create', 'pages.dashboard.elections.create')->name('create');
    Volt::route('/{id}/edit', 'pages.dashboard.elections.edit')->name('edit');
    Volt::route('/{id}/candidates', 'pages.dashboard.elections.candidates')->name('candidates');
});



// Vote routes for voters
Route::middleware(['auth'])->prefix('vote')->name('vote.')->group(function () {
    Volt::route('/', 'pages.vote.index')->name('index');
    Volt::route('/{id}', 'pages.vote.show')->name('show');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
