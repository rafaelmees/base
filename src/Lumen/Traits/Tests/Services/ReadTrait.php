<?php

namespace Bludata\Lumen\Traits\Tests\Services;

trait ReadTrait
{
    /**
     * @depends testStore
     */
    public function testFindAll($entity)
    {
        $findAll = $this->getService()->findAll()->getResult();

        $this->assertGreaterThan(0, count($findAll));
        $this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $findAll[0]);

        $filter = array_values(array_filter($findAll, function($obj) use ($entity) {
            return $obj->getId() == $entity->getId();
        }));

        $this->assertEquals($filter[0]->getId(), $entity->getId());

        return $findAll;
    }

    /**
     * @depends testStore
     */
    public function testFind($entity)
    {
        $find = $this->getService()->find($entity->getId());

        $this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $find);
        $this->assertEquals($entity->getId(), $find->getId());

        return $find;
    }
}
