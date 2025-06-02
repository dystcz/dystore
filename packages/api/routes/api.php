<?php

use Dystore\Api\Domain\Payments\Contracts\HandlePaymentWebhookController;
use Dystore\Api\Facades\Api;
use Illuminate\Support\Facades\Route;

Api::routes();

// Payments
Route::post('{paymentDriver}/webhook', HandlePaymentWebhookController::class)
    ->name('payments.webhook');
