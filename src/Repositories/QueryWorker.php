<?php

namespace Bludata\Repositories;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class QueryWorker
{
    const CUSTOM_FILTERS_KEY = 'custom';
    const DEFAULT_TABLE_ALIAS = 't';

    /**
     * @var Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @var Doctrine\ORM\QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var Doctrine\ORM\Mapping\ClassMetadata
     */
    protected $classMetadata;

    /**
     * @var array
     */
    protected $entitys = [];

    /**
     * @var array
     */
    protected $queryFields = [];

    public function __construct($repository)
    {
        $this->repository = $repository;
        $this->queryBuilder = $this->repository->createQueryBuilder(self::DEFAULT_TABLE_ALIAS);
        $this->classMetadata = $this->repository->getClassMetadata();
    }

    /**
     * Retorna o nome da entity.
     *
     * @return string
     */
    public function getEntityName()
    {
        $this->repository->getEntityName();
    }

    /**
     * Retorna a chave primária da entity.
     *
     * @return string
     */
    public function getPrimaryKeyEntity()
    {
        return $this->getClassMetaData()->identifier[0];
    }

    /**
     * @return Doctrine\ORM\Query
     */
    protected function getQuery()
    {
        return $this->queryBuilder->getQuery()->useResultCache(false)->setHint(Query::HINT_INCLUDE_META_COLUMNS, true);
    }

    /**
     * Retorna um array com os objetos do resultado de $this->queryBuilder.
     *
     * @return array
     */
    public function getResult()
    {
        return $this->getQuery()->getResult();
    }

    /**
     * Retorna um objeto do resultado de $this->queryBuilder.
     *
     * @return Bludata\Entities\BaseEntity | null
     */
    public function getOneResult()
    {
        return $this->getQuery()->getOneOrNullResult();
    }

    /**
     * Converte os objetos de $this->getResult() em array.
     *
     * @return array
     */
    public function toArray(array $options = null)
    {
        $array = [];

        foreach ($this->getResult() as $item) {
            if (method_exists($item, 'toArray')) {
                array_push($array, $item->toArray($options));
            } else {
                array_push($array, $item);
            }
        }

        return $array;
    }

    /**
     * @return Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getClassMetaData()
    {
        return $this->classMetadata;
    }

    /**
     * Retorna a quantidade de elementos em $this->getResult().
     *
     * @return int
     */
    public function count()
    {
        return count($this->getResult());
    }

    /**
     * Aplica filtros em $this->queryBuilder.
     *
     * @param array $filters
     *
     * @return Bludata\Repositories\QueryWorker
     */
    public function withFilters(array $filters = null)
    {
        if ($filters) {
            foreach ($filters as $filter) {
                switch ($filter['type']) {
                    case 'select':
                        $this->select($filter['fields']);
                        break;
                    case 'andWhere':
                        $this->andWhere($filter['field'], $filter['operation'], $filter['value']);
                        break;
                    case 'orWhere':
                        $this->orWhere($filter['field'], $filter['operation'], $filter['value']);
                        break;
                    case 'andHaving':
                        $this->andHaving($filter['field'], $filter['operation'], $filter['value']);
                        break;
                    case 'orHaving':
                        $this->orHaving($filter['field'], $filter['operation'], $filter['value']);
                        break;
                    case 'addGroupBy':
                        $this->addGroupBy($filter['field']);
                        break;
                    case 'addOrderBy':
                        $this->addOrderBy($filter['field'], $filter['order']);
                        break;
                    case 'fkAddOrderBy':
                        $this->fkAddOrderBy($filter['field'], $filter['fkField'], $filter['order']);
                        break;
                    case 'paginate':
                        if (isset($filter['page'])) {
                            $this->paginate($filter['limit'], $filter['page']);
                        } else {
                            $this->paginate($filter['limit']);
                        }
                        break;
                }
            }
        }

        return $this;
    }

    /**
     * Set the page with paginate attribute.
     *
     * @param int $page
     * @param int $limit
     *
     * @return $this
     */
    public function paginate($limit = 25, $page = 0)
    {
        if ($limit > 0) {
            $this->queryBuilder->setMaxResults($limit);
        }

        if ($page > 0) {
            $this->queryBuilder->setFirstResult($page * $limit);
        }

        return $this;
    }

