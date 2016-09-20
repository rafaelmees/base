<?php

use Illuminate\Container\Container;

require __DIR__ . '/../vendor/autoload.php';

$app = new \Bludata\Tests\TestApp;

$factory = require __DIR__ . '/mocks.php';

$app->setMockFactory($factory);

Container::setInstance($app);

return $app;
