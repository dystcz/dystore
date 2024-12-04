<?php

namespace Dystore\Api\Domain\Storefront\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;
use Lunar\Models\Contracts\Channel as ChannelContract;
use Lunar\Models\Contracts\Currency as CurrencyContract;
use Lunar\Models\Contracts\Customer as CustomerContract;

/**
 * @property StorefrontSessionManager $storefrontSession
 */
class Storefront implements Arrayable
{
    public function __construct(
        public StorefrontSessionInterface $storefrontSession,
    ) {}

    public function getKey(): string
    {
        return $this->storefrontSession->getSessionKey();
    }

    public function getChannel(): ?ChannelContract
    {
        return $this->storefrontSession->getChannel();
    }

    public function getCustomerGroups(): ?Collection
    {
        return $this->storefrontSession->getCustomerGroups();
    }

    public function getCustomer(): ?CustomerContract
    {
        return $this->storefrontSession->getCustomer();
    }

    public function getCurrency(): ?CurrencyContract
    {
        return $this->storefrontSession->getCurrency();
    }

    public function toArray(): array
    {
        return [
            'channel' => $this->getChannel(),
            'customer_groups' => $this->getCustomerGroups(),
            'customer' => $this->getCustomer(),
            'currency' => $this->getCurrency(),
        ];
    }
}
