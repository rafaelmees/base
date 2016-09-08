<?php

namespace Bludata\Tests\Common\Traits\Stubs;

use Bludata\Common\Traits\AttributesTrait;

class AttributesTraitStub
{
    use AttributesTrait;

    protected $attr1 = 'Lorem Ipsum';

    private $attr2 = 'Ipsum Lorem';

    public function setAttr1($value)
    {
        $this->attr1 = $value;
    }

    public function getAttr1()
    {
        return $this->attr1;
    }
}
