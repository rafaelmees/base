<?php

namespace Bludata\Doctrine\ORM\Entities;

use Bludata\Doctrine\Common\Interfaces\BaseEntityInterface;
use Bludata\Doctrine\Common\Interfaces\EntityTimestampInterface;
use Bludata\Doctrine\ORM\Traits\SetPropertiesEntityTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use EntityManager;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
abstract class BaseEntity implements BaseEntityInterface, EntityTimestampInterface
{
    use SetPropertiesEntityTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="createdAt")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updatedAt")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="deletedAt")
     */
    protected $deletedAt;

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

        return $this;
    }

    public function getDiscr()
    {
        return $this->getRepository()->getClassMetadata()->discriminatorValue;
    }

    public function getDiscrName()
    {
        return $this->getRepository()->getClassMetadata()->discriminatorColumn['name'];
    }

    /**
     * Altera o campo updatedAt para forçar o persist da entity.
     */
    public function forcePersist()
    {
        $this->updatedAt = new DateTime();

        return $this;
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
     * @ORM\PostPersist
     */
    public function postPersist()
    {
        $this->getRepository()
            ->postSave($this);
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

    /**
     * @ORM\PostUpdate
     */
    public function postUpdate()
    {
        $this->getRepository()
            ->postSave($this);
    }

    /**
     * @ORM\PreFlush
     */
    public function preFlush()
    {
        $this->getRepository()
            ->preFlush($this);
    }

    public function getRepository()
    {
        return EntityManager::getRepository(get_class($this));
    }

    public function remove($abort = true)
    {
        $this->getRepository()->remove($this, $abort);

        return $this;
    }

    public function restoreRemoved()
    {
        $this->setDeletedAt(null)
            ->save();

        return $this;
    }

    public function save()
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

    final protected function checkOnyExceptInArray($key, array $options = null)
    {
        if (
            $options
            &&
            (
                (isset($options['only']) && is_array($options['only']) && !in_array($key, $options['only']))
                ||
                (isset($options['except']) && is_array($options['except']) && in_array($key, $options['except']))
            )
        ) {
            return false;
        }

        return true;
    }

    public function toArray(array $options = null)
    {
        $classMetadata = $this->getRepository()->getClassMetadata();
        $array = [];

        foreach ($this->getFillable() as $key) {
            $metaDataKey = $classMetadata->hasField($key) ? $classMetadata->getFieldMapping($key) : null;

            if ($this->checkOnyExceptInArray($key, $options)) {
                if (is_object($this->$key)) {
                    if ($this->$key instanceof DateTime) {
                        if ($this->$key) {
                            $dateFormat = 'Y-m-d';

                            if ($metaDataKey) {
                                switch ($metaDataKey['type']) {
                                    case 'datetime':
                                        $dateFormat = 'Y-m-d H:i:s';
                                        break;

                                    case 'time':
                                        $dateFormat = 'H:i:s';
                                        break;

                                    default:
                                        break;
                                }
                            }
                            $array[$key] = $this->$key->format($dateFormat);
                        }
                    } elseif ($this->$key instanceof ArrayCollection || $this->$key instanceof PersistentCollection) {
                        $ids = [];
                        foreach ($this->$key->getValues() as $item) {
                            $ids[] = $item->getId();
                        }
                        $array[$key] = $ids;
                    } else {
                        $array[$key] = $this->$key->getId();
                    }
                } else {
                    if ($metaDataKey['type'] == 'decimal') {
                        $array[$key] = (float) $this->$key;
                    } else {
                        $array[$key] = $this->$key;
                    }
                }
            }
        }

        return $array;
    }
}
