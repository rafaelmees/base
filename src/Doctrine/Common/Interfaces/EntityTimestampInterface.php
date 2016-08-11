<?php

namespace Bludata\Doctrine\Common\Interfaces;

interface EntityTimestampInterface
{
    public function getCreatedAt();

    public function getUpdatedAt();

    public function getDeletedAt();

    public function setDeletedAt($date);
}
