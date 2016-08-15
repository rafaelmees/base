<?php

namespace Bludata\Tests\Doctrine\ODM\MongoDB\Entities\Stubs;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Bludata\Doctrine\ODM\MongoDB\Entities\BaseEntity;

/**
 * @ODM\Document(repositoryClass="Bludata\Tests\Doctrine\ODM\MongoDB\Repositories\Stubs\EntityStubRepository")
 * @ODM\HasLifecycleCallbacks
 */
class EntityStub extends BaseEntity
{

    /**
     * @ODM\Field(type="string")
     */
    protected $attr1;

    /**
     * @ODM\Field(type="string")
     */
    protected $attr2;

    private $prePersistWasCall = false;

    private $preUpdateWasCall = false;

    public function getOnlyStore()
    {
        return ['attr1', 'attr2'];
    }

    public function getFillable()
    {
        return array_merge(parent::getFillable(), ['attr1', 'attr2']);
    }

    /**
     * @return
     */
    public function getPrePersistWasCall()
    {
        return $this->prePersistWasCall;
    }

    /**
     * @param  $prePersistWasCall
     *
     * @return static
     */
    public function setPrePersistWasCall($prePersistWasCall)
    {
        $this->prePersistWasCall = $prePersistWasCall;
        return $this;
    }

    /**
     * @return
     */
    public function getPreUpdateWasCall()
    {
        return $this->preUpdateWasCall;
    }

    /**
     * @param  $preUpdateWasCall
     *
     * @return static
     */
    public function setPreUpdateWasCall($preUpdateWasCall)
    {
        $this->preUpdateWasCall = $preUpdateWasCall;
        return $this;
    }

    /**
     * @ODM\PrePersist
     */
    public function prePersist()
    {
        $this->setPrePersistWasCall(true);
    }

    /**
     * @ODM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setPreUpdateWasCall(true);
    }

    /**
     * @return
     */
    public function getAttr2()
    {
        return $this->attr2;
    }

    /**
     * @param  $attr2
     *
     * @return static
     */
    public function setAttr2($attr2)
    {
        $this->attr2 = $attr2;
        return $this;
    }

    /**
     * @return
     */
    public function getAttr1()
    {
        return $this->attr1;
    }

    /**
     * @param  $attr1
     *
     * @return static
     */
    public function setAttr1($attr1)
    {
        $this->attr1 = $attr1;
        return $this;
    }
}
