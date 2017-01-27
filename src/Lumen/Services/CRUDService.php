<?php

namespace Bludata\Lumen\Services;

abstract class CRUDService extends BaseService
{
    use \Bludata\Lumen\Traits\Services\CreateTrait;
    use \Bludata\Lumen\Traits\Services\ReadTrait;
    use \Bludata\Lumen\Traits\Services\UpdateTrait;
    use \Bludata\Lumen\Traits\Services\DeleteTrait;

    abstract public function prePersistEntity(\Bludata\Doctrine\ORM\Entities\BaseEntity $entity);
}
