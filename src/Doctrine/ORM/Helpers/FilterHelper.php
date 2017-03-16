<?php

namespace Bludata\Doctrine\ORM\Helpers;

use EntityManager;

class FilterHelper
{
    public static function disableSoftDeleteableFilter()
    {
        if (EntityManager::getFilters()->isEnabled('soft-deleteable'))
        {
            EntityManager::getFilters()->disable('soft-deleteable');
        }
    }

    public static function enableSoftDeleteableFilter()
    {
        if (!EntityManager::getFilters()->isEnabled('soft-deleteable'))
        {
            EntityManager::getFilters()->enable('soft-deleteable');
        }
    }
}
