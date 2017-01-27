<?php

namespace Bludata\Lumen\Traits\Tests\Http\Controllers;

trait UpdateTrait
{
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
}
