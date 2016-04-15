<?php

namespace Bludata\Tests\Services;

use Bludata\Tests\BaseTest;

abstract class CRUDServiceTest extends BaseServiceTest
{
    public function testFindAll()
	{
		$entity = $this->getService()->store($this->getRepositoryTest()->getMockArray());

		$this->getService()->getMainRepository()->flush();

		$findAll = $this->getService()->findAll()->getResult();

		$this->assertGreaterThan(0, count($findAll));
		$this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $findAll[0]);
	}

	public function testStore()
	{
		$entity = $this->getService()->store($this->getRepositoryTest()->getMockArray());

		$repository = $this->getService()->getMainRepository();

		$repository->flush();

		$find = $repository->find($entity->getId());

		$this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $entity);
		$this->assertEquals($entity->getId(), $find->getId());
	}

	public function testUpdate()
	{
		$flushedMockArray = $this->getRepositoryTest()->getFlushedMockArray();
		$mockArray = $this->getRepositoryTest()->getMockArray();

		foreach ($this->getService()->getMainRepository()->getEntity()->getOnlyUpdate() as $key) {
			$flushedMockArray[$key] = $mockArray[$key];
		}

		$entity = $this->getService()->update($flushedMockArray['id'], $flushedMockArray);

		$repository = $this->getService()->getMainRepository();

		$repository->flush();

		$find = $repository->find($entity->getId());

		$this->assertInstanceOf($this->getService()->getMainRepository()->getEntityName(), $entity);
		$this->assertInstanceOf('\DateTime', $entity->getUpdatedAt());
		$this->assertEquals($entity->getId(), $find->getId());
	}

	/**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
	public function testRemove()
	{
		$entity = $this->getService()->store($this->getRepositoryTest()->getMockArray());

		$repository = $this->getService()->getMainRepository();

		$repository->flush();

		$this->getService()->remove($entity->getId());

		$repository->flush();

		$find = $repository->find($entity->getId());
	}
}
