<?php

use Illuminate\Container\Container;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Create a instance of Lumen application for test propouse
 */

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

$app = new TestApp;

$factory = require __DIR__ . '/mocks.php';

$app->setMockFactory($factory);

Container::setInstance($app);
