<?php

namespace Bludata\Lumen\Tests\Http\Controllers;

abstract class CRUDControllerTest extends BaseControllerTest
{
    use \Bludata\Lumen\Traits\Tests\Http\Controllers\CreateTrait;
    use \Bludata\Lumen\Traits\Tests\Http\Controllers\ReadTrait;
    use \Bludata\Lumen\Traits\Tests\Http\Controllers\UpdateTrait;
    use \Bludata\Lumen\Traits\Tests\Http\Controllers\DeleteTrait;
}
