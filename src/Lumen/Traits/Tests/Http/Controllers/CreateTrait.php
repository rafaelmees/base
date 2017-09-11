<?php

namespace Bludata\Lumen\Traits\Tests\Http\Controllers;

trait CreateTrait
{
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

        $this->assertNotNull($data['id']);
    }
}
