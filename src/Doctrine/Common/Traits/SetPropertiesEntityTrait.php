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
use Doctrine\ORM\Mapping\OneToOne;
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
                /**
                 * Armazena o valor enviado pelo usuário.
                 */
                $valueKey = is_string($value) && strlen($value) <= 0 ? null : $value;

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
                    $column = array_values($column);
                    $column = array_shift($column);
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
                    $toObject = array_values($toObject);
                    $toObject = array_shift($toObject);

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
                                   $annotation instanceof ManyToMany
                                   ||
                                   $annotation instanceof OneToOne;
                        });

                        /*
                         * Se for encontrado alguma das anotações, iremos realizar o tratamento adequado para a anotação encontrada.
                         */
                        if ($ormMapping) {
                            $ormMapping = array_values($ormMapping);
                            $ormMapping = array_shift($ormMapping);

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
                                $methodAdd = $toObject->customMethodAdd ? $toObject->customMethodAdd : 'add'.$ormMapping->targetEntity;
                                if (!method_exists($this, $methodAdd) && !$toObject->customMethodAdd) {
                                    throw new \BadMethodCallException('Para utilizar '.get_class($toObject).' em '.get_called_class().'::$'.$key.' você precisar declarar o método '.get_called_class().'::'.$methodAdd.'(), ou, informar o parâmetro '.get_class($toObject).'::customMethodAdd');
                                }

                                if (
                                    (
                                        $ormMapping instanceof OneToMany
                                        ||
                                        $ormMapping instanceof ManyToMany
                                    )
                                    && is_array($valueKey)
                                ) {
                                    if ($ormMapping instanceof OneToMany ) {
                                        /*
                                         * Percorremos a lista original de elementos
                                         */
                                        foreach ($this->$methodGet() as $element) {
                                            /**
                                             * Buscamos no array enviado pelo usuário um elemento com o mesmo ID do original.
                                             */
                                            $data = array_filter($valueKey, function ($value, $key) use ($element) {
                                                return isset($value['id']) && $value['id'] == $element->getId();
                                            }, ARRAY_FILTER_USE_BOTH);

                                            if ($data) {
                                                /**
                                                 * Caso o elemento seja encontrado, então atualizamos na lista original e removemos do array enviado pelo usuário.
                                                 */
                                                $keyData = array_keys($data)[0];

                                                $element->setPropertiesEntity($data[$keyData]);
                                                unset($valueKey[$keyData]);
                                            } else {
                                                /**
                                                 * Caso não seja encontrado, então significa que ele não será mais utilizado na lista, desse modo removemos da lista original.
                                                 */
                                                $this->$methodGet()->removeElement($element);
                                            }
                                        }

                                        /*
                                         * Aqui adicionamos na lista original os novos elementos que ainda não foram persistidos.
                                         */
                                        foreach ($valueKey as $value) {
                                            $this->$methodAdd($repositoryTargetEntity->findOrCreate($value));
                                        }
                                    } else {
                                        $this->$methodSet(new ArrayCollection());

                                        foreach ($valueKey as $value) {
                                            $this->$methodAdd($repositoryTargetEntity->find($value));
                                        }
                                    }
                                }
                            } else if ($ormMapping instanceof OneToOne) {
                                $this->$methodSet($repositoryTargetEntity->findOrCreate($valueKey));
                            }
                        }
                    }
                } else {
                    /*
                     * Seta a propriedade com o valor enviado pelo usuário.
                     */
                    $this->$methodSet($valueKey);
                }
            }
        }

        return $this;
    }
}