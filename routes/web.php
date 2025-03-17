<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Root URL route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// API documentation route
Route::get('/api', function () {
    return view('welcome');
})->name('api.home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('dashboard', function () {
//         return Inertia::render('dashboard');
//     })->name('dashboard');
// });

// require __DIR__.'/settings.php';
// require __DIR__.'/auth.php';
