<?php

namespace Bludata\Audit\Contracts;

interface AuditInterface
{
    /**
     * Create a new Audit register.
     */
    public function put($key, $value);

    /**
     * Get one or more Audit registers.
     *
     * @param string $type
     */
    public function get($key, $id = null, $type = null);

    /**
     * Delete a Audit register.
     *
     * @param string $key
     */
    public function delete($key);
}
