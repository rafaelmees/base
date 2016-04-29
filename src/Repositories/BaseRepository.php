<?php

namespace Bludata\Repositories;

use Bludata\Entities\BaseEntity;

abstract class BaseRepository extends QueryWorker
{
	/**
     * @param Bludata\Entities\BaseEntity $entity
     * 
     * @return Bludata\Repositories\BaseRepository
     */    
	public function remove(BaseEntity $entity)
	{
		$entity->onRemove();

		return $this->save($entity);
	}

	/**
	 * Valida se existe algum dado da entidade que precisa ser corrigido
	 * 
     * @param Bludata\Entities\BaseEntity $entity
     * 
     * @return Bludata\Repositories\BaseRepository
     */ 
	public function validate(BaseEntity $entity)
	{
		return $this;
	}
}
