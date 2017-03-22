<?php

namespace Bludata\Lumen\Services;

use Bludata\Lumen\Common\Interfaces\BaseServiceInterface;

abstract class BaseService implements BaseServiceInterface
{
    /**
     * @var Bludata\Lumen\Repositories\BaseRepository
     */
    protected $mainRepository;

    public function getMainRepository()
    {
        return $this->mainRepository;
    }
}
