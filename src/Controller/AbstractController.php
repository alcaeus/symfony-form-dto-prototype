<?php

namespace App\Controller;

use App\DTO\HandlerInterface;
use App\DTO\WrappingDto;
use App\Form\DtoFormFactory;
use App\Metadata\DtoMetadataReader;
use Symfony\Component\Form\Extension\Validator\Constraints\Form;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapperInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractController
{
    /** @var DtoMetadataReader */
    private $metadataReader;

    /** @var HandlerInterface */
    private $dtoHandler;

    public function __construct(DtoMetadataReader $metadataReader, HandlerInterface $dtoHandler)
    {
        $this->metadataReader = $metadataReader;
        $this->dtoHandler = $dtoHandler;
    }

    protected function handleDto(string $dtoClass, ?object &$object, Request $request, array $options = []): WrappingDto
    {
        $metadata = $this->metadataReader->getMetadata($dtoClass);
        $dto = $metadata->createInstance($options);

        $dto->initialize($object);

        $this->dtoHandler->handle($metadata, $dto, $request);

        return $dto;
    }
}
