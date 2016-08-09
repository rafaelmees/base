<?php

namespace Bludata\Lumen\Services;

abstract class BaseService
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
