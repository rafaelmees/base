<?php

namespace Bludata\Lumen\Tests\Http\Controllers;

use Bludata\Lumen\Tests\BaseTest;
use Bludata\Lumen\Helpers\CurlHelper;

abstract class BaseControllerTest extends BaseTest
{
    protected $curlHelper;

    public function __construct()
    {
        $this->curlHelper = new CurlHelper(env('BASE_URL').$this->getBaseRoute(), ['Content-Type: application/json']);
    }

    abstract public function getController();

    abstract public function getServiceTest();

    abstract public function getBaseRoute();
}
