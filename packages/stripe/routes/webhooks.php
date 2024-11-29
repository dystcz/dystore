<?php

use Dystcz\LunarApi\Domain\Payments\Http\Controllers\HandlePaymentWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::post(
    '/stripe/webhook',
    fn (Request $request) => App::make(HandlePaymentWebhookController::class)(
        Config::get('lunar-api.stripe.driver'),
        $request
    )
)
    ->name('payments.webhook');
