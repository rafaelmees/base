<?php

namespace Bludata\Lumen\Traits\Tests\Http\Controllers;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;

trait CreateTrait
{
    public function testStore(BaseEntityInterface $entity = null)
    {
        $entityArray = $entity ? $entity->toArray() : $this->getRepositoryTest()->getMockArray();
        $response = $this->curlHelper
                         ->post($entityArray)
                         ->send()
                         ->getResponse();

        $this->assertEquals(200, $response['code']);

        $data = json_decode($response['data'], true);

        $this->assertNotNull($data['id']);
    }
}
