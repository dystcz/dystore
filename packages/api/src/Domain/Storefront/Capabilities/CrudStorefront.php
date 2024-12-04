<?php

namespace Dystore\Api\Domain\Storefront\Capabilities;

use Dystore\Api\Domain\Storefront\Entities\Storefront;
use Dystore\Api\Domain\Storefront\Entities\StorefrontStorage;
use LaravelJsonApi\NonEloquent\Capabilities\CrudResource;

class CrudStorefront extends CrudResource
{
    private StorefrontStorage $storage;

    public function __construct(StorefrontStorage $storage)
    {
        parent::__construct();
        $this->storage = $storage;
    }

    public function read(Storefront $storefront): ?Storefront
    {
        return $storefront;
    }

    public function delete(Storefront $storefront): void
    {
        $this->storage->remove($storefront);
    }
}
