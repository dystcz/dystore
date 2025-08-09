<?php

namespace Dystore\Reviews\Domain\Reviews\Models;

use Dystore\Api\Base\Concerns\Publishable;
use Dystore\Api\Base\Enums\PublishedStatus;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Reviews\Domain\Reviews\Builders\ReviewBuilder;
use Dystore\Reviews\Domain\Reviews\Factories\ReviewFactory;
use Dystore\Reviews\Domain\Reviews\Observers\ReviewObserver;
use Dystore\Reviews\Domain\Reviews\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Lunar\Base\BaseModel;
use Lunar\Base\Traits\HasMedia;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;

/**
 * @method static ReviewBuilder query()
 */
#[ObservedBy([ReviewObserver::class])]
class Review extends BaseModel implements SpatieHasMedia
{
    use HasFactory;
    use HasMedia;
    use Publishable;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'meta' => AsArrayObject::class,
        'published_at' => 'datetime',
        'status' => PublishedStatus::class,
    ];

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
     *
     * @return MorphTo<Model,Review>
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * User relation.
     *
     * @return BelongsTo<User,Review>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            Config::get('auth.providers.users.model')
        );
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new PublishedScope);
    }

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): ReviewFactory
    {
        return ReviewFactory::new();
    }
}
