<?php

/**
 * Dump and die.
 */
if (!function_exists('dd')) {
    function dd()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            var_dump($arg);
        }
        exit();
    }
}

/*
 * Print and die
 */
if (!function_exists('pd')) {
    function pd()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            print_r($arg);
        }
        exit();
    }
}

/*
 * Dump and return
 */
if (!function_exists('dr')) {
    function dr()
    {
        $args = func_get_args();
        $result = '';
        foreach ($args as $arg) {
            $result = '';
            $type = gettype($arg);
            if ($type == 'object') {
                $type = get_class($arg);
            }
            if ($type == 'boolean') {
                $result = $arg ? 'true' : 'false';
            } else {
                $result = print_r($arg, true);
            }
            $result = sprintf('(%s) %s', $type, $result);
        }

        return $result;
    }
}

/*
 * Get Enviorment variable
 */
if (!function_exists('env')) {
    function env($key, $defaultValue = '')
    {
        $env = getenv($key);
        if (!$env) {
            return $defaultValue;
        }

        return $env;
    }
}
