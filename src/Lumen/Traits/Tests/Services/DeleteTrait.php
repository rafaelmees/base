<?php

namespace Bludata\Lumen\Traits\Tests\Services;

use Bludata\Doctrine\ORM\Helpers\FilterHelper;

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

    /**
     * @depends testRemove
     */
    public function testFindAllDestroyed($entity)
    {
        $findAllDestroyed = $this->getService()->findAllDestroyed()->getResult();

        $this->assertGreaterThan(0, count($findAllDestroyed));
        $this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $findAllDestroyed[0]);

        foreach ($findAllDestroyed as $entity)
        {
            $this->assertNotNull($entity->getDeletedAt());
        }

        $filter = array_values(array_filter($findAllDestroyed, function ($obj) use ($entity) {
            return $obj->getId() == $entity->getId();
        }));

        $this->assertEquals($filter[0]->getId(), $entity->getId());

        FilterHelper::enableSoftDeleteableFilter();

        return $findAllDestroyed;
    }

    /**
     * @depends testRemove
     */
    public function testRestoreDestroyed($entity)
    {
        $entityRestored = $this->getService()
                               ->restoreDestroyed($entity->getId())
                               ->flush();

        $this->assertNull($entityRestored->getDeletedAt());

        return $entityRestored;
    }
}
