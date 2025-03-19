<?php

namespace Dystore\Api\Domain\Prices\Http\Middleware;

use Closure;
use Dystore\Api\Domain\CustomerGroups\Models\CustomerGroup;
use Dystore\Api\Domain\Prices\Scopes\StorefrontApiScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Models\Price;

class StorefrontApiPricing
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        App::make(StorefrontSessionInterface::class)
            ->setCustomerGroups(CustomerGroup::where('handle', 'b2b')
                ->get()
            );

        Price::modelClass()::addGlobalScope(new StorefrontApiScope);

        return $next($request);
    }
}
