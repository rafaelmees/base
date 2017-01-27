<?php

namespace Bludata\Lumen\Traits\Services;

trait ReadTrait
{
    public function findAll(array $filters = null)
    {
        return $this->mainRepository->findAll()->withFilters($filters);
    }

    public function find($id)
    {
        return $this->mainRepository->find($id);
    }
}
