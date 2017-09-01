<?php

namespace Bludata\Common\Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InstanceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!($value instanceof $constraint->expected)) {
            if ((!empty($value) && !is_null($value)) || ($constraint->notBlankAndNotNull && (empty($value) || is_null($value)))) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }
}
