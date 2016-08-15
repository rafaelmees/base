<?php

namespace Bludata\Lumen\Authentication\JWT\Interfaces;

interface AuthRepositoryInterface
{
    /**
     * Retorna um usuário pelo token.
     *
     * @param string $token
     *
     * @return stdClass
     */
    public function getUserLoggedByToken($token);
}
