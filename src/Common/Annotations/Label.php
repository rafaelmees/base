<?php

namespace Bludata\Common\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Label extends Annotation
{
    protected $exclusive = false;

    public function getTargets()
    {
        return [
            self::CLASS_CONSTRAINT,
            self::PROPERTY_CONSTRAINT,
        ];
    }

    public function getLabel()
    {
        return $this->value;
    }

    public function isExclusive()
    {
        return $this->exclusive;
    }
}
