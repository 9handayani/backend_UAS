<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController; // Pastikan ini ada

Route::get('/', function () {
    return view('welcome');
});

// Route untuk Social Login
// {provider} akan otomatis berisi 'google' atau 'github' tergantung tombol mana yang diklik
Route::get('auth/{provider}/redirect', [SocialiteController::class, 'redirect']);
Route::get('auth/{provider}/callback', [SocialiteController::class, 'callback']);