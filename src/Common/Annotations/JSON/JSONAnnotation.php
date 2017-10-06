<?php

namespace Bludata\Common\Annotations\JSON;

abstract class JSONAnnotation
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
     * @return string to string passing value
     */
    abstract public function toString($value = null);

    /**
     * @return string to string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
