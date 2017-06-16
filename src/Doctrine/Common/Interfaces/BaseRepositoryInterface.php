<?php

namespace Bludata\Doctrine\Common\Interfaces;

interface BaseRepositoryInterface
{
    public function validate(BaseEntityInterface $entity);

    public function getEntityName();

    public function query();

    public function findAll();

    public function findOneBy(array $filters, $abort = true);

    public function find($id, $abort = true);

    public function findOrCreate($input);

    public function remove($target, $abort = true);

    public function findAllRemoved();

    public function findRemoved($id, $abort = true);

    public function preSave(BaseEntityInterface $entity);

    public function postSave(BaseEntityInterface $entity);

    public function preFlush(BaseEntityInterface $entity);

    public function save(BaseEntityInterface $entity);

    public function flush(BaseEntityInterface $entity = null);

    public function em();

    public function getClassMetadata();

    public function createEntity();

    public function createQueryWorker();

    public function getMessageNotFound();
}
