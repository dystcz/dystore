<?php

namespace Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Models;

use Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Builders\ProductNotificationBuilder;
use Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Factories\ProductNotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Notifiable;
use Lunar\Base\BaseModel;

/**
 * @method static ProductNotificationBuilder query()
 */
class ProductNotification extends BaseModel
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'email_verified_at',
        'sent_at',
        'email',
        'purchasable_id',
        'purchasable_type',
    ];

    protected $casts = [
        'sent_at' => 'timestamp',
        'email_verified_at' => 'timestamp',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ProductNotificationFactory
    {
        return ProductNotificationFactory::new();
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return ProductNotificationBuilder|static
     */
    public function newEloquentBuilder($query): ProductNotificationBuilder
    {
        return new ProductNotificationBuilder($query);
    }

    /**
     * Purchasable relation.
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }
}
