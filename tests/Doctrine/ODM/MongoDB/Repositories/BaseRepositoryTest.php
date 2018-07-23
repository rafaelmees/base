<?php

namespace Bludata\Tests\Doctrine\ODM\MongoDB\Repositories;

use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Builder;
use Bludata\Doctrine\ORM\Repositories\QueryWorker;
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
    public function testValidate($stub)
    {
        $obj = $this->app()->mock(EntityStub::class);
        $this->assertTrue($stub->validate($obj));
    }

    /**
     * @depends testIsInstanciable
     */
    public function testGetClassMetada($stub)
    {
        $this->assertInstanceOf(ClassMetadata::class, $stub->getClassMetadata());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testGetEntityName($stub)
    {
        $this->assertEquals(EntityStub::class, $stub->getEntityName());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testCreateEntity($stub)
    {
        $this->assertInstanceOf(EntityStub::class, $stub->createEntity());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testCreateQueryWorker($stub)
    {
        $this->assertInstanceOf(QueryWorker::class, $stub->createQueryWorker());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testQuery($stub)
    {
        $this->assertInstanceOf(Builder::class, $stub->query());
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
        $obj = $this->app()->mock(EntityStub::class);
        $this->assertEquals($stub, $stub->save($obj));
        return $obj;
    }

    /**
     * @depends testSave
     * @depends testIsInstanciable
     */
    public function testFlush($obj, $stub)
    {
        $this->assertTrue(method_exists($stub, 'flush'));
        $this->assertInstanceOf(EntityStubRepository::class, $stub->flush($obj));
        return $obj;
    }

    /**
     * @depends testFlush
     * @depends testIsInstanciable
     */
    public function testFindAll($obj, $stub)
    {
        $this->assertTrue(method_exists($stub, 'findAll'));
        $query = $stub->findAll();
        $this->assertInstanceOf(QueryWorker::class, $query);
        $this->assertTrue(method_exists($query, 'getResult'));
        $result = $query->getResult();
        $this->assertInstanceOf(Cursor::class, $result);
        $this->assertGreaterThan(0, $result->count());
        $this->assertContains($obj, $result->toArray());
    }

    /**
     * @depends testFlush
     * @depends testIsInstanciable
     */
    public function testFindOneBy($obj, $stub)
    {
        $this->assertTrue(method_exists($stub, 'findOneBy'));
        $found = $stub->findOneBy(['id' => $obj->getId()]);
        $this->assertEquals($obj, $found);
    }

    /**
     * @depends testFlush
     * @depends testIsInstanciable
     */
    public function testFind($obj, $stub)
    {
        $this->assertTrue(method_exists($stub, 'find'));
        $found = $stub->find($obj->getId());
        $this->assertEquals($obj, $found);
    }

    /**
     * @depends testFlush
     * @depends testIsInstanciable
     */
    public function testFindOrCreate($obj, $stub)
    {
        // find
        $this->assertTrue(method_exists($stub, 'find'));
        $this->assertTrue(method_exists($obj, 'getId'));
        $found = $stub->findOrCreate($obj->getId());
        $this->assertEquals($obj, $found);

        // find and create
        $new = $this->app()->mock(EntityStub::class);
        $created = $stub->findOrCreate($new->toArray());
        $this->assertInstanceOf(EntityStub::class, $created);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionCode 404
     * @depends testFlush
     * @depends testIsInstanciable
     */
    public function testRemove($obj, $stub)
    {
        $this->assertEquals($obj, $stub->remove($obj));
        $stub->flush();
        $stub->find($obj->getId());
    }
}