    /**
     * Add  joins.
     *
     * @param string $field
     *
     * @return $array
     */
    private function whereFieldJoin($field, $value = null, $operation = null)
    {
        $arr = explode('.', $field);
        $tempField = end($arr);
        $alias = prev($arr);
            // verifica se está solicitando um many to many
            $meta = $this->getClassMetaData();
        if (!empty($meta->associationMappings[$alias]['joinTable'])) {
            $table = $this->getFullFieldName($meta->associationMappings[$alias]['fieldName'], self::DEFAULT_TABLE_ALIAS);
            $alias = $this->tableAlias();
            if (!$operation) {
                $operation = '=';
            }
            $condicao = $this->makeExpression($tempField, $operation, $value, $alias);
            $this->queryBuilder->leftJoin($table, $alias, 'WITH', $condicao);

            return array('alias' => $alias, 'field' => $tempField);
        }
            //monta os joins
            $this->associationQueryFields($field);
            //monta os dados do where
            $field = $tempField;

        return array('alias' => $alias, 'field' => $field);
    }

    /**
     * Add a "and where" filter.
     *
     * @param string $field
     * @param string $operation
     * @param string $value
     *
     * @return $this
     */
    public function andWhere($field, $operation, $value = null, $alias = self::DEFAULT_TABLE_ALIAS)
    {
        if (strpos($field, '.') > 0) {
            $newValues = $this->whereFieldJoin($field, $value, $operation);
            $alias = $newValues['alias'];
            $field = $newValues['field'];
        }
        $this->queryBuilder->andWhere($this->makeExpression($field, $operation, $value, $alias));

        return $this;
    }

    /**
     * Add a "or where" filter.
     *
     * @param string $field
     * @param string $operation
     * @param string $value
     *
     * @return $this
     */
    public function orWhere($field, $operation, $value = null, $alias = self::DEFAULT_TABLE_ALIAS)
    {
        if (strpos($field, '.') > 0) {
            $newValues = $this->whereFieldJoin($field, $value, $operation);
            $alias = $newValues['alias'];
            $field = $newValues['field'];
        }
        $this->queryBuilder->orWhere($this->makeExpression($field, $operation, $value, $alias));

        return $this;
    }

    /**
     * Add a join filter.
     *
     * @param array  $meta
     * @param string $alias
     *
     * @return $this
     */
    public function manyToManyJoin($meta, $alias, $defaultAlias = self::DEFAULT_TABLE_ALIAS)
    {
        $table = $this->getFullFieldName($meta->associationMappings[$alias]['fieldName'], $defaultAlias);

        if (!in_array($table, $this->entitys)) {
            $this->queryBuilder->join($table, $alias);
            $this->entitys[] = $table;
        }

        return $this;
    }

    /**
     * Create an array of expressions.
     *
     * @param array $conditions
     *
     * @return $this
     */
    private function makeExpressions($conditions, $alias = self::DEFAULT_TABLE_ALIAS)
    {
        $expressions = [];
        foreach ($conditions as $attr) {
            $field = $attr['field'];
            if (strpos($field, '.') > 0) {
                $newValues = $this->whereFieldJoin($field, $attr['value'], $attr['operation']);
                $alias = $newValues['alias'];
                $field = $newValues['field'];
            }

            $expressions[] = $this->makeExpression($field, $attr['operation'], $attr['value'], $alias);
        }

        return $expressions;
    }

    /**
     * Add a "group by" key.
     *
     * @param string $field
     */
    public function addGroupBy($field)
    {
        $alias = self::DEFAULT_TABLE_ALIAS;
        $newAliasField = $this->fieldJoin($this->getClassMetaData(), $field, $alias);
        $alias = $newAliasField['alias'];
        $field = $newAliasField['field'];
        if (count($this->queryFields) > 0) {
            if (!in_array($this->getFullFieldName($field, $alias), $this->queryFields)) {
                $this->queryFields[] = $this->getFullFieldName($field, $alias);
            }
            foreach ($this->queryFields as $item) {
                if (strpos($item, '(') === false) {
                    $this->queryBuilder->addGroupBy($item);
                }
            }
        }

        return $this;
    }

    /**
     * Add a "and having" filter.
     *
     * @param string $field
     * @param string $operation
     * @param string $value
     *
     * @return $this
     */
    public function andHaving($field, $operation, $value = null)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Add a "or having" filter.
     *
     * @param string $field
     * @param string $order
     */
    public function orHaving($field, $operation, $value = null)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Add a "order by" filter.
     *
     * @param string $field
     * @param string $order
     */
    public function addOrderBy($field, $order = 'ASC')
    {
        $alias = self::DEFAULT_TABLE_ALIAS;
        if (strpos($field, '.') > 0) {
            $newValues = $this->whereFieldJoin($field);
            $alias = $newValues['alias'];
            $field = $newValues['field'];
        }
        $this->queryBuilder->addOrderBy($this->getFullFieldName($field, $alias), $order);

        return $this;
    }

