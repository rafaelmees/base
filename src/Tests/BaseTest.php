<?php

namespace Bludata\Tests;

use Faker\Factory;
use Laravel\Lumen\Testing\TestCase;

class BaseTest extends TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function getFaker($locale = 'pt_BR')
    {
        return Factory::create($locale);
    }
}
