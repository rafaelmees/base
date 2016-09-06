<?php

namespace Bludata\Common\Helpers;

class FormatHelper
{
    public static function onlyNumbers($value)
    {
        return preg_replace('/\D/i', '', $input);
    }

    public static function parseDate($date, $from = 'yyyy-mm-dd', $to = 'obj')
    {
        if (is_string($date) && $from == 'yyyy-mm-dd' && $to == 'obj') {
            return new \DateTime($date);
        } elseif (is_string($date) && $from == 'dd/mm/yyyy' && ($to == 'obj' || $to == 'yyyy-mm-dd')) {
            $explode = explode('/', $date);

            $date = $explode[2].'-'.$explode[1].'-'.$explode[0];

            return $to == 'obj' ? new \DateTime($date) : $date;
        }

        return $date;
    }
}
