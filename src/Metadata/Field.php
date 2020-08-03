<?php

namespace App\Metadata;

interface Field
{
    public function getName(): string;

    public function initialize(object $dto, ?object $object, array $options);

    public function setValue(?object &$object, object $dto, array $options): void;
}
