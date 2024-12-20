<?php

namespace Dystore\Api\Domain\Customers\Policies;

use Dystore\Api\Domain\Auth\Concerns\HandlesAuthorization;
use Dystore\Api\Domain\Users\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Lunar\Models\Contracts\Customer as CustomerContract;
use Lunar\Models\Customer;

class CustomerPolicy
{
    use HandlesAuthorization;

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
    public function view(?Authenticatable $user, CustomerContract $customer): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        return $this->check($user, $customer);
    }

    /**
     * Determine if the given user can create posts.
     */
    public function create(?Authenticatable $user): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(?Authenticatable $user, CustomerContract $customer): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        return $this->check($user, $customer);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?Authenticatable $user, CustomerContract $customer): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        return $this->check($user, $customer);
    }

    /**
     * Authorize a user to view a customer's addresses.
     */
    public function viewAddresses(?Authenticatable $user, CustomerContract $customer): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        return $this->check($user, $customer);
    }

    /**
     * Authorize a user to view a customer's orders.
     */
    public function viewOrders(?Authenticatable $user, CustomerContract $customer): bool
    {
        if ($this->isFilamentAdmin($user)) {
            return true;
        }

        return $this->check($user, $customer);
    }

    /**
     * Determine whether the user can view the model.
     */
    protected function check(?Authenticatable $user, CustomerContract $customer): bool
    {
        $customersTable = (new (Customer::modelClass()))->getTable();

        /** @var User $user */
        return $user
            ->customers()
            ->where("{$customersTable}.id", $customer->getRouteKey())
            ->exists();
    }
}
