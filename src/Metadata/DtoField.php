<?php

namespace App\Metadata;

use App\DTO\WrappingDto;
use Closure;

final class DtoField implements Field
{
    /** @var string */
    private $name;

    /** @var Dto */
    private $metadata;

    /** @var Closure */
    private $get;

    /** @var Closure|null */
    private $set;

    public function __construct(string $name, Dto $metadata, Closure $get, ?Closure $set)
    {
        $this->name = $name;
        $this->metadata = $metadata;
        $this->get = $get;
        $this->set = $set;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMetadata(): Dto
    {
        return $this->metadata;
    }

    public function getValue(object $dto)
    {
        $name = $this->name;

        return $dto->$name;
    }

    public function initialize(object $dto, ?object $object, array $options)
    {
        $name = $this->name;

        $value = ($this->get)($object, $options);
        $nestedDto = $this->metadata->createInstance($options);
        $nestedDto->initialize($value);

        $dto->$name = $nestedDto;
    }

    public function setValue(?object &$object, object $dto, array $options): void
    {
        if (!$this->set) {
            return;
        }

        $name = $this->name;

        $value = ($this->get)($object, $options);

        /** @var WrappingDto $nestedDto */
        $nestedDto = $dto->$name;
        $nestedDto->apply($value);

        ($this->set)($object, $value, $options);
    }
}
