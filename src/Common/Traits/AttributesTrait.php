<?php

namespace Bludata\Common\Traits;

use ArrayAccess;
use InvalidArgumentException;

trait AttributesTrait
{
    public function getAttributes()
    {
        $attributes = array_keys(
            get_class_vars(
                get_class($this)
            )
        );

        return array_combine(
            $attributes,
            array_map(
                function ($attr) {
                    $getMethod = $this->getMethod($attr);
                    if (method_exists($this, $getMethod)) {
                        return $this->$getMethod();
                    }

                    return $this->$attr;
                },
                $attributes
            )
        );
    }

    public function getMethod($attr)
    {
        return sprintf('get%s', ucfirst($attr));
    }

    public function setMethod($attr)
    {
        return sprintf('set%s', ucfirst($attr));
    }

    public function get($attr)
    {
        $attributes = $this->getAttributes();
        if (in_array($attr, array_keys($attributes))) {
            return $this->$attr;
        }

        $getMethod = $this->getMethod($attr);
        if (method_exists($this, $getMethod)) {
            return $this->$getMethod();
        }

        throw new InvalidArgumentException(
            sprintf(
                'The class "%s" don\'t have a method "%s"',
                get_class($this),
                $getMethod
            )
        );
    }

    public function set($attr, $value)
    {
        $attributes = $this->getAttributes();
        if (in_array($attr, array_keys($attributes))) {
            return $this->$attr = $value;
        }

        $setMethod = $this->setMethod($attr);
        if (method_exists($this, $setMethod)) {
            return $this->$setMethod($value);
        }

        throw new InvalidArgumentException(
            sprintf(
                'The class "%s" don\'t have a method "%s"',
                get_class($this),
                $setMethod
            )
        );
    }

    public function toArray()
    {
        return array_map(function ($attr) {
            if (method_exists($attr, 'toArray')) {
                return $attr->toArray();
            }

            if ($attr instanceof ArrayAccess) {
                $newParam = [];
                foreach ($attr as $p) {
                    if (method_exists($p, 'toArray')) {
                        $newParam[] = $p->toArray();
                        continue;
                    }

                    $newParam[] = $p;
                }

                return $newParam;
            }

            return $attr;
        }, $this->getAttributes());
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function toString()
    {
        return print_r($this, true);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function __get($attr)
    {
        return $this->get($attr);
    }

    public function __set($attr, $value)
    {
        return $this->set($attr, $value);
    }

    public function __isset($attr)
    {
        return isset($this->$attr);
    }

    public function __unset($attr)
    {
        unset($this->$attr);
    }
}
