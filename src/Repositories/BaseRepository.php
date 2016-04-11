<?php

namespace Bludata\Repositories;

abstract class BaseRepository extends QueryWorker
{
	/**
     * @return Bludata\Repositories\BaseRepository
     */    
	public function remove($entity)
	{
		$entity->onRemove();

		$this->save($entity);

		return $this;
	}
}
