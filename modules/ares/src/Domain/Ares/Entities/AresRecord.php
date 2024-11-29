<?php

namespace Modules\DystoreAres\Domain\Ares\Entities;

use Carbon\Carbon;
use h4kuna\Ares\Ares\Core\Data;
use Illuminate\Contracts\Support\Arrayable;

class AresRecord extends Data implements Arrayable
{
    public string $id;

    /**
     * @param  array  $args
     */
    public function __construct(...$args)
    {
        foreach ($args as $key => $value) {
            $this->{$key} = $value;
        }

        $this->id = $args['in'];
    }

    /**
     * Static constructor.
     */
    public static function fromData(Data $data): self
    {
        return new static(...$data->toArray());
    }

    /**
     * Get id.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get active.
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * Get city.
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Get company.
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * Get created.
     */
    public function getCreated(): ?Carbon
    {
        return Carbon::parse($this->created);
    }

    /**
     * Get dissolved.
     */
    public function getDissolved(): ?Carbon
    {
        return Carbon::parse($this->dissolved);
    }

    /**
     * Get city district.
     */
    public function getCityDistrict(): ?string
    {
        return $this->city_district;
    }

    /**
     * Get city post.
     */
    public function getCityPost(): ?string
    {
        return $this->city_post;
    }

    /**
     * Get in.
     */
    public function getIn(): string
    {
        return $this->in;
    }

    /**
     * Get tin.
     */
    public function getTin(): ?string
    {
        return $this->tin;
    }

    /**
     * Get is person.
     */
    public function getIsPerson(): bool
    {
        return $this->is_person;
    }

    /**
     * Get legal form code.
     */
    public function getLegalFormCode(): int
    {
        return $this->legal_form_code;
    }

    /**
     * Get house number.
     */
    public function getHouseNumber(): ?string
    {
        return $this->house_number;
    }

    /**
     * Get street.
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * Get district.
     */
    public function getDistrict(): ?string
    {
        return $this->district;
    }

    /**
     * Get vat payer.
     */
    public function getVatPayer(): ?bool
    {
        return $this->vat_payer;
    }

    /**
     * Get zip.
     */
    public function getZip(): ?string
    {
        return $this->zip;
    }

    /**
     * Get country.
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Get country code.
     */
    public function getCountryCode(): ?string
    {
        return $this->country_code;
    }

    /**
     * Cast to array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'company_name' => $this->getCompany(),
            'company_street' => $this->getStreet(),
            'company_city' => $this->getCity(),
            'company_zip' => $this->getZip(),
            'company_in' => $this->getIn(),
            'company_tin' => $this->getTin(),
            'company_country' => $this->getCountry(),
            'company_country_code' => $this->getCountryCode(),
        ];
    }
}
