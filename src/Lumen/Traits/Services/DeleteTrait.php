<?php

namespace Bludata\Lumen\Traits\Services;

trait DeleteTrait
{
    public function remove($id)
    {
        return $this->mainRepository
                    ->find($id)
                    ->remove();
    }

    public function findAllDestroyed()
    {
        return $this->mainRepository
                    ->findAllRemoved();
    }

    public function restoreDestroyed($id)
    {
        return $this->mainRepository
                    ->findRemoved($id)
                    ->restoreRemoved();
    }
}
