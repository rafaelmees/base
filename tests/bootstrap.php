<?php

use Illuminate\Container\Container;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Create a instance of Lumen application for test propouse
 */

$app = new Container;

Container::setInstance($app);
