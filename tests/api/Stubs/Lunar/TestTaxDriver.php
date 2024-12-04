<?php

namespace Dystore\Tests\Api\Stubs\Lunar;

use Lunar\Base\Addressable;
use Lunar\Base\Purchasable;
use Lunar\Base\TaxDriver;
use Lunar\Base\ValueObjects\Cart\TaxBreakdown;
use Lunar\Base\ValueObjects\Cart\TaxBreakdownAmount;
use Lunar\DataTypes\Price;
use Lunar\Models\Contracts\CartLine as CartLineContract;
use Lunar\Models\Contracts\Currency as CurrencyContract;
use Lunar\Models\Currency;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxRateAmount;

class TestTaxDriver implements TaxDriver
{
    /**
     * The taxable shipping address.
     */
    protected ?Addressable $shippingAddress = null;

    /**
     * The taxable billing address.
     */
    protected ?Addressable $billingAddress = null;

    /**
     * The currency model.
     */
    protected CurrencyContract $currency;

    /**
     * The purchasable item.
     */
    protected Purchasable $purchasable;

    /**
     * {@inheritDoc}
     */
    public function setShippingAddress(?Addressable $address = null): self
    {
        $this->shippingAddress = $address;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrency(CurrencyContract $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingAddress(?Addressable $address = null): self
    {
        $this->billingAddress = $address;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setPurchasable(Purchasable $purchasable): self
    {
        $this->purchasable = $purchasable;

        return $this;
    }

    /**
     * Set the cart line.
     */
    public function setCartLine(CartLineContract $cartLine): self
    {
        $this->cartLine = $cartLine;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getBreakdown($subTotal): TaxBreakdown
    {
        $breakdown = new TaxBreakdown;

        $currency = Currency::modelClass()::getDefault() ?: Currency::factory()->create();

        $taxAmount = TaxRateAmount::factory()->create();

        $result = round($subTotal * ($taxAmount->percentage / 100));

        $variant = ProductVariant::factory()->create();

        $amount = new TaxBreakdownAmount(
            price: new Price((int) $result, $currency, $variant->getUnitQuantity()),
            description: $taxAmount->taxRate->name,
            identifier: "tax_rate_{$taxAmount->taxRate->id}",
            percentage: $taxAmount->percentage
        );

        $breakdown->addAmount($amount);

        return $breakdown;
    }
}
