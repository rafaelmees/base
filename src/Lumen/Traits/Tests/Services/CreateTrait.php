<?php

namespace Bludata\Lumen\Traits\Tests\Services;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;

trait CreateTrait
{
    public function testStore(BaseEntityInterface $entity = null)
    {
        $entity = $entity
                    ? $entity
                    : $this->getService()
                           ->store($this->getRepositoryTest()->getMockArray())
                           ->flush();

        $repository = $this->getService()->getMainRepository();

        $this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $entity);
        $this->assertNotNull($entity->getId());
        $this->assertInstanceOf('DateTime', $entity->getCreatedAt());
    }
}