    /**
     * Add a "order by" filter.
     *
     * @param string $field
     * @param string $order
     */
    public function fkAddOrderBy($field, $fkField, $order = 'ASC')
    {
        $alias = $this->tableAlias();
        $this->queryBuilder->join($this->getFullFieldName($field), $alias);
        $this->queryBuilder->addOrderBy($this->getFullFieldName($fkField, $alias), $order);

        return $this;
    }

    /**
     * Add a select statement.
     *
     * @param associationField.fkField
     * @param $field
     */
    public function select($fields)
    {
        foreach ($fields as $key => $value) {
            if (is_int($key)) {
                $valor = $value;
                if (is_array($value)) {
                    if (!empty($value['expression'])) {
                        //é uma expressão
                        $expression = ['expression' => $value['expression'], 'alias' => $value['alias']];
                        $valor = $value['field'];
                        $this->associationQueryFields($valor, $expression);
                    }
                } else {
                    $this->associationQueryFields($valor);
                }
            } elseif (is_array($value)) {
                $alias = $this->tableAlias();
                $this->queryBuilder->join($this->getFullFieldName($key, self::DEFAULT_TABLE_ALIAS), $alias);
                foreach ($value as $valueField) {
                    $this->queryFields[] = $this->getFullFieldName($valueField, $alias);
                }
            }
        }
        $this->queryBuilder->select(implode(',', $this->queryFields));

        return $this;
    }
    /**
     * get the repository.
     *
     * @param associationField.fkField
     */
    private function getPathRepository($newEntity)
    {
        return app()->getRepositoryInterface($newEntity);
    }
    /**
     * get the class metadata.
     *
     * @param associationField.fkField
     */
    private function getMetaRepository($entity)
    {
        $repository = $this->getPathRepository($entity);

        return $repository ? $repository->getClassMetaData() : [];
    }

    /**
     * Add association join and select fields.
     *
     * @param associationField.fkField
     * @param $field
     */
    public function associationQueryFields($value, $expression = 0)
    {
        $pos = strpos($value, '.');
        if ($pos > 0) {
            $fk = substr($value, 0, $pos);
            $field = substr($value, ($pos + 1));
            $alias = $fk;
            if (substr_count($value, '.') > 1) {
                // tem mais de um join para chegar ao campo
                $count = 0;
                $arr = explode('.', $value);
                $field = end($arr);
                $fkTemp = $fk;
                $arrLength = count($arr);
                foreach ($arr as $key => $entity) {
                    if ($count == 0) {
                        $campo = $this->fkAssociation($this->getClassMetaData(), $entity, $arr[$count + 1], $entity, self::DEFAULT_TABLE_ALIAS);
                        if ($campo && !in_array($campo, $this->queryFields)) {
                            if ($expression == 0) {
                                $this->queryFields[] = $campo;
                            }
                        }
                    } elseif (($count + 1) < $arrLength) {
                        if ($this->getPathRepository(ucfirst($fkTemp))) {
                            $meta = $this->getMetaRepository(ucfirst($fkTemp));
                            $campo = $this->fkAssociation($meta, $entity, $arr[$count + 1], $entity, $fkTemp);
                        } else {
                            //busca a entidade correta
                            if ($count > 1) {
                                $metaAnterior = null;
                                $fkAnterior = ucfirst($arr[$count - 2]);
                                //verifica se a entidade anterior era um oneToMany
                                if (count($this->getPathRepository($fkAnterior)) == 0) {
                                    //verifica se havia uma entidade antes
                                    if (!empty($arr[$count - 3])) {
                                        $fkAnterior = ucfirst($arr[$count - 3]);
                                    } else {
                                        // pega a entidade default
                                        $fkAnterior = self::DEFAULT_TABLE_ALIAS;
                                        $metaAnterior = $meta;
                                    }
                                }
                                $metaAlias = $this->getFkMetaAlias($fkAnterior, $fkTemp, $metaAnterior);
                            } else {
                                $metaAlias = $this->getFkMetaAlias(self::DEFAULT_TABLE_ALIAS, $fkTemp, $this->getClassMetaData());
                            }
                            $fkTemp = $metaAlias['alias'];
                            $meta = $this->getMetaRepository(ucfirst($fkTemp));
                            $campo = $this->fkAssociation($meta, $entity, $arr[$count + 1], $entity, $fkTemp);
                        }
                        if ($campo && ($expression == 0 || ($expression != 0 && $count == ($arrLength - 1)))) {
                            $this->addQueryField($campo, $expression);
                        }
                    }
                    $fkTemp = $entity;
                    ++$count;
                }
            } else {
                if (empty($this->getClassMetaData()->associationMappings[$fk]) && count($this->getClassMetaData()->subClasses) > 0) {
                    //ignorado não é possível criar o join, provavelmente está no pai chamando o filho.
                    return $this;
                }
                // realiza o join para retornar o valor do campo
                $campo = $this->fkAssociation($this->getClassMetaData(), $fk, $field, $alias, self::DEFAULT_TABLE_ALIAS);
                $this->addQueryField($campo, $expression);
            }
        } else {
            $valor = $this->getFullFieldName($value);
            if (!empty($this->getClassMetaData()->associationMappings[$value])) {
                // é uma FK, retorna o ID
                $valor = 'IDENTITY('.$valor.') '.$value;
            }
            $this->addQueryField($valor, $expression);
        }
    }

