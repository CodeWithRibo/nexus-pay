<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
});

Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
    Route::get('register', [RegisterController::class, 'create'])->name('register.create');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');

    //------------Kiosk Landing Screen---------------//
    Route::get('kiosk/landing-screen', LandingScreenController::class)->name('kiosk.landing-screen');
    Route::get('kiosk/service-selection', ServiceSelectionController::class)->name('kiosk.service-selection');
});

Route::middleware(['auth', 'student'])->group(function() {
    //------------Kiosk Service Selection---------------//
    Route::get('kiosk/tuition-fee', TuitionFeeController::class)->name('kiosk.tuition-fee');
    Route::get('kiosk/other-fee', OtherSchoolFeeController::class)->name('kiosk.other-fee');

    Route::get('/', function () {
        return Inertia::render('Home');
    })->name('home');
});

Route::middleware(['auth', 'cashier'])->group(function () {
    Route::get('cashier/testing', function () {
        return 'Hello';
    });
});

Route::middleware(['auth', 'admin'])->group(function() {

});

