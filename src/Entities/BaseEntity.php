<?php

namespace Bludata\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use EntityManager;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity
{
	/**
     * @ORM\Id 
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="createdAt")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updatedAt", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", name="deletedAt", nullable=true)
     */
    private $deletedAt;

    /**
     * Se marcado como true, remove das propriedade do tipo ArrayCollection ou PersistentCollection, todos os registros que tiverem deletedAt
     * 
     * @var boolean
     */
    protected $removeDeletedAt = true;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new DateTime();

        EntityManager::getRepository(get_class($this))
                     ->preSave($this)
                     ->validate($this);
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new DateTime();

        EntityManager::getRepository(get_class($this))
                     ->preSave($this)
                     ->validate($this);
    }

    /**
     * Como um registro nunca será de fato deletado do banco de dados, este método terá de ser chamado manualmente, não podendo ser um evento.
     */
    public function preRemove()
    {
        $this->deletedAt = new DateTime();

        return $this;
    }

    /**
     * @ORM\PreUpdate @ORM\PrePersist
     */
    // final public function validateAndPreSave()
    // {
    //     $this->updatedAt = new DateTime();

    //     $className = explode('\\', get_class($this));
    //     $repository = app('Plutus\v1\Interfaces\\'.array_pop($className).'RepositoryInterface');
    //     $repository->validate($this)
    //                ->preSave($this);
    // }

    public function getId()
    {
    	return $this->id;
    }

    public function getCreatedAt()
    {
    	return $this->createdAt;
    }

    public function getUpdatedAt()
    {
    	return $this->updatedAt;
    }

    public function getDeletedAt()
    {
    	return $this->deletedAt;
    }

    protected function getFillable()
    {
        return ['id', 'createdAt', 'updatedAt', 'deletedAt'];
    }

    /**
     * Retona um array com o nome das propriedade que o cliente pode setar para realizar o store
     * É usado principalmente em $this->setPropertiesEntity e nos Controllers.
     * Este método não evita que uma propriedade seja alterada caso tenha seu método set().
     * 
     * @return array
     */
    abstract public function getOnlyStore();

    /**
     * Retona um array com o nome das propriedade que o cliente pode setar para realizar o update.
     * Por padrão retorna os mesmos valores de $this->getOnlyStore().
     * Este método pode ser sobrescrito nas classes filhas.
     * É usado principalmente em $this->setPropertiesEntity e nos Controllers.
     * Este método não evita que uma propriedade seja alterada caso tenha seu método set().
     * 
     * @return array
     */
    public function getOnlyUpdate()
    {
        return $this->getOnlyStore();
    }

    public function setPropertiesEntity(array $data)
    {
        foreach ($data as $key => $value)
        {
            $set = true;

            if (
                ((!isset($data['id']) || !is_numeric($data['id'])) && !in_array($key, $this->getOnlyStore())) 
                || 
                (isset($data['id']) && is_numeric($data['id']) && !in_array($key, $this->getOnlyUpdate()))
            ){
                $set = false;
            }

            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method) && $set)
            {
                $this->$method($value);
            }
        }

        return $this;
    }

    public function toArray()
    {
        $array = [];

        foreach ($this->getFillable() as $key)
        {
            if (is_object($this->$key))
            {
                if ($this->$key  instanceof DateTime)
                {
                    $array[$key] = $this->$key->format('Y-m-d H:i');
                }
                elseif ($this->$key instanceof ArrayCollection || $this->$key instanceof PersistentCollection)
                {
                    $ids = [];
                    foreach ($this->$key->getValues() as $item)
                    {
                        $ids[] = $item->getId();
                    }
                    $array[$key] = $ids;
                }
                else
                {
                    $array[$key] = $this->$key->getId();
                }
            }
            else
            {
                $array[$key] = $this->$key;
            }
        }

        return $array;
    }
}
