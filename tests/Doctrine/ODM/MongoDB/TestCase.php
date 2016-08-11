<?php

namespace Bludata\Tests\Doctrine\ODM\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;
use Bludata\Doctrine\Common\Interfaces\EntityManagerInterface;
use Bludata\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        $this->app()->bind(EntityManagerInterface::class, function () {
            return generageODMEntityManager();
        });
    }
}
