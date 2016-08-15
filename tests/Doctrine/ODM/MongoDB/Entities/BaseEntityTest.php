<?php

namespace Bludata\Tests\Doctrine\ODM\MongoDB\Entities;

use Bludata\Tests\Doctrine\ODM\MongoDB\TestCase;
use Bludata\Doctrine\Common\Interfaces\BaseRepositoryInterface;
use Bludata\Doctrine\Common\Interfaces\EntityManagerInterface;
use Bludata\Tests\Doctrine\ODM\MongoDB\Entities\Stubs\EntityStub;
use Bludata\Tests\Doctrine\ODM\MongoDB\Repositories\Stubs\EntityStubRepository;

class BaseEntityTest extends TestCase
{
    public function testIsInstanciable()
    {
        $stub = new EntityStub;
        $this->assertInstanceOf(EntityStub::class, $stub);
        return $stub;
    }

    /**
     * @depends testIsInstanciable
     */
    public function testGetCreatedAt($stub)
    {
        $this->assertTrue(method_exists($stub, 'getCreatedAt'));
        $this->assertObjectHasAttribute('createdAt', $stub);
        $now = $this->faker()->dateTime;
        $this->setObjectAttribute($stub, 'createdAt', $now);
        $this->assertEquals($now, $stub->getCreatedAt());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testGetUpdatedAt($stub)
    {
        $this->assertTrue(method_exists($stub, 'getUpdatedAt'));
        $this->assertObjectHasAttribute('updatedAt', $stub);
        $now = $this->faker()->dateTime;
        $this->setObjectAttribute($stub, 'updatedAt', $now);
        $this->assertEquals($now, $stub->getUpdatedAt());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testGetDeletedAt($stub)
    {
        $this->assertTrue(method_exists($stub, 'getDeletedAt'));
        $this->assertObjectHasAttribute('deletedAt', $stub);
        $now = $this->faker()->dateTime;
        $this->setObjectAttribute($stub, 'deletedAt', $now);
        $this->assertEquals($now, $stub->getDeletedAt());
        return $stub;
    }

    /**
     * @depends testGetDeletedAt
     */
    public function testSetDeletedAt($stub)
    {
        $this->assertTrue(method_exists($stub, 'setDeletedAt'));
        $now = $this->faker()->dateTime;
        $stub->setDeletedAt($now);
        $this->assertAttributeEquals($now, 'deletedAt', $stub);
    }

    /**
     * @depends testIsInstanciable
     */
    public function testForcePersist($stub)
    {
        $this->assertTrue(method_exists($stub, 'forcePersist'));
    }

    /**
     * @depends testIsInstanciable
     */
    public function testPrePersist($stub)
    {
        $this->assertTrue(method_exists($stub, 'prePersist'));
    }

    /**
     * @depends testIsInstanciable
     */
    public function testPreUpdate($stub)
    {
        $this->assertTrue(method_exists($stub, 'preUpdate'));
    }

    /**
     * @depends testIsInstanciable
     */
    public function testGetRepository($stub)
    {
        $this->assertTrue(method_exists($stub, 'getRepository'));
        $this->assertInstanceOf(BaseRepositoryInterface::class, $stub->getRepository());
        $this->assertInstanceOf(EntityStubRepository::class, $stub->getRepository());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testPrePersistCall($stub)
    {
        $stub->setAttr1('Lorem');
        $stub->save();
        $stub->flush();
        $this->assertTrue($stub->getPrePersistWasCall());
        return $stub;
    }

    /**
     * @depends testIsInstanciable
     */
    public function testSave($stub)
    {
        $this->assertEquals($stub, $stub->save());
        return $stub;
    }

    /**
     * @depends testSave
     */
    public function testRemove($stub)
    {
        $this->assertEquals($stub, $stub->remove());
        return $stub;
    }

    /**
     * @depends testSave
     */
    public function testFlush($stub)
    {
        $this->assertEquals($stub, $stub->flush());
        return $stub;
    }

    /**
     * @depends testFlush
     */
    public function testSaveWithFlush($stub)
    {
        $this->assertEquals($stub, $stub->save(true));
        return $stub;
    }

    /**
     * @depends testIsInstanciable
     */
    public function testGetId($stub)
    {
        $this->assertTrue(method_exists($stub, 'getId'));
        $this->assertObjectHasAttribute('id', $stub);
        $id = $this->faker()->randomNumber;
        $this->setObjectAttribute($stub, 'id', $id);
        $this->assertEquals($id, $stub->getId());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testGetOnlyStore($stub)
    {
        $this->assertTrue(method_exists($stub, 'getOnlyStore'));
        $this->assertInternalType('array', $stub->getOnlyStore());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testGetOnlyUpdate($stub)
    {
        $this->assertTrue(method_exists($stub, 'getOnlyUpdate'));
        $this->assertInternalType('array', $stub->getOnlyUpdate());
    }

    /**
     * @depends testIsInstanciable
     */
    public function testSetPropertiesEntity($stub)
    {
        $params = [
            'id' => $this->faker()->randomNumber,
            'createdAt' => $this->faker()->dateTime,
            'updatedAt' => $this->faker()->dateTime,
            'deletedAt' => $this->faker()->dateTime,
            'attr1' => $this->faker()->word,
            'attr2' => $this->faker()->word,
        ];

        $stub->setPropertiesEntity($params);

        $this->assertAttributeNotEquals($params['id'], 'id', $stub);
        $this->assertAttributeNotEquals($params['createdAt'], 'createdAt', $stub);
        $this->assertAttributeNotEquals($params['updatedAt'], 'updatedAt', $stub);
        $this->assertAttributeNotEquals($params['deletedAt'], 'deletedAt', $stub);
        $this->assertAttributeEquals($params['attr1'], 'attr1', $stub);
        $this->assertAttributeEquals($params['attr2'], 'attr2', $stub);
    }

    /**
     * @depends testIsInstanciable
     */
    public function testToArray($stub)
    {
        $this->assertTrue(method_exists($stub, 'toArray'));
        $result = $stub->toArray();
        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
    }
}
