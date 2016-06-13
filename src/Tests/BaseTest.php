<?php

namespace Bludata\Tests;

use Auth;
use Faker\Factory;
use Illuminate\Auth\GenericUser;
use Laravel\Lumen\Testing\TestCase;

abstract class BaseTest extends TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {

    }

    public function getFaker($locale = 'pt_BR')
    {
        return Factory::create($locale);
    }
}
