<?php

namespace Bludata\Lumen\Traits\Tests\Http\Controllers;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;

trait UpdateTrait
{
    public function testUpdate(BaseEntityInterface $entity = null)
    {
        $flushedMockArray = $entity ? $entity->toArray() : $this->getRepositoryTest()->getFlushedMockArray();

        $mockArray = $this->getRepositoryTest()->getMockArray();

        foreach ($this->getController()->getMainRepository()->createEntity()->getOnlyUpdate() as $key) {
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
