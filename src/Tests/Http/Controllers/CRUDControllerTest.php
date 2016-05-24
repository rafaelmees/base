<?php

namespace Bludata\Tests\Http\Controllers;

abstract class CRUDControllerTest extends BaseControllerTest
{
    public function testIndex()
	{
		$this->getServiceTest()->getRepositoryTest()->getFlushedMockArray();

		$this->response = $this->getController()->index($this->createRequest());

		$json = json_decode($this->response->getContent(), true);

        $this->assertResponseOk();
        $this->assertGreaterThan(0, count($json));
	}

	public function testStore()
	{
        $this->response = $this->getController()->store($this->createRequest($this->getServiceTest()->getRepositoryTest()->getMockArray()));

        $json = json_decode($this->response->getContent(), true);

        $this->assertResponseOk();
        $this->assertGreaterThan(0, $json['id']);
	}

	public function testUpdate()
	{
        $flushedMockArray = $this->getServiceTest()->getRepositoryTest()->getFlushedMockArray();
		$mockArray = $this->getServiceTest()->getRepositoryTest()->getMockArray();

		foreach ($this->getController()->getMainService()->getMainRepository()->createEntity()->getOnlyUpdate() as $key) {
			if (is_bool($flushedMockArray[$key]))
			{
				$flushedMockArray[$key] = !$flushedMockArray[$key];
			}
			else 
			{
				$flushedMockArray[$key] = $mockArray[$key];
			}
		}

        $this->response = $this->getController()->update($this->createRequest($flushedMockArray), $flushedMockArray['id']);

        $json = json_decode($this->response->getContent(), true);

        $this->assertResponseOk();
        $this->assertEquals($flushedMockArray['id'], $json['id']);
        $this->assertEquals(true, strtotime($json['updatedAt']));
	}

	public function testDestroy()
	{
		$flushedMockArray = $this->getServiceTest()->getRepositoryTest()->getFlushedMockArray();

		$this->response = $this->getController()->destroy($flushedMockArray['id']);

		$json = json_decode($this->response->getContent(), true);

        $this->assertResponseOk();
        $this->assertEquals($flushedMockArray['id'], $json['id']);
        $this->assertEquals(true, strtotime($json['deletedAt']));
	}
}
