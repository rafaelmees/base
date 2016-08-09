<?php

namespace Bludata\Doctrine\Common\Interfaces;

interface EntityTimestampInterface
{
    public function getCreatedAt();

    public function getUpdatedAt();

    public function getDeletedAt();

    public function setDeletedAt($date);

    public function forcePersist();

    public function prePersist();

    public function preUpdate();
}
