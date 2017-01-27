<?php

namespace Bludata\Lumen\Traits\Tests\Services;

trait UpdateTrait
{
	public function testUpdate()
    {
        $flushedMockArray = $this->getRepositoryTest()->getFlushedMockArray();
        $mockArray = $this->getRepositoryTest()->getMockArray();

        foreach ($this->getService()->getMainRepository()->createEntity()->getOnlyUpdate() as $key) {
            if (is_bool($flushedMockArray[$key])) {
                $flushedMockArray[$key] = !$flushedMockArray[$key];
            } else {
                $flushedMockArray[$key] = $mockArray[$key];
            }
        }

        $entity = $this->getService()
                       ->update($flushedMockArray['id'], $flushedMockArray)
                       ->flush();

        $repository = $this->getService()->getMainRepository();

        $find = $repository->find($entity->getId());

        $this->assertInstanceOf($repository->getEntityName(), $entity);
        $this->assertInstanceOf('\DateTime', $entity->getUpdatedAt());
        $this->assertEquals($entity->getId(), $find->getId());
    }
}
