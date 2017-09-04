<?php

namespace Bludata\Lumen\Traits\Tests\Services;

trait DeleteTrait
{
    /**
     * @depends testStore
     */
    public function testRemove($entity)
    {
        $entityRemoved = $this->getService()
                ->remove($entity->getId())
                ->flush();

        $this->assertInstanceOf('DateTime', $entityRemoved->getDeletedAt());
        $this->assertNull($this->getService()->getMainRepository()->find($entity->getId(), false));

        return $entityRemoved;
    }
}
