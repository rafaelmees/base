<?php

namespace Bludata\Doctrine\ORM\Repositories;

use Doctrine\ORM\Query;

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
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

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
    protected $tables = [];
    /**
     * @var array
     */
    protected $queryFields = [];
    /**
     * @var array
     */
    protected $expressions = [];
    /**
     * @var array
     */
    protected $groupFields = [];
    /**
     * @var array
     */
    protected $field = [];
    /**
     * @var int
     */
    protected $position = 0;
    /**
     * @var array
     */
    protected $fieldValue = [];

    public function __construct($repository)
    {
        $this->em = $repository->em();
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
    public function getQuery()
    {
        $query = $this->queryBuilder->getQuery();

        if (method_exists($query, 'setHint')) {
            $query->setHint(Query::HINT_INCLUDE_META_COLUMNS, true);
        }

        return $query;
    }

    /**
     * Retorna um array com os objetos do resultado de $this->queryBuilder.
     *
     * @return array
     */
    public function getResult()
    {
        $query = $this->getQuery();

        if (method_exists($query, 'getResult')) {
            return $query->getResult();
        }

        return $query->execute();
    }

    /**
     * Retorna um objeto do resultado de $this->queryBuilder.
     *
     * @return Bludata\Doctrine\ORM\Entities\BaseEntity | null
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
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @return QueryWorker
     */
    public function setBuilder($builder)
    {
        $this->queryBuilder = $builder;

        return $this;
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
     * @return QueryWorker
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
            //monta os joins
            $this->fieldValue = [
                'value'     => $value,
                'operation' => $operation,
            ];
            $newAliasField = $this->associationQueryFields($field);
            $alias = $newAliasField['alias'];
            $field = $newAliasField['field'];
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
            //monta os joins
            $newAliasField = $this->associationQueryFields($field);
            $alias = $newAliasField['alias'];
            $field = $newAliasField['field'];
        }
        $this->queryBuilder->orWhere($this->makeExpression($field, $operation, $value, $alias));

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
                //monta os joins
                $newAliasField = $this->associationQueryFields($field);
                $alias = $newAliasField['alias'];
                $field = $newAliasField['field'];
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
        if (strpos($field, '.') > 0) {
            //monta os joins
            $newAliasField = $this->associationQueryFields($field);
        }
        if (count($this->queryFields) > 0) {
            foreach ($this->queryFields as $item) {
                $parts = [];
                if (strpos($item, ' AS ')) {
                    $item = str_replace(')', '', str_replace('(', '', $item));
                    $parts = explode('AS', $item);
                    $item = trim($parts[0]);
                }
                if (!in_array($item, $this->groupFields)) {
                    $this->queryBuilder->addGroupBy($item);
                    $this->groupFields[] = $item;
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
            //monta os joins
            $newAliasField = $this->associationQueryFields($field);
            $alias = $newAliasField['alias'];
            $field = $newAliasField['field'];
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
                $this->associationQueryFields($value);
            } elseif (is_array($value)) {
                $alias = $this->tableAlias();
                $this->queryBuilder->join($this->getFullFieldName($key, self::DEFAULT_TABLE_ALIAS), $alias);
                foreach ($value as $valueField) {
                    $this->queryFields[] = $this->getFullFieldName($valueField, $alias);
                }
            } else {
                $this->queryFields[] = $value;
            }
        }
        $this->queryBuilder->select(implode(',', $this->queryFields));

        return $this;
    }

    /**
     * Add association join and select fields.
     *
     * @param associationField.fkField
     * @param $field
     */
    public function associationQueryFields($campo)
    {
        $this->field = $campo;
        $pos = strpos($campo, '.');
        if ($pos > 0) {
            $arr = explode('.', $campo);
            $lastField = end($arr);

            if (count($arr) == 2 && $arr[0] == self::DEFAULT_TABLE_ALIAS) {
                //não é um campo composto
                return [
                    'field' => $lastField,
                    'alias' => $arr[0],
                ];
            }

            $tempMeta = '';
            foreach ($arr as $key => $value) {
                $this->position = $key;

                if ($this->position < count($arr) - 1) {
                    $dados = $this->getMetaAndAliases();

                    $alias = $dados['alias'];
                    $parentAlias = $dados['parentAlias'];

                    if ($tempMeta) {
                        $meta = $tempMeta;
                        $tempMeta = '';
                    } else {
                        $meta = $dados['parentMeta'];
                    }

                    if ($meta->isAssociationWithSingleJoinColumn($value)) {
                        //manyToOne
                        $association = $meta->getAssociationMapping($value);

                        $this->setLeftJoin(
                            $meta->getAssociationTargetClass($value),
                            $association['joinColumns'][0]['referencedColumnName'],
                            $association['fieldName'],
                            $alias,
                            $parentAlias
                        );
                    } elseif ($meta->isCollectionValuedAssociation($value)) {
                        $association = $meta->getAssociationMapping($value);
                        if (empty($association['mappedBy']) && empty($association['joinTable'])) {
                            //não tem como fazer o join
                            $this->critical(sprintf('"%s" não é uma associação válida', $campo));
                            continue;
                        }
                        if (!empty($association['joinTable'])) {
                            //manyToMany
                            $this->setManyToManyJoin(
                                $this->getFullFieldName($association['fieldName'], $parentAlias),
                                $alias,
                                $this->setManyToManyValuedCondition($association, $alias, $arr)
                            );
                        } else {
                            //oneToMany

                            $this->setLeftJoin(
                                $meta->getAssociationTargetClass($value),
                                $this->getTargetField($dados['meta'], $meta, $value),
                                $meta->getIdentifierColumnNames()[0],
                                $alias,
                                $parentAlias
                            );
                        }
                    } elseif ($meta->isSingleValuedAssociation($value)) {
                        //oneToOne
                        $association = $meta->getAssociationMapping($value);

                        $this->setLeftJoin(
                            $meta->getAssociationTargetClass($value),
                            $this->getTargetField($dados['meta'], $meta, $value),
                            $meta->getIdentifierColumnNames()[0],
                            $alias,
                            $parentAlias
                        );
                    } else {
                        //subClass
                        if (count($meta->subClasses) > 0) {
                            $temp = $this->getSubClassFields($meta, $value);
                            if (!empty($temp['meta'])) {
                                $this->setLeftJoin($temp['table'], $temp['targetField'], $temp['parentField'], $alias, $parentAlias);
                                $tempMeta = $temp['meta'];
                            }
                        }
                    }
                }
            }
        } else {
            //não possui joins
            $this->position = 0;
            $meta = $this->getClassMetadata();
            $lastField = $campo;
            $alias = self::DEFAULT_TABLE_ALIAS;
        }
        //adiciona o campo ao select
        $this->setQueryField($meta, $lastField, $alias);

        return [
            'field' => $lastField,
            'alias' => $alias,
        ];
    }

    /**
     * Get the classMetadata and alias from the current position in the field.
     *
     * @return string
     */
    private function getMetaAndAliases()
    {
        $arr = explode('.', $this->field);
        $meta = $this->getClassMetadata();
        $metaAnterior = [];
        $parent = '';
        $alias = '';

        for ($i = 0; $i <= $this->position; $i++) {
            $metaAnterior = $meta;

            if ($meta->hasAssociation($arr[$i])) {
                $class = $meta->getAssociationTargetClass($arr[$i]);
            } elseif (count($meta->subClasses) > 0) {
                $temp = $this->getSubClassFields($meta, $arr[$i]);
                if (!empty($temp['meta'])) {
                    $class = $temp['table'];
                }
            }

            $meta = $this->em->getClassMetadata($class);

            if ($i < $this->position) {
                $parent .= $parent != '' ? '_' : '';
                $parent .= $arr[$i];
            }
            if ($i == $this->position) {
                $alias .= $parent;
                $alias .= $parent != '' ? '_' : '';
                $alias .= $arr[$i];
            }
        }

        if ($parent == '' && $alias != '') {
            $parent = self::DEFAULT_TABLE_ALIAS;
        }
        if ($alias == '') {
            $alias = self::DEFAULT_TABLE_ALIAS;
        }

        return [
            'meta'        => $meta,
            'parentMeta'  => $metaAnterior,
            'alias'       => $alias,
            'parentAlias' => $parent,
        ];
    }

    /**
     * Create a join.
     *
     * @param string $table
     * @param string $field
     * @param string $parentField
     * @param string $alias
     * @param string $parentAlias
     */
    private function setJoin($table, $field, $parentField, $alias, $parentAlias)
    {
        if (!in_array($alias, $this->tables)) {
            $condition = $this->getFullFieldName($field, $alias).' = '.$this->getFullFieldName($parentField, $parentAlias);
            $this->queryBuilder->join($table, $alias, 'WITH', $condition);
            $this->tables[] = $alias;
        }
    }

    /**
     * Create a left join with optional where.
     *
     * @param string $table
     * @param string $field
     * @param string $parentField
     * @param string $alias
     * @param string $parentAlias
     * @param bool   $withWhere
     */
    private function setLeftJoin($table, $field, $parentField, $alias, $parentAlias, $withWhere = false)
    {
        if (!in_array($alias, $this->tables)) {
            $condition = $this->getFullFieldName($field, $alias).' = '.$this->getFullFieldName($parentField, $parentAlias);
            $this->queryBuilder->leftJoin($table, $alias, 'WITH', $condition);
            if ($withWhere) {
                $this->queryBuilder->andWhere($condition);
            }
            $this->tables[] = $alias;
        }
    }

    /**
     * Create a condition with the value.
     *
     * @param array  $association
     * @param string $alias
     * @param array  $arr
     *
     * @return mix|null
     */
    private function setManyToManyValuedCondition($association, $alias, $arr)
    {
        if (empty($this->fieldValue['value']) || $this->position < count($arr) - 2) {
            return null;
        }
        $targetField = $this->position == count($arr) - 1 ? $association['joinTable']['joinColumns'][0]['referencedColumnName'] : end($arr);

        return $this->makeExpression($targetField, $this->fieldValue['operation'], $this->fieldValue['value'], $alias);
    }

    /**
     * Create a manyToMany join.
     *
     * @param string $table
     * @param string $alias
     * @param mix    $condition
     */
    private function setManyToManyJoin($table, $alias, $condition = null)
    {
        if (!in_array($alias, $this->tables)) {
            if ($condition) {
                $this->queryBuilder->join($table, $alias, 'WITH', $condition);
            } else {
                $this->queryBuilder->join($table, $alias);
            }
            $this->tables[] = $alias;
        }
    }

    /**
     * Add the field in the select field list.
     *
     * @param $meta
     * @param $value
     * @param $alias
     */
    private function setQueryField($meta, $value, $alias)
    {
        $campo = $this->getFullFieldName($value, $alias);

        if ($meta->isSingleValuedAssociation($value) && $value != $alias) {
            $targetField = $meta->getAssociationMapping($value)['joinColumns'][0]['referencedColumnName'];
            $alias = $alias == self::DEFAULT_TABLE_ALIAS ? substr($campo, strpos($campo, '.') + 1) : $alias.'_'.$targetField;
            $campo = 'IDENTITY('.$campo.') '.$alias;
        } elseif ($this->position > 0) {
            $campo = '('.$campo.') AS '.$alias.'_'.$value;
        }
        // acrescenta o campo ao select
        $this->queryFields[] = $campo;
    }

    /**
     * Get the fields to create a join with a subClass.
     *
     * @param $meta
     * @param $value
     *
     * @return string
     */
    private function getSubClassFields($meta, $value)
    {
        foreach ($meta->subClasses as $subClass) {
            $delimiter = strpos($subClass, '/') > 0 ? '/' : '\\';
            $temp = explode($delimiter, $subClass);
            $tempMeta = $this->em->getClassMetadata($subClass);
            if (end($temp) == $value) {
                return [
                    'table'       => $subClass,
                    'parentField' => $meta->getIdentifierColumnNames()[0],
                    'targetField' => $tempMeta->getIdentifierColumnNames()[0],
                    'meta'        => $tempMeta,
                ];
            }
        }
    }

    /**
     * Get the target field.
     *
     * @param $meta
     * @param $parentMeta
     * @param $value
     *
     * @return string
     */
    private function getTargetField($meta, $parentMeta, $value)
    {
        if (count($parentMeta->parentClasses) > 0) {
            foreach ($parentMeta->parentClasses as $classe) {
                $associationsByTargetClass = $meta->getAssociationsByTargetClass($classe);
                if (count($associationsByTargetClass) > 0) {
                    $parentTable = lcfirst(substr($classe, strrpos($classe, strpos($classe, '\\') !== false ? '\\' : '/') + 1));
                    $field = $this->searchAssociationField($associationsByTargetClass, $parentTable, $value);
                    if ($field) {
                        return $field;
                    }
                }
            }
        }
        $associationsByTargetClass = $meta->getAssociationsByTargetClass($parentMeta->getName());
        $field = $this->searchAssociationField($associationsByTargetClass, lcfirst($parentMeta->getTableName()), $value);
        if ($field) {
            return $field;
        }

        return $meta->getIdentifierColumnNames()[0];
    }

    /**
     * Search the field in the associations list.
     *
     * @param $associationsByTargetClass
     * @param string $parentTable
     * @param $value
     *
     * @return string
     */
    private function searchAssociationField($associationsByTargetClass, $parentTable, $value)
    {
        foreach ($associationsByTargetClass as $table => $association) {
            if ($table == $parentTable && $association['inversedBy'] == $value) {
                return $association['fieldName'];
            }
        }
    }

    /**
     * @param mixed  $field
     * @param string $expression
     * @param string $alias
     */
    private function getSelectExpression($expression, $field, $alias, $fieldAlias = self::DEFAULT_TABLE_ALIAS)
    {
        $validExpressions = ['SUM', 'MIN', 'MAX', 'AVG', 'COUNT'];
        if (in_array(trim(strtoupper($expression)), $validExpressions)) {
            if (strpos($field, '.') === false) {
                $field = getFullFieldName($field, $fieldAlias);
            }
            $this->queryFields[] = sprintf('%s(%s) AS %s', $expression, $field, $alias);
        }
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
        $originalValue = $value;

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
            case 'memberof':
                $expression = ':memberId MEMBER OF '.$field;
                $this->queryBuilder->setParameter('memberId', $originalValue);
                break;
            case 'like':
                $expression = $this->queryBuilder->expr()->like('LOWER('.$field.')', strtolower($value));
                break;
            case 'notlike':
                $expression = $this->queryBuilder->expr()->notLike($field, $value);
                break;
            case 'isinstanceof':
                $expression = $alias.' INSTANCE OF '.$value;
                break;
            case 'notinstanceof':
                $expression = $alias.' NOT INSTANCE OF '.$value;
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
