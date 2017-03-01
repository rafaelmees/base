<?php
namespace Bludata\Common\Annotations\XML;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @Attributes({
 *   @Attribute("name", type="string")
 * })
 */
class Field
{
    /**
     * @Required
     */
    protected $name;

    public function __construct(array $values)
    {
        $this->name = $values['name'];
    }

    /**
     * @return Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }
}
