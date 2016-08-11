<?php

namespace Bludata\Tests\Doctrine\ODM\MongoDB\Entities\Stubs;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Bludata\Doctrine\ODM\MongoDB\Entities\BaseEntity;

/**
 * @ODM\Document(repositoryClass="Bludata\Tests\Doctrine\ODM\MongoDB\Repositories\Stubs\EntityStubRepository")
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
