<?php

namespace App\Form;

use App\DTO\WrappingDto;
use App\Metadata\Dto;
use App\Metadata\DtoField;
use App\Metadata\ScalarField;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use function get_class;

class DtoFormFactory
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function create(Dto $metadata, WrappingDto $dto, array $options = [], ?string $name = ''): FormBuilderInterface
    {
        $builder = $this->formFactory->createNamedBuilder(
            $name,
            FormType::class,
            $dto,
            $options + ['method' => 'POST', 'csrf_protection' => false]
        );

        foreach ($metadata->getFields() as $field) {
            $propertyPath = 'wrappedObject.' . $field->getName();

            if ($field instanceof DtoField) {
                $builder->add($this->create(
                    $field->getMetadata(),
                    $field->getValue($dto->getWrappedObject()),
                    ['property_path' => $propertyPath],
                    $field->getName()
                ));

                continue;
            }

            if ($field instanceof ScalarField) {
                $builder->add($field->getName(), null, ['property_path' => $propertyPath]);
            }
        }

        return $builder;
    }
}
