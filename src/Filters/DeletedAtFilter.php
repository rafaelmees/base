<?php

namespace Bludata\Filters;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DeletedAtFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $target, $alias)
    {
        return $alias.'.deletedAt isnull';
    }
}
