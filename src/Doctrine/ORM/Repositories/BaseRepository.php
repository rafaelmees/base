<?php

namespace Bludata\Doctrine\ORM\Repositories;

use Bludata\Common\Annotations\Label;
use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;
use Bludata\Doctrine\Common\Interfaces\BaseRepositoryInterface;
use Bludata\Doctrine\ORM\Helpers\FilterHelper;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityRepository;
use ReflectionClass;
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

    /**
     * Método executado no evento ORM\PreFlush.
     */
    public function preFlush(BaseEntityInterface $entity)
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

            abort(400, json_encode($errors, JSON_UNESCAPED_UNICODE));
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

    public function defaultFilters($queryWorker)
    {
        return $queryWorker;
    }

    /**
     * @return QueryWorker
     */
    public function findAll()
    {
        $queryWorker = $this->createQueryWorker();
        $this->defaultFilters($queryWorker);

        return $queryWorker;
    }

    public function findBy(array $filters, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $queryWorker = $this->findAll();

        foreach ($filters as $key => $value) {
            $queryWorker->andWhere($key, '=', $value);
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $order) {
                $queryWorker->addOrderBy($field, $order);
            }
        }

        if ($limit) {
            $queryWorker->paginate($limit);
        }
        if ($offset) {
            $queryWorker->getBuilder()->setFirstResult($offset);
        }

        return $queryWorker->getResult();
    }

    public function findOneBy(array $filters, $abort = true)
    {
        $queryWorker = $this->findAll();

        foreach ($filters as $key => $value) {
            $queryWorker->andWhere($key, '=', $value);
        }

        $entity = $queryWorker->getOneResult();
        if (!$entity && $abort) {
            abort(404, $this->getMessageNotFound());
        }

        return $entity;
    }

    public function find($id, $abort = true)
    {
        if (is_object($id)) {
            return $id;
        }

        $entity = $this->findAll()
            ->andWhere('id', '=', $id)
            ->getOneResult();

        if (!$entity && $abort) {
            abort(404, $this->getMessageNotFound());
        }

        return $entity;
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

        if (is_string($input) && is_object(json_decode($input)) && is_array(json_decode($input, true))) {
            $input = json_decode($input, true);
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

        if (is_numeric($input) || is_string($input)) {
            return $this->find($input);
        }

        throw new \InvalidArgumentException('O parâmetro $input pode ser um null | string | array | numeric');
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
        FilterHelper::disableSoftDeleteableFilter();

        $removed = $this->findAll()
            ->andWhere('deletedAt', 'isnotnull');

        return $removed;
    }

    public function findRemoved($id, $abort = true)
    {
        $removed = $this->findAllRemoved()
            ->andWhere('id', '=', $id)
            ->getOneResult();

        if (!$removed && $abort) {
            abort(404, $this->getMessageNotFound());
        }

        FilterHelper::enableSoftDeleteableFilter();

        return $removed;
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
        return parent::getEntityManager();
    }

    public function isUsedByEntitys(BaseEntityInterface $entity)
    {
        $entities = [];
        $meta = $this->getClassMetadata();
        $associations = $meta->getAssociationNames();
        foreach ($this->em()->getMetadataFactory()->getAllMetadata() as $metadata) {
            foreach ($metadata->getAssociationNames() as $field) {
                if ($metadata->isAssociationWithSingleJoinColumn($field) &&
                    $metadata->getAssociationTargetClass($field) == $this->getEntityName()
                ) {
                    //ignore cascade
                    $skip = false;
                    foreach ($associations as $association) {
                        if (count($meta->getAssociationMapping($association)['cascade']) &&
                            $meta->getAssociationTargetClass($association) == $metadata->getName()
                        ) {
                            $skip = true;
                            break;
                        }
                    }
                    if ($skip) {
                        continue;
                    }

                    $qb = $this->em()->createQueryBuilder();
                    $qb->select('COUNT(t)')
                        ->from($metadata->getName(), 't')
                        ->andWhere('t.'.$metadata->getAssociationMapping($field)['fieldName'].' = ?1')
                        ->setParameter(1, $entity->getId());

                    //ignore deleted
                    if ($metadata->hasField('deletedAt')) {
                        $qb->andWhere('t.deletedAt IS NULL');
                    } elseif (count($metadata->parentClasses)) {
                        $count = 1;
                        foreach ($metadata->parentClasses as $parent) {
                            $parentMetaData = $this->em()->getClassMetadata($parent);
                            if ($parentMetaData->hasField('deletedAt')) {
                                $id = $parentMetaData->getIdentifierFieldNames()[0];
                                $qb->join($parent, 't'.$count, 'WITH', 't'.$count.'.'.$id.' = t.'.$id)
                                    ->andWhere('t'.$count.'.deletedAt IS NULL');
                                $count++;
                            }
                        }
                    }
                    if ($qb->getQuery()->getSingleScalarResult() > 0) {
                        $annotationReader = new AnnotationReader();
                        $reflection = new ReflectionClass(app($metadata->getName()));
                        $classAnnotations = $annotationReader->getClassAnnotations($reflection);
                        $labelAnnotation = array_filter($classAnnotations, function ($annotation) {
                            return $annotation instanceof Label;
                        });

                        $labelTemp = null;

                        if (is_array($labelAnnotation)) {
                            $labelTemp = array_values($labelAnnotation);
                        }

                        $labelAnnotation = $labelTemp ? $labelTemp[0] : null;
                        $entities[] = $labelAnnotation ? $labelAnnotation->value : $metadata->getTableName();
                    }
                }
            }
        }
        if (count($entities)) {
            abort(404, 'Esse registro está sendo utilizado por: '.implode(', ', $entities).'.');
        }
    }
}
