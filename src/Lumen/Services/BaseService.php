<?php

namespace Bludata\Lumen\Services;

abstract class BaseService
{
    /**
     * @var Bludata\LumenRepositories\BaseRepository
     */
    protected $mainRepository;

    public function getMainRepository()
    {
        return $this->mainRepository;
    }
}
