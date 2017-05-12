<?php

use Bludata\Common\Annotations\XML\Entity;
use Bludata\Common\Annotations\XML\Field;
use Bludata\Common\Traits\AttributesTrait;

/**
 * @Bludata\Common\Annotations\XML\Entity(name="foo")
 */
class PropertyAnnotationStub
{
    use AttributesTrait;

    /**
     * @Bludata\Common\Annotations\XML\Field(name="bar")
     */
    protected $property;
}
