<?php

namespace Bludata\Doctrine\Common\Traits;

use Bludata\Common\Helpers\FormatHelper;
use Bludata\Doctrine\Common\Annotations\ToObject;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use ReflectionClass;
use ReflectionProperty;

trait SetPropertiesEntityTrait
{
    public function setPropertiesEntity(array $data)
    {
        foreach ($data as $key => $value) {
            $set = true;

            if (
                ((!isset($data['id']) || !is_numeric($data['id'])) && !in_array($key, $this->getOnlyStore()))
                ||
                (isset($data['id']) && is_numeric($data['id']) && !in_array($key, $this->getOnlyUpdate()))
            ) {
                $set = false;
            }

            $methodSet = 'set'.ucfirst($key);
            $methodGet = 'get'.ucfirst($key);

            if (method_exists($this, $methodSet) && $set) {
                /*
                 * Seta a propriedade com o valor enviado pelo usuário.
                 */
                $this->$methodSet(is_string($value) && strlen($value) <= 0 ? null : $value);

                /**
                 * Armazena o valor enviado pelo usuário.
                 */
                $valueKey = $this->$methodGet();

                /**
                 * Classes utilizadas para buscar os metadados da classe e suas propriedades.
                 */
                $reflectionClass = new ReflectionClass(get_called_class());
                $reflectionProperty = new ReflectionProperty(get_called_class(), $key);
                $annotationReader = new AnnotationReader();

                $propertyAnnotations = $annotationReader->getPropertyAnnotations($reflectionProperty);

                /**
                 * Busca a anotação Doctrine\ORM\Mapping\Column.
                 */
                $column = array_filter($propertyAnnotations, function ($annotation) {
                    return $annotation instanceof Column;
                });

                if ($column) {
                    $column = $column[0];
                }

                /**
                 * Busca a anotação Bludata\Doctrine\Common\Annotations\ToObject.
                 */
                $toObject = array_filter($propertyAnnotations, function ($annotation) {
                    return $annotation instanceof ToObject;
                });

                /*
                 * Verifica se a propriedade está usando a anotação Bludata\Doctrine\Common\Annotations\ToObject.
                 */
                if ($toObject) {
                    /*
                     * Caso seja um campo de data, utilizamos o método FormatHelper::parseDate para converter o valor enviado pelo usuário para um objeto DateTime.
                     */
                    if ($column instanceof Column && ($column->type == 'date' || $column->type == 'datetime')) {
                        $this->$methodSet(
                            FormatHelper::parseDate($valueKey, ($column->type == 'date' ? 'Y-m-d' : 'Y-m-d H:i:s'))
                        );
                    } else {
                        /**
                         * Busca pelas anotações Doctrine\ORM\Mapping\ManyToOne || Doctrine\ORM\Mapping\OneToMany || Doctrine\ORM\Mapping\ManyToMany.
                         */
                        $ormMapping = array_filter($propertyAnnotations, function ($annotation) {
                            return $annotation instanceof ManyToOne
                                   ||
                                   $annotation instanceof OneToMany
                                   ||
                                   $annotation instanceof ManyToMany;
                        });

                        /*
                         * Se for encontrado alguma das anotações, iremos realizar o tratamento adequado para a anotação encontrada.
                         */
                        if ($ormMapping) {
                            $ormMapping = $ormMapping[0];

                            $targetEntityName = $reflectionClass->getNamespaceName().'\\'.$ormMapping->targetEntity;
                            $targetEntity = new $targetEntityName();
                            $repositoryTargetEntity = $targetEntity->getRepository();

                            /*
                             * Se a propriedade estiver utilizando a anotação Doctrine\ORM\Mapping\ManyToOne e o usuário
                             * informou um número, então buscamos o devido objeto pelo seu id.
                             */
                            if ($ormMapping instanceof ManyToOne && is_numeric($valueKey)) {
                                $this->$methodSet(
                                    $repositoryTargetEntity->find($valueKey)
                                );
                            } elseif (($ormMapping instanceof OneToMany || $ormMapping instanceof ManyToMany) && is_array($valueKey)) {
                                /**
                                 * Caso a propriedade esteja utilizando as anotações Doctrine\ORM\Mapping\OneToMany || Doctrine\ORM\Mapping\ManyToMany,
                                 * então o usuário terá que implementar o método addX?().
                                 * Do contrário será lançada uma BadMethodCallException.
                                 */
                                $methodAdd = 'add'.$ormMapping->targetEntity;
                                if (!method_exists($this, $methodAdd)) {
                                    throw new \BadMethodCallException('Para utilizar Bludata\Doctrine\Common\Annotations\ToObject em '.get_called_class().'::$'.$key.' você precisar declarar o método '.get_called_class().'::'.$methodAdd.'()');
                                }

                                if (
                                    (
                                        $ormMapping instanceof OneToMany
                                        ||
                                        $ormMapping instanceof ManyToMany
                                    )
                                    && is_array($valueKey)
                                ) {
                                    $this->$methodSet(new ArrayCollection());

                                    foreach ($valueKey as $value) {
                                        $this->$methodAdd(
                                            $ormMapping instanceof OneToMany ? $repositoryTargetEntity->findOrCreate($value) : $repositoryTargetEntity->find($value)
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }
}
