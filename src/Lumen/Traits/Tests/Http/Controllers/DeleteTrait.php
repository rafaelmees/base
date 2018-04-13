<?php

namespace Bludata\Lumen\Traits\Tests\Http\Controllers;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;

trait DeleteTrait
{
    public function testDestroy(BaseEntityInterface $entity = null)
    {
        $entity = $entity ? $entity : $this->getRepositoryTest()->getFlushedMockObject();

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
