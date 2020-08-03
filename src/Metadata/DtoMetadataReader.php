<?php

namespace App\Metadata;

use App\Metadata\Annotation\ClosureFromCallable;
use Closure;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use function class_exists;
use function get_debug_type;
use function sprintf;

final class DtoMetadataReader
{
    /** @var Reader */
    private $annotationReader;

    public function __construct()
    {
        $this->annotationReader = new AnnotationReader(new DocParser());
    }

    public function getMetadata(string $class): Dto
    {
        if (!class_exists($class)) {
            throw new \RuntimeException(sprintf('Cannot find class "%s".', $class));
        }

        $reflectionClass = new ReflectionClass($class);

        /** @var Annotation\Dto|null $classAnnotation */
        $classAnnotation = $this->annotationReader->getClassAnnotation($reflectionClass, Annotation\Dto::class);
        if (!$classAnnotation) {
            throw new \RuntimeException(sprintf('Class "%s" has no DTO metadata attached', $class));
        }

        $metadata = new Dto(
            $class,
            $this->getClosure($classAnnotation->get),
            $this->getClosure($classAnnotation->set)
        );

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            /** @var Annotation\Field|null $propertyAnnotation */
            $propertyAnnotation = $this->annotationReader->getPropertyAnnotation($property, Annotation\Field::class);

            if (!$propertyAnnotation) {
                continue;
            }

            $field = null;
            $get = $this->getClosure($propertyAnnotation->get);
            $set = $this->getClosure($propertyAnnotation->set);

            if ($propertyAnnotation->type) {
                try {
                    $field = new DtoField(
                        $property->getName(),
                        $this->getMetadata($propertyAnnotation->type),
                        $get,
                        $set
                    );
                } catch (MetadataNotFoundException $e) {
                }
            }

            $metadata->addField($field ?? new ScalarField($property->getName(), $get, $set));
        }

        return $metadata;
    }

    private function getClosure($source): ?Closure
    {
        if ($source === null) {
            return null;
        }

        if ($source instanceof Closure) {
            return $source;
        }

        if ($source instanceof ClosureFromCallable) {
            return Closure::fromCallable($source->callable)();
        }

        throw new InvalidArgumentException(sprintf('Cannot read closure from given option of type "%s".', get_debug_type($source)));
    }
}
