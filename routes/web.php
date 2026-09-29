<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ExpoController;
use Illuminate\Support\Facades\Route;

// Ochiq xarita
Route::get('/', [ExpoController::class, 'index'])->name('expo.index');
Route::get('/expo/places', [ExpoController::class, 'data'])->name('expo.data');

// Admin: joyni saqlash / bo'shatish, rasm yuklash
Route::middleware(['auth', 'admin'])->prefix('expo')->name('expo.')->group(function () {
    Route::put('/places/{stand}/{place}', [ExpoController::class, 'update'])->whereNumber('place')->name('places.update');
    Route::delete('/places/{stand}/{place}', [ExpoController::class, 'destroy'])->whereNumber('place')->name('places.destroy');
    Route::post('/uploads', [ExpoController::class, 'upload'])->middleware('throttle:60,1')->name('uploads.store');
});

// Kirish (ro'yxatdan o'tish yo'q — adminlar "php artisan expo:admin" bilan yaratiladi)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
