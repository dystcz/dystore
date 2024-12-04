<?php

namespace Dystore\Api\Domain\CartAddresses\Policies;

use Dystore\Api\Domain\Auth\Concerns\HandlesAuthorization;
use Dystore\Api\Domain\Carts\Contracts\CurrentSessionCart;
use Dystore\Api\Facades\Api;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Lunar\Models\Cart;
use Lunar\Models\Contracts\CartAddress as CartAddressContract;

class CartAddressPolicy
{
    use HandlesAuthorization;

    private Request $request;

    public function __construct()
    {
        $this->request = App::get('request');
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?Authenticatable $user): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?Authenticatable $user, CartAddressContract $cartAddress): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        return $this->check($user, $cartAddress);
    }

    /**
     * Determine if the given user can create posts.
     */
    public function create(?Authenticatable $user): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        $cartAddressCartId = $this->request->input('data.relationships.cart.data.id', 0);

        if (! $cartAddressCartId) {
            return false;
        }

        if (Api::usesHashids()) {
            $cartAddressCartId = (new (Cart::modelClass()))->decodedRouteKey($cartAddressCartId);
        }

        $cartId = App::make(CurrentSessionCart::class)?->getRouteKey();

        return (string) $cartId === (string) $cartAddressCartId;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(?Authenticatable $user, CartAddressContract $cartAddress): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        return $this->check($user, $cartAddress);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?Authenticatable $user, CartAddressContract $cartAddress): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        return $this->check($user, $cartAddress);
    }

    /**
     * Determine whether the user can view the model.
     */
    protected function check(?Authenticatable $user, CartAddressContract $cartAddress): bool
    {
        return (string) App::make(CurrentSessionCart::class)?->getRouteKey() === (string) $cartAddress->cart->getRouteKey();
    }
}
