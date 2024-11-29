<?php

namespace Dystcz\LunarProductNotification\Domain\ProductNotifications\Factories;

use Dystcz\LunarProductNotification\Domain\ProductNotifications\Models\ProductNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductNotificationFactory extends Factory
{
    protected $model = ProductNotification::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
        ];
    }

    public function isSent(): self
    {
        return $this->state(fn () => ['sent_at' => now()]);
    }
}
