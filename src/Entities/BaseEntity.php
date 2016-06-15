<?php

namespace Bludata\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use EntityManager;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
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
     * @var \DateTime $createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="createdAt")
     */
    private $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updatedAt")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="deletedAt")
     */
    private $deletedAt;

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

    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->getRepository()
             ->preSave($this)
             ->validate($this);
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->getRepository()
             ->preSave($this)
             ->validate($this);
    }

    public function getRepository()
    {
        return EntityManager::getRepository(get_class($this));
    }

    public function remove()
    {
        $this->getRepository()->remove($this);

        return $this;
    }

    public function save($flush = false)
    {
        $this->getRepository()->save($this);

        return $this;
    }

    public function flush($all = true)
    {
        $this->getRepository()->flush($all ? null : $this);

        return $this;
    }

    public function getId()
    {
    	return $this->id;
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

    public function toArray(array $options = null)
    {
        $array = [];

        foreach ($this->getFillable() as $key)
        {
            $show = true;

            if (
                $options
                &&
                (
                    (isset($options['only']) && is_array($options['only']) && !in_array($key, $options['only']))
                    ||
                    (isset($options['except']) && is_array($options['except']) && in_array($key, $options['except']))
                )
            ){
                $show = false;
            }

            if ($show)
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
        }

        return $array;
    }
}
