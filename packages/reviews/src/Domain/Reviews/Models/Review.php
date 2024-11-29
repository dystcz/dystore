<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\Models;

use Dystcz\LunarApiReviews\Domain\Reviews\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Lunar\Base\BaseModel;

class Review extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'rating',
        'comment',
        'published_at',
        'user_id',
        'purchasable_id',
        'purchasable_type',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(static function (self $review): void {
            $review->user_id = $review->user_id ?: Auth::user()?->id;
        });
    }

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): ReviewFactory
    {
        return ReviewFactory::new();
    }

    /**
     * Purchasable relation.
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * User relation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            Config::get('auth.providers.users.model')
        );
    }

    /**
     * Scope published reviews.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }
}
