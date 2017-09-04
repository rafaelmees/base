<?php

namespace Bludata\Lumen\Traits\Tests\Services;

trait CreateTrait
{
    public function testStore()
    {
        $entity = $this->getService()
                        ->store($this->getRepositoryTest()->getMockArray())
                        ->flush();

        $repository = $this->getService()->getMainRepository();

        $this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $entity);
        $this->assertNotNull($entity->getId());
        $this->assertInstanceOf('DateTime', $entity->getCreatedAt());

        return $entity;
    }
}
