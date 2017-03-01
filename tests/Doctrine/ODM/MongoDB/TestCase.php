<?php

namespace Bludata\Tests\Doctrine\ODM\MongoDB;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Bludata\Doctrine\Common\Interfaces\EntityManagerInterface;
use Bludata\Doctrine\ODM\MongoDB\EntityManager;
use Bludata\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        $this->app()->bind(EntityManagerInterface::class, function () {
            $config = new Configuration;
            $config->setProxyDir(__DIR__ .'/Entities/Proxies');
            $config->setProxyNamespace('Tests\\Proxy');
            $config->setHydratorDir(__DIR__ .'/Entities/Hydrators');
            $config->setHydratorNamespace('Tests\\Hydrators');
            $config->setDefaultDB('test-base-api-php');
            $config->setMetadataDriverImpl(AnnotationDriver::create( __DIR__ .'/Entities/Stubs'));
            $connection = new Connection(env('DB_MONGODB_HOST', 'localhost'));
            AnnotationDriver::registerAnnotationClasses();
            return EntityManager::create($connection, $config);
        });
    }
}
