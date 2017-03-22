<?php

namespace Bludata\Lumen\Common\Interfaces;

use Bludata\Doctrine\ORM\Entities\BaseEntity;

interface CRUDServiceInterface extends BaseServiceInterface
{
    public function store(array $data);

    public function findAll(array $filters = null);

	public function find($id);

	public function update($id, array $data);

    public function remove($id);

	public function findAllDestroyed();

	public function restoreDestroyed($id);

	public function prePersistEntity(BaseEntity $entity);
}
