<?php

namespace Dystcz\LunarApi\Domain\CartAddresses\Policies;

use Dystcz\LunarApi\Domain\Carts\Contracts\CurrentSessionCart;
use Dystcz\LunarApi\LunarApi;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Lunar\Models\Cart;
use Lunar\Models\Contracts\CartAddress;

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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?Authenticatable $user, CartAddress $cartAddress): bool
    {
        return $this->check($user, $cartAddress);
    }

    /**
     * Determine if the given user can create posts.
     */
    public function create(?Authenticatable $user): bool
    {
        $cartAddressCartId = $this->request->input('data.relationships.cart.data.id', 0);

        if (! $cartAddressCartId) {
            return false;
        }

        if (LunarApi::usesHashids()) {
            $cartAddressCartId = (new (Cart::modelClass()))->decodedRouteKey($cartAddressCartId);
        }

        $cartId = App::make(CurrentSessionCart::class)?->getRouteKey();

        return (string) $cartId === (string) $cartAddressCartId;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(?Authenticatable $user, CartAddress $cartAddress): bool
    {
        return $this->check($user, $cartAddress);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?Authenticatable $user, CartAddress $cartAddress): bool
    {
        return $this->check($user, $cartAddress);
    }

    /**
     * Determine whether the user can view the model.
     */
    protected function check(?Authenticatable $user, CartAddress $cartAddress): bool
    {
        return (string) App::make(CurrentSessionCart::class)?->getRouteKey() === (string) $cartAddress->cart->getRouteKey();
    }
}
