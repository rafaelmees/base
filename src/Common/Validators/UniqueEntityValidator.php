<?php

namespace Bludata\Common\Validators;

use Bludata\Common\Annotations\Label;
use Bludata\Doctrine\ORM\Helpers\FilterHelper;
use Datetime;
use Doctrine\Common\Annotations\AnnotationReader;
use EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use ReflectionClass;
use ReflectionProperty;

class UniqueEntityValidator extends ConstraintValidator
{
    public function getMessage($entity, $constraint)
    {
        $entityName = $entity->getRepository()->getEntityName();

        $message = $constraint->message;

        $classAnnotations = $this->getAnnotations('Class', $entityName);
        $labelAnnotation  = array_filter($classAnnotations, function($annotation){
            return $annotation instanceof Label;
        });

        $labelTemp = null;
        if(is_array($labelAnnotation)) {
            $labelTemp = array_values($labelAnnotation);
        }

        $labelAnnotation = $labelTemp ? $labelTemp[0] : null;

        if($labelAnnotation) {
            $message = str_replace('%entity%', ucfirst($labelAnnotation->value), $message);
        }

        /*
         * Campos para a validação informado na annotation
         */
        $fields = array_map('trim', $constraint->fields);
        foreach ($fields as $field) {
            $propertyAnnotations = $this->getAnnotations('Property', $entityName, $field);

            $labelAnnotation = array_filter($propertyAnnotations, function($annotation){
                return $annotation instanceof Label;
            });

            $labelTemp = null;
            if(is_array($labelAnnotation)) {
                $labelTemp = array_values($labelAnnotation);
            }

            $labelAnnotation = $labelTemp ? $labelTemp[0] : null;

            if($labelAnnotation) {
                $message = str_replace('%'.$field.'%', ucfirst($labelAnnotation->value), $message);
            }
        }

        return $message;
    }

    /**
     * Busca as annotations de uma classe ou das propriedades da classe
     * @param  string $type  [Class or Property]
     * @param  [type] $class [Name class]
     * @param  [type] $field [Field to get annotations]
     * @return [array]
     */
    public function getAnnotations($type = 'Class', $entity, $field = null):array
    {
        $annotationReader = new AnnotationReader();

        if($type == 'Property' && is_null($field)) {
            abort(501, 'Necessário informar o campo');
        }

        $reflection  = $type == 'Class' ? new ReflectionClass($entity) : new ReflectionProperty($entity, $field);
        $annotations = $type == 'Class' ? $annotationReader->getClassAnnotations($reflection) : $annotationReader->getPropertyAnnotations($reflection);

        return $annotations;
    }

    public function validate($value, Constraint $constraint)
    {
        $entity = $this->context->getRoot();
        $fields = array_map('trim', $constraint->fields);
        $validate = true;

        /*
         * Desabilita os filtros utilizados pelo doctrine
         */
        FilterHelper::disableSoftDeleteableFilter();

        foreach ($constraint->disabledFilters as $filter) {
            if (EntityManager::getFilters()->isEnabled($filter)) {
                EntityManager::getFilters()->disable($filter);
            }
        }

        $query = $entity->getRepository()->findAll($constraint->withDefaultFilters);

        foreach ($fields as $field) {
            $methodGet = 'get' . ucfirst($field);
            if (empty($entity->$methodGet())) {
                $validate = false;
                break;
            }

            $val = $entity->$methodGet();
            if ($val instanceof Datetime) {
                $val = $val->format('Y-m-d H:i:s');
            }

            if (is_object($val)) {
                $val = $val->getId();
            }
            $query->andWhere($field, '=', $val);
        }

        $results = $query->getResult();

        if ($validate && count($results)) {
            $message = $this->getMessage($entity, $constraint);

            if ($results[0]->getDeletedAt() != '') {
                $message .= ' Acesse a lixeira para restaurar o registro.';
            }

            if ($results[0]->getId() != $entity->getId()) {
                $this->context
                     ->buildViolation($message)
                     ->addViolation();
            }
        }

        /*
         * Habilita novamente os filtros utilizados pelo doctrine
         */
        FilterHelper::enableSoftDeleteableFilter();

        foreach ($constraint->disabledFilters as $filter) {
            if (!EntityManager::getFilters()->isEnabled($filter)) {
                EntityManager::getFilters()->enable($filter);
            }
        }
    }
}
