<?php

namespace Bludata\Lumen\Traits\Tests\Http\Controllers;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;

trait CreateTrait
{
    public function testStore(BaseEntityInterface $entity = null)
    {
        $entity = $entity ? $entity : $this->getRepositoryTest()->getMockObject();
        $response = $this->curlHelper
                         ->post($entity->toArray())
                         ->send()
                         ->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertNotNull($data['id']);
    }
}
