<?php

namespace Bludata\Lumen\Traits\Services;

trait UpdateTrait
{
    public function update($id, array $data)
    {
        $entity = $this->mainRepository->find($id);

        $entity->setPropertiesEntity($data);

        $this->prePersistEntity($entity);

        $entity->save();

        return $entity;
    }
}
