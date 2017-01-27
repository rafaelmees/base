<?php

namespace Bludata\Lumen\Http\Controllers;

abstract class CRUDController extends BaseController
{
    use \Bludata\Lumen\Traits\Http\Controllers\CreateTrait;
    use \Bludata\Lumen\Traits\Http\Controllers\ReadTrait;
    use \Bludata\Lumen\Traits\Http\Controllers\UpdateTrait;
    use \Bludata\Lumen\Traits\Http\Controllers\DeleteTrait;
}
