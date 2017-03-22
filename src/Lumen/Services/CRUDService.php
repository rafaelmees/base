<?php

namespace Bludata\Lumen\Services;

use Bludata\Doctrine\ORM\Entities\BaseEntity;
use Bludata\Lumen\Common\Interfaces\CRUDServiceInterface;

abstract class CRUDService extends BaseService implements CRUDServiceInterface
{
    use \Bludata\Lumen\Traits\Services\CreateTrait;
    use \Bludata\Lumen\Traits\Services\ReadTrait;
    use \Bludata\Lumen\Traits\Services\UpdateTrait;
    use \Bludata\Lumen\Traits\Services\DeleteTrait;

    abstract public function prePersistEntity(BaseEntity $entity);
}
