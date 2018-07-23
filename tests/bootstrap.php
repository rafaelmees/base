<?php

use Illuminate\Container\Container;
use Psr\Log\LoggerInterface;

require __DIR__ . '/../vendor/autoload.php';

$app = new \Bludata\Tests\TestApp;

$factory = require __DIR__ . '/mocks.php';

$app->setMockFactory($factory);

$app->bind(LoggerInterface::class, \Bludata\Tests\Lumen\Traits\LogTraitStub::class);

Container::setInstance($app);

return $app;
