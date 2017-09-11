<?php

namespace Bludata\Lumen\Traits\Tests\Services;

trait DeleteTrait
{
    public function testRemove()
    {
        $entity = $this->getRepositoryTest()->getFlushedMockObject();

        $entityRemoved = $this->getService()
                ->remove($entity->getId())
                ->flush();

        $this->assertInstanceOf('DateTime', $entityRemoved->getDeletedAt());
        $this->assertNull($this->getService()->getMainRepository()->find($entity->getId(), false));
    }
}
