<?php

use Illuminate\Container\Container;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Create a instance of Lumen application for test propouse
 */

class TestApp extends Container {

    public function abort($code, $message)
    {
        throw new Exception(sprintf('[code: %s] %s', $code, $message));
    }

}

$app = new TestApp;

Container::setInstance($app);
