<?php

namespace Bludata\Services;

use Bludata\Entities\BaseEntity;
use EntityManager;

abstract class CRUDService extends BaseService
{
	abstract public function prePersistEntity(BaseEntity $entity);

	public function findAll(array $filters = null)
	{
		return $this->mainRepository->findAll()->withFilters($filters);
	}

	public function find($id)
	{
		return $this->mainRepository->find($id);
	}

	public function store(array $data)
	{
		$entity = $this->mainRepository->createEntity();

        $entity->setPropertiesEntity($data);

        $this->prePersistEntity($entity);

        $entity->save();

        return $entity;
	}

	public function update($id, array $data)
	{
		$entity = $this->mainRepository->find($id);

        $entity->setPropertiesEntity($data);

        $this->prePersistEntity($entity);

        $entity->save();

        return $entity;
	}

	public function remove($id)
	{
		return $this->mainRepository
					->find($id)
					->remove();
	}
}
