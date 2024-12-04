<?php

use Dystore\Api\Domain\Payments\Http\Controllers\HandlePaymentWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

$adapterType = Config::get('dystore.stripe.type', 'stripe');

Route::post(
    "/{$adapterType}/webhook",
    fn (Request $request) => App::make(HandlePaymentWebhookController::class)(
        Config::get('dystore.stripe.driver', 'stripe'),
        $request
    )
)->name("payments.webhook.{$adapterType}");
