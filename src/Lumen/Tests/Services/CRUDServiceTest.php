<?php

namespace Bludata\Lumen\Tests\Services;

abstract class CRUDServiceTest extends BaseServiceTest
{
    use \Bludata\Lumen\Traits\Tests\Services\CreateTrait;
    use \Bludata\Lumen\Traits\Tests\Services\ReadTrait;
    use \Bludata\Lumen\Traits\Tests\Services\UpdateTrait;
    use \Bludata\Lumen\Traits\Tests\Services\DeleteTrait;
}
