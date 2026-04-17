<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Kiosk\CashInsertionController;
use App\Http\Controllers\Kiosk\OutstandingBalancesController;
use App\Http\Controllers\Kiosk\DestroyTransactionPaymentController;
use App\Http\Controllers\Kiosk\DynamicCashInsertionController;
use App\Http\Controllers\Kiosk\DynamicProcessingPaymentController;
use App\Http\Controllers\Kiosk\DynamicReceiptController;
use App\Http\Controllers\Kiosk\InitiatePaymentController;
use App\Http\Controllers\Kiosk\LandingScreenController;
use App\Http\Controllers\Kiosk\PaymentMethodController;
use App\Http\Controllers\Kiosk\PaymongoPaymentController;
use App\Http\Controllers\Kiosk\ProcessingPaymentController;
use App\Http\Controllers\Kiosk\ReceiptController;
use App\Http\Controllers\Kiosk\ServiceSelectionController;
use App\Http\Controllers\Kiosk\TuitionFeeController;
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
    Route::get('login', [LoginController::class, 'create'])->defaults('role', 'student')->name('login');
    Route::post('login', [LoginController::class, 'store'])->defaults('role', 'student')->name('login.store');

    Route::get('admin/login', [LoginController::class, 'create'])->defaults('role', 'admin')->name('admin.login');
    Route::post('admin/login', [LoginController::class, 'store'])->defaults('role', 'admin')->name('admin.login.store');

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
    Route::post('kiosk/paymongo/initiate', [PaymongoPaymentController::class, 'initiate'])->name('kiosk.paymongo.initiate');
    Route::get('kiosk/paymongo/checkout/{transaction_id}', [PaymongoPaymentController::class, 'checkout'])->name('kiosk.paymongo.checkout');
    Route::get('kiosk/paymongo/status/{transaction_id}', [PaymongoPaymentController::class, 'status'])->name('kiosk.paymongo.status');
    Route::get('kiosk/paymongo/return/{transaction_id}', [PaymongoPaymentController::class, 'handleReturn'])->name('kiosk.paymongo.return');

    Route::get('kiosk/outstanding-balance', OutstandingBalancesController::class)->name('kiosk.outstanding-balance');

//------------Dynamic Payment Method---------------//
    Route::get('kiosk/payment-method', PaymentMethodController::class)->name('kiosk.payment-method');
    Route::post('kiosk/payment-method/initiate', [InitiatePaymentController::class, 'initiate'])->name('kiosk.payment-method.initiate');
    Route::get('kiosk/cash-insertion/{transaction_id}', DynamicCashInsertionController::class)->name('kiosk.cash-insertion');
    Route::post('kiosk/start-processing/{transaction_id}', [DynamicProcessingPaymentController::class, 'start'])->name('kiosk.processing.start');
    Route::get('kiosk/processing/{transaction_id}', [DynamicProcessingPaymentController::class, 'index'])->name('kiosk.processing.index');
    Route::post('kiosk/processing/{transaction_id}', [DynamicProcessingPaymentController::class, 'process'])->name('kiosk.processing.process');
    Route::get('kiosk/receipt/{transaction_id}', DynamicReceiptController::class)->name('kiosk.receipt');

});

Route::middleware(['auth', 'admin'])->group(function() {
    Route::get('admin/dashboard', DashboardController::class)->name('admin.dashboard');

});
