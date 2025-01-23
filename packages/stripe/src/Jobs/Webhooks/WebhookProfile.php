<?php

namespace Dystore\Stripe\Jobs\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Spatie\StripeWebhooks\StripeWebhookProfile;
use Spatie\WebhookClient\Models\WebhookCall;

class WebhookProfile extends StripeWebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        if (! $this->checkValidEshopId($request)) {
            return false;
        }

        if ($this->checkWebhookCallExists($request)) {
            return false;
        }

        return true;
    }

    protected function checkWebhookCallExists(Request $request): bool
    {
        return WebhookCall::where('name', 'stripe')->where('payload->id', $request->get('id'))->exists();
    }

    protected function checkValidEshopId(Request $request): bool
    {
        if (in_array('*', Config::get('dystore.stripe.handle_eshop_ids'))) {
            return true;
        }

        if (in_array($this->getEshopIdFromRequest($request), Config::get('dystore.stripe.handle_eshop_ids'))) {
            return true;
        }

        return false;
    }

    protected function getEshopIdFromRequest(Request $request): ?string
    {
        return $request->input('data.object.metadata.eshop_id', null);
    }
}
