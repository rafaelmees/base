<?php

namespace Bludata\Tests\Doctrine\ODM\MongoDB\Repositories\Stubs;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;
use Bludata\Doctrine\Common\Interfaces\BaseRepositoryInterface;
use Bludata\Doctrine\ODM\MongoDB\Repositories\BaseRepository;

class EntityStubRepository extends BaseRepository implements BaseRepositoryInterface
{
    public function preSave(BaseEntityInterface $entity)
    {
        return $this;
    }

    public function getMessageNotFound()
    {
        return 'Não encontrado';
    }
}
