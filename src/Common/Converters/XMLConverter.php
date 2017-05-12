<?php

namespace Bludata\Common\Converters;

use Bludata\Common\Annotations\XML\Entity;
use Bludata\Common\Annotations\XML\Field;

class XMLConverter extends Converter
{
    public function toString($element)
    {
        if (count(get_class_annotations($element))) {
            return $this->convertXMLAnnotationElement($element);
        }

        throw new \Exception('Yet not implemented');
    }

    protected function convertXMLAnnotationElement($element)
    {
        $xmlEntityAnnotations = get_class_annotations($element, Entity::class);
        if (count($xmlEntityAnnotations) > 1) {
            throw new \Exception('Only one XML "Entity" annotation is permited when converting to string');
        }

        $xmlEntityAnnotation = array_pop($xmlEntityAnnotations);
        $xmlFieldAnnotations = get_property_annotations($element, null, Field::class);
        if (empty($xmlFieldAnnotations)) {
            return $xmlEntityAnnotation->toString();
        }

        $content = '';
        foreach ($xmlFieldAnnotations as $field => $fieldAnnotation) {
            $fieldAnnotation = array_values($fieldAnnotation)[0];
            $value = null;
            $reflectProperty = new \ReflectionProperty($element, $field);
            if (is_object($element)) {
                $value = $element->$field;
            }

            if (is_array($value)) {
                $subContent = '';
                foreach ($value as $item) {
                    $subContent .= $this->convertXMLAnnotationElement($item);
                }
                $value = $subContent;
            }

            $content .= $fieldAnnotation->toString($value);
        }

        return $xmlEntityAnnotation->toString($content);
    }
}
