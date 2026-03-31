<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Kiosk\LandingScreenController;
use App\Http\Controllers\Kiosk\CashInsertionController;
use App\Http\Controllers\Kiosk\ProcessingPaymentController;
use App\Http\Controllers\Kiosk\ReceiptController;
use App\Http\Controllers\Kiosk\CheckBalanceController;
use App\Http\Controllers\Kiosk\ServiceSelectionController;
use App\Http\Controllers\Kiosk\TuitionFeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware('auth')->group(function() {
    Route::post('logout', [LoginController::class, 'destroy'])->name('login.destroy');

    Route::get('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    });
});

Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');

    Route::get('register', [RegisterController::class, 'create'])->name('register.create');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');


});

//------------Kiosk---------------//
Route::get('kiosk/service-selection/{isLoggedIn?}', ServiceSelectionController::class)
    ->whereIn('isLoggedIn', ['0', '1', 'true', 'false'],)
    ->name('kiosk.service-selection');
Route::get('/{isLoggedIn?}', LandingScreenController::class)
    ->whereIn('isLoggedIn', ['0', '1', 'true', 'false'],)
    ->name('kiosk.landing-screen');

Route::middleware(['auth', 'student'])->group(function() {
    Route::get('kiosk/tuition-fee/payment-method', TuitionFeeController::class)->name('kiosk.tuition-fee.payment-method');
    Route::get('kiosk/other-fee/payment-method', CheckBalanceController::class)->name('kiosk.other-fee.payment-method');
    Route::get('kiosk/tuition-fee/cash-insertion', CashInsertionController::class)->name('kiosk.tuition-fee.cash-insertion');
    Route::get('kiosk/tuition-fee/processing', ProcessingPaymentController::class)->name('kiosk.tuition-fee.processing');
    Route::get('kiosk/tuition-fee/receipt', ReceiptController::class)->name('kiosk.tuition-fee.receipt');

    Route::get('/kiosk/outstanding-balance', CheckBalanceController::class)->name('kiosk.outstanding-balance');

});

Route::middleware(['auth', 'cashier'])->group(function () {
    Route::get('cashier/testing', function () {
        return 'Hello';
    });
});

Route::middleware(['auth', 'admin'])->group(function() {

});
