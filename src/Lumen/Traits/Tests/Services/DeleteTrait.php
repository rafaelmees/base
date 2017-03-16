<?php

namespace Bludata\Lumen\Traits\Tests\Services;

use Bludata\Doctrine\ORM\Helpers\FilterHelper;

trait DeleteTrait
{
    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testRemove()
    {
        $entity = $this->getRepositoryTest()
                       ->getFlushedMockObject();

        $this->getService()
             ->remove($entity->getId())
             ->flush();

        $find = $this->getService()->getMainRepository()->find($entity->getId());
    }

    public function testDestroyed()
    {
        $entity = $this->getRepositoryTest()
                       ->getFlushedMockObject()
                       ->remove()
                       ->flush();

        $findAllDestroyed = $this->getService()->findAllDestroyed()->getResult();

        $this->assertGreaterThan(0, count($findAllDestroyed));
        $this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $findAllDestroyed[0]);

        foreach ($findAllDestroyed as $entity)
        {
            $this->assertNotNull($entity->getDeletedAt());
        }

        FilterHelper::enableSoftDeleteableFilter();
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testRestoreDestroyed()
    {
        $entity = $this->getRepositoryTest()
                       ->getFlushedMockObject()
                       ->remove()
                       ->flush();

        $this->getService()->getMainRepository()->clear($entity);

        $retored = $this->getService()
                        ->restoreDestroyed($entity->getId())
                        ->flush();

        $find = $this->getService()->getMainRepository()->findRemoved($entity->getId());
    }
}
