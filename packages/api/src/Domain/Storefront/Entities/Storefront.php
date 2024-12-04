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

    /**
     * Get id.
     */
    public function getId(): string
    {
        return $this->storefrontSession->getSessionKey();
    }

    /**
     * Get description.
     */
    public function getChannel(): ?ChannelContract
    {
        return $this->storefrontSession->getChannel();
    }

    /**
     * Get customer groups.
     */
    public function getCustomerGroups(): ?Collection
    {
        return $this->storefrontSession->getCustomerGroups();
    }

    /**
     * Get customer.
     */
    public function getCustomer(): ?CustomerContract
    {
        return $this->storefrontSession->getCustomer();
    }

    /**
     * Get curency.
     */
    public function getCurrency(): ?CurrencyContract
    {
        return $this->storefrontSession->getCurrency();
    }

    /**
     * Cast to array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'channel' => $this->getChannel(),
            'customer_groups' => $this->getCustomerGroups(),
            'customer' => $this->getCustomer(),
            'currency' => $this->getCurrency(),
        ];
    }
}
