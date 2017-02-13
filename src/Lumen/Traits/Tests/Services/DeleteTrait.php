<?php

namespace Bludata\Lumen\Traits\Tests\Services;

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
}
