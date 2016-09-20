<?php

namespace Bludata\Tests;

use Exception;
use Illuminate\Container\Container;

class TestApp extends Container
{
    public function abort($code, $message)
    {
        throw new Exception(sprintf('[code: %s] %s', $code, $message), $code);
    }

    public function setMockFactory($factory)
    {
        $this->offsetSet('mock.factory', $factory);
    }

    public function mock($key)
    {
        return $this->make('mock.factory')->mock($key);
    }
}
