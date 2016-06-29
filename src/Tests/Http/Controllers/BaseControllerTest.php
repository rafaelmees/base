<?php

namespace Bludata\Tests\Http\Controllers;

use Bludata\Helpers\CurlHelper;
use Bludata\Tests\BaseTest;

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
