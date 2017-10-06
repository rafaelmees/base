<?php

namespace Bludata\Common\Annotations\JSON;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("name", type="string")
 * })
 */
class Entity extends JSONAnnotation
{
    public function toString($fields = null)
    {
        $toString = '{';

        if (is_string($fields)) {
            $toString .= $fields;
        }

        $toString .= '}';

        return $toString;
    }
}
