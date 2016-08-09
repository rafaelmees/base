<?php

namespace Bludata\Lumen\Tests\Http\Controllers;

abstract class CRUDControllerTest extends BaseControllerTest
{
    public function testIndex()
    {
        $this->getServiceTest()->getRepositoryTest()->getFlushedMockObject();

        $response = $this->curlHelper->send()->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertGreaterThan(0, count($data));
        $this->assertGreaterThan(0, $data[0]['id']);
    }

    public function testShow()
    {
        $entity = $this->getServiceTest()->getRepositoryTest()->getFlushedMockObject();

        $response = $this->curlHelper->setPosFixUrl('/'.$entity->getId())->send()->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertEquals($entity->getId(), $data['id']);
    }

    public function testStore()
    {
        $response = $this->curlHelper
                         ->post(
                            $this->getServiceTest()->getRepositoryTest()->getMockArray()
                         )
                         ->send()
                         ->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertGreaterThan(0, $data['id']);
    }

    public function testUpdate()
    {
        $flushedMockArray = $this->getServiceTest()->getRepositoryTest()->getFlushedMockArray();
        $mockArray = $this->getServiceTest()->getRepositoryTest()->getMockArray();

        foreach ($this->getController()->getMainService()->getMainRepository()->createEntity()->getOnlyUpdate() as $key) {
            if (is_bool($flushedMockArray[$key])) {
                $flushedMockArray[$key] = !$flushedMockArray[$key];
            } else {
                $flushedMockArray[$key] = $mockArray[$key];
            }
        }

        $response = $this->curlHelper
                         ->setPosFixUrl('/'.$flushedMockArray['id'])
                         ->put($flushedMockArray)
                         ->send()
                         ->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertEquals($flushedMockArray['id'], $data['id']);

        $this->assertEquals(true, strtotime($data['updatedAt']));
    }

    public function testDestroy()
    {
        $entity = $this->getServiceTest()->getRepositoryTest()->getFlushedMockObject();

        $response = $this->curlHelper
                         ->setPosFixUrl('/'.$entity->getId())
                         ->delete()
                         ->send()
                         ->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertEquals($entity->getId(), $data['id']);
        $this->assertEquals(true, strtotime($data['deletedAt']));
    }
}
