<?php
namespace Bludata\Common\Annotations\XML;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("name", type="string")
 * })
 */
class Entity
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
