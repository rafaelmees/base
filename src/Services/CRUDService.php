<?php

namespace Bludata\Services;

use Bludata\Entities\BaseEntity;

abstract class CRUDService extends BaseService
{
	abstract public function prePersistEntity(BaseEntity $entity);

	public function findAll()
	{
		return $this->mainRepository->findAll();
	}

	public function store(array $data)
	{
		$entity = $this->mainRepository->getNewInstanceEntity();

        $entity->setPropertiesEntity($data);

        $this->prePersistEntity($entity);

        $this->mainRepository->save($entity);

        return $entity;
	}

	public function update($id, array $data)
	{
		$entity = $this->mainRepository->find($id);

        $entity->setPropertiesEntity($data);
        
        $this->prePersistEntity($entity);

        $this->mainRepository->save($entity);

        return $entity;
	}

	public function remove($id)
	{
		$entity = $this->mainRepository->find($id);

		$this->mainRepository->remove($entity);

		return $entity;
	}
}
