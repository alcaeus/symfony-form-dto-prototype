<?php

namespace App\Metadata;

use App\DTO\WrappingDto;
use Closure;

final class Dto
{
    /** @var string */
    private $type;

    /** @var Closure|null */
    private $get;

    /** @var Closure|null */
    private $set;

    /** @var Field[] */
    private $fields;

    public function __construct(string $type, ?Closure $get, ?Closure $set)
    {
        $this->type = $type;
        $this->get = $get;
        $this->set = $set;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

    /** @return WrappingDto */
    public function createInstance(array $options = []): object
    {
        return new WrappingDto($this, new $this->type, $options);
    }

    public function getValue(?object $object, array $options)
    {
        return $this->get ? ($this->get)($object, $options) : $object;
    }

    public function setValue(?object &$object, $dto, array $options): void
    {
        if (!$this->set) {
            return;
        }

        ($this->set)($object, $dto, $options);
    }

    /** @return Field[] */
    public function getFields(): array
    {
        return $this->fields;
    }
}
