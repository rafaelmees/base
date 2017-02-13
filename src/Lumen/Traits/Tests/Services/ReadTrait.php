<?php

namespace Bludata\Lumen\Traits\Tests\Services;

trait ReadTrait
{
    public function testFindAll()
    {
        $entity = $this->getRepositoryTest()
                       ->getFlushedMockObject();

        $findAll = $this->getService()->findAll()->getResult();

        $this->assertGreaterThan(0, count($findAll));
        $this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $findAll[0]);
    }

    public function testFind()
    {
        $entity = $this->getRepositoryTest()
                       ->getFlushedMockObject();

        $find = $this->getService()->find($entity->getId());

        $this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $find);
        $this->assertEquals($entity->getId(), $find->getId());
    }
}
