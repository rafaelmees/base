<?php

namespace Bludata\Common\Validators;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Instance extends Constraint
{
    public $message;
    public $expected;
    public $notBlankAndNotNull = false;

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
