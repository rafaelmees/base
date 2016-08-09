<?php

namespace Bludata\Lumen\Tests\Http\Controllers;

<<<<<<< HEAD:src/Lumen/Tests/Http/Controllers/BaseControllerTest.php
use Bludata\Helpers\CurlHelper;
=======
use Bludata\Lumen\Helpers\CurlHelper;
>>>>>>> 23d05296dece732c2042b36ddf80c2de5961911d:src/Lumen/Tests/Http/Controllers/BaseControllerTest.php
use Bludata\Lumen\Tests\BaseTest;

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
