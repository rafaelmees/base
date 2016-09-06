<?php

namespace Bludata\Common\Helpers;

use DateTime;
use InvalidArgumentException;

class FormatHelper
{
    public static function onlyNumbers($input)
    {
        return preg_replace('/\D/i', '', $input);
    }

    public static function parseDate($date, $from = 'Y-m-d', $to = 'obj')
    {
        if (!is_string($from)) {
            throw new InvalidArgumentException('Formato de entrada inválido');
        }

        if (!is_string($to)) {
            throw new InvalidArgumentException('Formato de saída inválido');
        }

        if ($date instanceOf DateTime && $to === 'obj') {
            return $date;
        }

        if ($date instanceOf DateTime) {
            return $date->format($to);
        }

        $dateObject = DateTime::createFromFormat($from, $date);

        if ($to === 'obj') {
            return $dateObject;
        }

        if (is_string($to)) {
            return $dateObject->format($to);
        }

        throw new InvalidArgumentException(
            sprintf(
                'Não foi possível converter a data "%s" do formato "%s" para o formato "%s"',
                $date,
                $from,
                $to
            )
        );
    }
}
