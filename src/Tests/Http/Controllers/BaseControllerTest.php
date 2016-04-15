<?php

namespace Bludata\Tests\Http\Controllers;

use Bludata\Tests\BaseTest;

abstract class BaseControllerTest extends BaseTest
{
    abstract public function getController();

    abstract public function getServiceTest();

    public function createRequest(array $replace = [])
    {
    	$request = app('\Illuminate\Http\Request');
        $request->headers->set('Content-Type', 'application/json');
        $request->replace($replace);

        return $request;
    }
}
