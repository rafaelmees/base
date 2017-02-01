<?php

namespace Bludata\Doctrine\ORM\Repositories;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;
use Bludata\Doctrine\Common\Interfaces\BaseRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\ValidatorBuilder;

abstract class BaseRepository extends EntityRepository implements BaseRepositoryInterface
{
    /**
     * Método executado nos eventos ORM\PrePersist e ORM\PreUpdate.
     */
    public function preSave(BaseEntityInterface $entity)
    {
        return $this;
    }

    /**
     * Método executado nos eventos ORM\PostPersist e ORM\PostUpdate.
     */
    public function postSave(BaseEntityInterface $entity)
    {
        return $this;
    }

    public function validate(BaseEntityInterface $entity)
    {
        $validator = (new ValidatorBuilder())
                    ->enableAnnotationMapping()
                    ->getValidator();

        $violations = $validator->validate($entity);

        $errors = [];

        if (count($violations)) {
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            abort(400, json_encode($errors));
        }
    }

    public function getClassMetadata()
    {
        return parent::getClassMetadata();
    }

    public function getEntityName()
    {
        return parent::getEntityName();
    }

    public function createEntity()
    {
        return app($this->getEntityName());
    }

    public function createQueryWorker()
    {
        return new QueryWorker($this);
    }

    public function query()
    {
        return $this->createQueryBuilder('t');
    }

    /**
     * @return QueryWorker
     */
    public function findAll()
    {
        return $this->createQueryWorker();
    }

    public function findOneBy(array $filters, $abort = true)
    {
        $entity = parent::findOneBy($filters);

        if (!$entity && $abort) {
            abort(404, $this->getMessageNotFound());
        }

        return $entity;
    }

    public function find($id, $abort = true)
    {
        return is_object($id) ? $id : $this->findOneBy(['id' => $id], $abort);
    }

    /**
     * Inserir ou atualizar um registro.
     *
     * @param null | string | int | array
     *
     * @throws InvalidArgumentException Se $input não for null | string | int | array é lançada a exceção
     */
    public function findOrCreate($input)
    {
        if (is_null($input)) {
            return $input;
        }

        if (is_string($input)) {
            $input = json_decode($input, true);
        }

        if (is_numeric($input)) {
            return $this->find($input);
        }

        if (is_array($input)) {
            if (array_key_exists('id', $input) && $input['id']) {
                $object = $this->find($input['id']);
            } else {
                $object = $this->createEntity();
            }

            $object->setPropertiesEntity($input);

            return $object;
        }

        throw new InvalidArgumentException('O parâmetro $input pode ser um null | string | int | array');
    }

    /**
     * Marcar um registro como deletado.
     *
     * @param object | int $target
     *
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException Se $target não for encontrado
     *
     * Bludata\Doctrine\Common\Interfaces
     */
    public function remove($target)
    {
        $entity = $this->find($target);

        $this->em()->remove($entity);

        return $entity;
    }

    /**
     * @param Bludata\Doctrine\Common\Interfaces\BaseEntityInterface $entity
     *
     * @return Bludata\Doctrine\ORM\Repositories\QueryWorker
     */
    public function save(BaseEntityInterface $entity)
    {
        $this->em()->persist($entity);

        return $this;
    }

    /**
     * @param Bludata\Doctrine\Common\Interfaces\BaseEntityInterface $entity
     *
     * @return Bludata\Doctrine\ORM\Repositories\QueryWorker
     */
    public function flush(BaseEntityInterface $entity = null)
    {
        $this->em()->flush($entity);

        return $this;
    }

    public function em()
    {
        return parent::getEntityManager();
    }
}
