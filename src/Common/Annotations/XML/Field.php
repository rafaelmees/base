<?php
namespace Bludata\Common\Annotations\XML;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @Attributes({
 *   @Attribute("name", type="string")
 * })
 */
class Field extends XMLAnnotation
{
}
