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
}
