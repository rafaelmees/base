<?php

namespace Bludata\Doctrine\Common\Interfaces;

interface EntityTimestampInterface
{
    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    public function getDeletedAt();

    /**
     * @return null|\Bludata\Doctrine\ORM\Entities\BaseEntity
     */
    public function setDeletedAt($date);
}
