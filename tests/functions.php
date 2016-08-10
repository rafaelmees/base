<?php

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Bludata\Doctrine\ODM\MongoDB\EntityManager;

/**
 * Generate a new DocumentManager for Test propouse only
 *
 * @return Doctrine\Common\Persistence\ObjectManager $dm
 */
function generageODMEntityManager()
{
    $config = new Configuration;
    $config->setProxyDir(__DIR__ .'/Doctrine/ODM/MongoDB/Entities/Proxies');
    $config->setProxyNamespace('Tests\\Proxy');
    $config->setHydratorDir(__DIR__ .'/Doctrine/ODM/MongoDB/Entities/Hydrators');
    $config->setHydratorNamespace('Tests\\Hydrators');
    $config->setDefaultDB('test-base-api-php');
    $config->setMetadataDriverImpl(AnnotationDriver::create( __DIR__ .'/Doctrine/ODM/MongoDB/Entities/Stubs'));
    $connection = new Connection;
    AnnotationDriver::registerAnnotationClasses();
    return EntityManager::create($connection, $config);
}
