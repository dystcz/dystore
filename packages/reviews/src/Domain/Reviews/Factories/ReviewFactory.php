<?php

namespace Dystore\Reviews\Domain\Reviews\Factories;

use Dystore\Api\Domain\Users\Models\User;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'name' => $this->faker->name,
            'comment' => $this->faker->text(100),
            'meta' => [
                'foo' => 'bar',
            ],
        ];
    }
}
