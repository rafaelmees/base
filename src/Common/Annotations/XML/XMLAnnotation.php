<?php

namespace Bludata\Common\Annotations\XML;

abstract class XMLAnnotation
{
    /**
     * @Required
     */
    protected $name;

    public function __construct(array $values)
    {
        $this->name = $values['name'];
    }

    /**
     * @return Value of $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Convert to string passing value
     */
    public function toString($value = null)
    {
        $toString = '<'.$this->getName().'>';

        if (is_scalar($value)) {
            $toString .= $value;
        }

        $toString .= '</'.$this->getName().'>';

        return $toString;
    }

    /**
     * @return Convert to string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
