<?php

namespace Modules\DystoreAres\Domain\Ares\JsonApi\V1;

use Dystcz\LunarApi\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Core\Schema\Schema;
use LaravelJsonApi\NonEloquent\Fields\Attribute;
use LaravelJsonApi\NonEloquent\Fields\ID;
use Modules\DystoreAres\Domain\Ares\Entities\AresRecord;

class AresRecordSchema extends Schema
{
    /**
     * Whether resources of this type have a self link.
     */
    protected bool $selfLink = false;

    /**
     * {@inheritDoc}
     */
    public static string $model = AresRecord::class;

    /**
     * {@inheritDoc}
     */
    public function fields(): iterable
    {
        return [
            ID::make(),

            Attribute::make('company_name'),
            Attribute::make('company_street'),
            Attribute::make('company_city'),
            Attribute::make('company_zip'),
            Attribute::make('company_in'),
            Attribute::make('company_tin'),
            Attribute::make('company_country'),
            Attribute::make('company_country_code'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function authorizable(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function repository(): AresRecordRepository
    {
        return AresRecordRepository::make()
            ->withServer($this->server)
            ->withSchema($this);
    }

    /**
     * {@inheritDoc}
     */
    public function uriType(): string
    {
        if ($this->uriType) {
            return $this->uriType;
        }

        return $this->uriType = $this->type();
    }

    /**
     * {@inheritDoc}
     */
    public static function type(): string
    {
        return SchemaType::get(AresRecord::class);
    }
}
