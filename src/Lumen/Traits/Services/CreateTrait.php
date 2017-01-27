<?php

namespace Bludata\Lumen\Traits\Services;

trait CreateTrait
{
    public function store(array $data)
    {
        $entity = $this->mainRepository->createEntity();

        $entity->setPropertiesEntity($data);

        $this->prePersistEntity($entity);

        $entity->save();

        return $entity;
    }
}
