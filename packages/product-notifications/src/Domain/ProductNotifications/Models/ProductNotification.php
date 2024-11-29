<?php

namespace Dystcz\LunarProductNotification\Domain\ProductNotifications\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Notifiable;
use Lunar\Base\BaseModel;

class ProductNotification extends BaseModel
{
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
     * Purchasable relation.
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnsent(Builder $query): Builder
    {
        return $query->whereNull('sent_at');
    }
}
