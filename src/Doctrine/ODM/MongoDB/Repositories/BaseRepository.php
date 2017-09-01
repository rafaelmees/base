<?php

namespace Bludata\Doctrine\ODM\MongoDB\Repositories;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;
use Bludata\Doctrine\Common\Interfaces\BaseRepositoryInterface;
use Bludata\Doctrine\ORM\Repositories\QueryWorker;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Validator\ValidatorBuilder;

abstract class BaseRepository extends DocumentRepository implements BaseRepositoryInterface
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

    /**
     * Método executado no evento ORM\PreFlush.
     */
    public function preFlush(BaseEntityInterface $entity)
    {
        return $this;
    }

    abstract public function getMessageNotFound();

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

            abort(400, json_encode($errors, JSON_UNESCAPED_UNICODE));
        }

        return true;
    }

    public function getClassMetadata()
    {
        return parent::getClassMetadata();
    }

    public function getEntityName()
    {
        return parent::getDocumentName();
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
     *
     * @return Bludata\Doctrine\Common\Interfaces\BaseEntityInterface
     */
    public function findOrCreate($input)
    {
        if (is_null($input)) {
            return $input;
        }

        if (is_string($input)) {
            if ($decoded = json_decode($input, true)) {
                $input = $decoded;
            }
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

        return $this->find($input);
    }

    /**
     * Marcar um registro como deletado.
     *
     * @param object | int $target
     *
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException Se $target não for encontrado
     *
     * @return Bludata\Doctrine\Common\Interfaces\BaseEntityInterface
     */
    public function remove($target, $abort = true)
    {
        $entity = $this->find($target);
        if ($abort) {
            $this->isUsedByEntitys($entity);
        }
        $this->em()->remove($entity);

        return $entity;
    }

    public function findAllRemoved()
    {
        abort(501, 'Not Implemented');
    }

    public function findRemoved($id, $abort = true)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * @param Bludata\Doctrine\Common\Interfaces\BaseEntityInterface $entity
     *
     * @return self
     */
    public function save(BaseEntityInterface $entity)
    {
        $this->em()->persist($entity);

        return $this;
    }

    /**
     * @param Bludata\Doctrine\Common\Interfaces\BaseEntityInterface $entity
     *
     * @return self
     */
    public function flush(BaseEntityInterface $entity = null)
    {
        $this->em()->flush($entity);

        return $this;
    }

    public function em()
    {
        return parent::getDocumentManager();
    }

    public function isUsedByEntitys(BaseEntityInterface $entity)
    {
    }
}
