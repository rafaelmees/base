<?php

use Illuminate\Container\Container;
use Psr\Log\LoggerInterface;

/**
 * Setup Annotations
 */
register_annotation_dir(__DIR__ . '/../src/Common/Annotations/XML');

/**
 * Setup Test Application
 */
require __DIR__ . '/../vendor/autoload.php';

$factory = require __DIR__ . '/mocks.php';
$app = new \Bludata\Tests\TestApp;
$app->setMockFactory($factory);
$app->bind(
    LoggerInterface::class,
    \Bludata\Tests\Lumen\Traits\LogTraitStub::class
);

Container::setInstance($app);

return $app;
