<?php

namespace Bludata\Helpers;

class Format
{
	public static function onlyNumbers($value)
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    public static function parseDate($date, $from = 'yyyy-mm-dd', $to = 'obj')
    {
        if (is_string($date) && $from == 'yyyy-mm-dd' && $to == 'obj')
        {
            return new \DateTime($date);
        }

        return $date;
    }
}
