<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route used by the password reset notification to generate reset URLs.
Route::get('/reset-password/{token}', function (string $token) {
    return 'Password reset placeholder. Token: '.$token;
})->name('password.reset');
