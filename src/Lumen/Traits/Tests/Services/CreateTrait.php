<?php

namespace Bludata\Lumen\Traits\Tests\Services;

trait CreateTrait
{
    public function testStore()
    {
        $entity = $this->getService()
                       ->store($this->getRepositoryTest()->getMockArray())
                       ->flush();

        $repository = $this->getService()
                           ->getMainRepository();

        $find = $repository->find($entity->getId());

        $this->assertInstanceOf($repository->getEntityName(), $entity);
        $this->assertEquals($entity->getId(), $find->getId());
    }
}
