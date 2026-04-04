<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Kiosk\DestroyTransactionPaymentController;
use App\Http\Controllers\Kiosk\LandingScreenController;
use App\Http\Controllers\Kiosk\CashInsertionController;
use App\Http\Controllers\Kiosk\ProcessingPaymentController;
use App\Http\Controllers\Kiosk\ReceiptController;
use App\Http\Controllers\Kiosk\CheckBalanceController;
use App\Http\Controllers\Kiosk\ServiceSelectionController;
use App\Http\Controllers\Kiosk\TuitionFeeController;
use App\Http\Controllers\Kiosk\InitiatePaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    Route::post('kiosk/logout-with-transaction', [DestroyTransactionPaymentController::class, 'destroyWithTransaction'])->name('login.destroy.with.transaction');
    Route::post('kiosk/remove-transaction', [DestroyTransactionPaymentController::class, 'removeTransaction'])->name('remove-transaction');

    Route::get('kiosk/tuition-fee/payment-method', TuitionFeeController::class)->name('kiosk.tuition-fee.payment-method');
    Route::post('kiosk/tuition-fee/initiate-payment', InitiatePaymentController::class)->name('kiosk.tuition-fee.initiate-payment');
    Route::get('kiosk/tuition-fee/cash-insertion/{transaction_id}', CashInsertionController::class)->name('kiosk.tuition-fee.cash-insertion');
    Route::post('kiosk/tuition-fee/start-processing/{transaction_id}', [ProcessingPaymentController::class, 'start'])->name('kiosk.tuition-fee.processing.start');
    Route::post('kiosk/tuition-fee/processing/{transaction_id}', [ProcessingPaymentController::class, 'process'])->name('kiosk.tuition-fee.processing.process');
    Route::get('kiosk/tuition-fee/processing/{transaction_id}', [ProcessingPaymentController::class, 'index'])->name('kiosk.tuition-fee.processing.index');
    Route::get('kiosk/tuition-fee/receipt/{transaction_id}', ReceiptController::class)->name('kiosk.tuition-fee.receipt');

    Route::get('kiosk/outstanding-balance', CheckBalanceController::class)->name('kiosk.outstanding-balance');

});

Route::middleware(['auth', 'cashier'])->group(function () {
    Route::get('cashier/testing', function () {
        return 'Hello';
    });
});

Route::middleware(['auth', 'admin'])->group(function() {

});
