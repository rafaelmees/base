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
}
