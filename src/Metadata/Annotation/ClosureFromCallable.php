<?php

namespace App\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;
use InvalidArgumentException;
use function is_callable;
use function print_r;
use function sprintf;

/**
 * @Annotation
 */
class ClosureFromCallable
{
    /** @var callable */
    public $callable;

    public function __construct(array $values)
    {
        if (!isset($values['value'])){
            $values['value'] = null;
        }

        if (!is_callable($values['value'], true)) {
            throw new InvalidArgumentException(sprintf('Provided value "%s" is not a valid callable!', print_r($values, true)));
        }

        $this->callable = $values['value'];
    }
}
