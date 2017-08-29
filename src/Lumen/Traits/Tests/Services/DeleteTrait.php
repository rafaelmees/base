<?php

namespace Bludata\Lumen\Traits\Tests\Services;

use Bludata\Doctrine\ORM\Helpers\FilterHelper;

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

        return $entityRemoved;
    }
}
