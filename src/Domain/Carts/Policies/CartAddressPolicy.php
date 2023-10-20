<?php

namespace Dystcz\LunarApi\Domain\Carts\Policies;

use Dystcz\LunarApi\Domain\Carts\Models\Cart;
use Dystcz\LunarApi\Domain\Carts\Models\CartAddress;
use Dystcz\LunarApi\LunarApi;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Lunar\Facades\CartSession;

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
        return CartSession::current()->id === $cartAddress->cart_id;
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
            $cartAddressCartId = (new Cart)->decodeHashedId($cartAddressCartId);
        }

        $cartId = (string) CartSession::current()->id;
        $cartAddressCartId = (string) $cartAddressCartId;

        return $cartId === $cartAddressCartId;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(?Authenticatable $user, CartAddress $cartAddress): bool
    {
        $cartId = (string) CartSession::current()->id;
        $cartAddressCartId = (string) $cartAddress->cart_id;

        return $cartId === $cartAddressCartId;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?Authenticatable $user, CartAddress $cartAddress): bool
    {
        return $this->update($user, $cartAddress);
    }
}
