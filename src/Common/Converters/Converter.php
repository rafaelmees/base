<?php

namespace Bludata\Common\Converters;

abstract class Converter
{
    abstract public function toString($element);
}
