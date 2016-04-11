<?php

namespace Bludata\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity
{
	/**
     * @ORM\Id 
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new DateTime();
    }

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

    public function onRemove()
    {
        $this->deletedAt = new DateTime();

        return $this;
    }

    protected function getFillable()
    {
        return ['id', 'createdAt', 'updatedAt', 'deletedAt'];
    }

    public function setPropertiesEntity(array $data, array $options = null)
    {
        foreach ($data as $key => $value)
        {
            $method = 'set'.ucfirst($key);

            $set = true;

            if (isset($options['except']) && count($options['except']))
            {
                if (in_array($key, $options['except']))
                {
                    $set = false;
                }
            }

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
