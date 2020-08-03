<?php

namespace App\Entity;

/** @ODM\EmbeddedDocument */
class Address
{
    /** @ODM\Field */
    private $street;

    /** @ODM\Field */
    private $city;

    /** @ODM\Field */
    private $postalCode;

    /** @ODM\Field */
    private $country;

    public function __construct(string $street, string $city, string $postalCode, string $country)
    {
        $this->street = $street;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->country = $country;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
