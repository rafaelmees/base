<?php

namespace Bludata\Tests\Services;

use Bludata\Tests\BaseTest;
use EntityManager;

abstract class CRUDServiceTest extends BaseServiceTest
{
    public function testFindAll()
	{
		$entity = $this->getService()
					   ->store($this->getRepositoryTest()->getMockArray())
					   ->flush();

		$findAll = $this->getService()->findAll()->getResult();

		$this->assertGreaterThan(0, count($findAll));
		$this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $findAll[0]);
	}

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

	public function testUpdate()
	{
		$flushedMockArray = $this->getRepositoryTest()->getFlushedMockArray();
		$mockArray = $this->getRepositoryTest()->getMockArray();

		foreach ($this->getService()->getMainRepository()->createEntity()->getOnlyUpdate() as $key) {
			if (is_bool($flushedMockArray[$key]))
			{
				$flushedMockArray[$key] = !$flushedMockArray[$key];
			}
			else 
			{
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

	/**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
	public function testRemove()
	{
		$entity = $this->getService()
					   ->store($this->getRepositoryTest()->getMockArray())
					   ->flush();

		$this->getService()
			 ->remove($entity->getId())
			 ->flush();

		$find = $this->getService()->getMainRepository()->find($entity->getId());
	}
}
