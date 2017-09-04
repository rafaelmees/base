<?php

namespace Bludata\Doctrine\Common\Interfaces;

interface BaseRepositoryInterface
{
    /**
     * @return boolean|null
     */
    public function validate(BaseEntityInterface $entity);

    public function getEntityName();

    public function query();

    /**
     * @return \Bludata\Doctrine\ORM\Repositories\QueryWorker
     */
    public function findAll();

    public function findOneBy(array $filters, $abort = true);

    public function find($id, $abort = true);

    public function findOrCreate($input);

    public function remove($target, $abort = true);

    /**
     * @return null|\Bludata\Doctrine\ORM\Repositories\QueryWorker
     */
    public function findAllRemoved();

    /**
     * @return null|\Bludata\Doctrine\ORM\Repositories\Bludata\Doctrine\ORM\Entities\BaseEntity
     */
    public function findRemoved($id, $abort = true);

    public function preSave(BaseEntityInterface $entity);

    public function postSave(BaseEntityInterface $entity);

    public function preFlush(BaseEntityInterface $entity);

    public function save(BaseEntityInterface $entity);

    public function flush(BaseEntityInterface $entity = null);

    public function em();

    public function getClassMetadata();

    public function createEntity();

    /**
     * @return \Bludata\Doctrine\ORM\Repositories\QueryWorker
     */
    public function createQueryWorker();

    /**
     * @return string
     */
    public function getMessageNotFound();
}
