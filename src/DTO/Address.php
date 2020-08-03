<?php

namespace App\DTO;

use App\Entity\Address as AddressEntity;
use App\Metadata\Annotation\ClosureFromCallable;
use App\Metadata\Annotation\Dto;
use App\Metadata\Annotation\Field;
use Closure;
use function debug_backtrace;
use const DEBUG_BACKTRACE_IGNORE_ARGS;

/**
 * @Dto(set=@ClosureFromCallable({Address::class, "createSetter"}))
 */
class Address
{
    /** @Field(get=@ClosureFromCallable({Address::class, "createStreetGetter"})) */
    public $street;

    /** @Field(get=@ClosureFromCallable({Address::class, "createCityGetter"})) */
    public $city;

    /** @Field(get=@ClosureFromCallable({Address::class, "createPostalCodeGetter"})) */
    public $postalCode;

    /** @Field(get=@ClosureFromCallable({Address::class, "createCountryGetter"})) */
    public $country;

    public static function createSetter(): Closure
    {
        return static function (?AddressEntity &$data, self $address): void {
            $data = new AddressEntity($address->street, $address->city, $address->postalCode, $address->country);
        };
    }

    public static function createStreetGetter(): Closure
    {
        return static function(?AddressEntity $address): string {
            return $address ? $address->getStreet() : '';
        };
    }

    public static function createCityGetter(): Closure
    {
        return static function(?AddressEntity $address): string {
            return $address ? $address->getCity() : '';
        };
    }

    public static function createPostalCodeGetter(): Closure
    {
        return static function(?AddressEntity $address): string {
            return $address ? $address->getPostalCode() : '';
        };
    }

    public static function createCountryGetter(): Closure
    {
        return static function(?AddressEntity $address): string {
            return $address ? $address->getCountry() : '';
        };
    }
}
