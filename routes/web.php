<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// Home page (Landing page)
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', function () {
    return redirect('/')->with('info', 'Login functionality coming soon!');
})->name('login');

Route::get('/register', function () {
    return redirect('/')->with('info', 'Registration functionality coming soon!');
})->name('register');
