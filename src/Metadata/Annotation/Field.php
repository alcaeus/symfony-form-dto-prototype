<?php

namespace App\Metadata\Annotation;

use App\Metadata\Annotation\ClosureFromCallable;
use Closure;

/**
 * @Annotation
 */
class Field
{
    public $set;

    public $get;

    public $type;

}
