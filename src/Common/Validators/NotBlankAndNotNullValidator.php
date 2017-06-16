<?php

namespace Bludata\Common\Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotBlankAndNotNullValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ((empty($value) || is_null($value)) && !is_bool($value) && !is_numeric($value)) {
            $this->context->buildViolation($constraint->message)->setParameter('%string%', $value)->addViolation();
        }
    }
}
