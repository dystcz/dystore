<?php

namespace Dystore\Reviews\Domain\Reviews\Models;

use Dystore\Reviews\Domain\Reviews\Builders\ReviewBuilder;
use Dystore\Reviews\Domain\Reviews\Factories\ReviewFactory;
use Dystore\Reviews\Domain\Reviews\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Config;
use Lunar\Base\BaseModel;

/**
 * @method static ReviewBuilder query()
 */
class Review extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'meta' => AsArrayObject::class,
        'published_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new PublishedScope);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return ReviewBuilder|static
     */
    public function newEloquentBuilder($query): ReviewBuilder
    {
        return new ReviewBuilder($query);
    }

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): ReviewFactory
    {
        return ReviewFactory::new();
    }

    /**
     * Get the name attribute.
     */
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->user?->customers->first()?->name ?? $value,
        );
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
}
