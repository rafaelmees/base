<?php

namespace Bludata\Common\Validators;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotBlankAndNotNull extends Constraint
{
    public $message;

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
