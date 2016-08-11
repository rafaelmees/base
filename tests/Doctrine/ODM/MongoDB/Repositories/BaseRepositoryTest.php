<?php

namespace Bludata\Tests\Doctrine\ODM\MongoDB\Repositories;

use Doctrine\ODM\MongoDB\DocumentManager;
use Bludata\Doctrine\Common\Interfaces\EntityManagerInterface;
use Bludata\Tests\Doctrine\ODM\MongoDB\TestCase;
use Bludata\Tests\Doctrine\ODM\MongoDB\Entities\Stubs\EntityStub;
use Bludata\Tests\Doctrine\ODM\MongoDB\Repositories\Stubs\EntityStubRepository;

class BaseRepositoryTest extends TestCase
{
    public function testIsInstanciable()
    {
        $em = $this->app(EntityManagerInterface::class);
        $stub = $em->getRepository(EntityStub::class);
        $this->assertInstanceOf(EntityStubRepository::class, $stub);
        return $stub;
    }

    /**
     * @depends testIsInstanciable
     */
    public function testEm($stub)
    {
        $this->assertTrue(method_exists($stub, 'em'));
        $this->assertInstanceOf(DocumentManager::class, $stub->em());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testSave($stub)
    {
        $this->assertTrue(method_exists($stub, 'save'));
        $obj = new EntityStub;
        $obj->setAttr1($this->faker()->word);
        $obj->setAttr2($this->faker()->word);
        $this->assertEquals($stub, $stub->save($obj));
        return $obj;
    }
}
