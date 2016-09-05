<?php

namespace Bludata\Common\Traits;

trait ToArray
{
    public function toArray()
    {
        if (method_exists($this, 'toArray')) {
            return $this->toArray();
        }

        return (array) $this;
    }
}
