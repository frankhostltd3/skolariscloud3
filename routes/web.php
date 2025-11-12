<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect('/')->with('info', 'Login functionality coming soon!');
})->name('login');

Route::get('/register', function () {
    return redirect('/')->with('info', 'Registration functionality coming soon!');
})->name('register');
