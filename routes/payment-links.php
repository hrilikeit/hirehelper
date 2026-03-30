<?php

use App\Http\Controllers\PaymentLinkController;
use Illuminate\Support\Facades\Route;

Route::get('/pay/{paymentLink}', [PaymentLinkController::class, 'show'])->name('payment-links.show');
Route::post('/pay/{paymentLink}/paypal', [PaymentLinkController::class, 'startPayPal'])->name('payment-links.paypal.start');
Route::get('/pay/{paymentLink}/paypal/return', [PaymentLinkController::class, 'handleReturn'])->name('payment-links.paypal.return');
Route::get('/pay/{paymentLink}/paypal/cancel', [PaymentLinkController::class, 'cancel'])->name('payment-links.paypal.cancel');
