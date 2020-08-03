<?php

namespace App\DTO;

use App\Entity\Address as AddressEntity;
use App\Metadata\Annotation\ClosureFromCallable;
use App\Metadata\Annotation\Dto;
use App\Metadata\Annotation\Field;
use Closure;
use InvalidArgumentException;

/**
 * @Dto()
 */
class EditAddress
{
    /**
     * @Field(
     *     get=@ClosureFromCallable({EditAddress::class, "createAddressGetter"}),
     *     set=@ClosureFromCallable({EditAddress::class, "createAddressSetter"}),
     *     type=Address::class
     * )
     * @var WrappingDto
     */
    public $address;

    public static function createAddressGetter(): Closure
    {
        return function (object $data): ?AddressEntity {
            return $data->getAddress();
        };
    }

    public static function createAddressSetter(): Closure
    {
        return function (object $data, AddressEntity $address, array $options) {
            $data->updateAddress($address);
        };
    }
}