    /**
     * Add a field or expression in the select array.
     *
     * @param string field
     * @param mix expression
     */
    private function addQueryField($campo, $expression = 0)
    {
        if ($campo && !in_array($campo, $this->queryFields)) {
            if ($expression == 0) {
                $this->queryFields[] = $campo;
            } else {
                //verifica se pode adicionar a expressão
                if (strpos($campo, ')') === false) {
                    $this->getSelectExpression($expression['expression'], $campo, $expression['alias']);
                } else {
                    $parts = explode(')', $campo);
                    //remove o alias
                    array_pop($parts);
                    $this->getSelectExpression($expression['expression'], implode(')', $parts), $expression['alias']);
                }
            }
        }
    }

    /**
     * Add a join statement.
     *
     * @todo  ignorar registros com deletedAt == true
     * @todo  validar a associação a partir do aluno: processos.ordensServico.servicoOrdemServico.itensServicoOrdemServico.itemServico
     *
     * @param $meta - getClassMetaData
     * @param $fk
     * @param $field
     * @param $alias
     * @param $defaultAlias
     */
    public function fkAssociation($meta, $fk, $field, $alias, $defaultAlias)
    {
        if ($association = $meta->associationMappings[$fk]) {
            if (!in_array($association['targetEntity'], $this->entitys)) {
                if (!empty($association['joinColumns'])) {
                    $condition = $this->getFullFieldName($association['fieldName'], $defaultAlias).' = '.$this->getFullFieldName($association['joinColumns'][0]['referencedColumnName'], $alias);
                    $this->queryBuilder->leftJoin($association['targetEntity'], $alias, 'WITH', $condition);
                    $this->queryBuilder->andWhere($condition);
                    $this->entitys[] = $association['targetEntity'];
                } else {
                    //está buscando de um arrayCollection
                    $repository = explode('\\', $association['targetEntity']);
                    if (!$association['mappedBy']) {
                        if (!empty($association['joinTable'])) {
                            $this->manyToManyJoin($meta, $fk, $defaultAlias);

                            return $this->getFullFieldName($field, $fk);
                        }

                        return;
                    }
                    $meta = $this->getMetaRepository(end($repository));

                    return $this->fkArrayAssociation($meta, $association['mappedBy'], $field, lcfirst(end($repository)), $defaultAlias, $association['targetEntity']);
                }
            }
            //verifica se o campo existe
            if ($this->getPathRepository(ucfirst($fk))) {
                $meta = $this->getMetaRepository(ucfirst($fk));
            } else {
                //busca a entidade correta
                $metaAlias = $this->getFkMetaAlias($defaultAlias, $fk, $meta);
                $meta = $metaAlias['meta'];
                $alias = $metaAlias['alias'];
            }
            if (empty($meta->associationMappings[$field]) && empty($meta->fieldMappings[$field])) {
                return;
            } elseif (!empty($meta->associationMappings[$field]) && empty($meta->associationMappings[$field]['joinColumns'])) {
                return;
            }
            //retorna o campo
            return '('.$this->getFullFieldName($field, $alias).') AS '.$this->getFullFieldName($field, $alias, '_');
        }
    }

    /**
     * Add joins and return the new field and alias.
     *
     * @param $meta - getClassMetaData
     * @param $field
     * @param $alias
     *
     * @return array
     */
    public function fieldJoin($meta, $field, $alias)
    {
        if (strpos($field, '.') > 0) {
            $arr = explode('.', $field);
            $fieldTemp = end($arr);
            $alias = prev($arr);
            if (!empty($meta->associationMappings[$alias]['joinTable'])) {
                $this->manyToManyJoin($meta, $alias);
            } else {
                //monta os joins
                $this->associationQueryFields($field);
            }
            $field = $fieldTemp;
        }

        return ['alias' => $alias, 'field' => $field];
    }

