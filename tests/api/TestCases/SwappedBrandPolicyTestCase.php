<?php

namespace Dystore\Tests\Api\TestCases;

use Dystore\Tests\Api\Stubs\Policies\TestBrandPolicy;
use Dystore\Tests\Api\TestCase;
use Illuminate\Support\Facades\Config;

abstract class SwappedBrandPolicyTestCase extends TestCase
{
    /**
     * @param  Application  $app
     */
    public function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        Config::set(
            'dystore.domains.brands.policy',
            TestBrandPolicy::class,
        );
    }
}
