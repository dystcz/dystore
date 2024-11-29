<?php

namespace Dystcz\LunarRewards\Tests\Stubs\Lunar;

use Illuminate\Database\Eloquent\Model;

class TestUrlGenerator
{
    /**
     * The instance of the model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Handle the URL generation.
     */
    public function handle(Model $model): void
    {
        // ...
    }
}
