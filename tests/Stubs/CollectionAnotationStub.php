<?php

use Bludata\Common\Annotations\XML\Entity;
use Bludata\Common\Annotations\XML\Field;
use Bludata\Common\Traits\AttributesTrait;

/**
 * @Bludata\Common\Annotations\XML\Entity(name="foo")
 */
class CollectionAnnotationStub
{
    use AttributesTrait;

    /**
     * @Bludata\Common\Annotations\XML\Field(name="bar")
     */
    protected $property;

    /**
     * @Bludata\Common\Annotations\XML\Collection(name="collection", reference="\PropertyAnnotationStub")
     */
    protected $collection;
}
