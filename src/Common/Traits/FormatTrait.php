<?php

namespace Bludata\Common\Traits;

trait FormatTrait
{
    function onlyNumbers($input)
    {
        $input = preg_replace('/\D/i', '', $input);

        if (!is_numeric($input)) {
            return;
        }

        return $input;
    }
}
