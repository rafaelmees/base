<?php

namespace Bludata\Common\Validators;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueEntity extends Constraint
{
    public $message;
    public $fields = [];
    public $withDefaultFilters = false;
    public $disabledFilters = [];

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getRequiredOptions()
    {
        return [
            'message',
            'fields',
        ];
    }
}
