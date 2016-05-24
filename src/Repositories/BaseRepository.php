<?php

namespace Bludata\Repositories;

use Bludata\Entities\BaseEntity;
use Doctrine\ORM\EntityRepository;

abstract class BaseRepository extends EntityRepository
{
	abstract function preSave(BaseEntity $entity);

	abstract function validate(BaseEntity $entity);

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

	protected function createQueryWorker()
	{
		return new QueryWorker($this);
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

        if (!$entity && $abort)
        {
            abort(404, $this->getMessageNotFound());
        }

        return $entity;
	}

	public function find($id, $abort = true)
	{
		return is_object($id) ? $id : $this->findOneBy(['id' => $id], $abort);
	}

    /**
     * Inseri ou atualiza um registro
     *
     * @param null | string | int | array
     *
     * @return null | Bludata\Entities\BaseEntity
     *
     * @throws InvalidArgumentException Se $input não for null | string | int | array é lançada a exceção
     */
    public function findOrCreate($input)
    {
        if (is_null($input))
        {
            return $input;
        }

        if (is_string($input))
        {
            $input = json_decode($input, true);
        }

        if (is_numeric($input))
        {
            return $this->find($input);
        }

        if (is_array($input))
        {
            if (array_key_exists('id', $input) && $input['id'])
            {
                $object = $this->find($input['id']);
            }
            else 
            {
                $object = $this->createEntity();
            }

            $object->setPropertiesEntity($input);

            return $object;
        }

        throw new InvalidArgumentException('O parâmetro $input pode ser um null | string | int | array');
    }

	/**
     * Marca um registro como deletado
     *
     * @param object | int $target
     *
     * @return Bludata\Entities\BaseEntity
     *
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException Se $target não for encontrado
     */
	public function remove($target)
	{
		return $this->find($target)
					->preRemove();
	}
}
