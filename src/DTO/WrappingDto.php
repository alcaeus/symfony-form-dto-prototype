<?php

namespace App\DTO;

use App\Metadata\Dto;
use App\Metadata\DtoField;

final class WrappingDto
{
    /** @var Dto */
    private $metadata;

    /** @var object */
    private $dto;

    /** @var array */
    private $options;

    /** @var bool */
    private $isApplied = false;

    public function __construct(Dto $dtoMetadata, object $dto, $options)
    {
        $this->metadata = $dtoMetadata;
        $this->dto = $dto;
        $this->options = $options;
    }

    public function isApplied(): bool
    {
        return $this->isApplied;
    }

    public function initialize(?object $object): void
    {
        // Initialize dto
        $dtoData = $this->metadata->getValue($object, $this->options);

        foreach ($this->metadata->getFields() as $field) {
            $field->initialize($this->dto, $dtoData, $this->options);
        }
    }

    public function apply(?object &$object): void
    {
        $dtoData = $this->metadata->getValue($object, $this->options);

        foreach ($this->metadata->getFields() as $field) {
            $field->setValue($dtoData, $this->dto, $this->options);
        }

        $this->metadata->setValue($object, $this->dto, $this->options);
    }

    public function getWrappedObject(): object
    {
        return $this->dto;
    }
}
