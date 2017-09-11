<?php

namespace Bludata\Lumen\Traits\Tests\Http\Controllers;

trait ReadTrait
{
    public function testIndex()
    {
        $this->getServiceTest()->getRepositoryTest()->getFlushedMockObject();

        $response = $this->curlHelper->send()->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertGreaterThan(0, count($data));
        $this->assertNotNull($data[0]['id']);
    }

    public function testShow()
    {
        $entity = $this->getServiceTest()->getRepositoryTest()->getFlushedMockObject();

        $response = $this->curlHelper->setPosFixUrl('/'.$entity->getId())->send()->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertEquals($entity->getId(), $data['id']);
    }
}
