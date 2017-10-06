<?php

namespace Bludata\Common\Annotations\JSON;

use Bludata\Common\Exceptions\InvalidTypeException;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @Attributes({
 *   @Attribute("name", type="string"),
 *   @Attribute("type", type="string"),
 *   @Attribute("order", type="integer")
 * })
 */
class Field extends JSONAnnotation
{
    /**
     * @Required
     * @Enum({"string", "integer", "float", "boolean"})
     */
    protected $type;

    /**
     * @Required
     */
    protected $order;

    public function __construct(array $values)
    {
        parent::__construct($values);

        $this->type = $values['type'];
        $this->order = $values['order'];
    }

    /**
     * @return Value of $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Value of $order
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function toString($value = null)
    {
        $toString = '"' . $this->getName() . '":';
        if ($this->getType() == 'string') {
            $toString .= '"' . $value . '"';
        }
        if ($this->getType() == 'integer' || $this->getType() == 'float') {
            if (!is_numeric($value)) {
                throw new InvalidTypeException(sprintf('Invalid type for %s, expected %s, %s given', $this->getName(), $this->getType(), gettype($value)));
            }
            $toString .= ($this->getType() == 'float' && is_integer($value)) ? number_format($value, 1) : $value;
        }
        if ($this->getType() == 'boolean') {
            $toString .= (boolean)$value;
        }

        return $toString;
    }
}
