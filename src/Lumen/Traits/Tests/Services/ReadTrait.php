<?php

namespace Bludata\Lumen\Traits\Tests\Services;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;

trait ReadTrait
{
    public function testFindAll(BaseEntityInterface $entity = null)
    {
        $entity = $entity
                    ? $entity
                    : $this->getRepositoryTest()->getFlushedMockObject();

        $findAll = $this->getService()->findAll()->getResult();

        $this->assertGreaterThan(0, count($findAll));
        $this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $findAll[0]);

        $filter = array_values(array_filter($findAll, function ($obj) use ($entity) {
            return $obj->getId() == $entity->getId();
        }));

        $this->assertEquals($filter[0]->getId(), $entity->getId());
    }

    public function testFind(BaseEntityInterface $entity = null)
    {
        $entity = $entity
                    ? $entity
                    : $this->getRepositoryTest()->getFlushedMockObject();

        $find = $this->getService()->find($entity->getId());

        $this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $find);
        $this->assertEquals($entity->getId(), $find->getId());
    }
}