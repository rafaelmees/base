<?php

namespace Bludata\Lumen\Traits\Tests\Http\Controllers;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;

trait ReadTrait
{
    public function testIndex(BaseEntityInterface $entity = null)
    {
        $entity = $entity ? $entity : $this->getRepositoryTest()->getFlushedMockObject();

        $response = $this->curlHelper->send()->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertGreaterThan(0, count($data));
        $this->assertNotNull($data[0]['id']);
    }

    public function testCount(BaseEntityInterface $entity = null)
    {
        $entity = $entity ? $entity : $this->getRepositoryTest()->getFlushedMockObject();

        $response = $this->curlHelper->setPosFixUrl('/count')->send()->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertArrayHasKey('count', $data);
        $this->assertGreaterThan(0, $data['count']);
    }

    public function testShow(BaseEntityInterface $entity = null)
    {
        $entity = $entity ? $entity : $this->getRepositoryTest()->getFlushedMockObject();

        $response = $this->curlHelper->setPosFixUrl('/'.$entity->getId())->send()->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertEquals($entity->getId(), $data['id']);
    }
}
