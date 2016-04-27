<?php

namespace Bludata\Repositories;

abstract class BaseRepository extends QueryWorker
{
	/**
     * @param Bludata\Entities\BaseEntity $entity
     * 
     * @return Bludata\Repositories\BaseRepository
     */    
	public function remove($entity)
	{
		$entity->onRemove();

		return $this->save($entity);
	}
}
