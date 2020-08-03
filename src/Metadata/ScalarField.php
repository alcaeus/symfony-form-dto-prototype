<?php

namespace App\Metadata;

use Closure;

final class ScalarField implements Field
{
    /** @var string */
    private $name;

    /** @var Closure */
    private $get;

    /** @var Closure|null */
    private $set;

    public function __construct(string $name, Closure $get, ?Closure $set)
    {
        $this->name = $name;
        $this->get = $get;
        $this->set = $set;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function initialize(object $dto, ?object $object, array $options)
    {
        $name = $this->name;

        $closure = $this->get;
        $value = $closure($object, $options);

        $dto->$name = $value;
    }

    public function setValue(?object &$object, object $dto, array $options): void
    {
        if (!$this->set) {
            return;
        }

        $name = $this->name;

        ($this->set)($object, $dto->$name, $options);
    }
}