    /**
     * @TODO GROUP_CONCAT()
     * Add a join statement from array collection
     *
     * @param $meta - getClassMetaData
     * @param $fk
     * @param $field
     * @param $alias
     * @param $defaultAlias
     * @param $targetEntity
     */
    public function fkArrayAssociation($meta, $fk, $field, $alias, $defaultAlias, $targetEntity)
    {
        if ($association = $meta->associationMappings[$fk]) {
            if (!in_array($targetEntity, $this->entitys)) {
                $condition = $this->getFullFieldName($association['fieldName'], $alias).' = '.
                $this->getFullFieldName($association['joinColumns'][0]['referencedColumnName'], $defaultAlias);
                $this->queryBuilder->join($targetEntity, $alias, 'WITH', $condition);
                $this->queryBuilder->andWhere($condition);
                $this->entitys[] = $targetEntity;
            }

            return '('.$this->getFullFieldName($field, $alias).') AS '.$this->getFullFieldName($field, $alias, '_');
        }
    }

    /**
     * @param string $alias
     * @param string $fk
     * @param array  $meta
     *
     * @return array
     */
    public function getFkMetaAlias($alias, $fk, $meta = null)
    {
        if (self::DEFAULT_TABLE_ALIAS != $alias) {
            $meta = $this->getMetaRepository(ucfirst($alias));
        }
        $repository = explode('\\', $meta->associationMappings[$fk]['targetEntity']);
        $meta = $this->getMetaRepository(end($repository));
        $alias = lcfirst(end($repository));

        return array('meta' => $meta, 'alias' => $alias);
    }

    /**
     * @param $field
     * @param string $alias
     *
     * @return string
     */
    protected function getFullFieldName($field, $alias = self::DEFAULT_TABLE_ALIAS, $separator = '.')
    {
        return sprintf('%s%s%s', $alias, $separator, $field);
    }

    /**
     * @param $field
     * @param $operation
     * @param null   $value
     * @param string $alias
     */
    protected function makeExpression($field, $operation, $value = null, $alias = self::DEFAULT_TABLE_ALIAS)
    {
        if (!is_array($value)) {
            $value = $this->queryBuilder->expr()->literal($value);
        }
        if ($field) {
            $field = $this->getFullFieldName($field, $alias);
        }
        $expression = null;
        switch (strtolower($operation)) {
            case '>':
                $expression = $this->queryBuilder->expr()->gt($field, $value);
                break;
            case '=':
                $expression = $this->queryBuilder->expr()->eq($field, $value);
                break;
            case '<':
                $expression = $this->queryBuilder->expr()->lt($field, $value);
                break;
            case '>=':
                $expression = $this->queryBuilder->expr()->gte($field, $value);
                break;
            case '<=':
                $expression = $this->queryBuilder->expr()->lte($field, $value);
                break;
            case '<>':
                $expression = $this->queryBuilder->expr()->neq($field, $value);
                break;
            case 'isnull':
                $expression = $this->queryBuilder->expr()->isNull($field);
                break;
            case 'isnotnull':
                $expression = $this->queryBuilder->expr()->isNotNull($field);
                break;
            case 'in':
                $expression = $this->queryBuilder->expr()->in($field, $value);
                break;
            case 'orx':
                $expression = $this->queryBuilder->expr()->orX()->addMultiple($this->makeExpressions($value, $alias));
                break;
            case 'andx':
                $expression = $this->queryBuilder->expr()->andX()->addMultiple($this->makeExpressions($value, $alias));
                break;
            case 'notin':
                $expression = $this->queryBuilder->expr()->notIn($field, $value);
                break;
            case 'contains':
                /*
                 * @todo implementar o metodo contains
                 */
                // $expression = $this->queryBuilder->expr()->contains($field, $value);
                break;
            case 'like':
                $expression = $this->queryBuilder->expr()->like('LOWER('.$field.')', strtolower($value));
                break;
            case 'notlike':
                $expression = $this->queryBuilder->expr()->notLike($field, $value);
                break;
            case 'isinstanceof':
                $expression = $this->queryBuilder->expr()->isInstanceOf($field, $value);
                break;
            case 'between':
                $expression = $this->queryBuilder->expr()->between($field, $this->queryBuilder->expr()->literal($value[0]), $this->queryBuilder->expr()->literal($value[1]));
                break;
            case 'dateparteq':
                $expression = $this->queryBuilder->expr()->eq("DATEPART('".$value['format']."', ".$field.')', $value['value']);
        }

        return $expression;
    }

    /**
     * @return string
     */
    protected function tableAlias()
    {
        return self::DEFAULT_TABLE_ALIAS.count($this->queryBuilder->getAllAliases());
    }
}
