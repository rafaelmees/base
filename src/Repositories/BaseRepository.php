<?php

namespace Bludata\Repositories;

abstract class BaseRepository extends QueryWorker
{
	/**
     * Busca todos os registros da entity
     *
     * @return Bludata\Repositories\QueryWorker
     */
    public function findAll()
    {
        return parent::findAll()->defaultFilters();
    }

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

	/**
	 * Adiciona filtros padrão em $this->queryBuilder
	 * 
     * @return Bludata\Repositories\BaseRepository
     */ 
	public function defaultFilters()
	{
		return $this->andWhere('deletedAt', 'isNull');
	}
}
